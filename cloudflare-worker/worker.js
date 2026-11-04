// Worker de Cloudflare para GEO Tracker IA.
//
// Rutas:
//   GET  /?url=<url>                      -> HTML de la URL como texto (compatibilidad con el frontend)
//   GET  /api/fetch?url=<url>&render=0|1  -> JSON { ok, status, finalUrl, html }
//   POST /api/plan   { prompt }           -> JSON de Anthropic tal cual (sin streaming)
//   POST /api/claude { model, max_tokens, system, messages, temperature } -> stream SSE de Anthropic
//
// Secretos / variables / bindings (ver wrangler.toml y README.md):
//   ANTHROPIC_API_KEY  (secret)   clave de Anthropic. Nunca se registra ni se devuelve.
//   ALLOWED_ORIGINS    (var)      orígenes permitidos separados por comas. Incluye "null" para file://.
//   RATE_KV            (KV, opc.) activa el límite de 30 peticiones / 10 min / IP.
//   BROWSER            (opc.)     Browser Rendering, solo para /api/fetch?render=1.

import puppeteer from '@cloudflare/puppeteer';

const ANTHROPIC_URL = 'https://api.anthropic.com/v1/messages';
const ANTHROPIC_VERSION = '2023-06-01';

const DEFAULT_MODEL = 'claude-sonnet-5-5';
const ALLOWED_MODELS = ['claude-sonnet-5-5', 'claude-opus-5-5', 'claude-haiku-5-5'];
const MAX_TOKENS_LIMIT = 16000;
const PLAN_MAX_TOKENS = 8000;

const FETCH_TIMEOUT_MS = 12000;
const RENDER_TIMEOUT_MS = 20000;
const MAX_HTML_BYTES = 3 * 1024 * 1024; // 3 MB
const MAX_REDIRECTS = 5;
const MAX_BODY_BYTES = 2 * 1024 * 1024; // límite del cuerpo de las peticiones POST
const MAX_PROMPT_CHARS = 200000;

const RATE_LIMIT = 30;
const RATE_WINDOW_MS = 10 * 60 * 1000;

const USER_AGENT = 'Mozilla/5.0 (compatible; GEOTrackerBot/1.0)';

/* ───────────────────────── Utilidades de respuesta ───────────────────────── */

function jsonResponse(body, status, extraHeaders) {
  return new Response(JSON.stringify(body), {
    status,
    headers: Object.assign({ 'Content-Type': 'application/json; charset=utf-8' }, extraHeaders || {}),
  });
}

function errorResponse(message, status, extraHeaders) {
  return jsonResponse({ error: { message } }, status, extraHeaders);
}

/* ─────────────────────────────────── CORS ────────────────────────────────── */

function allowedOrigins(env) {
  const raw = typeof env.ALLOWED_ORIGINS === 'string' ? env.ALLOWED_ORIGINS : 'null';
  return raw.split(',').map((s) => s.trim()).filter(Boolean);
}

// Devuelve las cabeceras CORS para el Origin de la petición, o null si no está permitido.
// Las peticiones sin cabecera Origin (curl, servidor a servidor) no necesitan CORS.
function corsHeaders(request, env) {
  const origin = request.headers.get('Origin');
  if (origin === null) return {};
  const list = allowedOrigins(env);
  if (!list.includes('*') && !list.includes(origin)) return null;
  return {
    'Access-Control-Allow-Origin': origin, // "null" cuando el HTML se abre como file://
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Max-Age': '86400',
    'Vary': 'Origin',
  };
}

/* ─────────────────────────── Límite de uso (KV opcional) ─────────────────── */

