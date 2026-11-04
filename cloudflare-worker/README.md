# Worker de Cloudflare — GEO Tracker IA

Worker `little-feather-e318`. La clave de Anthropic vive solo aquí, como *secret*; el HTML nunca la contiene.

## Rutas

| Ruta | Descripción |
|---|---|
| `GET /?url=<url>` | HTML de la URL como texto (la usa el frontend actual). |
| `GET /api/fetch?url=<url>&render=0\|1` | JSON `{ ok, status, finalUrl, html }`. `render=1` usa Browser Rendering (501 si no está activado). |
| `POST /api/plan` | Body `{ prompt }`. Devuelve el JSON de Anthropic tal cual (modelo `claude-sonnet-5-5`). |
| `POST /api/claude` | Proxy con streaming SSE. Acepta solo `model`, `max_tokens` (máx. 16000), `system`, `messages`, `temperature`. Modelos: `claude-sonnet-5-5`, `claude-opus-5-5`, `claude-haiku-5-5`. |

Las URLs se validan contra SSRF (solo http/https; se rechazan `localhost`, `127.*`, `10.*`, `172.16-31.*`, `192.168.*`, `169.254.*`, `::1`, etc., también en cada redirección). No hay ninguna función para saltarse captchas ni protecciones anti-bot.

## Despliegue

```bash
npm i -g wrangler
```

```bash
cd cloudflare-worker
npm install        # instala @cloudflare/puppeteer (lo importa el Worker)
```

```bash
wrangler login
```

```bash
wrangler secret put ANTHROPIC_API_KEY   # pega la clave cuando la pida
```

Edita `ALLOWED_ORIGINS` en `wrangler.toml` y añade el dominio donde sirves la app (el valor `null` ya cubre el HTML abierto como `file://`). Después:

```bash
wrangler deploy
```

## Probar en local

Crea `cloudflare-worker/.dev.vars` (está en `.gitignore`) con `ANTHROPIC_API_KEY=sk-ant-...` y ejecuta `wrangler dev`.

```bash
curl "http://localhost:8787/api/fetch?url=https://example.com"
curl -i -X OPTIONS http://localhost:8787/api/claude -H "Origin: null" -H "Access-Control-Request-Method: POST"
curl "http://localhost:8787/api/fetch?url=http://localhost"   # debe dar 400
```

## Opcional: límite de uso con KV (30 peticiones / 10 min / IP)

Se aplica a `/api/claude` y `/api/plan`. Sin el binding `RATE_KV` no hay límite.

```bash
wrangler kv namespace create RATE_KV
```

Copia el `id` que devuelve y descomenta en `wrangler.toml`:

```toml
[[kv_namespaces]]
binding = "RATE_KV"
id = "EL_ID_QUE_DEVOLVIO_EL_COMANDO"
```

Vuelve a ejecutar `wrangler deploy`.

## Opcional: Browser Rendering (`/api/fetch?render=1`)

1. Activa Browser Rendering en el panel de Cloudflare (Workers & Pages → Browser Rendering). Requiere un plan que lo incluya.
2. Descomenta en `wrangler.toml`:

```toml
[browser]
binding = "BROWSER"
```

3. `wrangler deploy`. Sin el binding, `render=1` responde 501.