// Devuelve null si la petición puede continuar, o una Response 429.
async function checkRateLimit(request, env, cors) {
  if (!env.RATE_KV) return null; // sin binding, sin límite
  try {
    const ip = request.headers.get('CF-Connecting-IP') || 'unknown';
    const now = Date.now();
    const windowId = Math.floor(now / RATE_WINDOW_MS);
    const key = 'rl:' + ip + ':' + windowId;
    const count = parseInt((await env.RATE_KV.get(key)) || '0', 10) || 0;
    if (count >= RATE_LIMIT) {
      const retryAfter = Math.max(1, Math.ceil(((windowId + 1) * RATE_WINDOW_MS - now) / 1000));
      return errorResponse(
        'Demasiadas peticiones: máximo ' + RATE_LIMIT + ' cada 10 minutos. Inténtalo más tarde.',
        429,
        Object.assign({ 'Retry-After': String(retryAfter) }, cors)
      );
    }
    // KV no es atómico: el límite es aproximado, suficiente para frenar abusos simples.
    await env.RATE_KV.put(key, String(count + 1), { expirationTtl: 660 });
  } catch (_) {
    // Si KV falla, no bloqueamos al usuario legítimo.
  }
  return null;
}

/* ──────────────────────── Validación de URL (anti-SSRF) ──────────────────── */

function isPrivateIPv4(host) {
  const m = host.match(/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/);
  if (!m) return false;
  const a = Number(m[1]);
  const b = Number(m[2]);
  return (
    a === 0 ||
    a === 10 ||
    a === 127 ||
    (a === 169 && b === 254) ||
    (a === 172 && b >= 16 && b <= 31) ||
    (a === 192 && b === 168) ||
    (a === 100 && b >= 64 && b <= 127) // CGNAT
  );
}

function isPrivateIPv6(host) {
  const h = host.replace(/^\[|\]$/g, '').toLowerCase();
  if (!h.includes(':')) return false;
  if (h === '::1' || h === '::') return true;
  if (/^f[cd][0-9a-f]{2}:/.test(h)) return true; // fc00::/7 (ULA)
  if (/^fe[89ab][0-9a-f]:/.test(h)) return true; // fe80::/10 (link-local)
  // IPv4 mapeada (::ffff:127.0.0.1 o ::ffff:7f00:1)
  const mapped = h.match(/^::ffff:(.+)$/);
  if (mapped) {
    if (mapped[1].includes('.')) return isPrivateIPv4(mapped[1]);
    const parts = mapped[1].split(':');
    if (parts.length === 2) {
      const hi = parseInt(parts[0], 16);
      const lo = parseInt(parts[1], 16);
      return isPrivateIPv4([hi >> 8, hi & 255, lo >> 8, lo & 255].join('.'));
    }
    return true;
  }
  return false;
}

// Devuelve { url } si es válida o { error } con el motivo.
function validateTargetUrl(raw) {
  if (!raw) return { error: 'Falta el parámetro url.' };
  let url;
  try {
    url = new URL(raw);
  } catch (_) {
    return { error: 'URL no válida.' };
  }
  if (url.protocol !== 'http:' && url.protocol !== 'https:') {
    return { error: 'Solo se permiten URLs http o https.' };
  }
  if (url.username || url.password) {
    return { error: 'No se permiten credenciales en la URL.' };
  }
  const host = url.hostname.toLowerCase().replace(/\.$/, '');
  if (
    !host ||
    host === 'localhost' ||
    host.endsWith('.localhost') ||
    host.endsWith('.local') ||
    host.endsWith('.internal') ||
    isPrivateIPv4(host) ||
    isPrivateIPv6(host)
  ) {
    return { error: 'Host no permitido (local o red privada).' };
  }
  return { url };
}

/* ──────────────────────────── Lectura de páginas ─────────────────────────── */

// Lee el cuerpo hasta maxBytes y descarta el resto.
async function readBodyLimited(response, maxBytes) {
  if (!response.body) return '';
  const reader = response.body.getReader();
  const chunks = [];
  let total = 0;
  while (total < maxBytes) {
    const { done, value } = await reader.read();
    if (done) break;
    const remaining = maxBytes - total;
    chunks.push(value.length > remaining ? value.subarray(0, remaining) : value);
    total += Math.min(value.length, remaining);
  }
  try { await reader.cancel(); } catch (_) { /* ya cerrado */ }

  const bytes = new Uint8Array(total);
  let offset = 0;
  for (const c of chunks) { bytes.set(c, offset); offset += c.length; }

  let charset = 'utf-8';
  const ct = response.headers.get('Content-Type') || '';
  const m = ct.match(/charset=["']?([\w-]+)/i);
  if (m) charset = m[1];
  let decoder;
  try { decoder = new TextDecoder(charset); } catch (_) { decoder = new TextDecoder('utf-8'); }
  return decoder.decode(bytes);
}

// Descarga una URL siguiendo redirecciones a mano (máx. MAX_REDIRECTS) para
// validar cada salto contra la lista anti-SSRF. Devuelve { status, finalUrl, html }.
async function fetchPage(startUrl) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), FETCH_TIMEOUT_MS);
  try {
    let current = startUrl;
    for (let hop = 0; hop <= MAX_REDIRECTS; hop++) {
      const res = await fetch(current.toString(), {
        method: 'GET',
        redirect: 'manual',
        signal: controller.signal,
        headers: {
          'User-Agent': USER_AGENT,
          'Accept': 'text/html,application/xhtml+xml,*/*;q=0.8',
          'Accept-Language': 'es-ES,es;q=0.9,en;q=0.8',
        },
      });
      if (res.status >= 300 && res.status < 400 && res.headers.get('Location')) {
        let next;
        try { next = new URL(res.headers.get('Location'), current); } catch (_) { throw new Error('Redirección no válida.'); }
        const check = validateTargetUrl(next.toString());
        if (check.error) throw new Error('Redirección bloqueada: ' + check.error);
        try { await res.body?.cancel(); } catch (_) { /* nada */ }
        current = check.url;
        continue;
      }
      const html = await readBodyLimited(res, MAX_HTML_BYTES);
      return { status: res.status, finalUrl: current.toString(), html };
    }
    throw new Error('Demasiadas redirecciones.');
  } finally {
    clearTimeout(timer);
  }
}

// Renderiza con Cloudflare Browser Rendering. No intenta eludir captchas ni protecciones anti-bot.
async function renderPage(env, startUrl) {
  const browser = await puppeteer.launch(env.BROWSER);
  try {
    const page = await browser.newPage();
    await page.setUserAgent(USER_AGENT);
    await page.setRequestInterception(true);
    page.on('request', (req) => {
      // También se aplica la lista anti-SSRF a cada subpetición y redirección del navegador.
      if (validateTargetUrl(req.url()).error && !req.url().startsWith('data:')) req.abort();
      else req.continue();
    });
    const response = await page.goto(startUrl.toString(), {
      waitUntil: 'networkidle2',
      timeout: RENDER_TIMEOUT_MS,
    });
    const finalUrl = page.url();
    if (validateTargetUrl(finalUrl).error) throw new Error('Redirección bloqueada.');
    let html = await page.content();
    if (html.length > MAX_HTML_BYTES) html = html.slice(0, MAX_HTML_BYTES);
    return { status: response ? response.status() : 200, finalUrl, html };
  } finally {
    try { await browser.close(); } catch (_) { /* nada */ }
  }
}

/* ────────────────────────────────── Rutas ────────────────────────────────── */

// GET /?url=  → HTML como texto, igual que antes.
async function handleLegacyFetch(url, cors) {
  const check = validateTargetUrl(url.searchParams.get('url'));
  if (check.error) return errorResponse(check.error, 400, cors);
  try {
    const page = await fetchPage(check.url);
    return new Response(page.html, {
      status: page.status,
      headers: Object.assign({ 'Content-Type': 'text/plain; charset=utf-8' }, cors),
    });
  } catch (err) {
    return errorResponse(fetchErrorMessage(err), 502, cors);
  }
}

// GET /api/fetch?url=&render=0|1  → JSON
async function handleApiFetch(url, env, cors) {
  const check = validateTargetUrl(url.searchParams.get('url'));
  if (check.error) return errorResponse(check.error, 400, cors);
  const render = url.searchParams.get('render') === '1';

  if (render && !env.BROWSER) {
    return errorResponse('Browser Rendering no está activado en este Worker (falta el binding BROWSER).', 501, cors);
  }
  try {
    const page = render ? await renderPage(env, check.url) : await fetchPage(check.url);
    return jsonResponse(
      { ok: page.status >= 200 && page.status < 300, status: page.status, finalUrl: page.finalUrl, html: page.html },
      200,
      cors
    );
  } catch (err) {
    return errorResponse(fetchErrorMessage(err), 502, cors);
  }
}

function fetchErrorMessage(err) {
  if (err && err.name === 'AbortError') return 'Timeout al leer la URL.';
  if (err && typeof err.message === 'string' && err.message.startsWith('Redirección')) return err.message;
  if (err && err.message === 'Demasiadas redirecciones.') return err.message;
  return 'No se pudo leer la URL.';
}

async function readJsonBody(request) {
  const declared = parseInt(request.headers.get('Content-Length') || '0', 10);
  if (declared > MAX_BODY_BYTES) return { error: 'Cuerpo demasiado grande.', status: 413 };
  const text = await request.text();
  if (text.length > MAX_BODY_BYTES) return { error: 'Cuerpo demasiado grande.', status: 413 };
  try {
    const data = JSON.parse(text);
    if (!data || typeof data !== 'object' || Array.isArray(data)) throw new Error('no objeto');
    return { data };
  } catch (_) {
    return { error: 'El cuerpo debe ser un JSON válido.', status: 400 };
  }
}

function anthropicHeaders(env) {
  return {
    'Content-Type': 'application/json',
    'x-api-key': env.ANTHROPIC_API_KEY,
    'anthropic-version': ANTHROPIC_VERSION,
  };
}

// POST /api/plan { prompt } → JSON de Anthropic tal cual
async function handlePlan(request, env, cors) {
  if (!env.ANTHROPIC_API_KEY) return errorResponse('El servidor no tiene configurada la clave de Anthropic.', 500, cors);
  const body = await readJsonBody(request);
  if (body.error) return errorResponse(body.error, body.status, cors);
  const prompt = body.data.prompt;
  if (typeof prompt !== 'string' || !prompt.trim()) return errorResponse('Falta el campo prompt.', 400, cors);
  if (prompt.length > MAX_PROMPT_CHARS) return errorResponse('El prompt es demasiado largo.', 413, cors);

  let upstream;
  try {
    upstream = await fetch(ANTHROPIC_URL, {
      method: 'POST',
      headers: anthropicHeaders(env),
      body: JSON.stringify({
        model: DEFAULT_MODEL,
        max_tokens: PLAN_MAX_TOKENS,
        messages: [{ role: 'user', content: prompt }],
      }),
    });
  } catch (_) {
    return errorResponse('No se pudo contactar con Anthropic.', 502, cors);
  }
  // Se devuelve el cuerpo y el estado de Anthropic sin tocarlos (el frontend lee data.content[0].text y data.error).
  return new Response(upstream.body, {
    status: upstream.status,
    headers: Object.assign({ 'Content-Type': 'application/json; charset=utf-8' }, cors),
  });
}

// Valida y filtra el cuerpo de /api/claude. Devuelve { payload } o { error }.
function buildClaudePayload(input) {
  const model = input.model === undefined ? DEFAULT_MODEL : input.model;
  if (!ALLOWED_MODELS.includes(model)) {
    return { error: 'Modelo no permitido. Usa uno de: ' + ALLOWED_MODELS.join(', ') + '.' };
  }

  let maxTokens = input.max_tokens === undefined ? PLAN_MAX_TOKENS : input.max_tokens;
  if (typeof maxTokens !== 'number' || !Number.isFinite(maxTokens) || maxTokens < 1) {
    return { error: 'max_tokens debe ser un número positivo.' };
  }
  maxTokens = Math.min(Math.floor(maxTokens), MAX_TOKENS_LIMIT);

  const messages = input.messages;
  if (!Array.isArray(messages) || messages.length === 0 || messages.length > 200) {
    return { error: 'messages debe ser una lista no vacía.' };
  }
  for (const m of messages) {
    if (!m || typeof m !== 'object' || (m.role !== 'user' && m.role !== 'assistant')) {
      return { error: 'Cada mensaje necesita role "user" o "assistant".' };
    }
    if (typeof m.content !== 'string' && !Array.isArray(m.content)) {
      return { error: 'El content de cada mensaje debe ser texto o una lista de bloques.' };
    }
  }

  // Lista blanca de campos: cualquier otro (tools, stream, metadata…) se descarta.
  const payload = { model, max_tokens: maxTokens, messages, stream: true };

  if (input.system !== undefined) {
    if (typeof input.system !== 'string' && !Array.isArray(input.system)) {
      return { error: 'system debe ser texto o una lista de bloques.' };
    }
    payload.system = input.system;
  }
  if (input.temperature !== undefined) {
    if (typeof input.temperature !== 'number' || input.temperature < 0 || input.temperature > 1) {
      return { error: 'temperature debe ser un número entre 0 y 1.' };
    }
    payload.temperature = input.temperature;
  }
  return { payload };
}

// POST /api/claude → proxy con streaming SSE
async function handleClaude(request, env, cors) {
  if (!env.ANTHROPIC_API_KEY) return errorResponse('El servidor no tiene configurada la clave de Anthropic.', 500, cors);
  const body = await readJsonBody(request);
  if (body.error) return errorResponse(body.error, body.status, cors);
  const built = buildClaudePayload(body.data);
  if (built.error) return errorResponse(built.error, 400, cors);

  let upstream;
  try {
    upstream = await fetch(ANTHROPIC_URL, {
      method: 'POST',
      headers: anthropicHeaders(env),
      body: JSON.stringify(built.payload),
    });
  } catch (_) {
    return errorResponse('No se pudo contactar con Anthropic.', 502, cors);
  }

  if (!upstream.ok) {
    // Se reenvía el código y el cuerpo JSON de error de Anthropic.
    return new Response(upstream.body, {
      status: upstream.status,
      headers: Object.assign({ 'Content-Type': 'application/json; charset=utf-8' }, cors),
    });
  }

  return new Response(upstream.body, {
    status: 200,
    headers: Object.assign(
      { 'Content-Type': 'text/event-stream', 'Cache-Control': 'no-cache, no-transform', 'X-Accel-Buffering': 'no' },
      cors
    ),
  });
}

/* ──────────────────────────────── Entrada ───────────────────────────────── */

export default {
  async fetch(request, env) {
    const cors = corsHeaders(request, env);
    if (cors === null) return errorResponse('Origen no permitido.', 403);

    // Preflight en todas las rutas
    if (request.method === 'OPTIONS') return new Response(null, { status: 204, headers: cors });

    try {
      const url = new URL(request.url);
      const path = url.pathname.replace(/\/+$/, '') || '/';

      if (path === '/' ) {
        if (request.method !== 'GET') return errorResponse('Método no permitido.', 405, cors);
        return await handleLegacyFetch(url, cors);
      }
      if (path === '/api/fetch') {
        if (request.method !== 'GET') return errorResponse('Método no permitido.', 405, cors);
        return await handleApiFetch(url, env, cors);
      }
      if (path === '/api/plan' || path === '/api/claude') {
        if (request.method !== 'POST') return errorResponse('Método no permitido.', 405, cors);
        const limited = await checkRateLimit(request, env, cors);
        if (limited) return limited;
        return path === '/api/plan' ? await handlePlan(request, env, cors) : await handleClaude(request, env, cors);
      }
      return errorResponse('Ruta no encontrada.', 404, cors);
    } catch (err) {
      // Solo el nombre del error: nunca cabeceras ni cuerpos que pudieran contener la clave.
      console.error('Error interno del Worker:', err && err.name);
      return errorResponse('Error interno del servidor.', 500, cors);
    }
  },
};
