<?php
session_start();
// Guard: si no hay sesión activa o el rol no es empleado/admin, redirigir al login
if (!isset($_SESSION['usuario']) || (int)($_SESSION['rol'] ?? -1) < 1) {
  header('Location: login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <script>
    // Rol inyectado desde PHP — disponible para todo el JS sin necesidad de fetch
    const SESSION_ROL = <?php echo (int)($_SESSION['rol'] ?? 0); ?>;
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bluease — GEO Tracker Enterprise</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .option.selected-neutral {
      background: rgba(100, 116, 139, 0.12);
      border-color: rgba(100, 116, 139, 0.38);
      color: var(--text-muted);
      font-weight: 600;
    }

    .q-help {
      font-size: 11px;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }
  </style>
</head>

<body>
  <div class="app">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo"><span>B</span></div>
        <div class="sidebar-brand">
          <span class="sidebar-title">Bluease</span>
          <span class="sidebar-subtitle">GEO Tracker</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-label"
          style="font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:rgba(148,163,184,.6);padding:8px 14px 4px;user-select:none">
          Análisis GEO</div>
        <div class="nav-item active" id="nav-1" onclick="goScreen(1)">
          <div class="nav-icon">1</div>
          <span class="nav-label">Configuración</span>
          <div class="nav-status"></div>
        </div>
        <div class="nav-item" id="nav-2" onclick="goScreen(2)">
          <div class="nav-icon">2</div>
          <span class="nav-label">Análisis</span>
          <div class="nav-status"></div>
        </div>
        <div class="nav-item" id="nav-3" onclick="goScreen(3)">
          <div class="nav-icon">3</div>
          <span class="nav-label">Dashboard</span>
          <div class="nav-status"></div>
        </div>
        <div class="nav-item" id="nav-4" onclick="goScreen(4)">
          <div class="nav-icon">4</div>
          <span class="nav-label">Plan de acción</span>
          <div class="nav-status"></div>
        </div>
        <div style="height:1px;background:rgba(255,255,255,.06);margin:10px 14px"></div>
        <div class="nav-section-label"
          style="font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:rgba(148,163,184,.6);padding:8px 14px 4px;user-select:none">
          Contenido</div>
        <div class="nav-item" id="nav-5" onclick="goScreen(5)">
          <div class="nav-icon">✨</div>
          <span class="nav-label">Generar Contenido</span>
          <div class="nav-status"></div>
        </div>
        <div class="nav-item" id="nav-6" onclick="goScreen(6)">
          <div class="nav-icon">📬</div>
          <span class="nav-label">Bandeja de aprobación</span>
          <div class="nav-status" id="bandeja-badge"></div>
        </div>
        <div class="nav-item" id="nav-7" onclick="goScreen(7)">
          <div class="nav-icon">📖</div>
          <span class="nav-label">Historial de Temas</span>
          <div class="nav-status"></div>
        </div>
        <div class="nav-item" id="nav-8" onclick="goScreen(8)">
          <div class="nav-icon">📚</div>
          <span class="nav-label">Biblioteca</span>
        </div>
        <!-- Sección Admin — solo visible para rol 2 -->
        <div id="nav-admin-section" style="display:none">
          <div style="height:1px;background:rgba(255,255,255,.06);margin:10px 14px"></div>
          <div class="nav-section-label"
            style="font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:rgba(148,163,184,.6);padding:8px 14px 4px;user-select:none">
            Administración</div>
          <div class="nav-item" id="nav-9" onclick="goScreen(9)">
            <div class="nav-icon">🔐</div>
            <span class="nav-label">Gestión de Permisos</span>
            <div class="nav-status"></div>
          </div>
        </div>
      </nav>
    </aside>

    <div class="main">
      <header class="topbar">
        <div class="topbar-left">
          <div class="breadcrumb">
            <span class="breadcrumb-item active" id="topbar-item">Configuración</span>
          </div>
        </div>

        <div class="topbar-right">
          <div class="progress-dots">
            <div class="progress-dot active" id="dot-1"></div>
            <div class="progress-dot" id="dot-2"></div>
            <div class="progress-dot" id="dot-3"></div>
            <div class="progress-dot" id="dot-4"></div>
            <div class="progress-dot" id="dot-5"></div>
            <div class="progress-dot" id="dot-6"></div>
            <div class="progress-dot" id="dot-7"></div>
          </div>

          <div class="topbar-divider"></div>

          <button class="theme-btn" onclick="toggleTheme()" title="Cambiar tema">
            <svg class="theme-icon sun-icon" viewBox="0 0 24 24" style="display:none">
              <path
                d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
            </svg>
            <svg class="theme-icon moon-icon" viewBox="0 0 24 24">
              <path
                d="M21.725 14.725a9.75 9.75 0 01-13.45-13.45.75.75 0 01.928.928 8.25 8.25 0 1011.594 11.594.75.75 0 01.928.928z" />
            </svg>
          </button>
        </div>
      </header>

      <main class="content">

        <div class="screen active" id="screen-1">
          <div class="screen-header">
            <h1>Configuración inicial</h1>
            <p>Define tu empresa y los competidores a analizar. El GEO Score se calculará de forma comparativa.</p>
          </div>

          <div class="card">
            <div class="card-header">
              <span class="card-title">Mi empresa</span>
            </div>
            <div class="form-row">
              <div>
                <label>Nombre de la empresa</label>
                <input type="text" class="fi" id="my-name" placeholder="Ej: Mi Empresa"
                  oninput="S.config.myCompany.name=this.value; save()">
              </div>
              <div>
                <label>Dominio web</label>
                <input type="text" class="fi" id="my-domain" placeholder="ejemplo.com"
                  oninput="S.config.myCompany.domain=this.value; save()">
              </div>
            </div>
            <div class="form-full">
              <label>Sector / categoría</label>
              <select class="fs" id="my-sector" onchange="S.config.myCompany.sector=this.value; save()">
                <option>Sostenibilidad en eventos</option>
                <option>SaaS B2B</option>
                <option>Consultoría medioambiental</option>
                <option>Tecnología</option>
                <option>Marketing Digital</option>
              </select>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <span class="card-title">Competidores a analizar</span>
              <span class="card-badge">Opcional</span>
            </div>
            <div class="tw">
              <table>
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Dominio</th>
                    <th>Sector</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody id="comp-body"></tbody>
              </table>
            </div>
            <button class="add-row" onclick="addComp()" style="margin-top: 16px;">
              <span>+</span> Añadir competidor
            </button>
          </div>

          <div style="display:flex;justify-content:flex-end;margin-top:8px">
            <button class="btn btn-primary" style="padding:12px 28px;font-size:14px" onclick="startAnalysis()">
              Iniciar análisis <span>→</span>
            </button>
          </div>
        </div>

        <div class="screen" id="screen-2">
          <div class="screen-header">
            <h1>Análisis GEO</h1>
            <p>Revisión automática de estructura web y verificación manual de presencia en IA generativa.</p>
          </div>

          <div id="prog-section" style="display:none;margin-bottom:24px">
            <div class="card">
              <div class="prog-wrap">
                <div class="prog-track">
                  <div class="prog-fill" id="prog-fill" style="width:0%"></div>
                </div>
                <span class="prog-lbl" id="prog-lbl">0%</span>
              </div>
              <div class="status-list" id="scraping-status"></div>
            </div>
          </div>

          <div class="comp-tabs" id="comp-tabs"></div>

          <div class="card">
            <div class="card-header">
              <span class="card-title">Análisis de <span id="comp-cur-name"
                  style="color:var(--text-primary)"></span></span>
              <button class="btn btn-secondary btn-sm" onclick="reanalizarCompetidor()" id="btn-reanalizar"
                style="display:none">
                ↻ Reanalizar scraping
              </button>
            </div>

            <div style="margin-bottom:28px">
              <div class="section-label">
                <svg class="theme-icon" style="width:16px;height:16px" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
                Estructura web (Scraping Automático)
              </div>
              <div class="check-list" id="auto-checks"></div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:32px;margin-top:32px">
              <div class="section-label">
                <svg class="theme-icon" style="width:16px;height:16px" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z" />
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
                  <line x1="12" y1="19" x2="12" y2="23" />
                  <line x1="8" y1="23" x2="16" y2="23" />
                </svg>
                Input manual (lo que no se puede automatizar)
              </div>

              <div class="manual-progress-wrap">
                <div class="mp-info">
                  <span class="mp-title">Progreso del análisis manual</span>
                  <span class="mp-desc" id="mp-text">0 de 11 métricas completadas</span>
                </div>
                <div class="mp-bar-bg">
                  <div class="mp-bar-fill" id="mp-fill" style="width: 0%"></div>
                </div>
              </div>

              <div id="dynamic-fallback-questions"></div>

              <div class="manual-q">
                <div class="q-label">¿Aparece en respuestas de ChatGPT?</div>
                <div class="q-help">ℹ️ Hacer la búsqueda manualmente y marcar</div>
                <div class="options" data-q="chatgpt">
                  <button class="option" onclick="selectOpt(this,'chatgpt','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'chatgpt','parcial')">Parcialmente</button>
                  <button class="option" onclick="selectOpt(this,'chatgpt','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'chatgpt','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">¿Aparece en respuestas de Perplexity?</div>
                <div class="q-help">ℹ️ Hacer la búsqueda manualmente y marcar</div>
                <div class="options" data-q="perplexity">
                  <button class="option" onclick="selectOpt(this,'perplexity','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'perplexity','parcial')">Parcialmente</button>
                  <button class="option" onclick="selectOpt(this,'perplexity','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'perplexity','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">¿Aparece en respuestas de Google SGE?</div>
                <div class="q-help">ℹ️ Hacer la búsqueda manualmente y marcar</div>
                <div class="options" data-q="googlesge">
                  <button class="option" onclick="selectOpt(this,'googlesge','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'googlesge','parcial')">Parcialmente</button>
                  <button class="option" onclick="selectOpt(this,'googlesge','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'googlesge','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">¿Tiene artículos comparativos?</div>
                <div class="q-help">ℹ️ Revisar el blog manualmente</div>
                <div class="options" data-q="comparativos">
                  <button class="option" onclick="selectOpt(this,'comparativos','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'comparativos','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'comparativos','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">¿Cita fuentes y datos verificados en sus artículos?</div>
                <div class="q-help">ℹ️ Revisar 2-3 artículos</div>
                <div class="options" data-q="fuentes">
                  <button class="option" onclick="selectOpt(this,'fuentes','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'fuentes','parcial')">Parcialmente</button>
                  <button class="option" onclick="selectOpt(this,'fuentes','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'fuentes','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">¿Tiene Schema Markup / datos estructurados?</div>
                <div class="q-help">ℹ️ Usar Schema Markup Validator de Google</div>
                <div class="options" data-q="schema">
                  <button class="option" onclick="selectOpt(this,'schema','si')">Sí</button>
                  <button class="option" onclick="selectOpt(this,'schema','no')">No</button>
                  <button class="option selected-neutral" onclick="selectOpt(this,'schema','nosé')">Sin
                    verificar</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">Valoración general del contenido</div>
                <div class="star-rating" id="star-container">
                  <button class="star-btn" onclick="selectStar(1)">★</button>
                  <button class="star-btn" onclick="selectStar(2)">★</button>
                  <button class="star-btn" onclick="selectStar(3)">★</button>
                  <button class="star-btn" onclick="selectStar(4)">★</button>
                  <button class="star-btn" onclick="selectStar(5)">★</button>
                </div>
              </div>

              <div class="manual-q">
                <div class="q-label">Notas libres</div>
                <textarea class="fi" id="notas-input" placeholder="Escribe aquí tus observaciones..."
                  onchange="updateNotas(this)"></textarea>
              </div>
            </div>
          </div>

          <div class="nav-actions">
            <button class="btn btn-secondary" onclick="goScreen(1)">Anterior</button>
            <button class="btn btn-ghost" onclick="guardarProgreso()" style="margin-left:auto;margin-right:12px;">💾
              Guardar borrador</button>
            <button class="btn btn-primary" onclick="goScreen(3)">Ver Dashboard <span>→</span></button>
          </div>
        </div>

        <div class="screen" id="screen-3">
          <div class="screen-header print-header">
            <div>
              <h1>Dashboard Comparativo GEO</h1>
              <p>Puntuación ponderada y análisis de brechas competitivas.</p>
            </div>
            <div class="dash-meta">
              <span class="last-sync" id="last-sync-date">Última actualización: --</span>
              <button class="btn btn-primary btn-sm no-print" onclick="window.print()">⬇ Exportar PDF</button>
            </div>
          </div>

          <div class="scores-grid" id="scores-grid"></div>

          <div class="card">
            <div class="card-header"><span class="card-title">Desglose de Métricas (RF-11)</span></div>
            <div class="tw">
              <table class="matrix-table" id="matrix-table"></table>
            </div>
            <div class="legend" style="margin-top:16px;font-size:13px;color:var(--text-muted);display:flex;gap:16px;">
              <span><span class="icon-si">✅</span> Sí (Completado)</span>
              <span><span class="icon-parcial">⚠️</span> Parcialmente</span>
              <span><span class="icon-no">❌</span> No (Cero puntos)</span>
            </div>
          </div>

          <div class="card" style="page-break-before: always;">
            <div class="card-header"><span class="card-title">Brechas Competitivas (RF-12)</span></div>
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px">Métricas en las que la competencia
              tiene mejor puntuación que nuestra empresa, ordenadas por impacto.</p>
            <div class="gaps-list" id="brechas-list"></div>
          </div>

          <div class="dash-actions no-print" style="margin-top:32px;display:flex;gap:12px;">
            <button class="btn btn-secondary" onclick="goScreen(2)">Volver al Análisis</button>
            <button class="btn btn-primary" onclick="goScreen(4)">Ver plan →</button>
          </div>
        </div>

        <div class="screen" id="screen-4">
          <div class="screen-header">
            <h1>Plan de acción GEO</h1>
            <p>Ruta priorizada generada por Claude basada en los resultados del análisis.</p>
          </div>

          <div style="display:flex;gap:20px;align-items:flex-start">

            <div style="width:230px;flex-shrink:0">
              <div class="card" style="padding:0;overflow:hidden">
                <div
                  style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                  <span style="font-size:13px;font-weight:600;color:var(--text-primary)">📚 Historial</span>
                  <span id="historial-count" style="font-size:11px;color:var(--text-muted)">0 planes</span>
                </div>
                <div id="historial-list" style="max-height:520px;overflow-y:auto">
                  <div style="padding:24px 16px;text-align:center;color:var(--text-muted);font-size:13px">
                    Aún no hay planes generados
                  </div>
                </div>
              </div>
            </div>

            <div style="flex:1;min-width:0">
              <div style="display:flex;justify-content:center;margin-bottom:20px">
                <button class="btn btn-primary" id="btn-generar-plan" style="padding:12px 28px;font-size:14px"
                  onclick="generarPlanAccion()">
                  ✨ Generar plan con Claude
                </button>
              </div>
              <div id="plan-container">
                <div style="text-align:center;padding:56px 24px;color:var(--text-muted)">
                  <div style="font-size:40px;margin-bottom:12px">📋</div>
                  <div style="font-size:15px">Completa el análisis en las pantallas anteriores</div>
                  <div style="font-size:13px;margin-top:6px;opacity:.7">y pulsa el botón para generar tu plan
                    personalizado</div>
                </div>
              </div>
            </div>

          </div>

          <div class="plan-actions">
            <button class="btn btn-secondary" onclick="goScreen(3)">Dashboard</button>
            <button class="btn btn-primary" onclick="goScreen(5)">Generar Contenido →</button>
          </div>
        </div>

        <div class="screen" id="screen-5">
          <div class="screen-header">
            <h1>Generador de Contenido GEO <span
                style="font-size:12px;background:var(--primary);color:white;padding:2px 8px;border-radius:12px;vertical-align:middle;margin-left:8px;">Machine-First</span>
            </h1>
            <p>Crea artículos estructurados como "Fuente de Datos" listos para ser citados por IA, junto con posts para
              RRSS.</p>
          </div>

          <div class="card" style="margin-bottom:20px;padding:0;overflow:hidden" id="motor-propuestas-card">

            <div
              style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)">
              <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:18px">🤖</span>
                <div>
                  <div style="font-size:14px;font-weight:600;color:var(--text-primary)">Motor de Propuestas Semanal
                  </div>
                  <div style="font-size:12px;color:var(--text-muted)" id="motor-status-label">Genera automáticamente
                    propuestas basadas en tus brechas GEO</div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <button class="btn btn-ghost btn-sm" onclick="toggleMotorConfig()" id="btn-motor-config"
                  style="font-size:12px">⚙️ Configurar</button>
                <button class="btn btn-primary btn-sm" onclick="generarLote()" id="btn-generar-lote"
                  style="font-size:12px">📦 Generar lote</button>
              </div>
            </div>

            <div id="motor-config-panel"
              style="display:none;padding:16px 20px;background:var(--bg-body);border-bottom:1px solid var(--border)">
              <div style="display:flex;gap:28px;align-items:flex-end;flex-wrap:wrap">
                <div>
                  <label
                    style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:8px">Artículos
                    por lote</label>
                  <div style="display:flex;gap:6px" id="config-tamano">
                    <button onclick="setTamanoLote(1)" class="btn btn-secondary btn-sm" id="tl-1">1</button>
                    <button onclick="setTamanoLote(2)" class="btn btn-primary btn-sm" id="tl-2">2</button>
                    <button onclick="setTamanoLote(3)" class="btn btn-secondary btn-sm" id="tl-3">3</button>
                    <button onclick="setTamanoLote(4)" class="btn btn-secondary btn-sm" id="tl-4">4</button>
                  </div>
                </div>
                <div>
                  <label
                    style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:8px">Frecuencia
                    (días)</label>
                  <input type="number" class="fi" min="1" max="30" id="config-frecuencia" style="width:80px"
                    onchange="setFrecuencia(this.value)" value="7">
                </div>
                <div style="font-size:12px;color:var(--text-muted);padding-bottom:6px">
                  <span id="config-proximo-label"></span>
                </div>
              </div>
            </div>

            <div id="motor-cola" style="padding:16px 20px">
              <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px">
                <div style="font-size:28px;margin-bottom:8px">📋</div>
                Pulsa "Generar lote" para crear propuestas basadas en tu Plan de Acción
              </div>
            </div>

          </div>
          <div id="brechas-generador-wrap" style="display:none;margin-bottom:20px">
            <div class="card" style="border-left:4px solid var(--warning);padding:18px 20px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <div>
                  <span style="font-size:13px;font-weight:600;color:var(--text-primary)">🎯 Tareas de Contenido (Plan de
                    Acción)</span>
                  <span style="font-size:12px;color:var(--text-muted);margin-left:10px">Haz clic en una tarea para
                    rellenar el generador con la sugerencia de la IA</span>
                </div>
                <span id="brechas-score-label"
                  style="font-size:12px;font-weight:600;color:var(--text-muted);white-space:nowrap"></span>
              </div>
              <div id="brechas-pills" style="display:flex;flex-wrap:wrap;gap:8px"></div>
            </div>
          </div>

          <div style="display: flex; gap: 24px; flex-wrap: wrap;">

            <div class="card" style="flex: 1; min-width: 300px;">
              <div class="card-header"><span class="card-title">Configuración del Contenido</span></div>

              <div id="brecha-activa-badge"
                style="display:none;margin-bottom:14px;padding:8px 12px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);border-radius:8px;font-size:12px;color:#b45309;display:none">
                <strong>Brecha activa:</strong> <span id="brecha-activa-nombre"></span>
                <button onclick="limpiarBrechaActiva()"
                  style="margin-left:8px;background:none;border:none;cursor:pointer;color:#b45309;font-size:11px;text-decoration:underline">Limpiar</button>
              </div>

              <div class="form-full">
                <label>Tipo de Contenido</label>
                <select class="fs" id="gen-tipo">
                  <option value="guia operativa">Guía Operativa / Paso a paso</option>
                  <option value="faq avanzada">FAQ Estructurada de Alto Nivel</option>
                  <option value="comparativo analitico">Artículo Comparativo (vs Competencia)</option>
                  <option value="glosario tecnico">Definición / Glosario</option>
                  <option value="caso de exito">Business Case / Caso de Éxito</option>
                </select>
              </div>

              <div class="form-full">
                <label>Tema principal / Detalles del Briefing</label>
                <textarea class="fi" id="gen-tema" rows="4"
                  style="resize: vertical; min-height: 90px; padding: 10px; line-height: 1.5;"
                  placeholder="Ej: Cómo calcular la huella de carbono de un festival... (Puedes añadir aquí contexto, estructura o detalles adicionales para guiar a la IA)"></textarea>
              </div>

              <div class="form-full">
                <label>Keywords objetivo (separadas por comas)</label>
                <input type="text" class="fi" id="gen-kws"
                  placeholder="Ej: eventos sostenibles, scope 3, compensación CO2">
              </div>

              <div class="form-row">
                <div>
                  <label>Voz de Marca</label>
                  <select class="fs" id="gen-tono">
                    <option value="formal y elegante, pero accesible y cercano">Elegante y Cercano (Marketing)</option>
                    <option value="directo, B2B y centrado en negocio y normativas">Consultor B2B (Directo)</option>
                  </select>
                </div>
                <div>
                  <label>Competidor de ref. (Opcional)</label>
                  <input type="text" class="fi" id="gen-competidor" placeholder="Ej: Creast">
                </div>
              </div>

              <button class="btn btn-primary" style="width:100%;margin-top:24px" onclick="generarTodo()">
                ✨ Generar Contenido GEO
              </button>
            </div>

            <!-- ══ BASE DE DATOS BLUEASE ══════════════════════════════════ -->
            <div class="card" style="flex:1;min-width:280px;max-width:340px">
              <div class="card-header" style="display:flex;align-items:center;gap:8px">
                <span style="font-size:16px">📊</span>
                <span class="card-title">Base de datos Bluease</span>
                <button onclick="toggleInfoBD()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:13px">ℹ️</button>
              </div>
              <div id="info-bd" style="display:none;margin-bottom:10px;padding:8px 10px;background:rgba(37,99,235,.06);border-radius:6px;font-size:11px;color:var(--text-muted);line-height:1.5">
                Escribe datos reales de Bluease (clientes, métricas, precios, funcionalidades...). Claude los usará como referencia verídica al generar el contenido.
              </div>
              <textarea id="bd-bluease"
                style="width:100%;min-height:200px;background:var(--bg-body);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:12px;color:var(--text-primary);resize:vertical;line-height:1.6;outline:none;font-family:var(--font);box-sizing:border-box"
                placeholder="Ej:
— Clientes: Rally Islas Canarias (FIA 3★), Transgrancanaria, Mad Blue Festival
— Certificaciones soportadas: ISO 20121, GHG Protocol Scope 1/2/3
— Precio: desde 299€/mes (plan Starter)
— BluPoints: sistema de puntuación de proveedores 0-100
— Reduce el tiempo de cálculo de huella en un 70%
— Genera reportes alineados con GRI Standards"
                oninput="guardarBD()"></textarea>
              <div style="font-size:10px;color:var(--text-muted);margin-top:6px;text-align:right" id="bd-chars">0 caracteres</div>
            </div>

            <!-- ══ RESTRICCIONES BLUEASE ══════════════════════════════════ -->
            <div class="card" style="flex:1;min-width:280px;max-width:340px">
              <div class="card-header" style="display:flex;align-items:center;gap:8px">
                <span style="font-size:16px">🚫</span>
                <span class="card-title">Qué NO debe decir</span>
                <button onclick="toggleInfoRest()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:13px">ℹ️</button>
              </div>
              <div id="info-rest" style="display:none;margin-bottom:10px;padding:8px 10px;background:rgba(239,68,68,.06);border-radius:6px;font-size:11px;color:var(--text-muted);line-height:1.5">
                Datos o afirmaciones incorrectas sobre Bluease que Claude debe evitar activamente al generar contenido.
              </div>
              <textarea id="rest-bluease"
                style="width:100%;min-height:200px;background:var(--bg-body);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:12px;font-size:12px;color:var(--text-primary);resize:vertical;line-height:1.6;outline:none;font-family:var(--font);box-sizing:border-box"
                placeholder="Ej:
— NO decir que Bluease es gratuito
— NO mencionar app móvil (no existe aún)
— NO inventar nombres de clientes
— NO afirmar disponibilidad fuera de España
— NO mencionar integración con Salesforce
— NO decir que somos ONG o sin ánimo de lucro"
                oninput="guardarRest()"></textarea>
              <div style="font-size:10px;color:var(--text-muted);margin-top:6px;text-align:right" id="rest-chars">0 caracteres</div>
            </div>

            <div class="card" style="flex: 2; min-width: 400px; display: flex; flex-direction: column;">
              <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <span class="card-title">Resultado Generado</span>
                <div id="botones-copiar" style="display: none; gap: 8px;">
                  <button class="btn btn-secondary btn-sm" onclick="copiarSeccion('markdown')"
                    title="Copia con los símbolos (###, **, etc)">📋 Copiar Markdown</button>
                  <button class="btn btn-secondary btn-sm" onclick="copiarSeccion('texto')"
                    title="Copia el texto limpio sin símbolos">📄 Copiar Texto Limpio</button>
                </div>
              </div>

              <div id="gen-loading"
                style="display:none; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; flex:1;">
                <div class="check-icon warn" style="font-size:32px; margin-bottom:16px;">✨</div>
                <h3 style="margin:0 0 16px 0">Redactando contenido GEO...</h3>
                <div class="prog-wrap"
                  style="width: 100%; max-width: 300px; background: var(--border); border-radius: 99px; height: 8px; overflow: hidden; margin-bottom: 8px;">
                  <div class="prog-fill"
                    style="width: 0%; height: 100%; background: var(--primary); transition: width 0.5s ease;"></div>
                </div>
                <span class="prog-lbl" style="font-size: 13px; color: var(--text-muted); font-weight: bold;">0%</span>
                <p style="color:var(--text-muted); font-size: 13px; margin-top: 16px; text-align: center;">Aplicando el
                  manual de estilo y estructura Machine-First.</p>
              </div>

              <div id="gen-resultado"
                style="flex: 1; background: var(--bg); padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); overflow-y: auto; max-height: 700px; line-height: 1.6; font-size: 15px;">

                <div id="gen-blog">
                  <div class="empty-state" style="text-align: center; color: var(--text-muted); margin-top: 40px;">
                    <div style="font-size: 32px; margin-bottom: 12px;">📝</div>
                    Rellena el formulario y haz clic en "Generar" para crear tu artículo optimizado y posts para redes.
                  </div>
                </div>

                <div id="gen-linkedin"></div>
                <div id="gen-instagram"></div>
              </div>

              <!-- Barra de acción: Aprobar / Rechazar -->
              <div id="barra-accion-gen" style="display:none;margin-top:12px;padding:14px 18px;
                background:var(--bg-surface);border:1px solid var(--border);border-radius:12px;
                display:none;align-items:center;gap:10px;flex-wrap:wrap">
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);flex:1;min-width:140px">
                  ¿Qué hacemos con este contenido?
                </span>
                <button class="btn btn-sm" id="btn-aprobar-gen"
                  style="background:rgba(99,102,241,.12);color:#6366f1;border:1px solid rgba(99,102,241,.3);font-weight:600"
                  onclick="accionarDesdeGenerador('edicion')">
                  🔖 Enviar a revisión
                </button>
                <button class="btn btn-sm" id="btn-rechazar-gen"
                  style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.25);font-weight:600"
                  onclick="accionarDesdeGenerador('descartado')">
                  ❌ Rechazar
                </button>
                <span id="gen-accion-label" style="font-size:11px;color:var(--text-muted)"></span>
              </div>
            </div>

          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             SCREEN 6 — BANDEJA DE APROBACIÓN (RF-25)
             ══════════════════════════════════════════════════════ -->
        <div class="screen" id="screen-6">
          <div class="screen-header">
            <h1>Bandeja de aprobación</h1>
            <p>Vista rápida de todas las propuestas de contenido. Aprueba, edita o descarta en menos de 5 minutos.</p>
          </div>

          <!-- RF-29: Indicador de carga de trabajo -->
          <div id="carga-trabajo"
            style="margin-bottom:20px;padding:14px 18px;background:var(--bg-surface);border:1px solid var(--border);border-radius:12px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
              <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:13px;font-weight:600;color:var(--text-primary)">Carga de trabajo</span>
                <span id="carga-label" style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:99px"></span>
              </div>
              <span id="carga-texto" style="font-size:12px;color:var(--text-muted)"></span>
            </div>
            <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden">
              <div id="carga-barra"
                style="height:100%;border-radius:4px;transition:width .5s cubic-bezier(.4,0,.2,1),background .4s"></div>
            </div>
          </div>

          <!-- Filtros de estado -->
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;align-items:center">
            <button class="btn btn-primary btn-sm" id="filtro-todos" onclick="filtrarBandeja('todos')">Todas</button>
            <button class="btn btn-secondary btn-sm" id="filtro-pendiente" onclick="filtrarBandeja('pendiente')">✍️ Por
              generar</button>
            <button class="btn btn-secondary btn-sm" id="filtro-aprobado" onclick="filtrarBandeja('aprobado')">✅
              Aprobadas</button>
            <button class="btn btn-secondary btn-sm" id="filtro-edicion" onclick="filtrarBandeja('edicion')">🔖 Por
              aprobar</button>
            <button class="btn btn-secondary btn-sm" id="filtro-descartado" onclick="filtrarBandeja('descartado')">🗑️
              Descartadas</button>
            <span id="bandeja-contador" style="margin-left:auto;font-size:12px;color:var(--text-muted)"></span>
          </div>

          <!-- Lista de propuestas -->
          <div id="bandeja-lista"></div>

          <div class="plan-actions">
            <button class="btn btn-secondary" onclick="goScreen(5)">← Generador</button>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             SCREEN 7 — HISTORIAL DE TEMAS (Registro)
             ══════════════════════════════════════════════════════ -->
        <div class="screen" id="screen-7">
          <div class="screen-header">
            <h1>Historial de Temas Generados</h1>
            <p>Registro de todos los artículos y propuestas generadas. La IA lee esta lista para evitar publicar temas
              duplicados a largo plazo.</p>
          </div>

          <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
              <span class="card-title">Temas Registrados en Memoria</span>
              <button class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="limpiarHistorialTemas()">🗑️
                Vaciar historial</button>
            </div>
            <div id="lista-historial-temas"></div>
          </div>
        </div>

        <!-- ══ PANTALLA 8 — BIBLIOTECA ══════════════════════════════ -->
        <div class="screen" id="screen-8">
          <div class="screen-header">
            <h1>Biblioteca de contenidos</h1>
            <p>Todos los artículos aprobados, listos para publicar en Webflow, LinkedIn e Instagram.</p>
          </div>

          <!-- Filtros y contador -->
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap">
            <button id="bib-filtro-todos" class="btn btn-primary btn-sm"
              onclick="filtrarBiblioteca('todos')">Todos</button>
            <button id="bib-filtro-pendiente" class="btn btn-secondary btn-sm"
              onclick="filtrarBiblioteca('pendiente')">⏳ Pendiente publicar</button>
            <button id="bib-filtro-publicado" class="btn btn-secondary btn-sm"
              onclick="filtrarBiblioteca('publicado')">✅ Publicados</button>
            <span id="bib-contador" style="margin-left:auto;font-size:12px;color:var(--text-muted)"></span>
          </div>

          <!-- Lista -->
          <div id="biblioteca-lista"></div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             SCREEN 9 — GESTIÓN DE PERMISOS (Admin only)
             ══════════════════════════════════════════════════════ -->
        <div class="screen" id="screen-9">
          <div class="screen-header">
            <h1>Gestión de Permisos</h1>
            <p>Administra los roles de todos los usuarios registrados. Los cambios se aplican en tiempo real sobre la base de datos.</p>
          </div>

          <!-- Stats rápidas -->
          <div id="permisos-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px"></div>

          <!-- Controles -->
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px">
            <input type="text" id="permisos-search" placeholder="🔍  Buscar por nombre o email..."
              oninput="filtrarUsuarios(this.value)"
              style="padding:10px 14px;background:var(--bg-surface);border:1px solid var(--border);
                     color:var(--text-primary);border-radius:8px;font-size:13px;width:280px;outline:none">
            <button class="btn btn-secondary btn-sm" onclick="cargarUsuarios()">↻ Recargar</button>
          </div>

          <!-- Tabla de usuarios -->
          <div class="card" style="padding:0;overflow:hidden">
            <div id="permisos-loading" style="text-align:center;padding:48px 24px;color:var(--text-muted)">
              <div style="font-size:28px;margin-bottom:10px">⏳</div>
              <div style="font-size:14px">Cargando usuarios...</div>
            </div>
            <div id="permisos-tabla" style="display:none">
              <table style="width:100%;border-collapse:collapse">
                <thead>
                  <tr style="background:var(--bg-surface);border-bottom:1px solid var(--border)">
                    <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted)">Usuario</th>
                    <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted)">Email</th>
                    <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted)">Rol actual</th>
                    <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted)">Cambiar rol</th>
                    <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted)">Acción</th>
                  </tr>
                </thead>
                <tbody id="permisos-tbody"></tbody>
              </table>
            </div>
            <div id="permisos-empty" style="display:none;text-align:center;padding:48px 24px;color:var(--text-muted)">
              <div style="font-size:28px;margin-bottom:10px">🔍</div>
              <div style="font-size:14px">No se encontraron usuarios con ese criterio.</div>
            </div>
            <div id="permisos-error" style="display:none;text-align:center;padding:48px 24px;color:var(--danger)">
              <div style="font-size:28px;margin-bottom:10px">⚠️</div>
              <div id="permisos-error-msg" style="font-size:14px"></div>
              <button class="btn btn-secondary btn-sm" style="margin-top:16px" onclick="cargarUsuarios()">Reintentar</button>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
  <div id="toasts"></div>

  <script>
    'use strict';

    /* CLAVE INMUTABLE DE ALMACENAMIENTO */
    const SK = 'bluease_geo_persist_v2_scraping_advanced';

    const W = {
      chatgpt: 15,
      perplexity: 15,
      googlesge: 10,
      faq: 10,
      headings: 8,
      casos: 8,
      comparativos: 7,
      blog: 7,
      fuentes: 5,
      glosario: 5,
      alttext: 5,
      schema: 5
    };
    const W_PARCIAL = {
      chatgpt: 8,
      perplexity: 8,
      googlesge: 5,
      faq: 5,
      headings: 4,
      casos: 4,
      comparativos: 3,
      blog: 3,
      fuentes: 2,
      glosario: 2,
      alttext: 2,
      schema: 2
    };

    const SCREENS = {
      1: 'Configuración',
      2: 'Análisis',
      3: 'Dashboard',
      4: 'Plan de acción',
      5: 'Generador Contenido',
      6: 'Bandeja de aprobación',
      7: 'Historial de Temas',
      8: 'Biblioteca',
      9: 'Gestión de Permisos'
    };

    /* ESTADO GLOBAL */
    let S = {
      screen: 1,
      activeComp: 0,
      config: {
        myCompany: {
          name: '',
          domain: '',
          sector: 'Sostenibilidad en eventos'
        },
        competitors: []
      },
      analysis: {
        scraping: [],
        metrics: {},
        done: false,
        lastSync: null
      },
      plan: {},
      contenido: null,
      propuestas: {
        config: {
          tamanoLote: 2,
          frecuenciaDias: 7,
          ultimaGeneracion: null
        },
        cola: []
      },
      historialTemas: [],
      historialPlanes: []
    };

    /* UTILIDADES GLOBALES */
    function esc(v) {
      const d = document.createElement('div');
      d.textContent = String(v || '');
      return d.innerHTML
    }

    function save() {
      try {
        localStorage.setItem(SK, JSON.stringify(S));
      } catch (e) {}
    }

    function load() {
      try {
        const r = localStorage.getItem(SK);
        if (!r) return false;
        const v = JSON.parse(r);

        if (v.config) {
          if (v.config.myCompany) S.config.myCompany = {
            ...S.config.myCompany,
            ...v.config.myCompany
          };
          if (v.config.competitors && Array.isArray(v.config.competitors)) S.config.competitors = v.config.competitors;
        }

        if (v.analysis) S.analysis = Object.assign(S.analysis, v.analysis);
        if (v.plan) S.plan = Object.assign(S.plan, v.plan);
        if (v.screen !== undefined) S.screen = v.screen;
        if (v.activeComp !== undefined) S.activeComp = v.activeComp;
        if (v.propuestas) {
          S.propuestas = v.propuestas;
          // Sanear: si quedó alguna propuesta en 'generando' por cierre inesperado, revertir a 'pendiente'
          if (S.propuestas.cola) {
            S.propuestas.cola.forEach(function(p) {
              if (p.estado === 'generando') {
                p.estado = 'pendiente';
                p.errorMotor = null;
              }
            });
          }
        }
        if (v.historialPlanes) S.historialPlanes = v.historialPlanes;
        if (v.historialTemas) S.historialTemas = v.historialTemas;

        return true;
      } catch (e) {
        return false;
      }
    }

    function getSt(idx, qId, sc) {
      const manual = S.analysis.metrics[idx + '_' + qId];
      if (manual === 'nosé') return null;
      if (manual === 'si' || manual === 'parcial' || manual === 'no') return manual;
      if (sc && sc.unreachable) return null;
      if (sc && sc.blockedByAntiBot) return null;

      const hasSc = sc && !sc.error;
      if (qId === 'blog' && hasSc) return sc.blog ? 'si' : (sc.isSPA ? 'parcial' : 'no');
      if (qId === 'faq' && hasSc) return sc.faq ? 'si' : (sc.isSPA ? 'parcial' : 'no');
      if (qId === 'headings' && hasSc) return sc.h ? 'si' : 'no';
      if (qId === 'casos' && hasSc) return sc.casos ? 'si' : (sc.isSPA ? 'parcial' : 'no');
      if (qId === 'glosario' && hasSc) return sc.glosario ? 'si' : (sc.isSPA ? 'parcial' : 'no');
      if (qId === 'schema' && hasSc) return sc.schema ? 'si' : 'no';
      if (qId === 'alttext' && hasSc) {
        if (sc.alttext === null) return null;
        return sc.alttext ? 'si' : (sc.isSPA ? 'parcial' : 'no');
      }
      return 'no';
    }

    function score(idx) {
      let p = 0;
      const sc = S.analysis.scraping[idx];
      ['chatgpt', 'perplexity', 'googlesge', 'faq', 'headings', 'casos', 'comparativos', 'blog', 'fuentes', 'glosario', 'alttext', 'schema'].forEach(q => {
        const status = getSt(idx, q, sc);
        if (status === 'si') p += W[q];
        else if (status === 'parcial') p += W_PARCIAL[q];
      });
      return Math.min(100, p);
    }

    function toggleTheme() {
      const html = document.documentElement;
      const isDark = html.getAttribute('data-theme') === 'dark';
      const sunIcon = document.querySelector('.sun-icon');
      const moonIcon = document.querySelector('.moon-icon');

      if (isDark) {
        html.removeAttribute('data-theme');
        localStorage.setItem('bluease_theme', 'light');
        if (sunIcon) sunIcon.style.display = 'none';
        if (moonIcon) moonIcon.style.display = 'block';
      } else {
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('bluease_theme', 'dark');
        if (sunIcon) sunIcon.style.display = 'block';
        if (moonIcon) moonIcon.style.display = 'none';
      }
    }

    function initTheme() {
      const savedTheme = localStorage.getItem('bluease_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        const sunIcon = document.querySelector('.sun-icon');
        const moonIcon = document.querySelector('.moon-icon');
        if (sunIcon) sunIcon.style.display = 'block';
        if (moonIcon) moonIcon.style.display = 'none';
      }
    }

    function toast(icon, msg, t = '', d = 3500) {
      const c = document.getElementById('toasts');
      if (!c) return;
      const el = document.createElement('div');
      el.className = 'toast' + (t ? ' ' + t : '');
      el.innerHTML = `<span>${icon}</span><span>${esc(msg)}</span>`;
      c.appendChild(el);
      setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
      }, d);
    }

    /* --- MOTOR DE SCRAPING HÍBRIDO --- */
    const MY_WORKER = 'https://little-feather-e318.marcoscabelloflorido084.workers.dev';

    // ── API KEY ANTHROPIC (uso interno — no compartir este archivo públicamente) ──
    const ANTHROPIC_API_KEY = 'KEY DE ANTROPIC AQUÍ'; // REEMPLAZAR POR TU CLAVE REAL
    const ANTHROPIC_MODEL = 'claude-sonnet-4-5';

    // Llama directamente a Anthropic sin pasar por Cloudflare — sin límite de 30s
    async function llamarAnthropicDirecto(prompt, maxTokens = 8000) {
      const res = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': ANTHROPIC_API_KEY,
          'anthropic-version': '2023-06-01',
          'anthropic-dangerous-direct-browser-access': 'true'
        },
        body: JSON.stringify({
          model: ANTHROPIC_MODEL,
          max_tokens: maxTokens,
          messages: [{
            role: 'user',
            content: prompt
          }]
        })
      });
      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error('Error Anthropic (' + res.status + '): ' + (err.error?.message || res.statusText));
      }
      const data = await res.json();
      if (data.error) throw new Error(data.error.message || JSON.stringify(data.error));
      const texto = data.content && data.content[0] && data.content[0].text;
      if (!texto) throw new Error('La API no devolvió contenido.');
      return texto;
    }

    function normalizeUrl(raw) {
      let u = raw.trim();
      if (!/^https?:\/\//i.test(u)) u = 'https://' + u;
      return u;
    }

    function fetchWithTimeout(url, ms) {
      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), ms);
      return fetch(url, {
          signal: controller.signal
        })
        .catch(err => {
          if (err.name === 'AbortError') throw new Error(`Timeout`);
          throw err;
        })
        .finally(() => clearTimeout(timer));
    }

    async function fetchHtmlConProxy(rawUrl, onStatus = null) {
      const targetUrl = normalizeUrl(rawUrl);
      if (!targetUrl.includes('.')) throw new Error('Falta el .com o extensión en el dominio');

      if (onStatus) onStatus('Conectando con Worker…');
      let html = null;

      try {
        const response = await fetchWithTimeout(`${MY_WORKER}?url=${encodeURIComponent(targetUrl)}`, 12000);
        if (!response.ok) throw new Error(`Worker HTTP ${response.status}`);
        html = await response.text();
        if (html.includes('Error 403') || html.includes('Access Denied') || (html.toLowerCase().includes('cloudflare') && html.toLowerCase().includes('captcha'))) {
          throw new Error('Bloqueado por Anti-Bot');
        }
        if (!html || html.length < 150) throw new Error('Respuesta vacía');
      } catch (err) {
        if (onStatus) onStatus('Worker falló. Usando respaldo…');
        const fallbackUrl = `https://api.allorigins.win/get?url=${encodeURIComponent(targetUrl)}`;
        const fbResponse = await fetchWithTimeout(fallbackUrl, 15000);
        if (!fbResponse.ok) throw new Error(`HTTP ${fbResponse.status} en Respaldo`);
        const fbData = await fbResponse.json();
        html = fbData.contents;
      }

      if (!html || html.length < 200) throw new Error('Contenido final vacío');
      if (onStatus) onStatus('✓ HTML recibido');
      return html;
    }

    async function fetchConReintentos(rawUrl, maxReintentos = 2, onStatus = null) {
      let ultimoError = null;
      for (let i = 0; i < maxReintentos; i++) {
        try {
          return await fetchHtmlConProxy(rawUrl, onStatus);
        } catch (err) {
          ultimoError = err;
          if (err.message.includes('Falta el .com')) throw err;
          if (i < maxReintentos - 1) {
            if (onStatus) onStatus(`Reintentando (${i + 2}/${maxReintentos})…`);
            await new Promise(r => setTimeout(r, 1500 * (i + 1)));
          }
        }
      }
      throw ultimoError;
    }

    function resolveUrl(href, base) {
      if (!href) return null;
      try {
        if (/^https?:\/\//i.test(href)) return href;
        return new URL(href, base).href;
      } catch (e) {
        return null;
      }
    }

    function getContentArea(doc) {
      return doc.querySelector('main, [role="main"], article, .main-content, #main-content, .page-content, #page-content, .entry-content, .post-content, .content-area, #content, .content, .site-content') || doc.body;
    }

    function isChrome(el) {
      return !!el.closest('nav, header, footer, aside, .sidebar, .widget, [role="navigation"], [role="banner"], [role="contentinfo"], .navbar, .nav, .menu, .footer, .header');
    }

    function detectarSchemaGeneral(doc) {
      return doc.querySelectorAll('script[type="application/ld+json"]').length > 0;
    }

    function detectarFaq(doc) {
      const content = (doc.querySelector('main, article, #content, .content, [role="main"]') || doc.body);
      if (doc.querySelector('[itemtype*="FAQPage"]')) return true;
      const scripts = Array.from(doc.querySelectorAll('script[type="application/ld+json"]'));
      if (scripts.some(s => /"faqpage"|"question"/i.test(s.textContent.replace(/\s+/g, '')))) return true;

      const headings = Array.from(content.querySelectorAll('h1, h2, h3, h4, h5, h6'));
      const faqKws = ['preguntas frecuentes', 'faq', 'faqs', 'dudas frecuentes', 'preguntas habituales', 'q&a', 'preguntas y respuestas'];
      if (headings.some(h => faqKws.some(kw => h.textContent.toLowerCase().includes(kw)))) return true;

      const questionHeadings = headings.filter(h => {
        const t = h.textContent.trim();
        return (t.startsWith('¿') && t.includes('?')) || t.endsWith('?');
      });
      if (questionHeadings.length >= 3) return true;

      const hasQuestionTitle = headings.some(h => /¿|\?|por qué elegir|cómo funciona|ventajas/i.test(h.textContent));
      const accordionSelectors = [
        'details', '[class*="accordion"]', '[class*="toggle"]', '[class*="collapse"]', '[class*="faq-item"]',
        '.elementor-accordion-item', '.elementor-toggle-item', '.et_pb_toggle', '.wp-block-pb-accordion-item',
        '[data-bs-toggle="collapse"]', '[data-toggle="collapse"]', '.accordion-panel', '.acc-item', '.MuiAccordion-root'
      ].join(', ');

      const accordions = content.querySelectorAll(accordionSelectors);
      if (hasQuestionTitle && accordions.length >= 3) return true;

      const text = content.innerText || "";
      if (/Q:.*A:/i.test(text) || /Pregunta:.*Respuesta:/i.test(text) || /¿.*\?.*(R:|Respuesta:)/i.test(text)) return true;

      const links = Array.from(doc.querySelectorAll('a[href]'));
      if (links.some(a => {
          const t = a.textContent.trim().toLowerCase();
          const h = (a.getAttribute('href') || '').toLowerCase();
          return (t === 'faq' || t === 'faqs' || t === 'preguntas frecuentes' || h.endsWith('/faq') || h.includes('/preguntas-frecuentes'));
        })) return true;

      return false;
    }

    function detectarBlog(doc) {
      const hoy = new Date();
      const añoActual = hoy.getFullYear();
      const añoPasado = añoActual - 1;
      const regexAno = new RegExp(`\\b(${añoActual}|${añoPasado})\\b`);

      if (doc.querySelector('link[type="application/rss+xml"]')) return true;

      const tieneContenidoReciente = (documento) => {
        const area = getContentArea(documento);
        const nodos = Array.from(area.childNodes).filter(n => {
          if (n.nodeType === 1) {
            const tag = n.nodeName.toLowerCase();
            const cls = (n.className || '').toLowerCase();
            return !['footer', 'nav'].includes(tag) && !cls.includes('footer') && !cls.includes('copyright');
          }
          return true;
        });
        return regexAno.test(nodos.map(n => n.textContent || '').join(' '));
      };

      if (doc.querySelector('meta[property="og:type"][content="article"]') && tieneContenidoReciente(doc)) return true;
      if (doc.querySelector('meta[name="generator"][content*="WordPress"]') && tieneContenidoReciente(doc)) return true;

      const timeTags = Array.from(doc.querySelectorAll('time[datetime]'));
      if (timeTags.some(t => regexAno.test(t.getAttribute('datetime') || ''))) return true;

      const enlaces = Array.from(doc.querySelectorAll('a[href]'));
      const kwsBlog = ['/blog', '/noticias', '/news', '/insights', '/articulos', '/recursos', '/resources'];
      const linkBlog = enlaces.find(a => kwsBlog.some(kw => (a.getAttribute('href') || '').toLowerCase().includes(kw)));
      if (linkBlog && tieneContenidoReciente(doc)) return true;

      const posts = doc.querySelectorAll('article, .post, .blog-post, .type-post, .entry, [class*="post-item"]');
      if (posts.length > 0) {
        const postsText = Array.from(posts).map(p => p.textContent).join(' ');
        if (regexAno.test(postsText)) return true;
      }

      return false;
    }

    function detectarEncabezados(doc) {
      const contentArea = getContentArea(doc);
      const cv = (sel) => Array.from(contentArea.querySelectorAll(sel)).filter(el => !isChrome(el) && el.textContent.replace(/\s+/g, '').trim().length > 2).length;
      const h1 = cv('h1'),
        h2 = cv('h2'),
        h3 = cv('h3');
      return {
        h1,
        h2,
        h3,
        ok: h1 >= 1 && h2 >= 2
      };
    }

    function detectarCasos(doc) {
      const kwsUrl = ['casos-de-exito', 'caso-de-exito', 'casos-de-uso', 'case-stud', 'success-stor', 'customer-stor', 'testimonial', 'clientes', 'customers', 'clients', 'portfolio', 'resultados'];
      const kwsTexto = ['caso de éxito', 'casos de éxito', 'caso de uso', 'casos de uso', 'nuestros clientes', 'nuestros resultados', 'success stories', 'case studies', 'case study', 'client stories'];
      const links = Array.from(doc.querySelectorAll('a[href]'));

      if (links.some(a => {
          const href = (a.getAttribute('href') || '').toLowerCase();
          const text = (a.textContent || '').trim().toLowerCase();
          return kwsUrl.some(kw => href.includes(kw)) || kwsTexto.some(kw => text.includes(kw));
        })) return true;

      if (doc.querySelector('.testimonials, .testimonial-section, #testimonials, .case-studies, #case-studies, .case-study, .success-stories, .clients-section, #clients')) return true;

      const blockquotes = Array.from(doc.querySelectorAll('blockquote')).filter(bq => !isChrome(bq));
      if (blockquotes.length >= 2) {
        if (blockquotes.some(bq => bq.querySelector('cite, .author, .name, figcaption, footer') || /[-—]\s*\w+/.test(bq.textContent))) return true;
      }
      return false;
    }

    function detectarGlosario(doc) {
      const jsonLdScripts = Array.from(doc.querySelectorAll('script[type="application/ld+json"]'));
      if (jsonLdScripts.some(s => {
          try {
            const c = s.textContent.toLowerCase().replace(/\s+/g, '');
            return c.includes('"@type":"definedterm"') || c.includes('"@type":"definedtermset"') || c.includes('"@type":"glossary"');
          } catch (e) {
            return false;
          }
        })) return true;

      const contentArea = getContentArea(doc);
      if (contentArea.querySelectorAll('dt').length >= 5 && contentArea.querySelectorAll('dd').length >= 5) return true;

      if (doc.querySelector('.glossary, #glossary, #glosario, .dictionary, #diccionario, .terms-list, .lexicon')) return true;

      const kwsGlosario = ['glosario', 'glossary', 'diccionario', 'dictionary', 'lexicon', 'terminología', 'términos clave', 'key terms'];
      const textoLegal = ['condicion', 'condition', 'service', 'servicio', 'privacidad', 'privacy', 'legal', 'uso', 'cookie'];
      const links = Array.from(doc.querySelectorAll('a[href]'));

      if (links.some(a => {
          const texto = (a.textContent || '').trim().toLowerCase();
          const href = (a.getAttribute('href') || '').toLowerCase();
          if (textoLegal.some(t => texto.includes(t) || href.includes(t))) return false;
          return kwsGlosario.some(kw => texto === kw || texto.startsWith(kw + ' ') || href.includes(`/${kw}`) || href.includes(`-${kw}`) || href.endsWith(kw));
        })) return true;

      const anchorLinks = links.filter(a => (a.getAttribute('href') || '').startsWith('#'));
      const parentMap = new Map();
      anchorLinks.forEach(a => {
        const letra = a.textContent.trim();
        if (!/^[A-ZÁÉÍÓÚÑ]$/i.test(letra)) return;
        const parent = a.parentElement;
        if (!parent) return;
        if (!parentMap.has(parent)) parentMap.set(parent, new Set());
        parentMap.get(parent).add(letra.toUpperCase());
      });

      for (const letras of parentMap.values()) {
        if (letras.size >= 8) return true;
      }

      return false;
    }

    function detectarTablasListas(doc) {
      const contentArea = doc.querySelector('main, [role="main"], article, #content, .content, .site-content, .page-wrapper') || doc.body;
      const tables = contentArea.querySelectorAll('table, [role="table"], .pricing-table, .comparison-table, .data-table');
      const hasTables = tables.length > 0;

      let hasSemantic = false;
      const uls = Array.from(contentArea.querySelectorAll('ul, ol, dl')).filter(ul => !ul.closest('nav, header, footer, [role="navigation"]'));
      for (let ul of uls) {
        const lis = Array.from(ul.querySelectorAll('li, dt'));
        if (lis.length >= 3) {
          const avgLen = lis.reduce((sum, li) => sum + li.textContent.trim().length, 0) / lis.length;
          if (avgLen > 12) {
            hasSemantic = true;
            break;
          }
        }
      }

      const listClasses = Array.from(contentArea.querySelectorAll('.elementor-icon-list-item, .list-item, .feature, .benefit, [class*="list-item"], .icon-box, [class*="icon-box"]'))
        .filter(el => !el.closest('nav, header, footer'));
      const hasClasses = listClasses.length >= 3;

      const bulletRegex = /^[\s\u200B\n]*[•·✓✔✅\-\*—➔➜►]/;
      const textNodes = Array.from(contentArea.querySelectorAll('p, div, span, li')).filter(el => !el.closest('nav, header, footer'));
      let visualBullets = 0;
      for (let node of textNodes) {
        if (node.children.length > 2) continue;
        const text = node.textContent.trim();
        if (text.length > 8 && text.length < 300 && bulletRegex.test(text)) visualBullets++;
        else if (text.includes('\n•') || text.includes('\n- ') || text.includes('\n·') || text.includes('\n✓')) visualBullets += 2;
      }

      let hasIconTextPattern = false;
      const containers = Array.from(contentArea.querySelectorAll('div, section, ul')).filter(el => !el.closest('nav, header, footer'));

      for (let container of containers) {
        let validRows = 0;
        for (let child of container.children) {
          const tag = child.tagName.toLowerCase();
          if (tag === 'a' || tag === 'button') continue;
          const text = child.textContent.trim();
          const hasIcon = child.querySelector('svg, img');
          if (hasIcon && text.length > 10 && text.length < 250) {
            validRows++;
          }
        }
        if (validRows >= 3) {
          hasIconTextPattern = true;
          break;
        }
      }

      return {
        hasLists: hasSemantic || hasClasses || visualBullets >= 3 || hasIconTextPattern,
        hasTables: hasTables
      };
    }

    function detectarAltText(doc) {
      const contentArea = getContentArea(doc);

      const images = Array.from(contentArea.querySelectorAll('img')).filter(img => {
        if (isChrome(img)) return false;
        const src = (img.getAttribute('src') || img.getAttribute('data-src') || img.getAttribute('data-original') || '').toLowerCase();
        if (src.endsWith('.svg')) return false;
        const cls = (img.className || '').toLowerCase();
        if (/(logo|icon|pixel|tracker|avatar|profile|spinner|loader|thumb|emoji)/i.test(src)) return false;
        if (/(logo|icon|avatar|sprite)/i.test(cls)) return false;
        if (img.getAttribute('role') === 'presentation' || img.getAttribute('aria-hidden') === 'true' || img.getAttribute('alt') === '') return false;
        const w = parseInt(img.getAttribute('width') || '0');
        const h = parseInt(img.getAttribute('height') || '0');
        if ((w > 0 && w < 50) || (h > 0 && h < 50)) return false;
        return true;
      });

      if (images.length === 0) return null;

      const badWords = ['imagen', 'image', 'foto', 'photo', 'pic', 'picture', 'captura', 'screenshot', 'whatsapp', 'sin título', 'untitled', 'banner', 'slide', 'hero'];
      const fileRegex = /^[\w\-]+\.(jpg|png|gif|jpeg|svg|webp|avif)$/i;

      const validAlts = images.filter(img => {
        const alt = (img.getAttribute('alt') || '').trim();
        if (alt.length < 5 || fileRegex.test(alt)) return false;
        const altL = alt.toLowerCase();
        if (badWords.some(w => altL === w || altL.startsWith(w + ' '))) return false;
        if (alt.split(/\s+/).length < 2) return false;
        return true;
      });

      return (validAlts.length / images.length) >= 0.60;
    }

    function analizarFechasYArticulos(docs) {
      let fechasEncontradas = [];

      const parseDateFlexible = (dateStr) => {
        if (!dateStr) return null;
        let d = new Date(dateStr);
        if (!isNaN(d) && d.getFullYear() > 2000) return d;

        const meses = {
          ene: 0,
          jan: 0,
          feb: 1,
          mar: 2,
          abr: 3,
          apr: 3,
          may: 4,
          jun: 5,
          jul: 6,
          ago: 7,
          aug: 7,
          sep: 8,
          oct: 9,
          nov: 10,
          dic: 11,
          dec: 11
        };
        let s = dateStr.toLowerCase().replace(/\bde\b/g, '').replace(/,/g, '').replace(/\s+/g, ' ').trim().replace(/(\d+)(st|nd|rd|th)/g, '$1');

        const m1 = s.match(/(\d{1,2})\s+([a-záéíóú]{3})[a-záéíóú]*\s+(\d{4})/);
        if (m1 && meses[m1[2]] !== undefined) return new Date(+m1[3], meses[m1[2]], +m1[1]);

        const m2 = s.match(/([a-z]{3})[a-z]*\s+(\d{1,2})\s+(\d{4})/);
        if (m2 && meses[m2[1]] !== undefined) return new Date(+m2[3], meses[m2[1]], +m2[2]);

        const m3 = s.match(/(\d{1,4})[-\/.](\d{1,2})[-\/.](\d{1,4})/);
        if (m3) {
          const [p1, p2, p3] = [+m3[1], +m3[2], +m3[3]];
          if (p1 > 2000 && p1 < 2100) return new Date(p1, p2 - 1, p3);
          if (p3 > 2000 && p3 < 2100) return new Date(p3, p2 - 1, p1);
        }
        return null;
      };

      const YY = `20[1-9]\\d`;
      const regexTextDate = new RegExp(`\\b\\d{1,2}\\s+(?:de\\s+)?[a-záéíóú]{3,}\\s+(?:de\\s+)?${YY}\\b|\\b${YY}[-/.]\\d{1,2}[-/.]\\d{1,2}\\b|\\b\\d{1,2}[-/.]\\d{1,2}[-/.]${YY}\\b|\\b(?:Ene|Feb|Mar|Abr|May|Jun|Jul|Ago|Sep|Oct|Nov|Dic|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\\s+\\d{1,2}(?:st|nd|rd|th)?\\,?\\s+${YY}\\b`, 'gi');

      docs.forEach(doc => {
        if (!doc) return;

        doc.querySelectorAll('script[type="application/ld+json"]').forEach(script => {
          try {
            const data = JSON.parse(script.textContent);
            const items = Array.isArray(data) ? data : (data['@graph'] || [data]);
            items.forEach(item => {
              if (item['@type'] && (item['@type'].includes('Article') || item['@type'].includes('BlogPosting') || item['@type'].includes('NewsArticle'))) {
                if (item.datePublished) fechasEncontradas.push(parseDateFlexible(item.datePublished));
              }
            });
          } catch (e) {
            const matches = script.textContent.match(/"(?:datePublished|uploadDate)"\s*:\s*"([^"]+)"/gi);
            if (matches) matches.forEach(m => fechasEncontradas.push(parseDateFlexible(m.split('"')[3])));
          }
        });

        doc.querySelectorAll('meta[property="article:published_time"], meta[itemprop="datePublished"], meta[name="pubdate"]').forEach(m =>
          fechasEncontradas.push(parseDateFlexible(m.getAttribute('content')))
        );

        const area = doc.querySelector('main, [role="main"], article, #content, .content, .site-content') || doc.body;
        const articleCards = area.querySelectorAll('article, .post, .blog-post, .type-post, .entry, [class*="post-item"], [class*="blog-item"]');

        if (articleCards.length > 0) {
          articleCards.forEach(card => {
            const timeTag = card.querySelector('time');
            if (timeTag) {
              fechasEncontradas.push(parseDateFlexible(timeTag.getAttribute('datetime')) || parseDateFlexible(timeTag.textContent));
            } else {
              const text = card.textContent.replace(/\s+/g, ' ');
              const matches = text.match(regexTextDate);
              if (matches) fechasEncontradas.push(parseDateFlexible(matches[0]));
            }
          });
        } else {
          const queue = [area];
          let head = 0;
          while (head < queue.length) {
            const node = queue[head++];
            if (!node || node.nodeName === 'FOOTER' || node.nodeName === 'NAV' || node.nodeName === 'ASIDE' || (node.className && typeof node.className === 'string' && node.className.includes('widget'))) continue;

            if (node.nodeType === 3) {
              const text = node.nodeValue.trim();
              if (text.length >= 8 && text.length <= 150) {
                const matches = text.match(regexTextDate);
                if (matches) matches.forEach(m => fechasEncontradas.push(parseDateFlexible(m)));
              }
            } else if (node.nodeType === 1 && !['SCRIPT', 'STYLE', 'NOSCRIPT'].includes(node.nodeName)) {
              for (const child of node.childNodes) queue.push(child);
            }
          }
        }

        doc.querySelectorAll('a[href]').forEach(a => {
          const match = (a.getAttribute('href') || '').match(/\/(20[1-9]\d)\/(0[1-9]|1[0-2])\//);
          if (match) fechasEncontradas.push(new Date(+match[1], +match[2] - 1, 1));
        });
      });

      const maxYear = new Date().getFullYear() + 1;
      const validDates = fechasEncontradas.filter(d => d && !isNaN(d.getTime()) && d.getFullYear() >= 2015 && d.getFullYear() <= maxYear).sort((a, b) => b - a);

      if (validDates.length === 0) return {
        ultimoPost: null,
        numUltimoAno: 0
      };

      const uniqueDates = [];
      const seen = new Set();
      validDates.forEach(d => {
        const key = d.toISOString().split('T')[0];
        if (!seen.has(key)) {
          seen.add(key);
          uniqueDates.push(d);
        }
      });

      const unAnoAtras = new Date();
      unAnoAtras.setFullYear(unAnoAtras.getFullYear() - 1);
      const latestDate = uniqueDates[0];
      const formatoUI = [latestDate.getDate().toString().padStart(2, '0'), (latestDate.getMonth() + 1).toString().padStart(2, '0'), latestDate.getFullYear()].join('/');

      return {
        ultimoPost: formatoUI,
        numUltimoAno: uniqueDates.filter(d => d >= unAnoAtras).length
      };
    }

    /* MOTOR PRINCIPAL */
    async function scrapeSingle(domain, onStatus = null) {
      const r = {
        blog: false,
        faq: false,
        h: false,
        h1: 0,
        h2: 0,
        h3: 0,
        schema: false,
        casos: false,
        glosario: false,
        alttext: false,
        tablas: false,
        listas: false,
        ultimoPost: null,
        postsAno: 0,
        error: null,
        isSPA: false,
        tieneJsHeavy: false,
        unreachable: false,
        blockedByAntiBot: false
      };

      const baseUrl = normalizeUrl(domain);

      try {
        const html = await fetchConReintentos(baseUrl, 2, onStatus);
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const docsToAnalyze = [doc];

        r.tieneJsHeavy = doc.querySelectorAll('script[src]').length > 6 || /__NEXT_DATA__|_reactFiber|__vue_component|ng-version/i.test(html);

        r.blog = detectarBlog(doc);
        r.faq = detectarFaq(doc);
        r.schema = detectarSchemaGeneral(doc);
        const hh = detectarEncabezados(doc);
        r.h1 = hh.h1;
        r.h2 = hh.h2;
        r.h3 = hh.h3;
        r.h = hh.ok;

        const linkAlBlog = Array.from(doc.querySelectorAll('a[href]')).find(a => ['blog', 'noticias', 'news', 'insights'].some(kw => (a.getAttribute('href') || '').toLowerCase().includes(kw)));
        if (linkAlBlog) {
          const urlBlog = resolveUrl(linkAlBlog.getAttribute('href'), baseUrl);
          if (urlBlog && urlBlog !== baseUrl) {
            try {
              const blogHostname = new URL(urlBlog).hostname.replace(/^www\./, '');
              const baseHostname = new URL(baseUrl).hostname.replace(/^www\./, '');
              if (blogHostname === baseHostname || blogHostname.endsWith('.' + baseHostname)) {
                if (onStatus) onStatus('Analizando blog…');
                const blogHtml = await fetchConReintentos(urlBlog, 2, null);
                const blogDoc = parser.parseFromString(blogHtml, 'text/html');
                r.blog = detectarBlog(blogDoc);
                docsToAnalyze.push(blogDoc);
              }
            } catch (e) {
              console.warn(`[Deep crawl] Blog falló:`, e.message);
            }
          }
        }

        let hasGoodAlt = null,
          hasTables = false,
          hasLists = false;

        docsToAnalyze.forEach(d => {
          if (!d) return;
          if (!r.casos) r.casos = detectarCasos(d);
          if (!r.glosario) r.glosario = detectarGlosario(d);
          if (!r.schema) r.schema = detectarSchemaGeneral(d);

          const altResult = detectarAltText(d);
          if (altResult === true) hasGoodAlt = true;
          else if (altResult === false && hasGoodAlt === null) hasGoodAlt = false;

          const tl = detectarTablasListas(d);
          if (tl.hasTables) hasTables = true;
          if (tl.hasLists) hasLists = true;
        });

        r.alttext = hasGoodAlt;
        r.tablas = hasTables;
        r.listas = hasLists;

        const analisisFechas = analizarFechasYArticulos(docsToAnalyze);
        r.ultimoPost = analisisFechas.ultimoPost;
        r.postsAno = analisisFechas.numUltimoAno;

        if (!r.blog && !r.faq && r.tieneJsHeavy) r.isSPA = true;

      } catch (e) {
        r.error = e.message;
        const isAntiBot = /bloqueada|anti-bot|captcha|403/i.test(e.message);
        const isTimeout = /timeout/i.test(e.message);
        const isNetwork = /failed to fetch|network|vacía|vacío/i.test(e.message);

        r.blockedByAntiBot = isAntiBot;
        r.unreachable = isTimeout || isNetwork;
        r.isSPA = isTimeout && !isAntiBot;
      }

      return r;
    }

    /* ─────────────────────────────────────────────────────────
       LOGICA DE UI Y FLUJO
       ───────────────────────────────────────────────────────── */
    async function runAnalysis() {
      const entities = [S.config.myCompany, ...S.config.competitors];
      document.getElementById('prog-section').style.display = 'block';

      if (!S.analysis.scraping || S.analysis.scraping.length !== entities.length) {
        S.analysis.scraping = new Array(entities.length).fill(null);
      }
      S.analysis.metrics = S.analysis.metrics || {};

      const stEl = document.getElementById('scraping-status');
      stEl.innerHTML = entities.map((e, i) =>
        `<div class="status-item" id="si-${i}">
       <div class="status-dot"></div>
       <span class="si-name">${esc(e.name || e.domain || '—')}</span>
       <span class="si-info" id="si-info-${i}" style="margin-left:auto;font-size:11px;color:var(--text-muted);font-family:var(--mono)"></span>
     </div>`
      ).join('');

      let done = 0;
      const setProg = (d) => {
        const p = Math.round(d / entities.length * 100);
        document.getElementById('prog-fill').style.width = p + '%';
        document.getElementById('prog-lbl').textContent = p + '%';
      };
      setProg(0);

      for (let i = 0; i < entities.length; i++) {
        const e = entities[i];
        const el = document.getElementById('si-' + i);
        const infoEl = document.getElementById('si-info-' + i);
        if (el) el.classList.add('running');
        if (infoEl) infoEl.textContent = 'analizando profundamente…';

        let r;
        if (!e.domain || e.domain.trim() === '') {
          r = {
            error: 'Sin dominio configurado',
            isSPA: false
          };
        } else {
          try {
            r = await scrapeSingle(e.domain, (msg) => {
              if (infoEl) infoEl.textContent = msg;
            });
          } catch (err) {
            r = {
              error: err.message,
              isSPA: false
            };
          }
        }
        S.analysis.scraping[i] = r;

        if (el) {
          el.classList.remove('running');
          el.classList.add(r.error ? 'error' : 'done');
        }

        if (infoEl) {
          if (r.error) {
            let errorMsg = '❌ Error de acceso';
            if (r.error.includes('Falta el .com')) errorMsg = '❌ Falta el .com o extensión';
            else if (r.blockedByAntiBot) errorMsg = '🔒 bloqueado anti-bot';
            else if (r.isSPA) errorMsg = '⚙️ web JS pesado';
            else if (r.unreachable) errorMsg = '🌐 sin conexión o caída';
            infoEl.textContent = errorMsg;
            infoEl.style.color = 'var(--danger)';
          } else {
            const detected = [r.blog && '📝 Blog', r.faq && '❓ FAQ', r.casos && '💼 Casos', (r.tablas || r.listas) && '📊 Tablas'].filter(Boolean);
            infoEl.textContent = detected.length ? detected.join(' · ') : 'sin señales fuertes';
            infoEl.style.color = detected.length ? 'var(--success)' : 'var(--text-muted)';
          }
        }

        done++;
        setProg(done);
        if (S.activeComp === i) renderAnalysis(i);
      }

      S.analysis.done = true;
      S.analysis.lastSync = new Date().toLocaleString();
      save();
      renderTabs();
    }

    function renderAnalysis(idx) {
      const sc = S.analysis.scraping[idx];
      const autoEl = document.getElementById('auto-checks');
      const dynamicFallback = document.getElementById('dynamic-fallback-questions');
      const btnReanalizar = document.getElementById('btn-reanalizar');

      if (!sc) {
        autoEl.innerHTML = `<div class="check-item"><span class="check-icon warn">⏳</span><span class="check-text">Pendiente de analizar</span></div>`;
        if (dynamicFallback) dynamicFallback.innerHTML = '';
        if (btnReanalizar) btnReanalizar.style.display = 'none';
        return;
      }

      if (btnReanalizar) btnReanalizar.style.display = 'inline-flex';

      if (sc.error || sc.isSPA) {
        if (dynamicFallback) {
          dynamicFallback.innerHTML = `
        <div style="margin-bottom: 32px; padding: 24px; background: var(--warning-light); border: 1px dashed var(--warning); border-radius: var(--radius-lg);">
          <div class="section-label" style="color: #b45309; margin-bottom: 16px;">Scraping Fallido: Completa estas métricas a mano</div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene blog activo con contenido reciente?</div><div class="options" data-q="blog"><button class="option" onclick="selectOpt(this,'blog','si')">Sí</button><button class="option" onclick="selectOpt(this,'blog','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'blog','no')">No</button></div></div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene sección FAQ / Preguntas Frecuentes?</div><div class="options" data-q="faq"><button class="option" onclick="selectOpt(this,'faq','si')">Sí</button><button class="option" onclick="selectOpt(this,'faq','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'faq','no')">No</button></div></div>
          <div class="manual-q">
            <div class="q-label" style="color: #92400e;">Fecha del último contenido publicado</div>
            <div class="q-help">ℹ️ Revisar el blog manualmente (DD/MM/AAAA)</div>
            <input type="text" class="fi" placeholder="DD/MM/AAAA" value="${S.analysis.metrics[idx + '_ultimoPost'] || ''}" onchange="updateFechaManual(this)" style="margin-top:8px;max-width:180px">
          </div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene estructura de encabezados H1/H2?</div><div class="options" data-q="headings"><button class="option" onclick="selectOpt(this,'headings','si')">Sí</button><button class="option" onclick="selectOpt(this,'headings','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'headings','no')">No</button></div></div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene tablas o listas en el contenido?</div><div class="options" data-q="tablas"><button class="option" onclick="selectOpt(this,'tablas','si')">Sí</button><button class="option" onclick="selectOpt(this,'tablas','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'tablas','no')">No</button></div></div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Las imágenes tienen alt text descriptivo?</div><div class="options" data-q="alttext"><button class="option" onclick="selectOpt(this,'alttext','si')">Sí</button><button class="option" onclick="selectOpt(this,'alttext','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'alttext','no')">No</button></div></div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene casos de uso / éxito documentados?</div><div class="options" data-q="casos"><button class="option" onclick="selectOpt(this,'casos','si')">Sí</button><button class="option" onclick="selectOpt(this,'casos','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'casos','no')">No</button></div></div>
          <div class="manual-q"><div class="q-label" style="color: #92400e;">¿Tiene glosario o sección de definiciones?</div><div class="options" data-q="glosario"><button class="option" onclick="selectOpt(this,'glosario','si')">Sí</button><button class="option" onclick="selectOpt(this,'glosario','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'glosario','no')">No</button></div></div>
          <div class="manual-q">
            <div class="q-label" style="color: #92400e;">Número de artículos publicados en el último año</div>
            <div class="q-help">ℹ️ Contar posts con fecha reciente en el blog</div>
            <input type="number" min="0" class="fi" placeholder="0" value="${S.analysis.metrics[idx + '_postsAno'] !== undefined ? S.analysis.metrics[idx + '_postsAno'] : ''}" onchange="updatePostsAnoManual(this)" style="margin-top:8px;max-width:120px">
          </div>
          <div class="manual-q" style="margin-bottom: 0;"><div class="q-label" style="color: #92400e;">¿Tiene Schema Markup / datos estructurados?</div><div class="options" data-q="schema"><button class="option" onclick="selectOpt(this,'schema','si')">Sí</button><button class="option" onclick="selectOpt(this,'schema','parcial')">Parcialmente</button><button class="option" onclick="selectOpt(this,'schema','no')">No</button></div></div>
        </div>
      `;
        }
      } else {
        if (dynamicFallback) dynamicFallback.innerHTML = '';
      }

      if (sc.error) {
        let errorMsg = `<div class="check-item"><span class="check-icon fail">✕</span><span class="check-text"><strong>Error:</strong> ${esc(sc.error)}</span></div>`;
        if (sc.isSPA) errorMsg += `<div class="check-item" style="margin-top:8px"><span class="check-icon warn">⚠️</span><span class="check-text">Esta web usa JavaScript intensivo.</span></div>`;
        autoEl.innerHTML = errorMsg;
      } else {
        let html = '';
        html += `<div class="check-item"><span class="check-icon ${sc.faq ? 'ok' : 'fail'}">${sc.faq ? '✓' : '✕'}</span><span class="check-text"><strong>Tiene página de FAQ:</strong> ${sc.faq ? 'Sí' : 'No'}</span></div>`;
        html += `<div class="check-item"><span class="check-icon ${sc.blog ? 'ok' : 'fail'}">${sc.blog ? '✓' : '✕'}</span><span class="check-text"><strong>Tiene blog activo:</strong> ${sc.blog ? 'Sí' : 'No'}</span></div>`;
        if (sc.blog || sc.ultimoPost || S.analysis.metrics[idx + '_ultimoPost']) {
          const _ultimoPost = S.analysis.metrics[idx + '_ultimoPost'] || sc.ultimoPost;
          html += `<div class="check-item"><span class="check-icon ${_ultimoPost ? 'ok' : 'warn'}">${_ultimoPost ? '✓' : '!'}</span><span class="check-text"><strong>Último contenido publicado:</strong> ${_ultimoPost || 'Fecha desconocida'}</span></div>`;
        }
        html += `<div class="check-item"><span class="check-icon ${sc.h ? 'ok' : 'fail'}">${sc.h ? '✓' : '✕'}</span><span class="check-text"><strong>Usa encabezados H1/H2/H3:</strong> ${sc.h ? 'Sí' : 'No'}</span></div>`;
        const hasTablasListas = sc.tablas || sc.listas;
        html += `<div class="check-item"><span class="check-icon ${hasTablasListas ? 'ok' : 'fail'}">${hasTablasListas ? '✓' : '✕'}</span><span class="check-text"><strong>Tiene tablas o listas en el contenido:</strong> ${hasTablasListas ? 'Sí' : 'No'}</span></div>`;
        html += `<div class="check-item"><span class="check-icon ${sc.alttext ? 'ok' : 'fail'}">${sc.alttext ? '✓' : '✕'}</span><span class="check-text"><strong>Alt text en imágenes:</strong> ${sc.alttext ? 'Optimizado' : 'Faltante o deficiente'}</span></div>`;
        html += `<div class="check-item"><span class="check-icon ${sc.casos ? 'ok' : 'fail'}">${sc.casos ? '✓' : '✕'}</span><span class="check-text"><strong>Tiene página de casos de éxito:</strong> ${sc.casos ? 'Sí' : 'No'}</span></div>`;
        html += `<div class="check-item"><span class="check-icon ${sc.glosario ? 'ok' : 'fail'}">${sc.glosario ? '✓' : '✕'}</span><span class="check-text"><strong>Tiene glosario o definiciones:</strong> ${sc.glosario ? 'Sí' : 'No'}</span></div>`;
        autoEl.innerHTML = html;
      }

      document.querySelectorAll('[data-q]').forEach(g => {
        const q = g.getAttribute('data-q');
        const rawManual = S.analysis.metrics[idx + '_' + q];
        g.querySelectorAll('.option').forEach(b => b.className = 'option');
        if (!rawManual || rawManual === 'nosé') {
          const neutralBtn = Array.from(g.querySelectorAll('.option')).find(b => b.textContent.trim() === 'Sin verificar');
          if (neutralBtn) neutralBtn.classList.add('selected-neutral');
          return;
        }
        if (rawManual === 'no') {
          const noBtn = Array.from(g.querySelectorAll('.option')).find(b => b.textContent.trim() === 'No');
          if (noBtn) noBtn.classList.add('selected-no');
          return;
        }
        const btns = g.querySelectorAll('.option');
        if (rawManual === 'si' && btns[0]) btns[0].classList.add('selected-yes');
        else if (rawManual === 'parcial' && btns[1]) btns[1].classList.add('selected-partial');
      });

      let filled = 0;
      const baseFields = ['chatgpt', 'perplexity', 'googlesge', 'comparativos', 'fuentes', 'schema', 'estrellas', 'notas'];
      let totalFields = 8;
      if (sc.error || sc.isSPA) {
        baseFields.push('blog', 'faq', 'headings', 'casos', 'glosario', 'alttext');
        totalFields = 14;
      }
      baseFields.forEach(f => {
        const raw = S.analysis.metrics[idx + '_' + f];
        if (raw && raw !== 'nosé') filled++;
      });

      const progressText = document.getElementById('mp-text');
      const progressFill = document.getElementById('mp-fill');
      if (progressText) progressText.textContent = `${filled} de ${totalFields} métricas verificadas`;
      if (progressFill) progressFill.style.width = `${(filled / totalFields) * 100}%`;

      const stars = S.analysis.metrics[idx + '_estrellas'] || 0;
      document.querySelectorAll('.star-btn').forEach((btn, i) => btn.classList.toggle('active', i < stars));
      const notasInput = document.getElementById('notas-input');
      if (notasInput) notasInput.value = S.analysis.metrics[idx + '_notas'] || '';
    }

    function selectOpt(btn, qId, val) {
      const g = btn.closest('[data-q]');
      g.querySelectorAll('.option').forEach(b => b.className = 'option');
      if (val === 'si') btn.classList.add('selected-yes');
      else if (val === 'no') btn.classList.add('selected-no');
      else if (val === 'nosé') btn.classList.add('selected-neutral');
      else btn.classList.add('selected-partial');
      S.analysis.metrics[S.activeComp + '_' + qId] = val;
      save();
      renderAnalysis(S.activeComp);
      if (S.screen === 3) renderDashboard();
    }

    function selectStar(val) {
      S.analysis.metrics[S.activeComp + '_estrellas'] = val;
      save();
      renderAnalysis(S.activeComp);
    }

    function updateNotas(el) {
      S.analysis.metrics[S.activeComp + '_notas'] = el.value;
      save();
      renderAnalysis(S.activeComp);
    }

    function updateFechaManual(el) {
      S.analysis.metrics[S.activeComp + '_ultimoPost'] = el.value.trim();
      save();
    }

    function updatePostsAnoManual(el) {
      const v = parseInt(el.value);
      S.analysis.metrics[S.activeComp + '_postsAno'] = isNaN(v) ? 0 : v;
      save();
    }

    function renderTabs() {
      const entities = [S.config.myCompany, ...S.config.competitors];
      document.getElementById('comp-tabs').innerHTML = entities.map((e, i) => {
        const sc = S.analysis.scraping[i];
        const pts = S.analysis.done ? score(i) : null;
        const estado = !sc ? '' : sc.error ? ' 🔴' : sc.isSPA ? ' ⚠️' : ' ✅';
        const badge = pts !== null ? `<span style="font-size:10px;opacity:.7;margin-left:4px">${pts}pts</span>` : '';
        return `<button class="comp-tab${i === S.activeComp ? ' active' : ''}" onclick="selTab(${i})">${esc(e.name || 'Entidad ' + i)}${estado}${badge}</button>`;
      }).join('');
      document.getElementById('comp-cur-name').textContent = entities[S.activeComp]?.name || '';
    }

    function selTab(idx) {
      S.activeComp = idx;
      document.querySelectorAll('.comp-tab').forEach((t, i) => t.classList.toggle('active', i === idx));
      document.getElementById('comp-cur-name').textContent = [S.config.myCompany, ...S.config.competitors][idx]?.name || '';
      renderAnalysis(idx);
    }

    async function analizarIndividual(idx) {
      const entities = [S.config.myCompany, ...S.config.competitors];
      const entity = entities[idx];
      if (!entity || !entity.domain) {
        toast('⚠️', 'Dominio no configurado', 'warn');
        return;
      }

      S.activeComp = idx;
      renderTabs();
      document.getElementById('auto-checks').innerHTML = `<div class="check-item"><span class="check-icon warn">⏳</span><span class="check-text">Analizando ${entity.domain}...</span></div>`;

      try {
        const r = await scrapeSingle(entity.domain);
        S.analysis.scraping[idx] = r;
        if (r.error) {
          let msg = 'Error de acceso';
          if (r.error.includes('Falta el .com')) msg = 'Falta el .com o extensión';
          else if (r.blockedByAntiBot) msg = 'Bloqueado por anti-bot';
          else if (r.isSPA) msg = 'Web con JS pesado — completa manualmente';
          else if (r.unreachable) msg = 'Web caída o sin conexión';
          toast('⚠️', msg, 'warn');
        } else {
          toast('✓', 'Análisis completado', '');
        }
        save();
        renderAnalysis(idx);
      } catch (e) {
        S.analysis.scraping[idx] = {
          error: e.message,
          isSPA: false
        };
        save();
        renderAnalysis(idx);
        toast('✕', 'Error de análisis', 'error');
      }
    }

    async function reanalizarCompetidor() {
      const btn = document.getElementById('btn-reanalizar');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '↻ Analizando...';
      }
      await analizarIndividual(S.activeComp);
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '↻ Reanalizar scraping';
      }
    }

    function renderDashboard() {
      const entities = [S.config.myCompany, ...S.config.competitors];
      document.getElementById('last-sync-date').textContent = S.analysis.lastSync ? `Última actualización: ${S.analysis.lastSync}` : 'Análisis no completado';

      const scores = entities.map((e, i) => ({
        name: e.name || 'Entidad ' + i,
        sc: score(i),
        i
      }));
      document.getElementById('scores-grid').innerHTML = scores.map((s, i) => `
    <div class="score-card${i === 0 ? ' highlight' : ''}">
      <div class="score-accent"></div>
      <div class="score-label">${i === 0 ? 'Nuestra empresa' : 'Competidor ' + (i)}</div>
      <div class="score-name">${esc(s.name)}</div>
      <div class="score-value">${s.sc}<span>/100</span></div>
    </div>`).join('');

      let matrixHtml = '<thead><tr><th>Métrica</th>';
      entities.forEach(e => matrixHtml += `<th>${esc(e.name || e.domain)}</th>`);
      matrixHtml += '</tr></thead><tbody>';

      const mList = [{
          id: 'chatgpt',
          n: 'Aparece en ChatGPT'
        }, {
          id: 'perplexity',
          n: 'Aparece en Perplexity'
        },
        {
          id: 'googlesge',
          n: 'Aparece en Google SGE'
        }, {
          id: 'faq',
          n: 'Tiene FAQ estructurada'
        },
        {
          id: 'headings',
          n: 'Usa encabezados H1/H2'
        }, {
          id: 'casos',
          n: 'Casos de uso / éxito'
        },
        {
          id: 'comparativos',
          n: 'Artículos comparativos'
        }, {
          id: 'blog',
          n: 'Contenido actualizado'
        },
        {
          id: 'fuentes',
          n: 'Cita fuentes y datos'
        }, {
          id: 'glosario',
          n: 'Glosario o definiciones'
        },
        {
          id: 'alttext',
          n: 'Alt text en imágenes'
        }, {
          id: 'schema',
          n: 'Schema Markup'
        }
      ];

      mList.forEach(m => {
        matrixHtml += `<tr><td>${m.n}</td>`;
        entities.forEach((_, i) => {
          const val = getSt(i, m.id, S.analysis.scraping[i]);
          const icon = val === 'si' ? '<span class="icon-si">✅</span>' : (val === 'parcial' ? '<span class="icon-parcial">⚠️</span>' : '<span class="icon-no">❌</span>');
          matrixHtml += `<td>${icon}</td>`;
        });
        matrixHtml += `</tr>`;
      });
      document.getElementById('matrix-table').innerHTML = matrixHtml + '</tbody>';

      let gaps = [];
      mList.forEach(m => {
        let myPts = getSt(0, m.id, S.analysis.scraping[0]) === 'si' ? W[m.id] : (getSt(0, m.id, S.analysis.scraping[0]) === 'parcial' ? W_PARCIAL[m.id] : 0);
        let maxCompPts = 0;
        for (let i = 1; i < entities.length; i++) {
          let cSt = getSt(i, m.id, S.analysis.scraping[i]);
          let cPts = cSt === 'si' ? W[m.id] : (cSt === 'parcial' ? W_PARCIAL[m.id] : 0);
          if (cPts > maxCompPts) maxCompPts = cPts;
        }
        if (maxCompPts > myPts) gaps.push({
          name: m.n,
          pts: maxCompPts - myPts
        });
      });

      gaps.sort((a, b) => b.pts - a.pts);
      document.getElementById('brechas-list').innerHTML = gaps.length ?
        gaps.map(g => `<div class="gap-item"><span class="gap-name">${esc(g.name)}</span><span class="gap-pts">-${g.pts} pts respecto al líder</span></div>`).join('') :
        `<div class="empty-state" style="padding:20px"><div class="empty-state-icon" style="font-size:32px">✓</div><div class="empty-state-text" style="color:var(--success)">Excelente, superas o igualas todas las métricas de la competencia.</div></div>`;
    }

    function guardarProgreso() {
      save();
      toast('💾', 'Progreso guardado en disco duro local.');
    }

    function renderTable() {
      const tb = document.getElementById('comp-body');
      tb.innerHTML = '';
      S.config.competitors.forEach((c, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input class="tdi" placeholder="Nombre" value="${esc(c.name)}" oninput="S.config.competitors[${i}].name=this.value; save()"></td>
      <td><input class="tdi" placeholder="dominio.com" value="${esc(c.domain)}" oninput="S.config.competitors[${i}].domain=this.value; save()"></td>
      <td><input class="tdi" placeholder="Sector" value="${esc(c.sector || '')}" oninput="S.config.competitors[${i}].sector=this.value; save()"></td>
      <td><button class="btn btn-danger btn-sm" onclick="removeComp(${i})">Eliminar</button></td>`;
        tb.appendChild(tr);
      });
    }

    function addComp() {
      if (S.screen === 1) syncDOM();
      S.config.competitors.push({
        name: '',
        domain: '',
        sector: ''
      });
      renderTable();
      save();
    }

    function removeComp(i) {
      S.config.competitors.splice(i, 1);
      renderTable();
      save();
    }

    function syncDOM() {
      const scr1 = document.getElementById('screen-1');
      if (scr1 && scr1.classList.contains('active')) {
        S.config.myCompany.name = document.getElementById('my-name')?.value || '';
        S.config.myCompany.domain = document.getElementById('my-domain')?.value || '';
        S.config.myCompany.sector = document.getElementById('my-sector')?.value || '';
        S.config.competitors = Array.from(document.querySelectorAll('#comp-body tr')).map(r => {
          const i = r.querySelectorAll('.tdi');
          return {
            name: i[0].value,
            domain: i[1].value,
            sector: i[2] ? i[2].value : ''
          };
        });
      }
    }

    function goScreen(n) {
      if (S.screen === 1) syncDOM();
      save();
      document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
      document.querySelectorAll('.nav-item').forEach(s => s.classList.remove('active', 'done'));
      document.querySelectorAll('.progress-dot').forEach((d, i) => {
        if (d) {
          d.classList.remove('active', 'done');
          if (i + 1 < n) d.classList.add('done');
          else if (i + 1 === n) d.classList.add('active');
        }
      });
      document.getElementById('screen-' + n)?.classList.add('active');
      document.getElementById('nav-' + n)?.classList.add('active');
      for (let i = 1; i < n; i++) document.getElementById('nav-' + i)?.classList.add('done');
      var topbarItem = document.getElementById('topbar-item');
      if (topbarItem) topbarItem.textContent = SCREENS[n];
      S.screen = n;
      save();
      if (n === 1) {
        document.getElementById('my-name').value = S.config.myCompany.name;
        document.getElementById('my-domain').value = S.config.myCompany.domain;
        renderTable();
      }
      if (n === 2) {
        renderTabs();
        renderAnalysis(S.activeComp);
      }
      if (n === 3) renderDashboard();
      if (n === 4) {
        renderHistorial();
        if (S.historialPlanes && S.historialPlanes.length > 0) {
          renderPlan(S.historialPlanes[0].plan, 0);
        } else if (S.plan && S.plan.acciones && S.plan.acciones.length > 0) {
          renderPlan(S.plan, -1);
        }
      }
      if (n === 5) {
        renderBrechasGenerador();
        initMotorPropuestas();
      }
      if (n === 6) {
        renderBandeja('todos');
      }
      if (n === 7) {
        renderHistorialTemas();
      }
      if (n === 8) {
        renderBiblioteca('todos');
        actualizarBadgeBiblioteca();
      }
      if (n === 9) {
        cargarUsuarios();
      }
      window.scrollTo(0, 0);
    }

    function startAnalysis() {
      if (S.screen === 1) syncDOM();
      S.analysis.scraping = [];
      S.analysis.done = false;
      S.activeComp = 0;

      if (S.analysis.metrics) {
        const autoKeys = ['_casos', '_glosario', '_alttext', '_schema', '_blog', '_faq', '_headings', '_tablas', '_ultimoPost', '_postsAno'];
        Object.keys(S.analysis.metrics).forEach(k => {
          if (autoKeys.some(suffix => k.endsWith(suffix))) {
            delete S.analysis.metrics[k];
          }
        });
      }
      save();
      goScreen(2);
      renderTabs();
      runAnalysis();
    }

    function init() {
      initTheme();
      if (load()) toast('✓', 'Sesión recuperada');
      else S.config.myCompany = {
        name: '',
        domain: '',
        sector: 'Sostenibilidad en eventos'
      }, S.config.competitors = [];
      document.getElementById('my-name').value = S.config.myCompany.name || '';
      document.getElementById('my-domain').value = S.config.myCompany.domain || '';
      renderTable();
      const n = S.screen || 1;
      goScreen(n);
      if (n === 2 && !S.analysis.done) runAnalysis();
      cargarBDyRest();
      checkAdmin();
    }

    /* ─────────────────────────────────────────────────────────
       PLAN DE ACCIÓN — RF-15
       ───────────────────────────────────────────────────────── */

    function construirPrompt() {
      const entities = [S.config.myCompany, ...S.config.competitors];
      const miEmpresa = S.config.myCompany;
      const miScore = score(0);

      const metricas = [{
          id: 'chatgpt',
          label: 'Aparece en ChatGPT',
          pts: 15
        },
        {
          id: 'perplexity',
          label: 'Aparece en Perplexity',
          pts: 15
        },
        {
          id: 'googlesge',
          label: 'Aparece en Google SGE',
          pts: 10
        },
        {
          id: 'faq',
          label: 'Tiene FAQ estructurada',
          pts: 10
        },
        {
          id: 'headings',
          label: 'Usa encabezados H1/H2',
          pts: 8
        },
        {
          id: 'casos',
          label: 'Tiene casos de uso/éxito',
          pts: 8
        },
        {
          id: 'comparativos',
          label: 'Artículos comparativos',
          pts: 7
        },
        {
          id: 'blog',
          label: 'Contenido actualizado',
          pts: 7
        },
        {
          id: 'fuentes',
          label: 'Cita fuentes y datos',
          pts: 5
        },
        {
          id: 'glosario',
          label: 'Glosario o definiciones',
          pts: 5
        },
        {
          id: 'alttext',
          label: 'Alt text en imágenes',
          pts: 5
        },
        {
          id: 'schema',
          label: 'Schema Markup',
          pts: 5
        },
      ];

      const tablaEstado = metricas.map(m => {
        const est = getSt(0, m.id, S.analysis.scraping[0]);
        const txt = est === 'si' ? 'SI' : est === 'parcial' ? 'PARCIAL' : 'NO';
        const ptsObtenidos = est === 'si' ? m.pts : (est === 'parcial' ? W_PARCIAL[m.id] : 0);
        return '  ' + m.label + ': ' + txt + ' (' + ptsObtenidos + '/' + m.pts + ' pts)';
      }).join('\n');

      const brechas = [];
      metricas.forEach(m => {
        const miEst = getSt(0, m.id, S.analysis.scraping[0]);
        const miPts = miEst === 'si' ? m.pts : (miEst === 'parcial' ? W_PARCIAL[m.id] : 0);
        let maxComp = 0;
        for (let i = 1; i < entities.length; i++) {
          const cEst = getSt(i, m.id, S.analysis.scraping[i]);
          const cPts = cEst === 'si' ? m.pts : (cEst === 'parcial' ? W_PARCIAL[m.id] : 0);
          if (cPts > maxComp) maxComp = cPts;
        }
        if (maxComp > miPts) brechas.push({
          label: m.label,
          gap: maxComp - miPts,
          max: m.pts
        });
      });
      brechas.sort((a, b) => b.gap - a.gap);

      const listaBrechas = brechas.length > 0 ?
        brechas.map(b => '  - ' + b.label + ': ' + b.gap + ' pts por debajo del líder (máximo: ' + b.max + ' pts)').join('\n') :
        '  - Sin brechas detectadas respecto a la competencia';

      const listaComp = S.config.competitors.length > 0 ?
        S.config.competitors.map((c, i) => '  - ' + (c.name || c.domain) + ': ' + score(i + 1) + '/100 pts').join('\n') :
        '  - Sin competidores configurados';

      return 'Eres un consultor experto en GEO (Generative Engine Optimization) para empresas SaaS de sostenibilidad.\n\n' +
        'Tu tarea es analizar los datos de posicionamiento GEO y generar un plan de accion concreto y priorizado.\n\n' +
        'DATOS DE LA EMPRESA:\n' +
        '  - Nombre: ' + (miEmpresa.name || miEmpresa.domain) + '\n' +
        '  - Sector: ' + miEmpresa.sector + '\n' +
        '  - Puntuacion GEO actual: ' + miScore + '/100\n\n' +
        'PUNTUACIONES DE COMPETIDORES:\n' + listaComp + '\n\n' +
        'ESTADO DE LAS 12 METRICAS GEO:\n' + tablaEstado + '\n\n' +
        'BRECHAS RESPECTO A LA COMPETENCIA (ordenadas por impacto):\n' + listaBrechas + '\n\n' +
        'INSTRUCCION CRITICA: Responde UNICAMENTE con un objeto JSON valido. Absolutamente nada antes ni despues del JSON. Sin bloques de codigo markdown, sin explicaciones.\n\n' +
        'El JSON debe tener exactamente esta estructura:\n' +
        '{"resumen":"resumen ejecutivo en 2-3 frases","acciones":[{"prioridad":1,"titulo":"titulo corto","impacto":"alto","esfuerzo":"bajo","plazo":"inmediato","descripcion":"descripcion detallada y accionable","puntos_potenciales":10,"requiere_articulo":true,"tipo_sugerido":"faq avanzada","tema_sugerido":"Titulo concreto del articulo","kws_sugeridas":"keyword1, keyword2"}],"quick_wins":["accion rapida 1","accion rapida 2"],"mensaje_final":"frase motivacional concreta"}\n\n' +
        'Reglas estrictas:\n' +
        '- impacto: solo los valores "alto", "medio" o "bajo"\n' +
        '- esfuerzo: solo los valores "alto", "medio" o "bajo"\n' +
        '- plazo: solo los valores "inmediato", "corto" o "medio"\n' +
        '- Genera entre 4 y 6 acciones ordenadas de mayor a menor prioridad\n' +
        '- Se especifico y accionable, evita generalidades\n' +
        '- Prioriza las brechas detectadas respecto a la competencia\n' +
        '- requiere_articulo: true si la accion se puede cumplir creando contenido (blog, FAQ, glosario, comparativo, caso de exito, guia paso a paso). false si es una accion tecnica que debe hacerse manualmente (configurar schema markup, mejorar alt text, verificar en ChatGPT, conseguir backlinks, redisenar estructura web)\n' +
        '- tipo_sugerido: usar SOLO uno de estos valores exactos: "faq avanzada", "guia operativa", "comparativo analitico", "glosario tecnico", "caso de exito". Solo si requiere_articulo es true\n' +
        '- tema_sugerido: titulo concreto del articulo optimizado para busquedas de IA. Solo si requiere_articulo es true\n' +
        '- kws_sugeridas: 3-4 keywords del sector separadas por comas. Solo si requiere_articulo es true';
    }

    async function generarPlanAccion() {
      const btn = document.getElementById('btn-generar-plan');
      const container = document.getElementById('plan-container');

      if (!S.analysis.done) {
        toast('⚠️', 'Completa el análisis antes de generar el plan', 'warn');
        return;
      }

      btn.disabled = true;
      btn.textContent = '⏳ Generando plan…';
      container.innerHTML =
        '<div style="text-align:center;padding:56px 24px;color:var(--text-muted)">' +
        '<div style="font-size:40px;margin-bottom:16px">🤖</div>' +
        '<div style="font-size:15px">Claude está analizando los datos…</div>' +
        '<div style="font-size:13px;margin-top:8px;opacity:.7">Esto puede tardar unos segundos</div>' +
        '</div>';

      try {
        const prompt = construirPrompt();

        const response = await fetch(MY_WORKER + '/api/plan', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            prompt: prompt
          })
        });

        if (!response.ok) {
          const errText = await response.text().catch(function() {
            return '';
          });
          throw new Error('Error del servidor (' + response.status + ')' + (errText ? ': ' + errText.slice(0, 120) : ''));
        }

        const data = await response.json();

        if (data.error) {
          throw new Error('Error de la API de Anthropic: ' + JSON.stringify(data.error));
        }

        const texto = data.content && data.content[0] && data.content[0].text;
        if (!texto) {
          throw new Error('La API no devolvió contenido.');
        }

        let jsonLimpio;
        const matchBloque = texto.match(/```(?:json)?\s*(\{[\s\S]*?\})\s*```/i);
        if (matchBloque) {
          jsonLimpio = matchBloque[1];
        } else {
          const matchLlaves = texto.match(/\{[\s\S]*\}/);
          if (!matchLlaves) throw new Error('Claude no devolvió un JSON válido');
          jsonLimpio = matchLlaves[0];
        }

        let plan;
        try {
          plan = JSON.parse(jsonLimpio);
        } catch (parseErr) {
          throw new Error('La respuesta de Claude no es JSON válido. Inténtalo de nuevo.');
        }

        if (!plan.acciones || !Array.isArray(plan.acciones) || plan.acciones.length === 0) {
          throw new Error('El plan generado no contiene acciones. Inténtalo de nuevo.');
        }

        S.plan = plan;

        if (!S.historialPlanes) S.historialPlanes = [];
        var ahora = new Date();
        var fechaLabel = ahora.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          }) +
          ' ' + ahora.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
          });
        S.historialPlanes.unshift({
          id: ahora.getTime(),
          fechaLabel: fechaLabel,
          plan: plan,
          notionChecks: {}
        });
        if (S.historialPlanes.length > 20) S.historialPlanes = S.historialPlanes.slice(0, 20);

        // PIPELINE DIRECTO AL GENERADOR DE CONTENIDO (RF-27)
        var accionesContenido = (plan.acciones || []).filter(function(a) {
          return a.requiere_articulo === true;
        });
        if (accionesContenido.length > 0) {
          var p = getPropuestas();
          var nuevasPropuestas = accionesContenido.map(function(a, i) {
            return {
              id: ahora.getTime() + i,
              origen: 'plan',
              brechaLabel: a.titulo,
              tipo: a.tipo_sugerido || 'guia operativa',
              tema: a.tema_sugerido || a.titulo,
              kws: a.kws_sugeridas || '',
              gap: a.puntos_potenciales || 0,
              estado: 'pendiente',
              fechaCreacion: ahora.getTime()
            };
          });
          p.cola = nuevasPropuestas.concat(p.cola.filter(function(x) {
            return x.origen !== 'plan';
          }));
          if (p.cola.length > 20) p.cola = p.cola.slice(0, 20);
        }

        save();
        renderHistorial();
        renderPlan(plan, 0);

        var msg = accionesContenido.length > 0 ?
          'Plan generado · ' + accionesContenido.length + ' artículo(s) enviado(s) al Generador ✨' :
          'Plan de acción generado correctamente';
        toast('✓', msg);

      } catch (e) {
        console.error('[Plan de acción]', e);
        container.innerHTML =
          '<div class="card" style="border-left:4px solid var(--danger)">' +
          '<p style="color:var(--danger);font-weight:600;margin-bottom:8px">❌ Error al generar el plan</p>' +
          '<p style="color:var(--text-secondary);font-size:14px;margin-bottom:12px">' + esc(e.message) + '</p>' +
          '<p style="color:var(--text-muted);font-size:13px;margin:0">' +
          'Comprueba que el Worker de Cloudflare está funcionando correctamente.' +
          '</p>' +
          '</div>';
        toast('✕', 'Error al generar el plan', 'error');
      } finally {
        btn.disabled = false;
        btn.textContent = '✨ Regenerar plan';
      }
    }

    function renderHistorial() {
      if (!S.historialPlanes) S.historialPlanes = [];
      var lista = document.getElementById('historial-list');
      var count = document.getElementById('historial-count');
      if (!lista) return;

      count.textContent = S.historialPlanes.length + (S.historialPlanes.length === 1 ? ' plan' : ' planes');

      if (S.historialPlanes.length === 0) {
        lista.innerHTML = '<div style="padding:24px 16px;text-align:center;color:var(--text-muted);font-size:13px">Aún no hay planes generados</div>';
        return;
      }

      lista.innerHTML = S.historialPlanes.map(function(entrada, idx) {
        var notionTotal = Object.keys(entrada.notionChecks || {}).length;
        var notionHecho = Object.values(entrada.notionChecks || {}).filter(Boolean).length;
        var todoNotion = notionTotal > 0 && notionHecho === notionTotal;
        var activo = idx === (window._planActivoIdx !== undefined ? window._planActivoIdx : 0);

        return '<div onclick="verPlanHistorial(' + idx + ')" style="' +
          'padding:12px 16px;cursor:pointer;border-bottom:1px solid var(--border);transition:background .15s;' +
          (activo ? 'background:rgba(37,99,235,0.08);border-left:3px solid var(--primary)' : 'border-left:3px solid transparent') +
          '">' +
          '<div style="font-size:12px;font-weight:600;color:var(--text-primary);margin-bottom:3px">' + entrada.fechaLabel + '</div>' +
          '<div style="font-size:11px;color:var(--text-muted)">' + (entrada.plan.acciones ? entrada.plan.acciones.length : 0) + ' acciones' +
          (notionTotal > 0 ? ' · <span style="color:' + (todoNotion ? 'var(--success)' : 'var(--warning)') + '">' + notionHecho + '/' + notionTotal + ' en Notion</span>' : '') +
          '</div>' +
          '</div>';
      }).join('');
    }

    function verPlanHistorial(idx) {
      if (!S.historialPlanes || !S.historialPlanes[idx]) return;
      window._planActivoIdx = idx;
      renderHistorial();
      renderPlan(S.historialPlanes[idx].plan, idx);
    }

    function toggleNotion(historialIdx, accionIdx, checked) {
      if (!S.historialPlanes || !S.historialPlanes[historialIdx]) return;
      if (!S.historialPlanes[historialIdx].notionChecks) S.historialPlanes[historialIdx].notionChecks = {};
      S.historialPlanes[historialIdx].notionChecks[accionIdx] = checked;
      save();
      renderHistorial();
      renderPlan(S.historialPlanes[historialIdx].plan, historialIdx);
    }

    function renderPlan(plan, historialIdx) {
      if (!plan || !plan.acciones) return;
      window._planActivoIdx = historialIdx >= 0 ? historialIdx : 0;

      var notionChecks = {};
      if (historialIdx >= 0 && S.historialPlanes && S.historialPlanes[historialIdx]) {
        notionChecks = S.historialPlanes[historialIdx].notionChecks || {};
      }

      var colImpacto = {
        alto: {
          bg: 'rgba(239,68,68,.12)',
          color: '#ef4444'
        },
        medio: {
          bg: 'rgba(245,158,11,.12)',
          color: '#b45309'
        },
        bajo: {
          bg: 'rgba(34,197,94,.12)',
          color: '#16a34a'
        }
      };
      var textoPlazo = {
        inmediato: '⚡ Inmediato',
        corto: '📅 Corto plazo',
        medio: '🗓 Medio plazo'
      };

      function badgeImpacto(nivel) {
        var c = colImpacto[nivel] || colImpacto.medio;
        return '<span style="background:' + c.bg + ';color:' + c.color + ';padding:2px 10px;border-radius:99px;font-size:11px;font-weight:600;text-transform:capitalize;white-space:nowrap">Impacto ' + (nivel || '—') + '</span>';
      }

      var quickWins = Array.isArray(plan.quick_wins) ? plan.quick_wins : [];
      var acciones = Array.isArray(plan.acciones) ? plan.acciones : [];
      var html = '';

      html += '<div class="card" style="border-left:4px solid var(--primary);margin-bottom:20px">';
      html += '<div style="font-size:11px;font-weight:600;letter-spacing:.08em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px">Resumen ejecutivo</div>';
      html += '<p style="font-size:14px;line-height:1.7;color:var(--text-primary);margin:0">' + esc(plan.resumen || '') + '</p>';
      html += '</div>';

      if (quickWins.length > 0) {
        html += '<div class="card" style="margin-bottom:20px">';
        html += '<div class="card-header"><span class="card-title">⚡ Quick wins — Hazlo esta semana</span></div>';
        html += '<ul style="margin:8px 0 0 0;padding-left:20px">';
        quickWins.forEach(function(qw) {
          html += '<li style="margin-bottom:7px;color:var(--text-secondary);font-size:14px;line-height:1.5">' + esc(qw) + '</li>';
        });
        html += '</ul></div>';
      }

      var accionesContenido = acciones.filter(function(a) {
        return a.requiere_articulo === true;
      });
      var accionesManuales = acciones.filter(function(a) {
        return a.requiere_articulo !== true;
      });

      var notionHecho = Object.values(notionChecks).filter(Boolean).length;
      var notionTotal = acciones.length;

      if (accionesManuales.length > 0) {
        html += '<div style="margin-bottom:14px;display:flex;align-items:center;justify-content:space-between">';
        html += '<div style="display:flex;align-items:center;gap:8px">';
        html += '<span style="font-size:16px">🛠️</span>';
        html += '<span class="card-title" style="font-size:15px">Tareas manuales</span>';
        html += '<span style="font-size:11px;color:var(--text-muted);background:var(--bg-body);padding:2px 8px;border-radius:99px;border:1px solid var(--border)">' + accionesManuales.length + ' tarea' + (accionesManuales.length > 1 ? 's' : '') + '</span>';
        html += '</div>';
        html += '<span style="font-size:12px;color:var(--text-muted)">' + notionHecho + '/' + notionTotal + ' en Notion</span>';
        html += '</div>';

        accionesManuales.forEach(function(a) {
          var aIdx = acciones.indexOf(a);
          var enNotion = !!notionChecks[aIdx];
          html += renderAccionCard(a, aIdx, historialIdx, enNotion, colImpacto, textoPlazo, badgeImpacto, false);
        });
      }

      if (accionesContenido.length > 0) {
        html += '<div style="margin-top:' + (accionesManuales.length > 0 ? '28px' : '0') + ';margin-bottom:14px;display:flex;align-items:center;gap:8px">';
        html += '<span style="font-size:16px">✨</span>';
        html += '<span class="card-title" style="font-size:15px">Tareas que se cumplen generando contenido</span>';
        html += '<span style="font-size:11px;font-weight:600;color:#059669;background:rgba(16,185,129,.1);padding:2px 8px;border-radius:99px;border:1px solid rgba(16,185,129,.2)">' + accionesContenido.length + ' tareas estratégicas</span>';
        html += '</div>';

        html += '<div style="padding:10px 14px;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.18);border-radius:8px;margin-bottom:14px;font-size:12px;color:#059669;display:flex;align-items:center;gap:8px">';
        html += '<span style="font-size:14px">💡</span>';
        html += 'Usa el Generador de Contenido (Pestaña 5) para crear artículos que cumplan estas tareas automáticamente. ';
        html += '<span onclick="goScreen(5)" style="text-decoration:underline;cursor:pointer;font-weight:600">Ir al Generador →</span>';
        html += '</div>';

        accionesContenido.forEach(function(a) {
          var aIdx = acciones.indexOf(a);
          var enNotion = !!notionChecks[aIdx];
          html += renderAccionCard(a, aIdx, historialIdx, enNotion, colImpacto, textoPlazo, badgeImpacto, true);
        });
      }

      if (plan.mensaje_final) {
        html += '<div style="text-align:center;padding:24px 16px;color:var(--text-muted);font-style:italic;font-size:13px;line-height:1.6">' + esc(plan.mensaje_final) + '</div>';
      }

      document.getElementById('plan-container').innerHTML = html;
    }

    function renderAccionCard(a, aIdx, historialIdx, enNotion, colImpacto, textoPlazo, badgeImpacto, esContenido) {
      var html = '';
      html += '<div class="card" style="margin-bottom:14px;position:relative;overflow:hidden;' +
        (enNotion ? 'opacity:.7;' : '') +
        (esContenido ? 'border-left:3px solid rgba(16,185,129,.4);' : '') +
        '">';
      html += '<div style="position:absolute;top:10px;right:14px;font-size:48px;font-weight:800;opacity:.04;color:var(--text-primary);line-height:1;pointer-events:none;user-select:none">' + (a.prioridad || '') + '</div>';

      html += '<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;flex-wrap:wrap">';
      html += '<span style="font-weight:600;font-size:14px;color:var(--text-primary);flex:1;min-width:140px">' + esc(a.titulo || '') + '</span>';
      html += '<div style="display:flex;gap:6px;align-items:center;flex-shrink:0;flex-wrap:wrap">';
      html += badgeImpacto(a.impacto);
      html += '<span style="font-size:12px;color:var(--text-muted);white-space:nowrap">' + (textoPlazo[a.plazo] || a.plazo || '') + '</span>';
      html += '<span style="font-size:13px;font-weight:700;color:var(--primary);white-space:nowrap">+' + (a.puntos_potenciales || 0) + ' pts</span>';
      html += '</div>';
      html += '</div>';

      html += '<p style="color:var(--text-secondary);font-size:14px;line-height:1.65;margin:0 0 12px 0">' + esc(a.descripcion || '') + '</p>';

      if (esContenido && a.tema_sugerido) {
        html += '<div style="padding:8px 10px;background:rgba(16,185,129,.06);border-radius:6px;margin-bottom:10px;font-size:12px;color:#059669">';
        html += '<strong>Sugerencia de la IA:</strong> ' + esc(a.tema_sugerido);
        if (a.tipo_sugerido) html += ' <span style="opacity:.7">· ' + esc(a.tipo_sugerido) + '</span>';
        html += '</div>';
      }

      html += '<label style="display:inline-flex;align-items:center;gap:7px;cursor:pointer;' +
        'padding:5px 11px;border-radius:6px;font-size:12px;font-weight:500;' +
        'border:1px solid ' + (enNotion ? 'rgba(99,102,241,.4)' : 'var(--border)') + ';' +
        'background:' + (enNotion ? 'rgba(99,102,241,.08)' : 'var(--bg-body)') + ';' +
        'color:' + (enNotion ? '#6366f1' : 'var(--text-muted)') + ';' +
        'transition:all .15s">' +
        '<input type="checkbox"' + (enNotion ? ' checked' : '') +
        ' onchange="toggleNotion(' + historialIdx + ',' + aIdx + ',this.checked)"' +
        ' style="accent-color:#6366f1;width:14px;height:14px">' +
        (enNotion ? '✓ Pasado a Notion' : 'Pasar a Notion') +
        '</label>';

      html += '</div>';
      return html;
    }

    /* ─────────────────────────────────────────────────────────
       MÓDULO 2A: MOTOR DE PROPUESTAS SEMANAL (RF-21 a RF-24)
       ───────────────────────────────────────────────────────── */

    function getTemasRecientes() {
      if (!S.historialTemas) S.historialTemas = [];
      const noventaDias = 90 * 24 * 60 * 60 * 1000;
      const ahora = Date.now();

      const historicos = S.historialTemas
        .filter(item => (ahora - item.fecha) < noventaDias)
        .map(item => item.titulo);

      var p = getPropuestas();
      var enCola = p.cola.map(item => item.tema);

      var todos = [...new Set([...historicos, ...enCola])];
      if (todos.length === 0) return 'Ninguno todavía';
      return todos.map((t, i) => (i + 1) + ". " + t).join('\n');
    }

    function getPropuestas() {
      if (!S.propuestas) S.propuestas = {
        config: {
          tamanoLote: 2,
          frecuenciaDias: 7,
          ultimaGeneracion: null
        },
        cola: []
      };
      if (!S.propuestas.config) S.propuestas.config = {
        tamanoLote: 2,
        frecuenciaDias: 7,
        ultimaGeneracion: null
      };
      if (!S.propuestas.cola) S.propuestas.cola = [];
      return S.propuestas;
    }

    function initMotorPropuestas() {
      var p = getPropuestas();

      var freq = document.getElementById('config-frecuencia');
      if (freq) freq.value = p.config.frecuenciaDias;
      actualizarBotonesLote(p.config.tamanoLote);
      actualizarLabelProximo();
      actualizarStatusLabel();
      renderColaPropostas();

      if (p.config.ultimaGeneracion) {
        var diasPasados = (Date.now() - p.config.ultimaGeneracion) / 86400000;
        if (diasPasados >= p.config.frecuenciaDias && p.cola.length === 0) {
          toast('🔔', 'Han pasado ' + Math.floor(diasPasados) + ' días. Puedes generar un nuevo lote.', 'warn');
        }
      }
    }

    function toggleMotorConfig() {
      var panel = document.getElementById('motor-config-panel');
      if (panel) panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }

    function setTamanoLote(n) {
      getPropuestas().config.tamanoLote = n;
      save();
      actualizarBotonesLote(n);
    }

    function setFrecuencia(val) {
      var v = Math.max(1, Math.min(30, parseInt(val) || 7));
      getPropuestas().config.frecuenciaDias = v;
      save();
      actualizarLabelProximo();
    }

    function actualizarBotonesLote(activo) {
      [1, 2, 3, 4].forEach(function(n) {
        var btn = document.getElementById('tl-' + n);
        if (!btn) return;
        btn.className = n === activo ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm';
      });
    }

    function actualizarLabelProximo() {
      var p = getPropuestas();
      var el = document.getElementById('config-proximo-label');
      if (!el) return;
      if (!p.config.ultimaGeneracion) {
        el.textContent = 'Sin lote anterior';
        return;
      }
      var proximo = new Date(p.config.ultimaGeneracion + p.config.frecuenciaDias * 86400000);
      el.textContent = 'Próximo lote: ' + proximo.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    }

    function actualizarStatusLabel() {
      var p = getPropuestas();
      var el = document.getElementById('motor-status-label');
      if (!el) return;
      if (!p.config.ultimaGeneracion) {
        el.textContent = 'Sin lotes generados — pulsa "Generar lote" para empezar';
        return;
      }
      var pendientes = p.cola.filter(function(x) {
        return x.estado === 'pendiente';
      }).length;
      var completados = p.cola.filter(function(x) {
        return x.estado === 'completado';
      }).length;
      var fecha = new Date(p.config.ultimaGeneracion).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
      el.textContent = 'Último lote: ' + fecha + ' · ' + pendientes + ' pendientes · ' + completados + ' completados';
    }

    async function generarLote() {
      var p = getPropuestas();

      var btn = document.getElementById('btn-generar-lote');
      if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳ Generando...';
      }

      var planActivo = S.historialPlanes && S.historialPlanes.length > 0 ? S.historialPlanes[0].plan : null;

      if (!planActivo || !planActivo.acciones) {
        toast('⚠️', 'Ve a "Plan de acción" y genera un plan primero.', 'warn');
        if (btn) {
          btn.disabled = false;
          btn.textContent = '📦 Generar lote';
        }
        return;
      }

      var tareasContenido = planActivo.acciones.filter(function(a) {
        return a.requiere_articulo === true;
      });

      if (tareasContenido.length === 0) {
        toast('✓', 'No hay tareas de contenido pendientes en tu plan de acción.', '');
        if (btn) {
          btn.disabled = false;
          btn.textContent = '📦 Generar lote';
        }
        return;
      }

      var tamano = p.config.tamanoLote;
      var historialEvitar = getTemasRecientes();
      var empresaName = S.config.myCompany.name || 'Bluease';
      var sector = S.config.myCompany.sector || 'sostenibilidad en eventos';

      var contextoTareas = tareasContenido.map(function(t) {
        return "- Tarea: \"" + t.titulo + "\"\n  Objetivo: " + t.descripcion;
      }).join('\n\n');

      var promptLote =
        'Eres un estratega de contenido GEO para la empresa "' + empresaName + '" del sector: "' + sector + '".\n\n' +
        'En nuestro Plan de Acción actual, tenemos las siguientes tareas estratégicas que se cumplen generando contenido:\n\n' +
        '<tareas_plan_accion>\n' + contextoTareas + '\n</tareas_plan_accion>\n\n' +
        'HISTORIAL DE TEMAS RECIENTES (¡PROHIBIDO REPETIR ESTOS TEMAS!):\n' +
        '<historial_prohibido>\n' + (historialEvitar ? historialEvitar : 'Ninguno todavía') + '\n</historial_prohibido>\n\n' +
        'Tu misión es generar un lote de EXACTAMENTE ' + tamano + ' propuestas de artículos concretos y originales que cumplan directamente con estas tareas estratégicas.\n' +
        'REGLA ANTI-DUPLICADOS: Revisa el <historial_prohibido> y asegúrate de proponer ángulos, enfoques y títulos TOTALMENTE DIFERENTES. Serás penalizado si repites algo similar.\n\n' +
        'Responde ÚNICAMENTE con un JSON válido sin texto antes ni después:\n' +
        '{"propuestas":[{"brechaLabel":"(Nombre exacto de la tarea del plan que resuelve)","titulo":"Título SEO del artículo","tipo":"(elige uno: guia operativa, faq avanzada, comparativo analitico, glosario tecnico, caso de exito)","kws":"keyword1, keyword2"}]}';

      try {
        var response = await fetch(MY_WORKER + '/api/plan', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            prompt: promptLote
          })
        });
        if (!response.ok) throw new Error('Error HTTP ' + response.status);

        var data = await response.json();
        if (data.error) throw new Error(data.error.message || JSON.stringify(data.error));

        var texto = data.content && data.content[0] && data.content[0].text;
        if (!texto) throw new Error('Respuesta vacía de la API');

        var jsonLimpio;
        var matchBloque = texto.match(/```(?:json)?\s*(\{[\s\S]*?\})\s*```/i);
        if (matchBloque) {
          jsonLimpio = matchBloque[1];
        } else {
          var matchLlaves = texto.match(/\{[\s\S]*\}/);
          if (!matchLlaves) throw new Error('Claude no devolvió un JSON válido');
          jsonLimpio = matchLlaves[0];
        }

        var resultado = JSON.parse(jsonLimpio);
        if (!resultado.propuestas || !Array.isArray(resultado.propuestas)) throw new Error('Formato de respuesta inesperado');

        if (!S.historialTemas) S.historialTemas = [];
        resultado.propuestas.forEach(function(prop) {
          S.historialTemas.push({
            titulo: prop.titulo,
            fecha: Date.now()
          });
        });

        var nuevasColas = resultado.propuestas.map(function(r, i) {
          return {
            id: Date.now() + i,
            origen: 'motor',
            brechaId: 'plan_accion',
            brechaLabel: r.brechaLabel || 'Tarea del Plan de Acción',
            tipo: r.tipo || 'guia operativa',
            tema: r.titulo || 'Tema sin título',
            kws: r.kws || '',
            gap: 0,
            estado: 'pendiente',
            fechaCreacion: Date.now()
          };
        });

        p.cola = nuevasColas.concat(p.cola);
        if (p.cola.length > 20) p.cola = p.cola.slice(0, 20);

        p.config.ultimaGeneracion = Date.now();
        save();

        actualizarStatusLabel();
        actualizarLabelProximo();
        renderColaPropostas();
        actualizarBadgeBandeja();
        toast('✓', 'Lote generado basado en el Plan de Acción');

      } catch (e) {
        console.error('[Motor propuestas]', e);
        toast('✕', 'Error al generar el lote: ' + e.message, 'error');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = '📦 Generar lote';
        }
      }
    }

    function renderColaPropostas() {
      var p = getPropuestas();
      var cola = document.getElementById('motor-cola');
      if (!cola) return;

      // Motor solo muestra propuestas pendientes o con error — el resto ya está en la bandeja
      var visibles = (p.cola || []).filter(function(prop) {
        return prop.estado === 'pendiente' || prop.estado === 'error' || prop.estado === 'generando';
      });

      if (visibles.length === 0) {
        cola.innerHTML =
          '<div style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px">' +
          '<div style="font-size:28px;margin-bottom:8px">✅</div>' +
          'No hay propuestas pendientes · Pulsa "Generar lote" para crear nuevas' +
          '</div>';
        return;
      }

      var TIPO_LABEL = {
        'faq avanzada': '❓ FAQ',
        'comparativo analitico': '⚖️ Comparativo',
        'caso de exito': '💼 Caso de éxito',
        'glosario tecnico': '📖 Glosario',
        'guia operativa': '📋 Guía'
      };
      var ESTADO_BADGE = {
        pendiente: {
          bg: 'rgba(100,116,139,.12)',
          color: 'var(--text-muted)',
          txt: '⏳ Pendiente'
        },
        generando: {
          bg: 'rgba(37,99,235,.12)',
          color: 'var(--primary)',
          txt: '⚙️ Generando…'
        },
        error: {
          bg: 'rgba(239,68,68,.12)',
          color: '#dc2626',
          txt: '❌ Error'
        }
      };

      var html = '<div style="display:flex;flex-direction:column;gap:10px">';

      visibles.forEach(function(prop) {
        // Obtener índice real en la cola completa para las acciones
        var idx = p.cola.indexOf(prop);
        var badge = ESTADO_BADGE[prop.estado] || ESTADO_BADGE.pendiente;
        var tl = TIPO_LABEL[prop.tipo] || '📝 Blog';
        var fecha = new Date(prop.fechaCreacion).toLocaleDateString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        });
        var esGenerando = prop.estado === 'generando';
        var tieneError = prop.estado === 'error' && prop.errorMotor;

        html += '<div style="border:1px solid var(--border);border-radius:10px;padding:14px 16px;' +
          'background:var(--bg-body);' +
          (esGenerando ? 'border-color:var(--primary);' : '') +
          (tieneError ? 'border-color:rgba(239,68,68,.4);' : '') +
          '">';

        // Cabecera: tipo + brecha + estado
        html += '<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px">';
        html += '<div style="flex:1">';
        html += '<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;flex-wrap:wrap">';
        html += '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;' +
          'background:rgba(37,99,235,.1);color:var(--primary)">' + tl + '</span>';
        html += '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;' +
          'background:rgba(16,185,129,.1);color:#059669">Cierra: ' + esc(prop.brechaLabel) + '</span>';
        html += '<span style="font-size:11px;padding:2px 8px;border-radius:99px;' +
          'background:' + badge.bg + ';color:' + badge.color + '">' + badge.txt + '</span>';
        html += '</div>';
        html += '<div style="font-size:14px;font-weight:600;color:var(--text-primary);line-height:1.4">' +
          esc(prop.tema) + '</div>';
        if (prop.kws) {
          html += '<div style="font-size:11px;color:var(--text-muted);margin-top:4px">🔑 ' +
            esc(prop.kws) + '</div>';
        }
        html += '</div>';
        html += '<div style="font-size:11px;color:var(--text-muted);white-space:nowrap;flex-shrink:0">' +
          fecha + '</div>';
        html += '</div>';

        // Mensaje de error si la generación falló
        if (tieneError) {
          html += '<div style="margin-bottom:10px;padding:8px 12px;background:rgba(239,68,68,.07);' +
            'border:1px solid rgba(239,68,68,.2);border-radius:6px;font-size:12px;color:#dc2626;' +
            'display:flex;align-items:center;gap:6px">' +
            '<span style="font-size:14px">🚫</span>' +
            '<span><strong>Misión fallida.</strong> ' + esc(prop.errorMotor) +
            ' — Inténtalo de nuevo.</span>' +
            '</div>';
        }

        // Spinner si está generando
        if (esGenerando) {
          html += '<div style="margin-bottom:10px;padding:8px 12px;background:rgba(37,99,235,.06);' +
            'border:1px solid rgba(37,99,235,.2);border-radius:6px;font-size:12px;' +
            'color:var(--primary);display:flex;align-items:center;gap:8px">' +
            '<span style="animation:spin 1s linear infinite;display:inline-block">⚙️</span>' +
            'Generando contenido, espera un momento…' +
            '</div>';
        }

        // Botones de acción
        html += '<div style="display:flex;gap:8px">';
        if (!esGenerando) {
          html += '<button class="btn btn-primary btn-sm" onclick="activarPropuesta(' + idx + ')" ' +
            'style="font-size:12px">' +
            (tieneError ? '🔄 Reintentar' : '✨ Generar este artículo') +
            '</button>';
        }
        html += '<button class="btn btn-ghost btn-sm" onclick="descartarPropuesta(' + idx + ')" ' +
          'style="font-size:12px;color:var(--danger)"' +
          (esGenerando ? ' disabled style="font-size:12px;color:var(--danger);opacity:.4"' : '') +
          '>✕ Descartar</button>';
        html += '</div>';

        html += '</div>';
      });

      html += '</div>';
      cola.innerHTML = html;
    }

    function activarPropuesta(idx) {
      var p = getPropuestas();
      var prop = p.cola[idx];
      if (!prop) return;

      var tipoSel = document.getElementById('gen-tipo');
      if (tipoSel) {
        for (var i = 0; i < tipoSel.options.length; i++) {
          if (tipoSel.options[i].value === prop.tipo) {
            tipoSel.selectedIndex = i;
            break;
          }
        }
      }
      var temaEl = document.getElementById('gen-tema');
      var kwsEl = document.getElementById('gen-kws');
      if (temaEl) temaEl.value = prop.tema;
      if (kwsEl) kwsEl.value = prop.kws;

      _brechaActiva = {
        id: prop.brechaId,
        label: prop.brechaLabel,
        gap: prop.gap
      };
      var badge = document.getElementById('brecha-activa-badge');
      var badgeNombre = document.getElementById('brecha-activa-nombre');
      if (badge) badge.style.display = 'flex';
      if (badgeNombre) badgeNombre.textContent = prop.brechaLabel;

      // Guardar el id único de la propuesta (no el índice, que puede cambiar)
      _propMotorId = prop.id;
      p.cola[idx].estado = 'generando';
      p.cola[idx].errorMotor = null; // limpiar error previo si lo había
      save();
      renderColaPropostas();
      actualizarStatusLabel();

      var form = document.querySelector('#screen-5 .card:nth-of-type(3)');
      if (form) form.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });

      toast('🎯', 'Propuesta activada — generando contenido...');
      generarTodo();
    }

    function descartarPropuesta(idx) {
      var p = getPropuestas();
      p.cola.splice(idx, 1);
      save();
      renderColaPropostas();
      actualizarStatusLabel();
    }

    var _brechaActiva = null;
    var _modoRegeneracion = null;
    var _notasRRSS = '';
    var _propMotorId = null; // id único de la propuesta del motor en generación

    function renderBrechasGenerador() {
      var wrap = document.getElementById('brechas-generador-wrap');
      var pills = document.getElementById('brechas-pills');
      var score0 = document.getElementById('brechas-score-label');

      if (!wrap || !pills) return;

      var planActivo = S.historialPlanes && S.historialPlanes.length > 0 ? S.historialPlanes[0].plan : null;
      if (!planActivo || !planActivo.acciones) {
        wrap.style.display = 'none';
        return;
      }

      var tareasContenido = planActivo.acciones.filter(a => a.requiere_articulo === true);
      if (tareasContenido.length === 0) {
        wrap.style.display = 'none';
        return;
      }

      var miScore = score(0);
      score0.textContent = 'Tu score actual: ' + miScore + '/100';

      pills.innerHTML = tareasContenido.map(function(t, i) {
        var idTarea = 'tarea_' + i;
        var activa = _brechaActiva && _brechaActiva.id === idTarea;
        var urgencia = t.prioridad === 1 ? '#ef4444' : t.prioridad === 2 ? '#f59e0b' : '#3b82f6';
        return '<button onclick="seleccionarBrecha(\'' + idTarea + '\',\'' + esc(t.titulo) + '\',' + (t.puntos_potenciales || 0) + ', \'' + esc(t.tipo_sugerido || '') + '\', \'' + esc(t.tema_sugerido || '') + '\', \'' + esc(t.kws_sugeridas || '') + '\')" ' +
          'style="padding:6px 13px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;' +
          'border:1px solid ' + (activa ? urgencia : 'rgba(0,0,0,.12)') + ';' +
          'background:' + (activa ? urgencia : 'var(--bg-body)') + ';' +
          'color:' + (activa ? '#fff' : urgencia) + ';' +
          'transition:all .15s">' +
          t.titulo + ' <span style="opacity:.75;font-weight:400">+' + (t.puntos_potenciales || 0) + ' pts</span>' +
          '</button>';
      }).join('');

      const wrapTitle = wrap.querySelector('.card > div > div > span:first-child');
      const wrapDesc = wrap.querySelector('.card > div > div > span:nth-child(2)');
      if (wrapTitle) wrapTitle.textContent = '🎯 Tareas de Contenido (Plan de Acción)';
      if (wrapDesc) wrapDesc.textContent = 'Haz clic en una tarea para rellenar el generador con la sugerencia de la IA';

      wrap.style.display = 'block';
    }

    function seleccionarBrecha(id, label, gap, tipoSugerido, temaSugerido, kwsSugeridas) {
      _brechaActiva = {
        id: id,
        label: label,
        gap: gap
      };

      var tipoSel = document.getElementById('gen-tipo');
      if (tipoSel && tipoSugerido) {
        for (var i = 0; i < tipoSel.options.length; i++) {
          if (tipoSel.options[i].value === tipoSugerido) {
            tipoSel.selectedIndex = i;
            break;
          }
        }
      }
      var temaEl = document.getElementById('gen-tema');
      var kwsEl = document.getElementById('gen-kws');
      if (temaEl) temaEl.value = temaSugerido || label;
      if (kwsEl) kwsEl.value = kwsSugeridas || '';

      var badge = document.getElementById('brecha-activa-badge');
      var badgeNombre = document.getElementById('brecha-activa-nombre');
      if (badge) badge.style.display = 'flex';
      if (badgeNombre) badgeNombre.textContent = label + ' (+' + gap + ' pts potenciales)';

      renderBrechasGenerador();
      toast('🎯', 'Tarea seleccionada en el generador');
    }

    function limpiarBrechaActiva() {
      _brechaActiva = null;
      var badge = document.getElementById('brecha-activa-badge');
      if (badge) badge.style.display = 'none';
      renderBrechasGenerador();
    }

    /* ─────────────────────────────────────────────────────────
       GENERADOR DE CONTENIDO (MARKDOWN PURO)
       ───────────────────────────────────────────────────────── */
    function crearSlug(texto) {
      return texto.toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '')
        .slice(0, 60);
    }

    // Genera UTM con los 5 parámetros estándar OBLIGATORIOS
    function generarUTM(source, medium, campaign, term, content) {
      const domain = S.config.myCompany.domain || 'miempresa.com';
      const baseUrl = domain.startsWith('http') ? domain : 'https://' + domain;
      const cleanUrl = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
      return cleanUrl +
        '?utm_source=' + encodeURIComponent(source) +
        '&utm_medium=' + encodeURIComponent(medium) +
        '&utm_campaign=' + encodeURIComponent(campaign) +
        '&utm_term=' + encodeURIComponent(term) +
        '&utm_content=' + encodeURIComponent(content);
    }

    // ── Función auxiliar común: valida formulario y prepara contexto ──
    function _getGenParams() {
      const tipo = document.getElementById('gen-tipo').value;
      const tema = document.getElementById('gen-tema').value.trim();
      const kws = document.getElementById('gen-kws').value;
      const tono = document.getElementById('gen-tono').value;
      const comp = document.getElementById('gen-competidor').value;
      if (!tema) {
        toast('⚠️', 'Por favor, introduce un tema principal.', 'warn');
        return null;
      }
      const empresaName = S.config.myCompany.name || 'Bluease';
      const sector = S.config.myCompany.sector || 'sostenibilidad en eventos';
      const slugArticulo = crearSlug(tema);
      const slugKws = kws ? crearSlug(kws.split(',')[0].trim()) : crearSlug(sector);
      const linkLinkedIn = generarUTM('linkedin', 'social', 'contenido_geo_blog', slugKws, slugArticulo);
      const linkInstagram = generarUTM('instagram', 'social', 'contenido_geo_blog', slugKws, slugArticulo);
      var contextBrecha = '';
      if (typeof _brechaActiva !== 'undefined' && _brechaActiva) {
        contextBrecha = '\nCONTEXTO DE TAREA DEL PLAN A CUMPLIR:\n' +
          '  - Score GEO actual: ' + score(0) + '/100.\n' +
          '  - Tarea que este artículo debe cumplir: "' + _brechaActiva.label + '".\n';
      }
      return {
        tipo,
        tema,
        kws,
        tono,
        comp,
        empresaName,
        sector,
        linkLinkedIn,
        linkInstagram,
        contextBrecha,
        slugArticulo
      };
    }

    // ── Función auxiliar: inicializar UI de loading ──
    function _iniciarLoading(label) {
      document.getElementById('gen-resultado').style.display = 'none';
      const loadingDiv = document.getElementById('gen-loading');
      loadingDiv.style.display = 'flex';
      loadingDiv.querySelector('h3').textContent = label || 'Redactando contenido GEO...';
      const botonesCopiar = document.getElementById('botones-copiar');
      if (botonesCopiar) botonesCopiar.style.display = 'none';
      const progressFill = loadingDiv.querySelector('.prog-fill');
      const progressLbl = loadingDiv.querySelector('.prog-lbl');
      progressFill.style.width = '0%';
      progressLbl.textContent = '0%';
      let pct = 0;
      const intv = setInterval(function() {
        if (pct < 92) {
          pct += Math.random() * 4 + 1;
          if (pct > 92) pct = 92;
        } else if (pct < 98) {
          pct += 0.12;
        }
        progressFill.style.width = Math.min(pct, 98) + '%';
        progressLbl.textContent = Math.round(Math.min(pct, 98)) + '%';
      }, 500);
      return {
        loadingDiv,
        progressFill,
        progressLbl,
        intv
      };
    }

    // ── Función auxiliar: finalizar con éxito ──
    function _finalizarLoading(loadingDiv, progressFill, progressLbl, intv) {
      clearInterval(intv);
      progressFill.style.width = '100%';
      progressLbl.textContent = '100%';

      // Eliminar propuesta del motor exactamente igual que descartarPropuesta:
      // splice → save() → renderColaPropostas() → actualizarStatusLabel()
      if (_propMotorId !== null && S.propuestas && S.propuestas.cola) {
        var p = getPropuestas();
        var motorIdx = p.cola.findIndex(function(x) {
          return x.id === _propMotorId;
        });
        if (motorIdx >= 0) {
          p.cola.splice(motorIdx, 1);
          save();
          renderColaPropostas();
          actualizarStatusLabel();
        }
        _propMotorId = null;
      }

      setTimeout(function() {
        loadingDiv.style.display = 'none';
        document.getElementById('gen-resultado').style.display = 'block';
        const botonesCopiar = document.getElementById('botones-copiar');
        if (botonesCopiar) botonesCopiar.style.display = 'flex';

        var barra = document.getElementById('barra-accion-gen');
        var label = document.getElementById('gen-accion-label');
        var btnAp = document.getElementById('btn-aprobar-gen');
        var btnRe = document.getElementById('btn-rechazar-gen');
        if (barra) {
          barra.style.display = 'flex';
          if (label) label.textContent = '';
          if (btnAp) {
            btnAp.disabled = false;
            btnAp.style.opacity = '1';
          }
          if (btnRe) {
            btnRe.disabled = false;
            btnRe.style.opacity = '1';
          }
        }

        toast('✓', 'Generado con éxito', 'success');
      }, 400);
    }

    // ── Genera blog y después RRSS en secuencia con un solo click ──
    // ── RNF-07: Validador de calidad mínima del blog ───────────────
    function toggleTooltipEst() {
      var t = document.getElementById('tooltip-estructuras');
      if (!t) return;
      var visible = t.style.display !== 'none';
      t.style.display = visible ? 'none' : 'block';
      if (!visible) {
        // Cerrar al hacer clic fuera
        setTimeout(function() {
          document.addEventListener('click', function cerrar(e) {
            if (!t.contains(e.target) && e.target.id !== 'btn-tooltip-est') {
              t.style.display = 'none';
              document.removeEventListener('click', cerrar);
            }
          });
        }, 10);
      }
    }

    function toggleInfoBD() {
      var el = document.getElementById('info-bd');
      if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function toggleInfoRest() {
      var el = document.getElementById('info-rest');
      if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function guardarBD() {
      var ta = document.getElementById('bd-bluease');
      var cnt = document.getElementById('bd-chars');
      if (ta) localStorage.setItem('bluease_geo_bd', ta.value);
      if (cnt && ta) cnt.textContent = ta.value.length + ' caracteres';
    }

    function guardarRest() {
      var ta = document.getElementById('rest-bluease');
      var cnt = document.getElementById('rest-chars');
      if (ta) localStorage.setItem('bluease_geo_rest', ta.value);
      if (cnt && ta) cnt.textContent = ta.value.length + ' caracteres';
    }

    function cargarBDyRest() {
      var bd = localStorage.getItem('bluease_geo_bd') || '';
      var rest = localStorage.getItem('bluease_geo_rest') || '';
      var taBd = document.getElementById('bd-bluease');
      var taRest = document.getElementById('rest-bluease');
      if (taBd) {
        taBd.value = bd;
        document.getElementById('bd-chars').textContent = bd.length + ' caracteres';
      }
      if (taRest) {
        taRest.value = rest;
        document.getElementById('rest-chars').textContent = rest.length + ' caracteres';
      }
    }

    // Detecta qué estructura usó Claude analizando patrones del markdown generado
    function detectarEstructura(markdown) {
      var txt = markdown || '';
      var tieneTldr = /\*\*TL;DR/i.test(txt);
      var tieneTabla = /\|.+\|.+\|/.test(txt);
      var tieneNumerada = /^\d+\.\s+\*\*/m.test(txt);
      var tieneCita = /^> .{20,}/m.test(txt);
      var tieneViñetas = (txt.match(/^[-*] .+/gm) || []).length;
      var tieneH2 = (txt.match(/^## .+/gm) || []).length;
      var parrafos = txt.split(/\n{2,}/).filter(function(p) {
        return !p.startsWith('#') && !p.startsWith('|') && !p.startsWith('>') &&
          !p.startsWith('-') && !p.startsWith('*') && p.trim().length > 30;
      });
      var maxParrafo = parrafos.reduce(function(max, p) {
        return Math.max(max, p.split(/\s+/).length);
      }, 0);

      // E5: lista numerada como eje principal
      if (tieneNumerada && tieneViñetas < 5 && !tieneTabla) return {
        num: 5,
        nombre: 'Lista + acción',
        color: '#8b5cf6'
      };
      // E2: texto narrativo con cita destacada, sin tabla ni viñetas
      if (tieneCita && tieneViñetas < 4 && !tieneTabla) return {
        num: 2,
        nombre: 'Narrativa + autoridad',
        color: '#0ea5e9'
      };
      // E3: muchas viñetas, sin TL;DR, tabla pequeña o sin tabla
      if (tieneViñetas >= 10 && !tieneTldr && tieneH2 >= 3) return {
        num: 3,
        nombre: 'Escaneable + visual',
        color: '#10b981'
      };
      // E4: párrafos largos, pocos elementos visuales
      if (maxParrafo > 150 && tieneViñetas < 6 && !tieneTabla) return {
        num: 4,
        nombre: 'Profundidad + reflexión',
        color: '#f59e0b'
      };
      // E1: tabla + viñetas + párrafos medianos (default para datos)
      return {
        num: 1,
        nombre: 'Datos + guía accionable',
        color: '#007AEB'
      };
    }

    function validarCalidadBlog(markdown, tipo) {
      var errores = [];
      var avisos = [];
      var texto = markdown || '';
      var parrafos = texto.split(/\n{2,}/).filter(function(p) {
        return p.trim().length > 20;
      });

      // ── Estructura básica ──────────────────────────────────────────
      var h1 = (texto.match(/^# .+/gm) || []).length;
      var h2 = (texto.match(/^## .+/gm) || []).length;
      if (h1 < 1) errores.push('Falta el H1 principal');
      if (h2 < 2) errores.push('Necesita al menos 2 secciones H2 (tiene ' + h2 + ')');

      // TL;DR obligatorio en todas las estructuras
      var tieneTldr = /\*\*TL;DR/i.test(texto) || /^TL;DR/im.test(texto);
      if (!tieneTldr) errores.push('Falta el bloque TL;DR — es obligatorio en todas las estructuras. Añade 3 viñetas en negrita con datos clave justo después del H1.');

      // Lista o tabla
      var tieneLista = /^[-*] .+/m.test(texto);
      var tieneTabla = /\|.+\|.+\|/m.test(texto);
      if (!tieneLista && !tieneTabla) errores.push('Falta una lista o tabla');

      // FAQ — validación reforzada: debe existir el H2 de sección FAQ y el número mínimo de preguntas
      var tieneFaqH2 = /^## .*(?:faq|preguntas frecuentes)/im.test(texto);
      var tieneFaqPreguntas = (texto.match(/^\*\*¿[^*\n]+\?\*\*/gm) || []).length;
      if (!tieneFaqH2) {
        errores.push('Falta el H2 de sección FAQ. Debe existir "## Preguntas Frecuentes (FAQ)" antes del CTA.');
      } else {
        var minFaqPorTipo = tipoNorm === 'faq avanzada' ? 8 :
          tipoNorm === 'guia operativa' ? 5 :
          tipoNorm === 'comparativo analitico' ? 5 :
          4;
        if (tieneFaqPreguntas < minFaqPorTipo) {
          avisos.push('FAQ con ' + tieneFaqPreguntas + ' pregunta(s) en formato **¿...?** — mínimo para "' + tipoNorm + '": ' + minFaqPorTipo + '.');
        }
      }

      // CTA final
      var final = texto.slice(-300).toLowerCase();
      var tieneCta = /contac|demo|prue|empiez|descubr|solicita|bluease|saber más|más información/i.test(final);
      if (!tieneCta) errores.push('Falta CTA al final');

      // ── Longitud por tipo — rangos GEO exactos ────────────────────
      var palabras = texto.trim().split(/\s+/).length;
      var rangos = {
        'faq avanzada': {
          min: 600,
          max: 900,
          parrafoMax: 120
        },
        'glosario tecnico': {
          min: 500,
          max: 800,
          parrafoMax: 80
        },
        'guia operativa': {
          min: 1200,
          max: 1800,
          parrafoMax: 150
        },
        'caso de exito': {
          min: 1500,
          max: 2200,
          parrafoMax: 180
        },
        'comparativo analitico': {
          min: 1400,
          max: 2000,
          parrafoMax: 180
        }
      };
      var tipoNorm = (tipo || '').toLowerCase().trim();
      var rangoActual = rangos[tipoNorm] || {
        min: 800,
        max: 1800,
        parrafoMax: 180
      };
      var minPalabras = rangoActual.min;
      var maxPalabras = rangoActual.max;
      var maxParrafo = rangoActual.parrafoMax;
      if (palabras < minPalabras) errores.push('Artículo corto (' + palabras + ' palabras, mínimo ' + minPalabras + ' para tipo "' + tipoNorm + '")');
      if (palabras > maxPalabras) errores.push('Artículo largo (' + palabras + ' palabras, máximo ' + maxPalabras + ' para tipo "' + tipoNorm + '")');

      // ── Criterios GEO: chunks y densidad de respuestas ────────────

      // 1. Answer Block inicial (40-80 palabras tras el H1)
      // Verificar también que el H1 no sea demasiado largo (máx 12 palabras para GEO)
      var h1Texto = (texto.match(/^# (.+)/m) || ['', ''])[1].trim();
      var palabrasH1 = h1Texto ? h1Texto.split(/\s+/).length : 0;
      if (palabrasH1 > 12) {
        avisos.push('H1 demasiado largo (' + palabrasH1 + ' palabras). Para GEO lo óptimo es 8-12 palabras con la keyword principal al inicio.');
      }
      var primerParrafo = '';
      for (var i = 0; i < parrafos.length; i++) {
        var p = parrafos[i].trim();
        if (!p.startsWith('#') && !p.startsWith('|') && !p.startsWith('-') && !p.startsWith('*') && p.length > 30) {
          primerParrafo = p;
          break;
        }
      }
      var palabrasPrimerParrafo = primerParrafo ? primerParrafo.split(/\s+/).length : 0;
      if (palabrasPrimerParrafo < 30) {
        avisos.push('Answer Block inicial demasiado corto (' + palabrasPrimerParrafo + ' palabras, recomendado 40-80)');
      }
      if (palabrasPrimerParrafo > 100) {
        avisos.push('Answer Block inicial demasiado largo (' + palabrasPrimerParrafo + ' palabras, máximo recomendado 80)');
      }

      // 2. Párrafos de desarrollo — límite según tipo
      var parrafosMuyLargos = parrafos.filter(function(p) {
        return !p.startsWith('#') && !p.startsWith('|') && !p.startsWith('-') && !p.startsWith('*') &&
          p.split(/\s+/).length > maxParrafo;
      });
      if (parrafosMuyLargos.length > 0) {
        avisos.push(parrafosMuyLargos.length + ' párrafo(s) superan las ' + maxParrafo + ' palabras (límite para "' + tipoNorm + '")');
      }

      // 3. Densidad de respuestas directas (≥6 por cada 1000 palabras)
      //    Proxy: contar frases cortas (≤25 palabras) con verbo principal — bullets, definiciones, datos
      var frasesCortasRich = (texto.match(/^[-*].{10,120}$/gm) || []).length +
        (texto.match(/\*\*[^*]{5,60}\*\*/g) || []).length +
        (texto.match(/\d+[%€$]|\d+\s*(tCO2|kg|km|€|%)/g) || []).length;
      var densidadRespuestas = Math.round((frasesCortasRich / palabras) * 1000);
      if (densidadRespuestas < 6 && palabras > 400) {
        avisos.push('Densidad de respuestas baja (' + densidadRespuestas + '/1000 palabras, recomendado ≥6) — añade más viñetas, datos o definiciones directas');
      }

      // 4. H2s formulados como preguntas (favorece extracción GEO)
      var h2s = texto.match(/^## .+/gm) || [];
      var h2Preguntas = h2s.filter(function(h) {
        return /¿|\?/.test(h);
      }).length;
      if (h2s.length > 0 && h2Preguntas === 0) {
        avisos.push('Ningún H2 está formulado como pregunta — considera convertir al menos 1-2 en preguntas reales de usuario');
      }

      // 5. Coherencia título-cuerpo: si el H1 menciona "N preguntas/items/términos", contar y verificar
      var h1Linea = (texto.match(/^# .+/m) || [''])[0];
      var numH1Match = h1Linea.match(/(\d+)\s+(preguntas?|términos?|estrategias?|pasos?|claves?|consejos?|errores?|tips?|métodos?|formas?|maneras?|razones?|beneficios?|casos?|ejemplos?|herramientas?|técnicas?|tendencias?|principios?)/i);
      if (numH1Match) {
        var nPrometido = parseInt(numH1Match[1], 10);
        var tipoItem = numH1Match[2].toLowerCase();

        // Contar elementos del cuerpo según el tipo
        var nReal = 0;
        if (tipoItem.indexOf('pregunta') === 0) {
          // Cuenta H2s con ¿? + items FAQ que sean preguntas (líneas en negrita con ¿? o terminando en ?)
          var h2PregBody = (texto.match(/^## .*[¿?].*$/gm) || []).length;
          var faqPregBody = (texto.match(/^\*\*[^*\n]*[¿?][^*\n]*\*\*/gm) || []).length;
          var faqAltBody = (texto.match(/^### [^\n]*[¿?]/gm) || []).length;
          nReal = h2PregBody + faqPregBody + faqAltBody;
        } else {
          // Para términos/items genéricos: contar H2 + H3 + items numerados + items con título en negrita
          var h2Body = (texto.match(/^## /gm) || []).length;
          var h3Body = (texto.match(/^### /gm) || []).length;
          var listaNumBody = (texto.match(/^\d+\.\s+\*\*/gm) || []).length;
          nReal = Math.max(h2Body + h3Body, listaNumBody);
        }

        if (nReal !== nPrometido && nReal > 0) {
          var diferencia = Math.abs(nReal - nPrometido);
          // Si la diferencia es grande (>20%), es un error que fuerza reintento
          if (diferencia / nPrometido > 0.2) {
            errores.push('El H1 promete ' + nPrometido + ' ' + tipoItem + ' pero el cuerpo solo contiene ' + nReal + '. Añade los que faltan o ajusta el número del título.');
          } else {
            avisos.push('Discrepancia menor: H1 dice ' + nPrometido + ' ' + tipoItem + ' y el cuerpo tiene ' + nReal);
          }
        }
      }

      // 6. Coherencia cifras TL;DR ↔ cuerpo
      // Extrae el bloque TL;DR (entre el primer --- o ** y el primer H2)
      var tldrMatch = texto.match(/\*\*TL;DR[:\s\S]*?\*\*[\s\S]*?(?=^## )/m) ||
        texto.match(/TL;DR[\s\S]*?(?=^## )/m);
      if (tldrMatch) {
        var tldrBloque = tldrMatch[0];
        // Extraer todos los números del TL;DR: porcentajes (42%), cifras con € o unidades, años
        var cifrasTldr = tldrBloque.match(/\d+(?:[.,]\d+)?(?:\s*%|\s*€|\s*tCO2|\s*kg|\s*km|\s*millones|\s*años?|\s*meses?)/gi) || [];
        // También extraer números simples > 2 cifras que no sean años (para evitar falsos positivos con 2024, 2025)
        var numSimpTldr = tldrBloque.match(/\b([3-9]\d|[1-9]\d{2,})\b/g) || [];
        var todasCifras = cifrasTldr.concat(numSimpTldr.filter(function(n) {
          var num = parseInt(n, 10);
          return num > 9 && (num < 2000 || num > 2030); // excluir años
        }));
        // Eliminar duplicados
        var cifrasUnicas = todasCifras.filter(function(v, i, a) {
          return a.indexOf(v) === i;
        });

        // Para cada cifra del TL;DR, verificar que aparece en el cuerpo (fuera del TL;DR)
        var cuerpo = texto.replace(tldrMatch[0], '');
        var cifrasNoRespaldadas = cifrasUnicas.filter(function(cifra) {
          // Normalizar: quitar espacios internos para comparar
          var cifraBase = cifra.replace(/\s+/g, '').replace(',', '.').toLowerCase();
          // Buscar la cifra en el cuerpo con cierta flexibilidad (±0 exacta o con unidad)
          var regex = new RegExp(cifraBase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i');
          var sinEspacios = new RegExp(cifra.replace(/\s+/g, '\\s*').replace(/[.*+?^${}()|[\]\\%€]/g, '\\$&'), 'i');
          return !regex.test(cuerpo) && !sinEspacios.test(cuerpo);
        });

        if (cifrasNoRespaldadas.length > 0) {
          errores.push('Cifras del TL;DR sin respaldo en el cuerpo: ' + cifrasNoRespaldadas.slice(0, 3).join(', ') + '. El mismo número debe aparecer en el cuerpo con el mismo valor exacto.');
        }
      }

      // Los avisos no bloquean — solo los errores detienen el reintento
      return {
        ok: errores.length === 0,
        errores: errores,
        avisos: avisos,
        palabras: palabras,
        minPalabras: minPalabras,
        maxPalabras: maxPalabras,
        densidadRespuestas: densidadRespuestas
      };
    }

    // Aprobar o rechazar desde la pantalla de generador — crea/actualiza la propuesta en la cola
    function accionarDesdeGenerador(estado) {
      if (!S.contenido || !S.contenido.generado) {
        toast('⚠️', 'No hay contenido generado para aprobar', 'warn');
        return;
      }

      var tema = (document.getElementById('gen-tema') || {}).value || 'Artículo generado';
      var tipo = (document.getElementById('gen-tipo') || {}).value || 'guia operativa';
      var kws = (document.getElementById('gen-kws') || {}).value || '';

      if (!S.propuestas) S.propuestas = {
        config: {
          tamanoLote: 2,
          frecuenciaDias: 7,
          ultimaGeneracion: null
        },
        cola: []
      };
      if (!S.propuestas.cola) S.propuestas.cola = [];

      var ahora = Date.now();

      // Buscar propuesta existente por brecha activa O por tema — sin importar si hay _brechaActiva
      var idx = S.propuestas.cola.findIndex(function(p) {
        if (p.estado === 'descartado') return false;
        if (_brechaActiva && (p.brechaLabel === _brechaActiva.label)) return true;
        return p.tema === tema;
      });

      var contenidoProp = {
        blog: S.contenido.blog || '',
        linkedin: S.contenido.linkedin || '',
        instagram: S.contenido.instagram || '',
        generado: true,
        _utmLinkedIn: S.contenido._utmLinkedIn || '',
        _utmInstagram: S.contenido._utmInstagram || ''
      };

      if (idx >= 0) {
        S.propuestas.cola[idx].estado = estado;
        S.propuestas.cola[idx].fechaEstado = ahora;
        S.propuestas.cola[idx].contenido = contenidoProp;
      } else {
        // Crear propuesta nueva siempre que no exista
        S.propuestas.cola.unshift({
          id: ahora,
          origen: 'generador',
          brechaId: _brechaActiva ? _brechaActiva.id : 'generador',
          brechaLabel: _brechaActiva ? _brechaActiva.label : tema,
          tipo: tipo,
          tema: tema,
          kws: kws,
          gap: _brechaActiva ? (_brechaActiva.gap || 0) : 0,
          estado: estado,
          fechaCreacion: ahora,
          fechaEstado: ahora,
          contenido: contenidoProp
        });
      }

      save();

      // Re-renderizar bandeja y biblioteca para reflejar el cambio inmediatamente
      actualizarBadgeBandeja();
      actualizarBadgeBiblioteca();
      if (typeof renderBandeja === 'function') renderBandeja(_filtroActivo);
      if (typeof renderBiblioteca === 'function') renderBiblioteca(_filtroBiblioteca);

      // Feedback visual
      var label = document.getElementById('gen-accion-label');
      var btnAp = document.getElementById('btn-aprobar-gen');
      var btnRe = document.getElementById('btn-rechazar-gen');
      if (btnAp) {
        btnAp.disabled = true;
        btnAp.style.opacity = '.5';
      }
      if (btnRe) {
        btnRe.disabled = true;
        btnRe.style.opacity = '.5';
      }

      if (estado === 'edicion') {
        if (label) label.innerHTML = '<span style="color:#6366f1;font-weight:600">🔖 Enviado a revisión — ve a Bandeja → Por aprobar</span>';
        toast('🔖', 'Enviado a revisión · ve a Bandeja → Por aprobar');
      } else if (estado === 'aprobado') {
        if (label) label.innerHTML = '<span style="color:#059669;font-weight:600">✅ Aprobado — aparece en Biblioteca</span>';
        toast('✅', 'Aprobado · aparece en Biblioteca');
      } else {
        if (label) label.innerHTML = '<span style="color:#dc2626;font-weight:600">❌ Rechazado</span>';
        toast('❌', 'Rechazado');
      }
    }

    async function generarTodo() {
      await generarBlog();
      // Solo continúa con RRSS si el blog se generó correctamente
      if (S.contenido && S.contenido.blog) {
        await generarRRSS();
      }
    }

    // ── BOTÓN 1: Generar solo el Blog ──────────────────────────────
    async function generarBlog() {
      const params = _getGenParams();
      if (!params) return;
      const {
        tipo: tipoRaw,
        tema,
        kws,
        tono,
        comp,
        empresaName,
        sector,
        contextBrecha
      } = params;
      const tipo = (tipoRaw || '').toLowerCase().trim();
      const {
        loadingDiv,
        progressFill,
        progressLbl,
        intv
      } = _iniciarLoading('📝 Generando artículo...');

      // RNF-06: mensajes de estado dinámicos
      var mensajesEstado = [
        '📝 Analizando el tema y la competencia...',
        '✍️ Redactando estructura y contenido...',
        '📊 Añadiendo datos, tablas y FAQ...',
        '🔍 Verificando calidad del artículo...',
      ];
      var msgIdx = 0;
      var msgIntv = setInterval(function() {
        if (msgIdx < mensajesEstado.length) {
          loadingDiv.querySelector('h3').textContent = mensajesEstado[msgIdx++];
        }
      }, 7000);

      // Contexto de regeneración
      var contextoRegeneracion = '';
      if (_modoRegeneracion && _modoRegeneracion.temaOriginal === tema) {
        contextoRegeneracion = '\nATENCIÓN — VERSIÓN ALTERNATIVA:\n' +
          'Ya existe una versión anterior de este artículo. DEBES generar una versión ' +
          'completamente diferente en cuanto a:\n' +
          '- Estructura y orden de las secciones\n' +
          '- Caso práctico o ejemplo principal (usa uno diferente)\n' +
          '- Ángulo narrativo (si antes fue técnico, ahora más estratégico, o viceversa)\n' +
          '- Datos y estadísticas destacados\n' +
          ((_modoRegeneracion.extractoAnterior) ?
            'El inicio del artículo anterior era:\n"""\n' + _modoRegeneracion.extractoAnterior + '\n"""\nNO repitas ese enfoque.\n' : '') +
          (_modoRegeneracion.notasRevision ?
            '\nINSTRUCCIONES DE REVISIÓN — PRIORIDAD MÁXIMA:\n' +
            'La responsable de revisión ha añadido estas notas que DEBES aplicar obligatoriamente:\n"""\n' +
            _modoRegeneracion.notasRevision + '\n"""\n' +
            'Estas instrucciones tienen prioridad sobre cualquier otra directriz.\n' : '');
        _modoRegeneracion = null;
      }

      // RNF-08: tono y contexto Bluease siempre presente
      var contextoMarca =
        '\nCONTEXTO DE MARCA OBLIGATORIO — LEE CON ATENCIÓN:\n' +
        '- Empresa: ' + empresaName + ' — plataforma SaaS B2B de medición y certificación de sostenibilidad para eventos.\n' +
        '- POSICIONAMIENTO CRÍTICO — MUY IMPORTANTE:\n' +
        '  · ' + empresaName + ' NO organiza eventos. NO certifica eventos directamente. Es el SOFTWARE que los organizadores de eventos CONTRATAN para medir, gestionar y obtener certificaciones de sostenibilidad.\n' +
        '  · Los organizadores de eventos son los clientes de ' + empresaName + '. ' + empresaName + ' es la herramienta que ellos usan.\n' +
        '  · NUNCA escribas: "organizamos el evento X", "nuestro evento", "eventos que hemos certificado", "logramos reducir emisiones en el evento Y".\n' +
        '  · SÍ puedes escribir: "con ' + empresaName + ', los organizadores de eventos pueden...", "organizadores que usan ' + empresaName + ' han logrado...", "' + empresaName + ' permite calcular automáticamente..."\n' +
        '  · En los CTAs: "los organizadores utilizan ' + empresaName + ' para..." — nunca "nosotros certificamos..."\n' +
        '- Tono: ' + tono + '. Formal pero cercano, orientado a organizadores de eventos profesionales.\n' +
        '- Evita el lenguaje genérico "las empresas deben..." — usa "los organizadores de eventos, con ' + empresaName + ', pueden...".\n';

      // Base de datos Bluease: datos reales para respaldar el contenido
      var bdBluease = (localStorage.getItem('bluease_geo_bd') || '').trim();
      var restBluease = (localStorage.getItem('bluease_geo_rest') || '').trim();
      var contextoBD = bdBluease ? (
        '\nBASE DE DATOS BLUEASE — DATOS REALES (úsalos en el artículo cuando sean relevantes):\n' +
        bdBluease + '\n' +
        'IMPORTANTE: SOLO puedes mencionar los clientes y eventos que aparecen en esta base de datos. NO inventes nombres de eventos, festivales, congresos o clientes que no estén listados aquí. Si necesitas un ejemplo, usa los clientes reales listados o habla de forma genérica ("un congreso corporativo de 500 personas").\n'
      ) : '';
      var contextoRest = restBluease ? (
        '\nRESTRICCIONES ABSOLUTAS — PROHIBIDO MENCIONAR O AFIRMAR:\n' +
        'Las siguientes afirmaciones son incorrectas o no deben aparecer bajo ningún concepto:\n' +
        restBluease + '\n'
      ) : '';

      const promptBlog = `Eres un redactor SEO experto en GEO para "${empresaName}" en el sector "${sector}".

Redacta un artículo de tipo "${tipo}" sobre: "${tema}".
Keywords: ${kws || 'usa las más relevantes'}.
${comp ? `Supera a: "${comp}".` : ''}
${contextBrecha}${contextoRegeneracion}${contextoMarca}${contextoBD}${contextoRest}

EXTENSIÓN SEGÚN TIPO DE CONTENIDO — LÍMITE ESTRICTO:
${tipo === 'faq avanzada'          ? '- TIPO: FAQ Avanzada. PALABRAS: mínimo 600, MÁXIMO ABSOLUTO 900. Si alcanzas 900 palabras PARA. No añadas más secciones.'
: tipo === 'glosario tecnico'      ? '- TIPO: Glosario Técnico. PALABRAS: mínimo 500, MÁXIMO ABSOLUTO 800. Cada término máximo 80 palabras. Si alcanzas 800 PARA.'
: tipo === 'guia operativa'        ? '- TIPO: Guía Operativa. PALABRAS: mínimo 1.200, MÁXIMO ABSOLUTO 1.800. Párrafos máximo 150 palabras. Si alcanzas 1.800 PARA aunque queden puntos por desarrollar.'
: tipo === 'caso de exito'         ? '- TIPO: Caso de Éxito. PALABRAS: mínimo 1.500, MÁXIMO ABSOLUTO 2.200. Párrafos máximo 180 palabras. Si alcanzas 2.200 PARA.'
: tipo === 'comparativo analitico' ? '- TIPO: Comparativo Analítico. PALABRAS: mínimo 1.400, MÁXIMO ABSOLUTO 2.000. Párrafos máximo 180 palabras. Si alcanzas 2.000 PARA.'
: '- PALABRAS: mínimo 800, MÁXIMO ABSOLUTO 1.800. Si alcanzas 1.800 PARA.'}

INSTRUCCIÓN CRÍTICA DE LONGITUD — NO NEGOCIABLE:
Antes de escribir cada sección nueva, estima si vas a superar el máximo. Si lo vas a superar, acorta esa sección o conviértela en viñeta. Un artículo de ${tipo === 'faq avanzada' || tipo === 'glosario tecnico' ? '700' : tipo === 'guia operativa' ? '1.500' : tipo === 'caso de exito' ? '1.800' : '1.600'} palabras perfectamente estructurado tiene MEJOR rendimiento GEO que uno de ${tipo === 'faq avanzada' || tipo === 'glosario tecnico' ? '1.200' : '2.500'} palabras con relleno. La calidad GEO depende de la densidad por párrafo, no de la longitud total.

ESTRUCTURA DEL ARTÍCULO — ELIGE LA MÁS ADECUADA:
Tienes 5 estructuras posibles. Elige la que mejor encaje con el tipo de contenido y el tema. Si el tema es técnico y comparativo, prefiere la 1 o la 3. Si es más divulgativo o de opinión, prefiere la 2 o la 4. Si es una lista accionable, prefiere la 5. NO uses siempre la misma — varía según el contexto.

**TL;DR OBLIGATORIO EN TODAS LAS ESTRUCTURAS:**
Todas las estructuras deben incluir un bloque TL;DR justo después del H1, con exactamente 3 viñetas en negrita que resuman los datos más importantes del artículo. Formato exacto:

**TL;DR:**
- **[Dato clave 1]**: descripción breve con cifra concreta.
- **[Dato clave 2]**: descripción breve con cifra concreta.
- **[Dato clave 3]**: descripción breve con cifra concreta.

Las cifras del TL;DR DEBEN aparecer literalmente en el cuerpo del artículo.

**ESTRUCTURA 1 — Datos + guía accionable** (ideal para: guías operativas, casos de éxito con datos)
1. H1 (máx 12 palabras, keyword al inicio)
2. Elemento visual VARIABLE — elige uno según el contenido: (a) Tabla Markdown si hay datos comparativos, (b) Gráfico textual con barras █ si hay porcentajes/evolución, ej. "Reducción emisiones: ████████░░ 82%", (c) Bloque de cita destacada (>) si es narrativo. NO uses siempre tabla.
3. Párrafo de contexto (60-80 palabras)
4. Guiones / viñetas accionables con datos concretos
5. Párrafo largo de desarrollo (120-180 palabras)
6. CTA mencionando ${empresaName}

**ESTRUCTURA 2 — Narrativa + autoridad** (ideal para: artículos de opinión, tendencias, análisis de mercado)
1. H1 (máx 12 palabras)
2. Todo texto seguido en bloques de 100-150 palabras — sin listas, estilo periodístico
3. Cita destacada en bloque (20-40 palabras, en Markdown con >)
4. Texto corto de cierre (40-60 palabras)
5. CTA mencionando ${empresaName}

**ESTRUCTURA 3 — Escaneable + visual** (ideal para: glosarios, comparativos, recursos rápidos)
1. H1 (máx 12 palabras)
2. Bloque de viñetas resumen (3-5 puntos)
3. Más viñetas por sección H2
4. Imagen horizontal (prompt DALL-E al final)
5. Párrafo de profundización (80-120 palabras)
6. Elemento visual VARIABLE — elige: (a) Tabla pequeña (3-4 col, máx 5 filas) si hay comparativa clara, (b) Gráfico de barras ASCII si hay datos numéricos, (c) Omitir si el contenido no lo necesita
7. Conclusión breve (30-50 palabras)
8. SIN CTA — solo el cierre

**ESTRUCTURA 4 — Profundidad + reflexión** (ideal para: análisis técnicos, whitepapers ligeros, FAQ avanzadas)
1. H1 (máx 12 palabras)
2. Frase destacada en negrita como subtítulo (una sola línea, impactante)
3. Texto largo de desarrollo en varios párrafos H2/H3 (no listas — desarrollo argumental)
4. Mini conclusión accionable (50-70 palabras)
5. SIN CTA explícita — integrar mención a ${empresaName} en la conclusión

**ESTRUCTURA 5 — Lista + acción** (ideal para: top N, pasos, checklist, rankings)
1. H1 (máx 12 palabras, debe incluir el número de ítems)
2. Lista numerada como cuerpo principal — cada ítem con título en negrita + 2-3 líneas de desarrollo
3. Párrafo de contexto o dato clave tras la lista (60-80 palabras)
4. CTA mencionando ${empresaName}

REGLAS GEO aplicables a TODAS las estructuras:
- H1 máximo 12 palabras con keyword principal al inicio
- Answer Block de 40-80 palabras tras cada H2
- Párrafos máximo según tipo de contenido
- Al menos 1-2 H2 formulados como preguntas reales
- Coherencia estricta entre números del H1 y elementos del cuerpo
- Cita al menos UN marco de referencia (ISO 20121, GHG Protocol, GRI, etc.)

REGLAS GEO — ESTRUCTURA DE CHUNKS (CRÍTICO PARA CITACIÓN POR IAs):
- **H1 conciso y directo**: máximo 12 palabras. Pon la keyword principal al inicio. Las IAs citan títulos cortos con más frecuencia que títulos largos. MAL: "Impacto Financiero de la Logística Inversa en Eventos B2B mediante Reciclaje y Economía Circular (Estudio 2024)". BIEN: "Logística Inversa en Eventos B2B: Cómo Recuperar €35.000 por Evento".
- **Answer Block obligatorio**: el primer párrafo tras el H1 y el primer párrafo tras cada H2 deben tener entre 40-80 palabras y responder directamente a la intención de búsqueda. Estructura "Pirámide Invertida": la conclusión primero, luego el desarrollo.
- **Párrafos de desarrollo**: máximo 180 palabras por párrafo. Las IAs extraen chunks de este tamaño; los párrafos más largos se truncan o se ignoran.
- **Densidad de respuestas**: al menos 6 respuestas directas (1-3 frases) por cada 1.000 palabras. Usa viñetas, definiciones en negrita y datos estadísticos concretos para aumentar densidad.
- **H2s como preguntas**: al menos 1-2 títulos H2 deben estar formulados como preguntas reales que un usuario haría en ChatGPT o Google (ej. "¿Cuánto cuesta certificar un evento con ISO 20121?").
- **Citas de expertos**: si incluyes citas, que tengan entre 20-40 palabras — sustanciales pero extraíbles.

REGLAS DE COHERENCIA INTERNA — CRÍTICO, NO NEGOCIABLE:

**REGLA DE ORO: Si el H1 menciona un número (ej. "20 Preguntas", "10 estrategias", "28 términos", "5 pasos"), el cuerpo del artículo DEBE contener EXACTAMENTE ese número de elementos individualizables. No uno más, no uno menos.**

PROCESO OBLIGATORIO antes de generar el H1 final:
1. PRIMERO decide el número exacto N que vas a desarrollar (ej. 20 preguntas).
2. ESCRIBE LITERALMENTE en tu planificación interna las N entradas con su título corto antes de empezar a redactar.
3. Verifica que tienes N elementos planificados.
4. Solo entonces escribe el H1 con el número N.
5. Al redactar el cuerpo, cuenta cada elemento que vas añadiendo (1, 2, 3...) para asegurar que llegas exactamente a N.

CÓMO ALCANZAR EL NÚMERO PROMETIDO EN ARTÍCULOS DE TIPO "N preguntas/términos/items":
- Los elementos pueden distribuirse entre: secciones H2 principales (cada una cuenta como 1 elemento), bloque FAQ final (cada pregunta cuenta como 1 elemento), y sub-elementos dentro de secciones si están claramente numerados o titulados (ej. dentro de "¿Qué certificaciones existen?" puedes listar 4 certificaciones que cuentan como 4 elementos si están claramente diferenciadas con título propio).
- Si vas a hacer un artículo de "20 preguntas", reparte así: 6-8 preguntas como H2 principales con respuesta extensa + 12-14 preguntas en bloque FAQ final con respuesta concisa de 2-3 frases. Suma exacta = 20.
- Si vas a hacer un artículo de "28 términos", reparte así: 10-12 términos desarrollados con H3 propio + 16-18 términos en sección de "términos adicionales" con definición de 1-2 líneas cada uno. Suma exacta = 28.

VERIFICACIÓN FINAL OBLIGATORIA:
Antes de cerrar el artículo, cuenta mentalmente los elementos del cuerpo. Si el número no coincide con el del título, AJUSTA EL CUERPO (añade los que falten o quita los que sobren) — nunca el lector debe poder contar y encontrar discrepancia.

Si el TL;DR menciona un dato cuantitativo (ej. "3 fases", "reducción del 40%", "6 certificaciones"), ese dato DEBE aparecer respaldado en el cuerpo con el mismo número exacto.

El título debe describir exactamente lo que el artículo contiene. Si es una guía práctica, el título debe decir "guía práctica", no "diccionario" si no hay un glosario numerado.

REQUISITOS DE DENSIDAD INFORMATIVA (GEO):
- Incluye cifras y porcentajes reales: ej. "un festival de 10.000 asistentes genera una media de 2,3 kg de residuos per cápita".
- Menciona casos o eventos reales del sector cuando sea posible.
- Usa nombres propios: herramientas, normativas, organismos, certificaciones.
- Cada H2 debe aportar información nueva, no repetir lo anterior con otras palabras.
- **Resalta en negrita** los conceptos clave, términos técnicos y datos importantes.
- Sin coloquialismos ni spanglish. Usa siempre ¿? en preguntas.
- PROHIBIDO el uso de guiones largos o rayas de incisión (—). Usa comas para los incisos.

SECCIÓN FAQ OBLIGATORIA — NO NEGOCIABLE:
Todo artículo DEBE incluir una sección de Preguntas Frecuentes (FAQ) justo antes de la conclusión final o el CTA. Esta sección es OBLIGATORIA independientemente del tipo de contenido ("faq avanzada", "glosario tecnico", "guia operativa", "caso de exito", "comparativo analitico").

FORMATO EXACTO DE LA SECCIÓN FAQ:

## Preguntas Frecuentes (FAQ)

**¿[Pregunta real que haría el usuario en ChatGPT o Google]?**
Respuesta directa y concisa en 2-4 frases con dato concreto o cifra si es posible.

**¿[Segunda pregunta real]?**
Respuesta directa y concisa en 2-4 frases.

[...resto de preguntas...]

REGLAS DE LA SECCIÓN FAQ:
- Número de preguntas según tipo de contenido:
  · "faq avanzada": mínimo 8 preguntas, máximo 12. Son el núcleo del artículo, desarrolla cada respuesta en 3-5 frases con datos reales.
  · "glosario tecnico": mínimo 4 preguntas, máximo 6. Enfocadas en dudas terminológicas y de aplicación práctica.
  · "guia operativa": mínimo 5 preguntas, máximo 8. Enfocadas en dudas de implementación ("¿cuánto tiempo lleva?", "¿qué herramientas necesito?").
  · "caso de exito": mínimo 4 preguntas, máximo 6. Enfocadas en replicabilidad y resultados ("¿se puede aplicar en eventos pequeños?").
  · "comparativo analitico": mínimo 5 preguntas, máximo 7. Enfocadas en criterios de decisión y diferencias clave.
- Cada pregunta debe estar formulada con ¿? y empezar con un pronombre interrogativo (¿Cuál, ¿Cuánto, ¿Cómo, ¿Qué, ¿Por qué, ¿Cuándo, ¿Dónde).
- Las preguntas deben ser reales: las que un organizador de eventos o profesional del sector teclearía en ChatGPT, Google o Perplexity.
- Las respuestas son directas (Pirámide Invertida: conclusión primero, luego contexto).
- NO repitas información ya desarrollada en el cuerpo del artículo. La FAQ responde dudas complementarias, no resume lo anterior.
- Incluye al menos una pregunta sobre ${empresaName} en la FAQ (ej. "¿Cómo ayuda ${empresaName} a gestionar la sostenibilidad en eventos?").
- Formato SIEMPRE en Markdown: **¿Pregunta?** en negrita, seguida de párrafo de respuesta. Sin numeración, sin viñetas para las preguntas.

AL FINAL DEL ARTÍCULO añade obligatoriamente estas secciones con exactamente este formato:

---
**ALT_TEXT:** [descripción de la imagen en 10-12 palabras con keywords del artículo]

**PROMPT_BLOG:** Flat modern illustration, corporate SaaS style, horizontal 16:9 composition, clean and balanced layout, professional structured scene, premium SaaS blog illustration quality. Flat design with subtle depth, smooth soft gradients, minimal shadows, no realism, no heavy textures, no glossy effects. Soft even low-contrast lighting, modern vector illustration feel, clean polished finish, professional sustainability-tech aesthetic. Color palette: dominant soft mint blue inspired by #007AEB, white, black, soft greys. Accent colors used across signage, UI elements, interface indicators, technical equipment, screens: #FFC107 as main visible accent (15-20% visual weight), #02BE24 as secondary support accent. Simple clean background, soft mint-blue atmosphere, minimal visual noise, subtle event environment. Diverse professional people in modern simplified style, natural poses, calm expressions, coordinated modern workwear. No wind turbines, no trash bins, no exaggerated plants, no dramatic lighting, no neon, no cyberpunk. No text, no logos. Scene related to: [describe aquí la escena específica del artículo en 15-20 palabras en inglés, relacionada con el tema del artículo y el sector de eventos corporativos sostenibles]

**PROMPT_INSTAGRAM:** Flat modern illustration, corporate SaaS style, square 1:1 format, clean balanced composition, premium SaaS illustration quality. Flat design with subtle depth, smooth soft gradients, minimal shadows, no realism. Soft even lighting, modern vector feel, clean polished finish. Color palette: dominant soft mint blue inspired by #007AEB, white, soft greys. Accent colors #FFC107 (main, 15-20% visual weight) and #02BE24 (secondary) across UI elements, indicators, decorative highlights. Clean white background, minimal visual noise. No text overlay, no logos. Eye-catching and dynamic but elegant composition. Scene related to: [describe aquí la escena específica del artículo en 10-15 palabras en inglés, visualmente impactante para Instagram]

Responde SOLO con el artículo en Markdown. Sin JSON ni etiquetas extra.`;

      var contenidoBlog = null;
      var intentos = 0;
      var maxIntentos = 3;
      var validacion = null;
      var promptActual = promptBlog;

      try {
        while (intentos < maxIntentos) {
          intentos++;

          if (intentos > 1) {
            loadingDiv.querySelector('h3').textContent =
              '🔄 Reintento ' + (intentos - 1) + '/2 — corrigiendo: ' + validacion.errores[0] + '...';
            // Inyectar los errores en el prompt para que Claude los corrija — sin esto siempre falla igual
            promptActual = promptBlog +
              '\n\nCORRECCIÓN OBLIGATORIA (intento ' + intentos + '):\n' +
              'La versión anterior NO cumplió estos requisitos. DEBES corregirlos obligatoriamente:\n' +
              validacion.errores.map(function(e) {
                return '- ' + e;
              }).join('\n');
          }

          contenidoBlog = await llamarAnthropicDirecto(promptActual, 12000);

          validacion = validarCalidadBlog(contenidoBlog, tipo);
          if (validacion.ok) break;

          if (intentos < maxIntentos) {
            console.warn('[RNF-07] Calidad insuficiente (intento ' + intentos + '):', validacion.errores);
            toast('🔄', 'Corrigiendo: ' + validacion.errores[0], '', 3000);
          }
        }

        // Continúa con lo generado aunque haya avisos menores
        if (!validacion.ok) {
          console.warn('[RNF-07] No se alcanzó calidad óptima tras ' + maxIntentos + ' intentos. Errores:', validacion.errores);

          // Truncado de emergencia si supera el máximo en más de un 10%
          var rangosLocal = {
            'faq avanzada': 900,
            'glosario tecnico': 800,
            'guia operativa': 1800,
            'caso de exito': 2200,
            'comparativo analitico': 2000
          };
          var maxLocal = rangosLocal[tipo] || 1800;
          if (contenidoBlog && validacion.palabras > maxLocal * 1.1) {
            var bloques = contenidoBlog.split(/\n{2,}/);
            var acum = 0;
            var cortados = [];
            for (var pi = 0; pi < bloques.length; pi++) {
              var wp = bloques[pi].split(/\s+/).length;
              if (acum + wp > maxLocal) break;
              cortados.push(bloques[pi]);
              acum += wp;
            }
            if (cortados.length > 2) {
              contenidoBlog = cortados.join('\n\n');
              validacion = validarCalidadBlog(contenidoBlog, tipo);
              console.info('[TRUNCADO] Artículo recortado a ~' + acum + ' palabras');
            }
          }

          if (validacion.errores.length > 0) {
            toast('⚠️', 'Generado con avisos — revisa el artículo', 'warn', 5000);
          }
        }

        clearInterval(msgIntv);

        // Guardar en estado
        if (!S.contenido) S.contenido = {};
        S.contenido.blog = contenidoBlog;
        S.contenido.generado = false; // It's only fully generated after RRSS

        // RNF-09: registrar en historial (evitar repetición 3 meses)
        if (!S.historialTemas) S.historialTemas = [];
        if (!S.historialTemas.some(t => t.titulo.toLowerCase() === tema.toLowerCase())) {
          S.historialTemas.push({
            titulo: tema,
            fecha: Date.now()
          });
        }

        // Vincular a propuesta activa si existe
        if (_brechaActiva && S.propuestas && S.propuestas.cola) {
          const pIdx = S.propuestas.cola.findIndex(x => x.estado === 'generando' &&
            (x.brechaLabel === _brechaActiva.label || x.tema === tema));
          if (pIdx >= 0) {
            if (!S.propuestas.cola[pIdx].contenido) S.propuestas.cola[pIdx].contenido = {};
            S.propuestas.cola[pIdx].contenido.blog = contenidoBlog;
            S.propuestas.cola[pIdx].contenido.generado = false;
          }
        }
        save();

        // Indicador de calidad en el resultado
        var avisosGeo = validacion.avisos && validacion.avisos.length > 0 ?
          ' · ⚡ ' + validacion.avisos.length + ' aviso(s) GEO' :
          '';
        // Detectar estructura usada por Claude
        var estructuraInfo = detectarEstructura(contenidoBlog);
        var badgeEstructura =
          '<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap">' +
          '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;' +
          'background:' + estructuraInfo.color + '20;color:' + estructuraInfo.color + ';border:1px solid ' + estructuraInfo.color + '40">' +
          '🏗️ Estructura ' + estructuraInfo.num + ' — ' + estructuraInfo.nombre +
          '</span>' +
          '<div style="position:relative;display:inline-block">' +
          '<button id="btn-tooltip-est" onclick="toggleTooltipEst()" ' +
          'style="background:none;border:1px solid var(--border);border-radius:99px;padding:2px 8px;' +
          'font-size:11px;color:var(--text-muted);cursor:pointer">ℹ️ Ver todas</button>' +
          '<div id="tooltip-estructuras" style="display:none;position:absolute;left:0;top:28px;z-index:200;' +
          'background:var(--bg-surface);border:1px solid var(--border);border-radius:10px;padding:14px;' +
          'width:340px;box-shadow:0 8px 32px rgba(0,0,0,.18);font-size:11px;line-height:1.6">' +
          '<div style="font-weight:700;margin-bottom:8px;color:var(--text-primary)">Las 5 estructuras disponibles</div>' +
          '<div style="display:flex;flex-direction:column;gap:7px">' +
          '<div><span style="font-weight:700;color:#007AEB">E1 Datos + guía:</span> Tabla → Párrafo → Viñetas → Párrafo largo → CTA</div>' +
          '<div><span style="font-weight:700;color:#0ea5e9">E2 Narrativa:</span> Texto seguido → Cita destacada → Texto corto → CTA</div>' +
          '<div><span style="font-weight:700;color:#10b981">E3 Escaneable:</span> Viñetas → Viñetas → Imagen → Párrafo → Tabla pequeña → Conclusión (sin CTA)</div>' +
          '<div><span style="font-weight:700;color:#f59e0b">E4 Profundidad:</span> Frase destacada → Texto largo H2/H3 → Mini conclusión (sin CTA)</div>' +
          '<div><span style="font-weight:700;color:#8b5cf6">E5 Lista:</span> Lista numerada → Párrafo contexto → CTA</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>';

        var badgeCalidad = validacion.ok ?
          '<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:rgba(16,185,129,.12);color:#059669;font-weight:600">✅ ' + validacion.palabras + ' palabras · Calidad OK' + avisosGeo + '</span>' :
          '<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:rgba(245,158,11,.12);color:#b45309;font-weight:600">⚠️ ' + validacion.palabras + ' palabras · Revisar: ' + validacion.errores.join(', ') + avisosGeo + '</span>';

        document.getElementById('gen-blog').innerHTML =
          badgeEstructura +
          '<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">' +
          '<h3 style="color:var(--text-primary);margin:0;font-size:18px;">📝 Artículo (Markdown Puro)</h3>' +
          badgeCalidad + '</div>' +
          '<div style="background:var(--bg-card);padding:16px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-family:var(--mono,monospace);font-size:14px;color:var(--text-primary);overflow-x:auto;">' + esc(contenidoBlog) + '</div>';
        document.getElementById('gen-linkedin').innerHTML = '';
        document.getElementById('gen-instagram').innerHTML = '';

      } catch (err) {
        clearInterval(intv);
        clearInterval(msgIntv);
        loadingDiv.style.display = 'none';
        document.getElementById('gen-resultado').style.display = 'block';
        document.getElementById('gen-blog').innerHTML =
          '<div style="background:var(--danger-light);padding:20px;border-radius:8px">' +
          '<strong style="color:var(--danger)">❌ Error:</strong> ' + esc(err.message) + '</div>' +
          '<button class="btn btn-primary" style="margin-top:16px" onclick="generarTodo()">Intentar de nuevo</button>';
        toast('✕', 'Error al generar el blog', 'error');

        if (_brechaActiva && S.propuestas && S.propuestas.cola) {
          const pIdx = S.propuestas.cola.findIndex(x => x.estado === 'generando' &&
            (x.brechaLabel === _brechaActiva.label || x.tema === tema));
          if (pIdx >= 0) {
            S.propuestas.cola[pIdx].estado = 'fallido';
            S.propuestas.cola[pIdx].mensajeError = 'Misión fallida: Inténtelo de nuevo. Error: ' + err.message;
            save();
            renderColaPropostas();
          }
        }
      }
    }

    // ── BOTÓN 2: Generar solo LinkedIn + Instagram ─────────────────
    async function generarRRSS() {
      if (!S.contenido || !S.contenido.blog) {
        toast('⚠️', 'Genera primero el Blog antes de los posts de RRSS.', 'warn');
        return;
      }
      const params = _getGenParams();
      if (!params) return;
      const {
        empresaName,
        sector,
        linkLinkedIn,
        linkInstagram,
        tema
      } = params;
      const {
        loadingDiv,
        progressFill,
        progressLbl,
        intv
      } = _iniciarLoading('📱 Generando posts para RRSS...');

      const extracto = S.contenido.blog.slice(0, 800);

      // Notas de revisión RRSS si vienen de una regeneración
      var instruccionesRRSS = '';
      if (_notasRRSS) {
        instruccionesRRSS = '\nINSTRUCCIONES DE REVISIÓN — PRIORIDAD MÁXIMA:\n' +
          'La responsable de revisión ha indicado los siguientes cambios que DEBES aplicar:\n"""\n' +
          _notasRRSS + '\n"""\nEstas instrucciones tienen prioridad sobre cualquier otra directriz.\n';
        _notasRRSS = ''; // limpiar tras usar
      }

      const promptRRSS = `Eres un experto en redes sociales B2B para "${empresaName}" del sector "${sector}".

REGLA ABSOLUTA ANTES DE EMPEZAR: El post de LinkedIn NO puede contener ningún hashtag (#). Cero. Si ves que has escrito un # en el post de LinkedIn, bórralo. Esta regla no tiene excepciones.

Basándote en este artículo de blog:
---
${extracto}...
---
${instruccionesRRSS}
Genera exactamente estos dos posts usando las etiquetas indicadas:

[LINKEDIN_START]
Post de LinkedIn (RF-5C):
- ~150 palabras. Tono formal, orientado a decisores B2B.
- Estructura: enganche → desarrollo → CTA.
- PROHIBIDO USAR HASHTAGS (#). Cero hashtags. Ni uno solo. Los hashtags en LinkedIn B2B restan credibilidad y profesionalidad. El post termina con el enlace, sin ningún # después.
- Enlace al final: ${linkLinkedIn}
[LINKEDIN_END]

[INSTAGRAM_START]
Post de Instagram (RF-5D):
- ~80 palabras. Tono cercano, apertura impactante.
- NO resumas el blog — crea curiosidad.
- 5-8 hashtags al final.
- Enlace al final: ${linkInstagram}
[INSTAGRAM_END]`;

      try {
        const textoRRSS = await llamarAnthropicDirecto(promptRRSS, 2000);

        function extraerSeccion(etiqueta, txt) {
          const re = new RegExp('\\[' + etiqueta + '_START\\]([\\s\\S]*?)\\[' + etiqueta + '_END\\]', 'i');
          const m = txt.match(re);
          return (m && m[1] && m[1].trim()) ? m[1].trim() : '';
        }
        const contenidoLinkedin = extraerSeccion('LINKEDIN', textoRRSS);
        const contenidoInstagram = extraerSeccion('INSTAGRAM', textoRRSS);

        // Guardar en estado
        if (!S.contenido) S.contenido = {};
        S.contenido.linkedin = contenidoLinkedin;
        S.contenido.instagram = contenidoInstagram;
        S.contenido.generado = true;

        // RF-33: extraer UTMs ANTES de usarlos
        function extraerUTM(texto) {
          const m = texto.match(/https?:\/\/[^\s\)\"]+utm_source=[^\s\)\"]+/i);
          return m ? m[0] : '';
        }

        function renderUTMbox(utm, id) {
          if (!utm) return '';
          return '<div style="margin-top:12px;padding:10px 14px;background:rgba(37,99,235,.07);' +
            'border:1px solid rgba(37,99,235,.2);border-radius:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">' +
            '<span style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;flex-shrink:0">🔗 UTM Link</span>' +
            '<code style="font-size:11px;color:var(--text-secondary);flex:1;word-break:break-all;min-width:0">' + esc(utm) + '</code>' +
            '<button onclick="copiarUTM(\'' + id + '\')" id="utm-btn-' + id + '" ' +
            'style="flex-shrink:0;padding:4px 12px;background:var(--primary);color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">📋 Copiar</button>' +
            '</div>';
        }

        const utmLinkedIn = extraerUTM(contenidoLinkedin || '');
        const utmInstagram = extraerUTM(contenidoInstagram || '');
        if (utmLinkedIn) S.contenido._utmLinkedIn = utmLinkedIn;
        if (utmInstagram) S.contenido._utmInstagram = utmInstagram;

        // Vincular a propuesta activa si existe
        if (_brechaActiva && S.propuestas && S.propuestas.cola) {
          const pIdx = S.propuestas.cola.findIndex(x => x.estado !== 'descartado' &&
            (x.brechaLabel === _brechaActiva.label || x.tema === tema));
          if (pIdx >= 0) {
            if (!S.propuestas.cola[pIdx].contenido) S.propuestas.cola[pIdx].contenido = {};
            S.propuestas.cola[pIdx].contenido.linkedin = contenidoLinkedin;
            S.propuestas.cola[pIdx].contenido.instagram = contenidoInstagram;
            S.propuestas.cola[pIdx].contenido.generado = true;
            S.propuestas.cola[pIdx].contenido._utmLinkedIn = utmLinkedIn || '';
            S.propuestas.cola[pIdx].contenido._utmInstagram = utmInstagram || '';
            S.propuestas.cola[pIdx].estado = 'completado';
          }
        }
        save();

        document.getElementById('gen-linkedin').innerHTML =
          '<h3 style="color:var(--accent-primary);margin-top:32px;border-top:2px solid var(--border);padding-top:24px;font-size:18px;">💼 Post para LinkedIn</h3>' +
          '<div style="background:var(--bg-card);padding:16px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:14px;color:var(--text-primary);">' + esc(contenidoLinkedin || '(No generado)') + '</div>' +
          renderUTMbox(utmLinkedIn, 'linkedin');

        document.getElementById('gen-instagram').innerHTML =
          '<h3 style="color:#e1306c;margin-top:24px;border-top:2px solid var(--border);padding-top:24px;font-size:18px;">📸 Post para Instagram</h3>' +
          '<div style="background:var(--bg-card);padding:16px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:14px;color:var(--text-primary);">' + esc(contenidoInstagram || '(No generado)') + '</div>' +
          renderUTMbox(utmInstagram, 'instagram');

        _finalizarLoading(loadingDiv, progressFill, progressLbl, intv);

      } catch (err) {
        clearInterval(intv);
        loadingDiv.style.display = 'none';
        document.getElementById('gen-resultado').style.display = 'block';
        document.getElementById('gen-linkedin').innerHTML =
          '<div style="background:var(--danger-light);padding:20px;border-radius:8px;margin-top:20px">' +
          '<strong style="color:var(--danger)">❌ Error RRSS:</strong> ' + esc(err.message) + '</div>' +
          '<button class="btn btn-secondary" style="margin-top:12px" onclick="generarRRSS()">Reintentar posts RRSS</button>';
        toast('✕', 'Error al generar posts', 'error');
      }
    }

    async function generarContenido() {
      const tipo = document.getElementById('gen-tipo').value;
      const tema = document.getElementById('gen-tema').value.trim();
      const kws = document.getElementById('gen-kws').value;
      const tono = document.getElementById('gen-tono').value;
      const comp = document.getElementById('gen-competidor').value;

      if (!tema) {
        toast('⚠️', 'Por favor, introduce un tema principal.', 'warn');
        return;
      }

      const empresaName = S.config.myCompany.name || "Bluease";
      const sector = S.config.myCompany.sector || "nuestro sector";

      document.getElementById('gen-resultado').style.display = 'none';
      const loadingDiv = document.getElementById('gen-loading');
      loadingDiv.style.display = 'flex';

      const botonesCopiar = document.getElementById('botones-copiar') || document.getElementById('btn-copiar');
      if (botonesCopiar) botonesCopiar.style.display = 'none';

      var barraAccionUI = document.getElementById('barra-accion-gen');
      if (barraAccionUI) barraAccionUI.style.display = 'none';

      const progressFill = loadingDiv.querySelector('.prog-fill');
      const progressLbl = loadingDiv.querySelector('.prog-lbl');
      let pct = 0;

      const intv = setInterval(function() {
        if (pct < 92) {
          pct += Math.random() * 4 + 1;
          if (pct > 92) pct = 92;
        } else if (pct < 98) {
          pct += 0.12;
        }
        progressFill.style.width = Math.min(pct, 98) + '%';
        progressLbl.textContent = Math.round(Math.min(pct, 98)) + '%';
      }, 500);

      const controller = new AbortController();
      const timeoutId = setTimeout(function() {
        controller.abort();
      }, 90000);

      var contextBrecha = '';
      if (typeof _brechaActiva !== 'undefined' && _brechaActiva) {
        var miScore0 = score(0);
        contextBrecha = '\nCONTEXTO DE TAREA DEL PLAN A CUMPLIR:\n' +
          '  - La empresa tiene un score GEO actual de ' + miScore0 + '/100.\n' +
          '  - La tarea del plan de acción que este artículo debe cumplir es: "' + _brechaActiva.label + '".\n' +
          '  - El objetivo de este artículo es resolver esta tarea para ganar autoridad GEO.\n';
      }

      const slugArticulo = crearSlug(tema);
      const linkLinkedIn = generarUTM('linkedin', 'social', 'blog', slugArticulo);
      const linkInstagram = generarUTM('instagram', 'social', 'blog', slugArticulo);

      const promptGEO = `Eres un redactor SEO experto y especialista Senior en GEO (Generative Engine Optimization) trabajando para la empresa "${empresaName}" en el sector "${sector}".

Tu tarea es redactar un contenido de tipo "${tipo}" sobre el tema: "${tema}".
Las palabras clave objetivo son: ${kws || 'Úsalas de forma natural según el tema'}.
${comp ? `El competidor a superar es: "${comp}". Nuestro texto debe ser una fuente más citable, técnica y estructurada.` : ''}
${contextBrecha}

REGLAS DE TONO Y MANUAL DE ESTILO (¡DIRECTRICES ESTRICTAS DE MARKETING!):
- **Voz de Marca:** El tono debe ser **${tono}**. Formal y elegante, pero accesible y cercano.
- **Ortografía de Preguntas:** DEBES usar el signo de interrogación de apertura y cierre (¿?).
- **Cero Coloquialismos:** ¡PROHIBIDO usar palabras informales como "demonios", "tajada", "brutal"! 
- **Cero Spanglish:** Usa "recinto" o "espacio" en lugar de "venue".
- **Cero Clichés Agresivos:** Evita frases hechas de relleno.
- **Ejemplos Reales:** Prioriza ejemplos prácticos con datos exactos.

REGLAS ESTRUCTURALES "MACHINE-FIRST":
1. **Estructura Base:** Título (H1) y encabezados jerárquicos (H2, H3).
2. **El "TL;DR":** Un resumen introductorio de viñetas de 3 líneas justo debajo del H1.
3. **Autoridad y Entidades:** Cita explícitamente marcos reconocidos (ej. GHG Protocol, ISO).
4. **Fórmulas Explícitas:** Incluye al menos una fórmula matemática clara.
5. **Precisión Cuantitativa:** Si das un dato, indica su fuente oficial.
6. **Ejemplo Real Completo:** Caso práctico cerrado con números concretos.
7. **Comparativas Interpretables:** Traduce los datos a analogías.
8. **Formatos Estructurados:** Usa al menos UNA TABLA en formato Markdown.
9. **Preguntas Directas (FAQ):** Un bloque final con 3 preguntas long-tail.
10. **Conclusión y Cierre:** Un párrafo de cierre elegante.
11. **Estilo de Imagen de Red Neuronal Luminosa:** El prompt generado debe seguir ESTRICTAMENTE el estilo de una intrincada y luminosa red neuronal en 3D sobre un fondo negro cósmico profundo. Debe incluir múltiples nodos esféricos incandescentes de color verde neón y blanco interconectados por finas y luminosas líneas de luz verde neón. La composición debe tener profundidad de campo. La luz neón verde es la fuente de iluminación principal. El prompt debe fusionar el tema del artículo con esta estética de datos luminosos.
12. **Lectura Escaneable:** Usa **negritas** (bold en Markdown) estratégicamente en palabras clave, conceptos principales y frases importantes dentro de todos los párrafos para romper el texto y facilitar una lectura mucho más visual y rápida.
13. **Aclaraciones con Comas:** Incorpora de manera orgánica comas (, texto,) para alternar acotaciones o ideas secundarias. ¡ESTÁ PROHIBIDO el uso de guiones largos o rayas de incisión (—) para hacer acotaciones!

CRÍTICO - FORMATO DE RESPUESTA DE SALIDA:
¡NO USES FORMATO JSON! Escribe tu respuesta utilizando EXACTAMENTE las siguientes etiquetas separadoras.
ADVERTENCIA: Si alguna sección está vacía o falta alguna etiqueta, el sistema fallará completamente. Las 3 secciones son OBLIGATORIAS.

[BLOG_START]
Aquí escribes TODO el artículo, el alt text y el prompt de imagen en Markdown.
[BLOG_END]

[INSTAGRAM_START]
OBLIGATORIO — Esta sección NUNCA puede estar vacía. Escribe el post de Instagram en texto plano.
- Texto de ~80 palabras.
- Tono: cercano, directo, con pregunta o dato impactante de apertura.
- No resume el blog, crea curiosidad para ir a leerlo.
- Sugerencia de 5 a 8 hashtags relevantes.
- Incluye este enlace UTM al final: ${linkInstagram}
- Incluye un prompt de imagen para Instagram que cumpla con las 'REGLAS DE IMAGEN (ESTRICTAS)' descritas anteriormente.
[INSTAGRAM_END]

[LINKEDIN_START]
OBLIGATORIO — Esta sección NUNCA puede estar vacía. Escribe el post de LinkedIn en texto plano.
- Texto de ~150 palabras.
- Tono: formal, orientado a decisores B2B (responsables de eventos, directores de sostenibilidad).
- Estructura: dato/pregunta de enganche → desarrollo → CTA con link al blog.
- Incluye este enlace UTM al final: ${linkLinkedIn}
[LINKEDIN_END]`;

      try {
        const response = await fetch(MY_WORKER + '/api/plan', {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            prompt: promptGEO
          }),
          signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
          throw new Error(`Error HTTP: ${response.status}`);
        }
        const data = await response.json();
        if (data.error) throw new Error(data.error.message || JSON.stringify(data.error));

        const texto = data.content && data.content[0] && data.content[0].text;
        if (!texto) throw new Error('La API no devolvió contenido.');

        function extraerSeccion(etiqueta, contenidoTotal) {
          var re = new RegExp('\\[' + etiqueta + '_START\\]([\\s\\S]*?)\\[' + etiqueta + '_END\\]', 'i');
          var m = contenidoTotal.match(re);
          if (m && m[1] && m[1].trim().length > 0) return m[1].trim();
          return '';
        }

        const contenidoBlog = extraerSeccion("BLOG", texto);
        const contenidoLinkedin = extraerSeccion("LINKEDIN", texto);
        const contenidoInstagram = extraerSeccion("INSTAGRAM", texto);

        if (!contenidoBlog || contenidoBlog.length < 50) {
          throw new Error('La IA se cortó antes de terminar. Asegúrate de tener max_tokens a 4000 en el Worker.');
        }

        S.contenido = {
          blog: contenidoBlog,
          linkedin: contenidoLinkedin,
          instagram: contenidoInstagram,
          generado: true
        };

        if (_propuestaEnGeneracionIdx >= 0 && S.propuestas && S.propuestas.cola[_propuestaEnGeneracionIdx]) {
          S.propuestas.cola[_propuestaEnGeneracionIdx].contenido = {
            blog: contenidoBlog,
            linkedin: contenidoLinkedin,
            instagram: contenidoInstagram,
            generado: true
          };
          S.propuestas.cola[_propuestaEnGeneracionIdx].estado = 'completado';
          S.propuestas.cola[_propuestaEnGeneracionIdx].mensajeError = '';
        } else if (_brechaActiva && S.propuestas && S.propuestas.cola) {
          var pIdx = S.propuestas.cola.findIndex(p => (p.estado === 'generando' || p.estado === 'pendiente') && p.tema === tema);
          if (pIdx >= 0) {
            S.propuestas.cola[pIdx].contenido = {
              blog: contenidoBlog,
              linkedin: contenidoLinkedin,
              instagram: contenidoInstagram,
              generado: true
            };
            S.propuestas.cola[pIdx].estado = 'completado';
          }
        }
        save();

        renderColaPropostas();
        if (typeof actualizarBadgeBandeja === 'function') actualizarBadgeBandeja();

        const temaGen = document.getElementById('gen-tema').value.trim();
        if (temaGen) {
          if (!S.historialTemas) S.historialTemas = [];
          const yaExiste = S.historialTemas.some(t => t.titulo.toLowerCase() === temaGen.toLowerCase());
          if (!yaExiste) {
            S.historialTemas.push({
              titulo: temaGen,
              fecha: Date.now()
            });
            save();
          }
        }

        document.getElementById('gen-blog').innerHTML =
          '<h3 style="color:var(--text-primary); margin-top:0; margin-bottom:16px; font-size:18px;">📝 Artículo (Markdown Puro)</h3>' +
          '<div style="background:var(--bg-card); padding:16px; border-radius:8px; border:1px solid var(--border); white-space:pre-wrap; font-family:var(--mono, monospace); font-size:14px; color:var(--text-primary); overflow-x:auto;">' + esc(contenidoBlog) + '</div>';

        document.getElementById('gen-linkedin').innerHTML =
          '<h3 style="color:var(--accent-primary); margin-top:32px; border-top:2px solid var(--border); padding-top:24px; font-size:18px;">💼 Post para LinkedIn</h3>' +
          '<div style="background:var(--bg-card); padding:16px; border-radius:8px; border:1px solid var(--border); white-space:pre-wrap; font-size:14px; color:var(--text-primary);">' + esc(contenidoLinkedin) + '</div>';

        document.getElementById('gen-instagram').innerHTML =
          '<h3 style="color:#e1306c; margin-top:24px; border-top:2px solid var(--border); padding-top:24px; font-size:18px;">📸 Post para Instagram</h3>' +
          '<div style="background:var(--bg-card); padding:16px; border-radius:8px; border:1px solid var(--border); white-space:pre-wrap; font-size:14px; color:var(--text-primary);">' + esc(contenidoInstagram) + '</div>';

        clearInterval(intv);
        progressFill.style.width = '100%';
        progressLbl.textContent = '100%';

        setTimeout(() => {
          loadingDiv.style.display = 'none';
          document.getElementById('gen-resultado').style.display = 'block';
          if (botonesCopiar) botonesCopiar.style.display = 'flex';

          if (barraAccionUI) {
            barraAccionUI.innerHTML = '<button class="btn btn-primary" style="width:100%;font-size:15px;padding:12px;" onclick="goScreen(6)">📬 Contenido generado con éxito. Visita la pestaña de Bandeja de Aprobación para gestionarlo →</button>';
            barraAccionUI.style.display = 'block';
          }

          toast('✓', 'Contenido generado con éxito', 'success');
          _propuestaEnGeneracionIdx = -1;
        }, 500);

      } catch (error) {
        clearInterval(intv);
        clearTimeout(timeoutId);
        loadingDiv.style.display = 'none';
        document.getElementById('gen-resultado').style.display = 'block';

        if (_propuestaEnGeneracionIdx >= 0 && S.propuestas && S.propuestas.cola[_propuestaEnGeneracionIdx]) {
          S.propuestas.cola[_propuestaEnGeneracionIdx].estado = 'fallido';
          S.propuestas.cola[_propuestaEnGeneracionIdx].mensajeError = 'Misión fallida: Inténtelo de nuevo. Motivo: ' + error.message;
          save();
          renderColaPropostas();
        }

        const esTimeout = error.name === 'AbortError';
        const errMsg = esTimeout ? '⏱️ Tiempo de espera agotado. Cloudflare tardó demasiado.' : esc(error.message);

        let errHtml =
          '<div style="background:var(--danger-light);padding:20px;border-radius:8px;margin-top:8px">' +
          '<strong style="color:var(--danger)">❌ Error al generar:</strong> ' + errMsg +
          '</div>' +
          '<div style="margin-top:10px; color:#dc2626; font-weight:600; font-size:14px;">⚠️ Misión fallida: Inténtelo de nuevo.</div>' +
          '<button class="btn btn-primary" style="margin-top:16px" onclick="generarContenido()">Intentar de nuevo</button>';

        document.getElementById('gen-blog').innerHTML = errHtml;
        document.getElementById('gen-linkedin').innerHTML = '';
        document.getElementById('gen-instagram').innerHTML = '';
        toast('✕', 'Misión fallida: Inténtelo de nuevo', 'error');
        _propuestaEnGeneracionIdx = -1;
      }
    }

    // Copiar UTM desde modal detalle (recibe el texto directamente)
    function copiarUTMModal(texto, btnId) {
      if (!texto) {
        toast('⚠️', 'No hay UTM para copiar', 'warn');
        return;
      }
      navigator.clipboard.writeText(texto).then(function() {
        var btn = document.getElementById(btnId);
        if (btn) {
          var orig = btn.textContent;
          btn.textContent = '✓ Copiado';
          btn.style.background = '#10b981';
          setTimeout(function() {
            btn.textContent = orig;
            btn.style.background = '';
          }, 2000);
        }
        toast('📋', 'UTM copiado');
      });
    }

    function copiarUTM(red) {
      const utm = red === 'linkedin' ?
        (S.contenido && S.contenido._utmLinkedIn) :
        (S.contenido && S.contenido._utmInstagram);
      if (!utm) {
        toast('⚠️', 'No hay UTM generado', 'warn');
        return;
      }
      navigator.clipboard.writeText(utm).then(function() {
        var btn = document.getElementById('utm-btn-' + red);
        if (btn) {
          btn.textContent = '✓ Copiado';
          btn.style.background = '#10b981';
          setTimeout(function() {
            btn.textContent = '📋 Copiar';
            btn.style.background = 'var(--primary)';
          }, 2000);
        }
        toast('📋', 'UTM de ' + (red === 'linkedin' ? 'LinkedIn' : 'Instagram') + ' copiado');
      });
    }

    function copiarSeccion(tipo) {
      if (!S.contenido) {
        toast('⚠️', 'No hay contenido generado', 'warn');
        return;
      }
      var texto = '';

      if (tipo === 'markdown') {
        texto = S.contenido.blog || '';
      } else if (tipo === 'texto') {
        var raw = S.contenido.blog || '';
        texto = raw
          .replace(/^#+\s+/gm, '') // Quita H1, H2, H3 (###)
          .replace(/\*\*(.*?)\*\*/g, '$1') // Quita Negritas (**)
          .replace(/\*(.*?)\*/g, '$1') // Quita Cursivas (*)
          .replace(/__(.*?)__/g, '$1') // Quita Negritas (__)
          .replace(/_(.*?)_/g, '$1') // Quita Cursivas (_)
          .replace(/\[([^\]]+)\]\([^\)]+\)/g, '$1') // Quita Enlaces dejando solo el texto
          .replace(/`(.*?)`/g, '$1') // Quita código inline
          .replace(/^>\s+/gm, '') // Quita símbolos de citas (>)
          .trim();
      } else if (tipo === 'linkedin') {
        texto = S.contenido.linkedin || '';
      } else if (tipo === 'instagram') {
        texto = S.contenido.instagram || '';
      }

      if (!texto) {
        toast('⚠️', 'No hay contenido para copiar', 'warn');
        return;
      }

      navigator.clipboard.writeText(texto).then(function() {
        var mensaje = tipo === 'texto' ? 'Texto limpio copiado' : 'Markdown copiado';
        toast('📋', mensaje);
      }).catch(function() {
        toast('✕', 'Error al copiar', 'error');
      });
    }

    /* ─────────────────────────────────────────────────────────
   MÓDULO 2B — BANDEJA DE APROBACIÓN (RF-25)
   ───────────────────────────────────────────────────────── */

    var _filtroActivo = 'todos';

    function actualizarBadgeBandeja() {
      // RF-29 cubre esta información — badge eliminado
    }

    function cambiarEstadoBandeja(idx, nuevoEstado) {
      if (!S.propuestas.cola || !S.propuestas.cola[idx]) return;
      var prop = S.propuestas.cola[idx];
      prop.estado = nuevoEstado;
      prop.fechaEstado = Date.now();

      // RNF-09: al descartar, registrar el tema en historial para no repetirlo 3 meses
      if (nuevoEstado === 'descartado' && prop.tema) {
        if (!S.historialTemas) S.historialTemas = [];
        if (!S.historialTemas.some(function(t) {
            return t.titulo.toLowerCase() === prop.tema.toLowerCase();
          })) {
          S.historialTemas.push({
            titulo: prop.tema,
            fecha: Date.now()
          });
        }
      }

      save();
      actualizarBadgeBandeja();
      actualizarBadgeBiblioteca();
      renderBandeja(_filtroActivo);
    }

    function filtrarBandeja(filtro) {
      _filtroActivo = filtro;
      ['todos', 'pendiente', 'aprobado', 'edicion', 'descartado'].forEach(function(f) {
        var btn = document.getElementById('filtro-' + f);
        if (!btn) return;
        btn.className = f === filtro ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm';
      });
      renderBandeja(filtro);
    }

    function renderBandeja(filtro) {
      var lista = document.getElementById('bandeja-lista');
      var contador = document.getElementById('bandeja-contador');
      if (!lista) return;

      var todas = (S.propuestas.cola || []).slice();

      var filtradas = filtro === 'todos' ?
        todas :
        todas.filter(function(p) {
          return p.estado === filtro;
        });

      if (contador) {
        contador.textContent = filtradas.length + ' propuesta' + (filtradas.length !== 1 ? 's' : '') +
          (filtro !== 'todos' ? ' · ' + todas.length + ' en total' : '');
      }

      var pendientes = todas.filter(function(p) {
        return p.estado === 'pendiente';
      }).length;
      var total = todas.length;
      var pct = total > 0 ? Math.round((pendientes / total) * 100) : 0;
      var barra = document.getElementById('carga-barra');
      var label = document.getElementById('carga-label');
      var texto = document.getElementById('carga-texto');
      if (barra && label && texto) {
        var color, bgLabel, colorLabel, nivel;
        if (pct < 50) {
          color = '#10b981';
          bgLabel = 'rgba(16,185,129,.12)';
          colorLabel = '#059669';
          nivel = '🟢 Ligera';
        } else if (pct < 70) {
          color = '#f59e0b';
          bgLabel = 'rgba(245,158,11,.12)';
          colorLabel = '#b45309';
          nivel = '🟡 Moderada';
        } else {
          color = '#ef4444';
          bgLabel = 'rgba(239,68,68,.12)';
          colorLabel = '#dc2626';
          nivel = '🔴 Alta';
        }
        barra.style.width = pct + '%';
        barra.style.background = color;
        label.textContent = nivel;
        label.style.background = bgLabel;
        label.style.color = colorLabel;
        texto.textContent = pendientes + ' pendiente' + (pendientes !== 1 ? 's' : '') +
          ' de ' + total + ' propuesta' + (total !== 1 ? 's' : '') + ' · ' + pct + '%';
      }

      actualizarBadgeBandeja();

      if (todas.length === 0) {
        lista.innerHTML =
          '<div style="text-align:center;padding:56px 24px;color:var(--text-muted)">' +
          '<div style="font-size:40px;margin-bottom:12px">📭</div>' +
          '<div style="font-size:15px;margin-bottom:6px">Bandeja vacía</div>' +
          '<div style="font-size:13px;opacity:.7">Genera un plan de acción o un lote de contenido para ver propuestas aquí.</div>' +
          '<button class="btn btn-primary" style="margin-top:20px" onclick="goScreen(4)">Ir al Plan de acción</button>' +
          '</div>';
        return;
      }

      if (filtradas.length === 0) {
        lista.innerHTML =
          '<div style="text-align:center;padding:40px 24px;color:var(--text-muted)">' +
          '<div style="font-size:32px;margin-bottom:10px">🔍</div>' +
          '<div style="font-size:14px">No hay propuestas con estado "' + filtro + '"</div>' +
          '</div>';
        return;
      }

      var TIPO_LABEL = {
        'faq avanzada': {
          icon: '❓',
          label: 'FAQ'
        },
        'comparativo analitico': {
          icon: '⚖️',
          label: 'Comparativo'
        },
        'caso de exito': {
          icon: '💼',
          label: 'Caso de éxito'
        },
        'glosario tecnico': {
          icon: '📖',
          label: 'Glosario'
        },
        'guia operativa': {
          icon: '📋',
          label: 'Guía'
        }
      };

      var ESTADO_CONFIG = {
        pendiente: {
          bg: 'rgba(245,158,11,.1)',
          color: '#b45309',
          label: '✍️ Por generar'
        },
        aprobado: {
          bg: 'rgba(16,185,129,.1)',
          color: '#059669',
          label: '✅ Aprobada'
        },
        edicion: {
          bg: 'rgba(37,99,235,.1)',
          color: '#1d4ed8',
          label: '🔖 Por aprobar'
        },
        descartado: {
          bg: 'rgba(100,116,139,.1)',
          color: '#475569',
          label: '🗑️ Descartada'
        },
        completado: {
          bg: 'rgba(16,185,129,.1)',
          color: '#059669',
          label: '✓ Completada'
        }
      };

      var html = '';

      filtradas.forEach(function(prop) {
        var idx = S.propuestas.cola.indexOf(prop);
        var tInfo = TIPO_LABEL[prop.tipo] || {
          icon: '📝',
          label: 'Blog'
        };
        var eConfig = ESTADO_CONFIG[prop.estado] || ESTADO_CONFIG.pendiente;
        var fecha = new Date(prop.fechaCreacion).toLocaleDateString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        });
        var esDelPlan = prop.origen === 'plan';

        html += '<div style="border:1px solid var(--border);border-radius:12px;padding:16px 18px;' +
          'background:var(--bg-surface);margin-bottom:10px;transition:box-shadow .15s" ' +
          'onmouseover="this.style.boxShadow=\'0 2px 12px rgba(0,0,0,.08)\'" ' +
          'onmouseout="this.style.boxShadow=\'none\'">';

        html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap">';
        html += '<span style="font-size:18px">' + tInfo.icon + '</span>';
        html += '<span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:99px;' +
          'background:rgba(37,99,235,.1);color:var(--primary)">' + tInfo.label + '</span>';
        if (esDelPlan) {
          html += '<span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:99px;' +
            'background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2)">📋 Del plan</span>';
        }
        html += '<span style="font-size:11px;padding:2px 9px;border-radius:99px;' +
          'background:' + eConfig.bg + ';color:' + eConfig.color + ';font-weight:600">' + eConfig.label + '</span>';
        html += '<span style="margin-left:auto;font-size:11px;color:var(--text-muted)">' + fecha + '</span>';
        html += '</div>';

        html += '<div style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:6px;line-height:1.4">' +
          esc(prop.tema || prop.brechaLabel || '—') + '</div>';

        html += '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px">';
        if (prop.brechaLabel) {
          html += '<span style="font-size:12px;color:#ef4444">🎯 Cierra: ' + esc(prop.brechaLabel) +
            (prop.gap ? ' (+' + prop.gap + ' pts)' : '') + '</span>';
        }
        if (prop.kws) {
          html += '<span style="font-size:12px;color:var(--text-muted)">🔑 ' + esc(prop.kws) + '</span>';
        }
        html += '</div>';

        html += '<div style="display:flex;gap:7px;flex-wrap:wrap;align-items:center">';

        var tieneContenidoGenerado = prop.contenido && prop.contenido.generado === true;

        // ✅ Aprobar — solo si hay contenido generado
        if (prop.estado !== 'aprobado' && tieneContenidoGenerado) {
          html += '<button class="btn btn-sm" style="background:rgba(16,185,129,.12);color:#059669;border:1px solid rgba(16,185,129,.3)" ' +
            'onclick="cambiarEstadoBandeja(' + idx + ',\'aprobado\')">✅ Aprobar</button>';
        }

        // 📝 Crear contenido — si no hay contenido todavía
        if (!tieneContenidoGenerado) {
          html += '<button class="btn btn-sm" style="background:rgba(37,99,235,.12);color:var(--primary);border:1px solid rgba(37,99,235,.3)" ' +
            'onclick="activarPropuestaBandeja(' + idx + ')">✨ Crear contenido</button>';
        }

        // 🔖 Ver y aprobar — botón principal cuando está en estado "edicion" (Por aprobar)
        if (tieneContenidoGenerado && prop.estado === 'edicion') {
          html += '<button class="btn btn-primary btn-sm" ' +
            'onclick="editarYAprobar(' + idx + ')">🔖 Ver y aprobar</button>';
        }

        // ✏️ Editar — si tiene contenido pero NO está en estado edicion
        if (tieneContenidoGenerado && prop.estado !== 'edicion') {
          html += '<button class="btn btn-sm" style="background:rgba(37,99,235,.1);color:var(--primary);border:1px solid rgba(37,99,235,.25)" ' +
            'onclick="editarYAprobar(' + idx + ')">✏️ Editar y aprobar</button>';
        }

        // 🔄 Regenerar — mismo tipo y tema, nueva versión
        html += '<button class="btn btn-sm" style="background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.3)" ' +
          'onclick="regenerarPropuesta(' + idx + ')">🔄 Regenerar</button>';

        // 🎲 Cambiar tema — mismo tipo, tema distinto
        html += '<button class="btn btn-sm" style="background:rgba(139,92,246,.1);color:#7c3aed;border:1px solid rgba(139,92,246,.3)" ' +
          'onclick="cambiarTemaPropuesta(' + idx + ')">🎲 Cambiar tema</button>';

        // ❌ Descartar
        if (prop.estado !== 'descartado') {
          html += '<button class="btn btn-ghost btn-sm" style="color:var(--danger)" ' +
            'onclick="cambiarEstadoBandeja(' + idx + ',\'descartado\')">❌ Descartar</button>';
        }

        // 👁 Ver contenido
        if (tieneContenidoGenerado) {
          html += '<button class="btn btn-secondary btn-sm" style="margin-left:auto" ' +
            'onclick="abrirDetallePropuesta(' + idx + ')">👁 Ver contenido</button>';
        }

        html += '</div>';
        html += '</div>';
      });

      lista.innerHTML = html;
    }

    // RF-27: Editar y aprobar — abre el editor de detalle y al cerrar aprueba
    function editarYAprobar(idx) {
      abrirDetallePropuesta(idx);
      // Cambia el botón del modal a "Aprobar y cerrar" (ya existe en el modal por defecto)
      cambiarEstadoBandeja(idx, 'edicion');
      toast('✏️', 'Edita el contenido y pulsa "Aprobar y cerrar"');
    }

    // RF-27: Regenerar — mismo tipo y tema, pide una versión alternativa a Claude
    function regenerarPropuesta(idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop) return;

      // Modal con textarea de notas de revisión
      var prev = document.getElementById('modal-regenerar');
      if (prev) prev.remove();

      var modal = document.createElement('div');
      modal.id = 'modal-regenerar';
      modal.style.cssText = 'position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.55);' +
        'display:flex;align-items:center;justify-content:center;padding:20px';

      modal.innerHTML =
        '<div style="background:var(--bg-surface);border:1px solid var(--border);border-radius:16px;' +
        'width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,.3);padding:24px">' +
        '<div style="margin-bottom:18px">' +
        '<div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:4px">🔄 Regenerar artículo</div>' +
        '<div style="font-size:13px;color:var(--text-muted)">' + esc(prop.tema || prop.brechaLabel || '') + '</div>' +
        '</div>' +

        // Textarea Blog
        '<div style="margin-bottom:14px">' +
        '<label style="font-size:12px;font-weight:600;color:var(--primary);display:flex;align-items:center;gap:6px;margin-bottom:6px">' +
        '<span style="background:rgba(37,99,235,.1);padding:2px 8px;border-radius:4px">📝 Blog</span>' +
        '<span style="font-weight:400;color:var(--text-muted)">Notas para el artículo</span>' +
        '</label>' +
        '<textarea id="notas-regeneracion-blog" placeholder="Ej: El tono es demasiado técnico, hazlo más accesible. Añade un ejemplo de festival de música. El CTA no convence..." ' +
        'style="width:100%;min-height:90px;background:var(--bg-card);border:1px solid var(--border);' +
        'border-radius:8px;padding:12px;font-size:13px;color:var(--text-primary);resize:vertical;' +
        'line-height:1.6;outline:none;font-family:var(--font);box-sizing:border-box"></textarea>' +
        '</div>' +

        // Textarea RRSS
        '<div style="margin-bottom:20px">' +
        '<label style="font-size:12px;font-weight:600;color:#7c3aed;display:flex;align-items:center;gap:6px;margin-bottom:6px">' +
        '<span style="background:rgba(124,58,237,.1);padding:2px 8px;border-radius:4px">📱 LinkedIn + Instagram</span>' +
        '<span style="font-weight:400;color:var(--text-muted)">Notas para los posts</span>' +
        '</label>' +
        '<textarea id="notas-regeneracion-rrss" placeholder="Ej: El post de LinkedIn es muy largo. En Instagram añade más emojis y un tono más cercano. Cambia el CTA por algo más directo..." ' +
        'style="width:100%;min-height:90px;background:var(--bg-card);border:1px solid var(--border);' +
        'border-radius:8px;padding:12px;font-size:13px;color:var(--text-primary);resize:vertical;' +
        'line-height:1.6;outline:none;font-family:var(--font);box-sizing:border-box"></textarea>' +
        '</div>' +

        '<div style="font-size:11px;color:var(--text-muted);margin-bottom:16px;padding:8px 12px;background:var(--bg-body);border-radius:6px">' +
        '💡 Puedes rellenar uno, los dos o ninguno. Los campos vacíos generan el contenido sin restricciones adicionales.' +
        '</div>' +

        '<div style="display:flex;gap:8px;justify-content:flex-end">' +
        '<button class="btn btn-secondary btn-sm" onclick="document.getElementById(\'modal-regenerar\').remove()">Cancelar</button>' +
        '<button class="btn btn-primary btn-sm" onclick="confirmarRegeneracion(' + idx + ')">🔄 Regenerar</button>' +
        '</div>' +
        '</div>';

      modal.addEventListener('click', function(e) {
        if (e.target === modal) modal.remove();
      });
      document.body.appendChild(modal);
      setTimeout(function() {
        var ta = document.getElementById('notas-regeneracion');
        if (ta) ta.focus();
      }, 100);
    }

    function confirmarRegeneracion(idx) {
      var modal = document.getElementById('modal-regenerar');
      var taBlog = document.getElementById('notas-regeneracion-blog');
      var taRRSS = document.getElementById('notas-regeneracion-rrss');
      var notasBlog = taBlog ? taBlog.value.trim() : '';
      var notasRRSS = taRRSS ? taRRSS.value.trim() : '';
      if (modal) modal.remove();

      var prop = S.propuestas.cola[idx];
      if (!prop) return;

      // Guardar notas en la propuesta para historial
      S.propuestas.cola[idx].notasRevision = notasBlog;
      S.propuestas.cola[idx].notasRevisionRRSS = notasRRSS;

      // Guardar notas RRSS en variable global para que generarRRSS las use
      _notasRRSS = notasRRSS;

      var extractoAnterior = '';
      if (prop.contenido && prop.contenido.blog) {
        extractoAnterior = prop.contenido.blog.slice(0, 400).trim();
      }
      _modoRegeneracion = {
        temaOriginal: prop.tema || prop.brechaLabel || '',
        extractoAnterior: extractoAnterior,
        notasRevision: notasBlog
      };

      // Activar en el generador con el mismo tipo y tema
      var tipoSel = document.getElementById('gen-tipo');
      if (tipoSel) {
        for (var i = 0; i < tipoSel.options.length; i++) {
          if (tipoSel.options[i].value === prop.tipo) {
            tipoSel.selectedIndex = i;
            break;
          }
        }
      }
      var temaEl = document.getElementById('gen-tema');
      var kwsEl = document.getElementById('gen-kws');
      if (temaEl) temaEl.value = prop.tema || prop.brechaLabel || '';
      if (kwsEl) kwsEl.value = prop.kws || '';

      _brechaActiva = {
        id: prop.brechaId || 'regenerar',
        label: prop.brechaLabel,
        gap: prop.gap || 0
      };

      S.propuestas.cola[idx].estado = 'pendiente';
      save();
      renderBandeja(_filtroActivo);

      goScreen(5);
      var hayNotas = notasBlog || notasRRSS;
      toast('🔄', hayNotas ? 'Regenerando con tus notas de revisión...' : 'Generando versión alternativa...');
      generarTodo();
    }

    // RF-27: Cambiar tema — mismo tipo de contenido, Claude sugiere un tema distinto automáticamente
    async function cambiarTemaPropuesta(idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop) return;

      toast('🎲', 'Buscando nuevo tema...', '', 2000);

      var sector = S.config.myCompany.sector || 'sostenibilidad en eventos';
      var empresa = S.config.myCompany.name || 'Bluease';
      var tipo = prop.tipo || 'guia operativa';
      var temaActual = prop.tema || prop.brechaLabel || '';
      var historial = getTemasRecientes();

      var promptNuevoTema =
        'Eres un estratega de contenido GEO para "' + empresa + '" del sector "' + sector + '".\n\n' +
        'Necesito un NUEVO tema para un artículo de tipo "' + tipo + '".\n' +
        'El tema anterior era: "' + temaActual + '" — NO lo repitas ni uses variaciones similares.\n\n' +
        'Temas ya usados (PROHIBIDO repetir):\n' + historial + '\n\n' +
        'Genera UN único título de artículo concreto, optimizado para búsquedas de IA, ' +
        'diferente a los anteriores. Responde SOLO con el título, sin explicaciones ni comillas.';

      try {
        var nuevoTema = await llamarAnthropicDirecto(promptNuevoTema, 200);
        nuevoTema = nuevoTema.trim().replace(/^["']|["']$/g, '');

        if (!nuevoTema || nuevoTema.length < 5) throw new Error('Tema vacío');

        // Actualizar la propuesta con el nuevo tema
        S.propuestas.cola[idx].tema = nuevoTema;
        S.propuestas.cola[idx].estado = 'pendiente';
        S.propuestas.cola[idx].contenido = null;
        save();

        // Activar en el generador
        var tipoSel = document.getElementById('gen-tipo');
        if (tipoSel) {
          for (var i = 0; i < tipoSel.options.length; i++) {
            if (tipoSel.options[i].value === prop.tipo) {
              tipoSel.selectedIndex = i;
              break;
            }
          }
        }
        var temaEl = document.getElementById('gen-tema');
        var kwsEl = document.getElementById('gen-kws');
        if (temaEl) temaEl.value = nuevoTema;
        if (kwsEl) kwsEl.value = '';

        _brechaActiva = {
          id: prop.brechaId || 'cambio_tema',
          label: prop.brechaLabel,
          gap: prop.gap || 0
        };

        renderBandeja(_filtroActivo);
        goScreen(5);
        toast('🎲', 'Nuevo tema: "' + nuevoTema.slice(0, 60) + (nuevoTema.length > 60 ? '…' : '') + '"');
        generarTodo();

      } catch (e) {
        toast('✕', 'Error al generar nuevo tema: ' + e.message, 'error');
      }
    }

    function activarPropuestaBandeja(idx) {
      if (S.propuestas.cola && S.propuestas.cola[idx]) {
        activarPropuesta(idx);
        goScreen(5);
      }
    }

    /* ─────────────────────────────────────────────────────────
       RF-26 — VISTA DETALLE DE PROPUESTA (pestañas + edición)
       ───────────────────────────────────────────────────────── */

    function abrirDetallePropuesta(idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop || !prop.contenido) return;

      var c = prop.contenido;

      var prev = document.getElementById('detalle-modal');
      if (prev) prev.remove();

      var modal = document.createElement('div');
      modal.id = 'detalle-modal';
      modal.style.cssText =
        'position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.55);' +
        'display:flex;align-items:flex-start;justify-content:center;padding:24px 16px;overflow-y:auto';

      modal.innerHTML =
        '<div style="background:var(--bg-surface);border:1px solid var(--border);border-radius:16px;' +
        'width:100%;max-width:860px;box-shadow:0 20px 60px rgba(0,0,0,.3);display:flex;flex-direction:column">' +
        '<div style="display:flex;align-items:center;justify-content:space-between;' +
        'padding:18px 24px;border-bottom:1px solid var(--border)">' +
        '<div>' +
        '<div style="font-size:16px;font-weight:600;color:var(--text-primary);margin-bottom:3px">' +
        esc(prop.tema || prop.brechaLabel || 'Propuesta') + '</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">Vista detalle · Edición inline activa</div>' +
        '</div>' +
        '<button onclick="cerrarDetalle()" style="background:none;border:none;font-size:22px;cursor:pointer;' +
        'color:var(--text-muted);line-height:1;padding:4px 8px" title="Cerrar">✕</button>' +
        '</div>' +
        '<div style="display:flex;border-bottom:1px solid var(--border);padding:0 24px;gap:0">' +
        '<button id="dtab-blog"      onclick="switchTabDetalle(\'blog\',' + idx + ')"      class="dtab dtab-active">📝 Blog</button>' +
        '<button id="dtab-linkedin"  onclick="switchTabDetalle(\'linkedin\',' + idx + ')"  class="dtab">💼 LinkedIn</button>' +
        '<button id="dtab-instagram" onclick="switchTabDetalle(\'instagram\',' + idx + ')" class="dtab">📸 Instagram</button>' +
        '<button id="dtab-imagen"    onclick="switchTabDetalle(\'imagen\',' + idx + ')"    class="dtab">🎨 Imagen</button>' +
        '</div>' +
        '<div id="dtab-content" style="padding:20px 24px;flex:1">' +
        renderTabDetalle('blog', c, idx) +
        '</div>' +
        '<div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end">' +
        '<button class="btn btn-secondary btn-sm" onclick="cerrarDetalle()">Cerrar</button>' +
        '<button class="btn btn-primary btn-sm" onclick="cambiarEstadoBandeja(' + idx + ',\'aprobado\');cerrarDetalle()">✅ Aprobar y cerrar</button>' +
        '</div>' +
        '</div>';

      if (!document.getElementById('dtab-styles')) {
        var st = document.createElement('style');
        st.id = 'dtab-styles';
        st.textContent =
          '.dtab{padding:10px 18px;border:none;border-bottom:3px solid transparent;background:none;' +
          'color:var(--text-muted);font-size:13px;font-weight:500;cursor:pointer;transition:all .15s}' +
          '.dtab:hover{color:var(--text-primary)}' +
          '.dtab-active{border-bottom-color:var(--primary);color:var(--primary);font-weight:600}';
        document.head.appendChild(st);
      }

      modal.addEventListener('click', function(e) {
        if (e.target === modal) cerrarDetalle();
      });

      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';
    }

    function cerrarDetalle() {
      var modal = document.getElementById('detalle-modal');
      if (modal) modal.remove();
      document.body.style.overflow = '';
    }

    var _tabActivaDetalle = 'blog';

    function switchTabDetalle(tab, idx) {
      _tabActivaDetalle = tab;
      ['blog', 'linkedin', 'instagram', 'imagen'].forEach(function(t) {
        var btn = document.getElementById('dtab-' + t);
        if (btn) btn.className = t === tab ? 'dtab dtab-active' : 'dtab';
      });
      var prop = S.propuestas.cola[idx];
      if (!prop) return;
      var content = document.getElementById('dtab-content');
      if (content) content.innerHTML = renderTabDetalle(tab, prop.contenido || {}, idx);
    }

    function renderTabDetalle(tab, c, idx) {
      if (tab === 'blog') {
        var htmlInicial = c.blogHtml || mdToHtmlEditor(c.blog || '');
        return '' +
          '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">' +
          '<span style="font-size:12px;color:var(--text-muted)">Editor visual — los cambios se guardan automáticamente.</span>' +
          '<div style="display:flex;gap:6px">' +
          '<button class="btn btn-secondary btn-sm" onclick="toggleVistaEditor(' + idx + ')" id="btn-toggle-vista">📄 Ver Markdown</button>' +
          '<button class="btn btn-secondary btn-sm" onclick="copiarDesdeEditor(' + idx + ')">📋 Copiar</button>' +
          '</div>' +
          '</div>' +
          '<div id="editor-toolbar" style="display:flex;flex-wrap:wrap;gap:3px;padding:8px 10px;align-items:center;' +
          'background:var(--bg-body);border:1px solid var(--border);border-bottom:none;border-radius:8px 8px 0 0">' +
          '<select onchange="ejecutarCmd(\'formatBlock\',this.value);this.value=\'p\'" ' +
          'style="padding:4px 6px;background:var(--bg-surface);border:1px solid var(--border);border-radius:5px;color:var(--text-secondary);font-size:12px;cursor:pointer;height:28px">' +
          '<option value="p">Párrafo</option><option value="h1">H1</option><option value="h2">H2</option><option value="h3">H3</option><option value="h4">H4</option>' +
          '</select>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<select onchange="ejecutarCmd(\'fontSize\',this.value);this.value=\'3\'" ' +
          'style="padding:4px 6px;background:var(--bg-surface);border:1px solid var(--border);border-radius:5px;color:var(--text-secondary);font-size:12px;cursor:pointer;height:28px">' +
          '<option value="1">Pequeño</option><option value="2">Normal S</option><option value="3" selected>Normal</option><option value="4">Grande</option><option value="5">Mayor</option><option value="6">Título</option>' +
          '</select>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'bold\')"         title="Negrita (Ctrl+B)"><b>B</b></button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'italic\')"       title="Cursiva (Ctrl+I)"><i>I</i></button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'underline\')"    title="Subrayado" style="text-decoration:underline">U</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'strikeThrough\')" title="Tachado" style="text-decoration:line-through">S</button>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'insertUnorderedList\')" title="Lista viñetas">• Lista</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'insertOrderedList\')"   title="Lista numerada">1. Lista</button>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'justifyLeft\')"   title="Izquierda">⬤◁</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'justifyCenter\')" title="Centrar">◁⬤▷</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'justifyRight\')"  title="Derecha">▷⬤</button>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'outdent\')" title="Reducir sangría">⇤</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'indent\')"  title="Aumentar sangría">⇥</button>' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<input type="color" value="#000000" title="Color texto" onchange="ejecutarCmd(\'foreColor\',this.value)" style="width:28px;height:28px;padding:2px;border:1px solid var(--border);border-radius:5px;cursor:pointer;background:none">' +
          '<input type="color" value="#ffff00" title="Resaltado"   onchange="ejecutarCmd(\'hiliteColor\',this.value)" style="width:28px;height:28px;padding:2px;border:1px solid var(--border);border-radius:5px;cursor:pointer;background:none">' +
          '<div style="width:1px;background:var(--border);margin:2px 4px;height:22px"></div>' +
          '<button class="tbtn2" onclick="insertarEnlace()" title="Insertar enlace">🔗</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'undo\')"  title="Deshacer">↩</button>' +
          '<button class="tbtn2" onclick="ejecutarCmd(\'redo\')"  title="Rehacer">↪</button>' +
          '</div>' +
          '<div id="editor-blog" contenteditable="true" oninput="guardarDesdeEditor(' + idx + ')" ' +
          'style="width:100%;min-height:420px;background:var(--bg-card);border:1px solid var(--border);' +
          'border-radius:0 0 8px 8px;padding:16px 18px;font-size:14px;color:var(--text-primary);' +
          'outline:none;line-height:1.75;overflow-y:auto;font-family:var(--font)">' +
          htmlInicial +
          '</div>' +
          '<textarea id="editor-blog-md" onblur="guardarEdicionDetalle(\'blog\',' + idx + ',this.value)" ' +
          'style="display:none;width:100%;min-height:420px;background:var(--bg-card);border:1px solid var(--border);' +
          'border-radius:0 0 8px 8px;padding:14px;font-family:var(--mono,monospace);font-size:13px;' +
          'color:var(--text-primary);resize:vertical;line-height:1.75;outline:none">' +
          esc(c.blog || '') +
          '</textarea>' +
          '<style id="rf28-styles">' +
          '.tbtn2{padding:4px 8px;background:var(--bg-surface);border:1px solid var(--border);border-radius:5px;' +
          'color:var(--text-secondary);font-size:12px;cursor:pointer;line-height:1.4;font-family:var(--font);transition:all .12s;height:28px}' +
          '.tbtn2:hover{background:var(--primary);color:#fff;border-color:var(--primary)}' +
          '#editor-blog h1{font-size:22px;font-weight:700;margin:16px 0 8px}' +
          '#editor-blog h2{font-size:18px;font-weight:600;margin:14px 0 6px}' +
          '#editor-blog h3{font-size:15px;font-weight:600;margin:12px 0 5px}' +
          '#editor-blog ul,#editor-blog ol{padding-left:20px;margin:8px 0}' +
          '#editor-blog li{margin-bottom:4px}' +
          '#editor-blog blockquote{border-left:3px solid var(--primary);padding-left:12px;margin:10px 0;color:var(--text-muted);font-style:italic}' +
          '#editor-blog a{color:var(--primary);text-decoration:underline}' +
          '</style>';
      }
      if (tab === 'linkedin') {
        // Si no hay UTM guardado, intentar extraerlo del texto del post
        var utmLI = c._utmLinkedIn || '';
        if (!utmLI && c.linkedin) {
          var mLI = c.linkedin.match(/https?:\/\/[^\s\)\"]+utm_source=[^\s\)\"]+/i);
          if (mLI) utmLI = mLI[0];
        }
        return '<div style="margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">' +
          '<span style="font-size:12px;color:var(--text-muted)">Post listo para publicar en LinkedIn.</span>' +
          '<button class="btn btn-secondary btn-sm" onclick="copiarTextoDetalle(\'linkedin\',' + idx + ')">📋 Copiar texto</button>' +
          '</div>' +
          '<div style="max-width:560px;margin:0 auto">' +
          '<div style="background:var(--bg-card);border:1px solid #0a66c2;border-radius:10px;padding:4px">' +
          '<div style="padding:10px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:6px">' +
          '<span style="font-size:13px">💼</span>' +
          '<span style="font-size:11px;font-weight:600;color:#0a66c2">LINKEDIN</span>' +
          '</div>' +
          '<textarea id="editor-linkedin" onblur="guardarEdicionDetalle(\'linkedin\',' + idx + ',this.value)" ' +
          'style="width:100%;min-height:220px;background:transparent;border:none;' +
          'padding:14px;font-size:14px;color:var(--text-primary);resize:vertical;' +
          'line-height:1.65;outline:none;font-family:var(--font)">' +
          esc(c.linkedin || '') +
          '</textarea>' +
          '</div>' +
          '</div>' +
          (utmLI ? '<div style="margin-top:12px;padding:10px 14px;background:rgba(10,102,194,.07);' +
            'border:1px solid rgba(10,102,194,.2);border-radius:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">' +
            '<span style="font-size:11px;font-weight:700;color:#0a66c2;text-transform:uppercase;letter-spacing:.5px;flex-shrink:0">🔗 UTM Link</span>' +
            '<code style="font-size:11px;color:var(--text-secondary);flex:1;word-break:break-all;min-width:0">' + esc(utmLI) + '</code>' +
            '<button id="utm-modal-btn-linkedin" onclick="copiarUTMModal(\'' + esc(utmLI) + '\',\'utm-modal-btn-linkedin\')" ' +
            'style="flex-shrink:0;padding:4px 12px;background:#0a66c2;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">📋 Copiar</button>' +
            '</div>' : '');
      }
      if (tab === 'instagram') {
        // Si no hay UTM guardado, intentar extraerlo del texto del post
        var utmIG = c._utmInstagram || '';
        if (!utmIG && c.instagram) {
          var mIG = c.instagram.match(/https?:\/\/[^\s\)\"]+utm_source=[^\s\)\"]+/i);
          if (mIG) utmIG = mIG[0];
        }
        return '<div style="margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">' +
          '<span style="font-size:12px;color:var(--text-muted)">Post listo para publicar en Instagram.</span>' +
          '<button class="btn btn-secondary btn-sm" onclick="copiarTextoDetalle(\'instagram\',' + idx + ')">📋 Copiar texto</button>' +
          '</div>' +
          '<div style="max-width:480px;margin:0 auto">' +
          '<div style="background:var(--bg-card);border:1px solid #e1306c;border-radius:10px;padding:4px">' +
          '<div style="padding:10px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:6px">' +
          '<span style="font-size:13px">📸</span>' +
          '<span style="font-size:11px;font-weight:600;color:#e1306c">INSTAGRAM</span>' +
          '</div>' +
          '<textarea id="editor-instagram" onblur="guardarEdicionDetalle(\'instagram\',' + idx + ',this.value)" ' +
          'style="width:100%;min-height:180px;background:transparent;border:none;' +
          'padding:14px;font-size:14px;color:var(--text-primary);resize:vertical;' +
          'line-height:1.65;outline:none;font-family:var(--font)">' +
          esc(c.instagram || '') +
          '</textarea>' +
          '</div>' +
          '</div>' +
          (utmIG ? '<div style="margin-top:12px;padding:10px 14px;background:rgba(225,48,108,.06);' +
            'border:1px solid rgba(225,48,108,.2);border-radius:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">' +
            '<span style="font-size:11px;font-weight:700;color:#e1306c;text-transform:uppercase;letter-spacing:.5px;flex-shrink:0">🔗 UTM Link</span>' +
            '<code style="font-size:11px;color:var(--text-secondary);flex:1;word-break:break-all;min-width:0">' + esc(utmIG) + '</code>' +
            '<button id="utm-modal-btn-instagram" onclick="copiarUTMModal(\'' + esc(utmIG) + '\',\'utm-modal-btn-instagram\')" ' +
            'style="flex-shrink:0;padding:4px 12px;background:#e1306c;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">📋 Copiar</button>' +
            '</div>' : '');
      }
      if (tab === 'imagen') {
        var blogTexto = c.blog || '';

        // Banner del Style Lock de Bluease
        var styleLockBanner =
          '<div style="margin-bottom:16px;padding:10px 14px;background:rgba(37,99,235,.06);' +
          'border:1px solid rgba(37,99,235,.2);border-radius:8px;font-size:11px;line-height:1.6">' +
          '<div style="font-weight:700;color:var(--primary);margin-bottom:6px">🎨 Style Lock Bluease activo</div>' +
          '<div style="display:flex;gap:16px;flex-wrap:wrap;color:var(--text-muted)">' +
          '<span>📐 Flat illustration · Vector SaaS</span>' +
          '<span>🎨 Mint blue dominante + blancos</span>' +
          '<span>🟡 <strong style="color:#b45309">#FFC107</strong> acento principal (15-20%)</span>' +
          '<span>🟢 <strong style="color:#059669">#02BE24</strong> acento secundario</span>' +
          '<span>❌ Sin viento · Sin papeleras · Sin naturaleza exagerada · Sin neón</span>' +
          '</div>' +
          '</div>';

        // Extractor para los 3 campos de imagen (5B)
        var altText = '';
        var promptBlog = '';
        var promptInsta = '';

        var altM = blogTexto.match(/\*\*ALT_TEXT:\*\*\s*([^\n]+)/i) ||
          blogTexto.match(/ALT_TEXT:\s*([^\n]+)/i) ||
          blogTexto.match(/\*\*Alt Text[^:]*:\*\*\s*([^\n]+)/i);
        if (altM) altText = altM[1].trim().replace(/^\[|\]$/g, '');

        var pBlogM = blogTexto.match(/\*\*PROMPT_BLOG:\*\*\s*([^\n]+)/i) ||
          blogTexto.match(/PROMPT_BLOG:\s*([^\n]+)/i);
        if (pBlogM) promptBlog = pBlogM[1].trim().replace(/^\[|\]$/g, '');

        var pInstaM = blogTexto.match(/\*\*PROMPT_INSTAGRAM:\*\*\s*([^\n]+)/i) ||
          blogTexto.match(/PROMPT_INSTAGRAM:\s*([^\n]+)/i);
        if (pInstaM) promptInsta = pInstaM[1].trim().replace(/^\[|\]$/g, '');

        // Fallback: si solo hay PROMPT_IMAGEN antiguo, usarlo en ambos
        if (!promptBlog && !promptInsta) {
          var legacyM = blogTexto.match(/\*\*PROMPT_IMAGEN:\*\*\s*([^\n]+)/i) ||
            blogTexto.match(/PROMPT_IMAGEN:\s*([^\n]+)/i) ||
            blogTexto.match(/\*\*Prompt[^:]*:\*\*[\s\S]*?```[^`\n]*\n([\s\S]*?)```/i);
          if (legacyM) {
            promptBlog = legacyM[1].trim();
            promptInsta = legacyM[1].trim();
          }
        }

        // Usar valores guardados si existen (ediciones manuales previas)
        if (c.alt) altText = c.alt;
        if (c.promptImg) promptBlog = c.promptImg;
        if (c.promptInsta) promptInsta = c.promptInsta;
        return styleLockBanner + '<div style="display:flex;flex-direction:column;gap:20px">' +

          // Alt Text
          '<div>' +
          '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px">🔍 Alt Text para SEO</div>' +
          '<textarea id="editor-alt" onblur="guardarEdicionDetalle(\'alt\',' + idx + ',this.value)" ' +
          'style="width:100%;min-height:56px;background:var(--bg-card);border:1px solid var(--border);' +
          'border-radius:8px;padding:12px;font-size:13px;color:var(--text-primary);resize:vertical;line-height:1.5;outline:none;font-family:var(--font)">' +
          esc(altText) + '</textarea>' +
          '<button class="btn btn-secondary btn-sm" style="margin-top:6px" onclick="copiarCampoImg(\'editor-alt\',\'alt\',' + idx + ')">📋 Copiar alt text</button>' +
          '</div>' +

          // Prompt Blog
          '<div style="background:rgba(0,122,235,.05);border:1px solid rgba(0,122,235,.2);border-radius:10px;padding:14px">' +
          '<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">' +
          '<span style="font-size:16px">🖼️</span>' +
          '<div>' +
          '<div style="font-size:12px;font-weight:700;color:var(--primary)">PROMPT BLOG — Formato 16:9 horizontal</div>' +
          '<div style="font-size:10px;color:var(--text-muted)">Flat illustration · Mint blue + #FFC107 (acento principal) + #02BE24 (acento secundario) · Sin texto ni logos · Estilo SaaS corporativo</div>' +
          '</div>' +
          '</div>' +
          '<textarea id="editor-prompt-blog" onblur="guardarEdicionDetalle(\'promptImg\',' + idx + ',this.value)" ' +
          'style="width:100%;min-height:90px;background:var(--bg-card);border:1px solid var(--border);' +
          'border-radius:8px;padding:12px;font-size:12px;color:var(--text-primary);resize:vertical;line-height:1.6;outline:none;font-family:var(--mono,monospace)">' +
          esc(promptBlog) + '</textarea>' +
          '<button class="btn btn-secondary btn-sm" style="margin-top:6px" onclick="copiarCampoImg(\'editor-prompt-blog\',\'promptImg\',' + idx + ')">📋 Copiar prompt blog</button>' +
          '</div>' +

          // Prompt Instagram
          '<div style="background:rgba(225,48,108,.04);border:1px solid rgba(225,48,108,.2);border-radius:10px;padding:14px">' +
          '<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">' +
          '<span style="font-size:16px">📸</span>' +
          '<div>' +
          '<div style="font-size:12px;font-weight:700;color:#e1306c">PROMPT INSTAGRAM — Formato 1:1 cuadrado</div>' +
          '<div style="font-size:10px;color:var(--text-muted)">Flat illustration · Mint blue + #FFC107 + #02BE24 · Sin texto overlay · Composición dinámica pero limpia</div>' +
          '</div>' +
          '</div>' +
          '<textarea id="editor-prompt-insta" onblur="guardarEdicionDetalle(\'promptInsta\',' + idx + ',this.value)" ' +
          'style="width:100%;min-height:90px;background:var(--bg-card);border:1px solid var(--border);' +
          'border-radius:8px;padding:12px;font-size:12px;color:var(--text-primary);resize:vertical;line-height:1.6;outline:none;font-family:var(--mono,monospace)">' +
          esc(promptInsta) + '</textarea>' +
          '<button class="btn btn-secondary btn-sm" style="margin-top:6px" onclick="copiarCampoImg(\'editor-prompt-insta\',\'promptInsta\',' + idx + ')">📋 Copiar prompt Instagram</button>' +
          '</div>' +

          '</div>';
      }
      return '';
    }

    // Copiar un campo del tab imagen y guardar edición
    function copiarCampoImg(editorId, campo, idx) {
      var el = document.getElementById(editorId);
      if (!el || !el.value.trim()) {
        toast('⚠️', 'No hay contenido para copiar', 'warn');
        return;
      }
      guardarEdicionDetalle(campo, idx, el.value);
      navigator.clipboard.writeText(el.value).then(function() {
        toast('📋', 'Copiado al portapapeles');
      });
    }

    // ── RF-28: EDITOR AVANZADO ──────────────────────────────
    function ejecutarCmd(cmd, valor) {
      var ed = document.getElementById('editor-blog');
      if (!ed) return;
      ed.focus();
      document.execCommand(cmd, false, valor || null);
    }

    function mdToHtmlEditor(md) {
      if (!md) return '';
      var h = md
        .replace(/```[\s\S]*?```/g, function(m) {
          return '<pre style="background:var(--bg-body);padding:10px;border-radius:6px;font-size:12px;overflow-x:auto"><code>' +
            m.replace(/```\w*/g, '').replace(/```/g, '').trim() + '</code></pre>';
        })
        .replace(/^#### (.+)$/gm, '<h4>$1</h4>')
        .replace(/^### (.+)$/gm, '<h3>$1</h3>')
        .replace(/^## (.+)$/gm, '<h2>$1</h2>')
        .replace(/^# (.+)$/gm, '<h1>$1</h1>')
        .replace(/\*\*\*(.+?)\*\*\*/g, '<b><i>$1</i></b>')
        .replace(/\*\*(.+?)\*\*/g, '<b>$1</b>')
        .replace(/\*(.+?)\*/g, '<i>$1</i>')
        .replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>')
        .replace(/^---+$/gm, '<hr>')
        .replace(/((?:^- .+\n?)+)/gm, function(m) {
          var items = m.trim().split('\n').map(function(l) {
            return '<li>' + l.replace(/^- /, '') + '</li>';
          }).join('');
          return '<ul>' + items + '</ul>';
        })
        .replace(/((?:^\d+\. .+\n?)+)/gm, function(m) {
          var items = m.trim().split('\n').map(function(l) {
            return '<li>' + l.replace(/^\d+\. /, '') + '</li>';
          }).join('');
          return '<ol>' + items + '</ol>';
        })
        .replace(/\n\n/g, '</p><p>');
      return '<p>' + h + '</p>';
    }

    function guardarDesdeEditor(idx) {
      var ed = document.getElementById('editor-blog');
      var prop = S.propuestas.cola[idx];
      if (!prop || !ed) return;
      if (!prop.contenido) prop.contenido = {};
      prop.contenido.blogHtml = ed.innerHTML;
      save();
    }

    function toggleVistaEditor(idx) {
      var ed = document.getElementById('editor-blog');
      var md = document.getElementById('editor-blog-md');
      var btn = document.getElementById('btn-toggle-vista');
      var tb = document.getElementById('editor-toolbar');
      if (!ed || !md) return;
      var modoMd = md.style.display !== 'none';
      if (modoMd) {
        ed.style.display = 'block';
        md.style.display = 'none';
        if (tb) tb.style.display = 'flex';
        if (btn) btn.textContent = '📄 Ver Markdown';
      } else {
        var prop = S.propuestas.cola[idx];
        md.value = (prop && prop.contenido && prop.contenido.blog) || '';
        ed.style.display = 'none';
        md.style.display = 'block';
        if (tb) tb.style.display = 'none';
        if (btn) btn.textContent = '🎨 Editor visual';
      }
    }

    function insertarEnlace() {
      var url = prompt('URL del enlace:', 'https://');
      if (url) ejecutarCmd('createLink', url);
    }

    function copiarDesdeEditor(idx) {
      var ed = document.getElementById('editor-blog');
      var md = document.getElementById('editor-blog-md');
      var modoMd = md && md.style.display !== 'none';
      var texto = modoMd ? (md.value || '') : (ed ? ed.innerText : '');
      if (!texto) {
        toast('⚠️', 'No hay contenido para copiar', 'warn');
        return;
      }
      navigator.clipboard.writeText(texto).then(function() {
        toast('📋', modoMd ? 'Markdown copiado' : 'Texto copiado');
      });
    }

    function guardarEdicionDetalle(campo, idx, valor) {
      var prop = S.propuestas.cola[idx];
      if (!prop) return;
      if (!prop.contenido) prop.contenido = {};
      prop.contenido[campo] = valor;
      save();
    }

    function copiarTextoDetalle(campo, idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop || !prop.contenido) return;
      var el = document.getElementById('editor-' + campo) ||
        document.getElementById('editor-prompt-img');
      var texto = el ? el.value : (prop.contenido[campo] || '');
      if (!texto) {
        toast('⚠️', 'No hay contenido para copiar', 'warn');
        return;
      }
      navigator.clipboard.writeText(texto).then(function() {
        toast('📋', 'Copiado al portapapeles');
      });
    }

    /* ─────────────────────────────────────────────────────────
       MÓDULO 3 — HISTORIAL DE TEMAS (ANTI-DUPLICADOS)
       ───────────────────────────────────────────────────────── */
    function renderHistorialTemas() {
      const contenedor = document.getElementById('lista-historial-temas');
      if (!contenedor) return;

      if (!S.historialTemas || S.historialTemas.length === 0) {
        contenedor.innerHTML = '<div style="text-align:center;padding:40px 24px;color:var(--text-muted);"><div style="font-size:32px;margin-bottom:10px">📭</div>No hay temas en el historial todavía. Todo lo que generes aparecerá aquí.</div>';
        return;
      }

      const temasOrdenados = [...S.historialTemas].sort((a, b) => b.fecha - a.fecha);

      let html = '<div style="display:flex;flex-direction:column;gap:8px;">';
      temasOrdenados.forEach(function(t) {
        const d = new Date(t.fecha).toLocaleDateString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        html += '<div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--bg-surface);border:1px solid var(--border);border-radius:8px;">' +
          '<div>' +
          '<div style="font-weight:600;font-size:14px;color:var(--text-primary);margin-bottom:4px;">' + esc(t.titulo) + '</div>' +
          '<div style="font-size:11px;color:var(--text-muted);">Registrado el ' + d + '</div>' +
          '</div>' +
          '<button class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="eliminarTemaHistorial(' + t.fecha + ')">✕ Eliminar</button>' +
          '</div>';
      });
      html += '</div>';
      contenedor.innerHTML = html;
    }

    function eliminarTemaHistorial(timestamp) {
      if (!S.historialTemas) return;
      S.historialTemas = S.historialTemas.filter(function(t) {
        return t.fecha !== timestamp;
      });
      save();
      renderHistorialTemas();
      toast('🗑️', 'Tema eliminado del historial');
    }

    function limpiarHistorialTemas() {
      if (confirm('¿Estás seguro de que quieres borrar todo el historial? La IA perderá la memoria y podría repetir temas.')) {
        S.historialTemas = [];
        save();
        renderHistorialTemas();
        toast('🗑️', 'Historial borrado por completo');
      }
    }

    /* ─────────────────────────────────────────────────────────
       MÓDULO BIBLIOTECA — RF-31 / RF-32
       ───────────────────────────────────────────────────────── */

    var _filtroBiblioteca = 'todos';

    // Badge del nav con nº de contenidos pendientes de publicar
    function actualizarBadgeBiblioteca() {
      var badge = document.getElementById('biblioteca-badge');
      if (!badge) return;
      var pendientes = (S.propuestas.cola || []).filter(function(p) {
        return p.estado === 'aprobado' && p.publicacion !== 'publicado';
      }).length;
      if (pendientes > 0) {
        badge.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;' +
          'min-width:18px;height:18px;border-radius:99px;font-size:10px;font-weight:700;' +
          'background:#10b981;color:#fff;padding:0 5px;flex-shrink:0';
        badge.textContent = pendientes;
      } else {
        badge.style.display = 'none';
        badge.textContent = '';
      }
    }

    function filtrarBiblioteca(filtro) {
      _filtroBiblioteca = filtro;
      ['todos', 'pendiente', 'publicado'].forEach(function(f) {
        var btn = document.getElementById('bib-filtro-' + f);
        if (btn) btn.className = f === filtro ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm';
      });
      renderBiblioteca(filtro);
    }

    function marcarPublicado(idx) {
      if (!S.propuestas.cola[idx]) return;
      S.propuestas.cola[idx].publicacion = 'publicado';
      S.propuestas.cola[idx].fechaPublicacion = Date.now();
      save();
      actualizarBadgeBiblioteca();
      renderBiblioteca(_filtroBiblioteca);
      toast('✅', 'Marcado como publicado');
    }

    function marcarPendiente(idx) {
      if (!S.propuestas.cola[idx]) return;
      S.propuestas.cola[idx].publicacion = 'pendiente';
      delete S.propuestas.cola[idx].fechaPublicacion;
      save();
      actualizarBadgeBiblioteca();
      renderBiblioteca(_filtroBiblioteca);
      toast('↩', 'Marcado como pendiente de publicar');
    }

    // Exportar como Markdown
    function exportarMarkdown(idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop || !prop.contenido || !prop.contenido.blog) {
        toast('⚠️', 'No hay contenido de blog para exportar', 'warn');
        return;
      }
      var blob = new Blob([prop.contenido.blog], {
        type: 'text/markdown;charset=utf-8'
      });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = (prop.tema || 'articulo').toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '').slice(0, 60) + '.md';
      a.click();
      URL.revokeObjectURL(a.href);
      toast('📥', 'Markdown descargado');
    }

    // Exportar como HTML
    function exportarHTML(idx) {
      var prop = S.propuestas.cola[idx];
      if (!prop || !prop.contenido || !prop.contenido.blog) {
        toast('⚠️', 'No hay contenido de blog para exportar', 'warn');
        return;
      }

      // Convertir Markdown a HTML básico
      var md = prop.contenido.blog;
      var html = md
        .replace(/^#### (.+)$/gm, '<h4>$1</h4>')
        .replace(/^### (.+)$/gm, '<h3>$1</h3>')
        .replace(/^## (.+)$/gm, '<h2>$1</h2>')
        .replace(/^# (.+)$/gm, '<h1>$1</h1>')
        .replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>')
        .replace(/^---+$/gm, '<hr>')
        .replace(/((?:^- .+\n?)+)/gm, function(m) {
          var items = m.trim().split('\n').map(function(l) {
            return '<li>' + l.replace(/^- /, '') + '</li>';
          }).join('');
          return '<ul>' + items + '</ul>';
        })
        .replace(/((?:^\d+\. .+\n?)+)/gm, function(m) {
          var items = m.trim().split('\n').map(function(l) {
            return '<li>' + l.replace(/^\d+\. /, '') + '</li>';
          }).join('');
          return '<ol>' + items + '</ol>';
        })
        .replace(/\n\n/g, '</p><p>');

      var fullHtml = '<!DOCTYPE html>\n<html lang="es">\n<head>\n' +
        '<meta charset="UTF-8">\n' +
        '<title>' + esc(prop.tema || 'Artículo') + '</title>\n' +
        '<style>body{font-family:Georgia,serif;max-width:800px;margin:40px auto;' +
        'padding:0 20px;line-height:1.7;color:#1e293b}' +
        'h1,h2,h3{color:#0056b3}table{width:100%;border-collapse:collapse;margin:16px 0}' +
        'th,td{border:1px solid #e2e8f0;padding:8px 12px;text-align:left}' +
        'th{background:#f8fafc;font-weight:600}blockquote{border-left:4px solid #007AEB;' +
        'padding-left:16px;color:#475569;font-style:italic}</style>\n</head>\n<body>\n' +
        '<p>' + html + '</p>\n</body>\n</html>';

      var blob = new Blob([fullHtml], {
        type: 'text/html;charset=utf-8'
      });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = (prop.tema || 'articulo').toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '').slice(0, 60) + '.html';
      a.click();
      URL.revokeObjectURL(a.href);
      toast('📥', 'HTML descargado (listo para Webflow)');
    }

    // Modal de contenido completo desde biblioteca
    function verContenidoBiblioteca(idx) {
      abrirDetallePropuesta(idx);
    }

    function renderBiblioteca(filtro) {
      var lista = document.getElementById('biblioteca-lista');
      var contador = document.getElementById('bib-contador');
      if (!lista) return;

      // Solo propuestas aprobadas
      var aprobadas = (S.propuestas.cola || []).filter(function(p) {
        return p.estado === 'aprobado';
      });

      var filtradas = filtro === 'todos' ?
        aprobadas :
        aprobadas.filter(function(p) {
          var pub = p.publicacion || 'pendiente';
          return pub === filtro;
        });

      if (contador) {
        contador.textContent = filtradas.length + ' artículo' + (filtradas.length !== 1 ? 's' : '') +
          (filtro !== 'todos' ? ' · ' + aprobadas.length + ' en total' : '');
      }

      // Actualizar badge siempre
      actualizarBadgeBiblioteca();

      if (aprobadas.length === 0) {
        lista.innerHTML =
          '<div style="text-align:center;padding:56px 24px;color:var(--text-muted)">' +
          '<div style="font-size:40px;margin-bottom:12px">📚</div>' +
          '<div style="font-size:15px;margin-bottom:6px">La biblioteca está vacía</div>' +
          '<div style="font-size:13px;opacity:.7">Aprueba propuestas en la Bandeja para que aparezcan aquí.</div>' +
          '<button class="btn btn-primary" style="margin-top:20px" onclick="goScreen(6)">Ir a la Bandeja →</button>' +
          '</div>';
        return;
      }

      if (filtradas.length === 0) {
        lista.innerHTML =
          '<div style="text-align:center;padding:40px 24px;color:var(--text-muted)">' +
          '<div style="font-size:32px;margin-bottom:10px">🔍</div>' +
          '<div style="font-size:14px">No hay artículos con estado "' +
          (filtro === 'pendiente' ? 'pendiente publicar' : 'publicado') + '"</div>' +
          '</div>';
        return;
      }

      var TIPO_LABEL = {
        'faq avanzada': {
          icon: '❓',
          label: 'FAQ'
        },
        'comparativo analitico': {
          icon: '⚖️',
          label: 'Comparativo'
        },
        'caso de exito': {
          icon: '💼',
          label: 'Caso de éxito'
        },
        'glosario tecnico': {
          icon: '📖',
          label: 'Glosario'
        },
        'guia operativa': {
          icon: '📋',
          label: 'Guía'
        }
      };

      var html = '';

      filtradas.forEach(function(prop) {
        var idx = S.propuestas.cola.indexOf(prop);
        var tInfo = TIPO_LABEL[prop.tipo] || {
          icon: '📝',
          label: 'Blog'
        };
        var pub = prop.publicacion || 'pendiente';
        var esPub = pub === 'publicado';
        var fechaAp = prop.fechaEstado ?
          new Date(prop.fechaEstado).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          }) :
          '—';
        var fechaPub = prop.fechaPublicacion ?
          new Date(prop.fechaPublicacion).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          }) :
          null;
        var tieneContenido = prop.contenido && prop.contenido.generado === true;

        html += '<div style="border:1px solid var(--border);border-radius:12px;padding:18px 20px;' +
          'background:var(--bg-surface);margin-bottom:12px;' +
          (esPub ? 'opacity:.75;' : '') + '">';

        // Cabecera
        html += '<div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;flex-wrap:wrap">';
        html += '<span style="font-size:20px">' + tInfo.icon + '</span>';
        html += '<div style="flex:1;min-width:0">';
        html += '<div style="font-size:15px;font-weight:700;color:var(--text-primary);' +
          'line-height:1.3;margin-bottom:5px">' + esc(prop.tema || prop.brechaLabel || '—') + '</div>';

        // Badges
        html += '<div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">';
        html += '<span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:99px;' +
          'background:rgba(37,99,235,.1);color:var(--primary)">' + tInfo.label + '</span>';

        // Estado publicación
        if (esPub) {
          html += '<span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:99px;' +
            'background:rgba(16,185,129,.12);color:#059669">✅ Publicado' +
            (fechaPub ? ' · ' + fechaPub : '') + '</span>';
        } else {
          html += '<span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:99px;' +
            'background:rgba(245,158,11,.12);color:#b45309">⏳ Pendiente publicar</span>';
        }

        html += '<span style="font-size:11px;color:var(--text-muted)">Aprobado: ' + fechaAp + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Links UTM — buscar en _utm guardado o extraer del texto del post
        var bUtmLI = (prop.contenido && prop.contenido._utmLinkedIn) || '';
        var bUtmIG = (prop.contenido && prop.contenido._utmInstagram) || '';
        if (!bUtmLI && prop.contenido && prop.contenido.linkedin) {
          var mBLI = prop.contenido.linkedin.match(/https?:\/\/[^\s\)\"]+utm_source=[^\s\)\"]+/i);
          if (mBLI) bUtmLI = mBLI[0];
        }
        if (!bUtmIG && prop.contenido && prop.contenido.instagram) {
          var mBIG = prop.contenido.instagram.match(/https?:\/\/[^\s\)\"]+utm_source=[^\s\)\"]+/i);
          if (mBIG) bUtmIG = mBIG[0];
        }

        if (tieneContenido && (bUtmLI || bUtmIG)) {
          html += '<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">';
          if (bUtmLI) {
            html += '<div style="display:flex;align-items:center;gap:6px;padding:5px 10px;' +
              'background:rgba(10,102,194,.08);border:1px solid rgba(10,102,194,.2);border-radius:6px;' +
              'font-size:11px;max-width:100%;overflow:hidden">' +
              '<span style="font-weight:700;color:#0a66c2;flex-shrink:0">💼 LinkedIn</span>' +
              '<code style="color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0">' +
              esc(bUtmLI) + '</code>' +
              '<button onclick="copiarUTMModal(\'' + bUtmLI.replace(/'/g, "\\'") + '\',\'bib-utm-li-' + idx + '\')" id="bib-utm-li-' + idx + '" ' +
              'style="flex-shrink:0;padding:2px 8px;background:#0a66c2;color:#fff;border:none;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer">📋 Copiar</button>' +
              '</div>';
          }
          if (bUtmIG) {
            html += '<div style="display:flex;align-items:center;gap:6px;padding:5px 10px;' +
              'background:rgba(225,48,108,.06);border:1px solid rgba(225,48,108,.2);border-radius:6px;' +
              'font-size:11px;max-width:100%;overflow:hidden">' +
              '<span style="font-weight:700;color:#e1306c;flex-shrink:0">📸 Instagram</span>' +
              '<code style="color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0">' +
              esc(bUtmIG) + '</code>' +
              '<button onclick="copiarUTMModal(\'' + bUtmIG.replace(/'/g, "\\'") + '\',\'bib-utm-ig-' + idx + '\')" id="bib-utm-ig-' + idx + '" ' +
              'style="flex-shrink:0;padding:2px 8px;background:#e1306c;color:#fff;border:none;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer">📋 Copiar</button>' +
              '</div>';
          }
          html += '</div>';
        }

        // Acciones
        html += '<div style="display:flex;gap:7px;flex-wrap:wrap;align-items:center">';

        // Ver contenido completo
        if (tieneContenido) {
          html += '<button class="btn btn-primary btn-sm" onclick="verContenidoBiblioteca(' + idx + ')">👁 Ver contenido completo</button>';
        }

        // Exportar
        if (tieneContenido) {
          html += '<button class="btn btn-secondary btn-sm" onclick="exportarMarkdown(' + idx + ')">📥 Exportar .md</button>';
          html += '<button class="btn btn-secondary btn-sm" onclick="exportarHTML(' + idx + ')">🌐 Exportar .html</button>';
        }

        // Marcar publicado / pendiente
        if (!esPub) {
          html += '<button class="btn btn-sm" style="margin-left:auto;background:rgba(16,185,129,.12);' +
            'color:#059669;border:1px solid rgba(16,185,129,.3)" ' +
            'onclick="marcarPublicado(' + idx + ')">✅ Marcar como publicado</button>';
        } else {
          html += '<button class="btn btn-ghost btn-sm" style="margin-left:auto;color:var(--text-muted)" ' +
            'onclick="marcarPendiente(' + idx + ')">↩ Desmarcar publicado</button>';
        }

        html += '</div>';
        html += '</div>';
      });

      lista.innerHTML = html;
    }

    /* ══════════════════════════════════════════════════════════
       GESTIÓN DE PERMISOS — API REST (Screen 9)
       ══════════════════════════════════════════════════════════ */

    const API_BASE = 'api/'; // Ruta relativa a la carpeta donde está el HTML
    var _usuariosTodos = []; // Cache local para el filtro de búsqueda

    // Comprueba si el usuario logueado es admin (rol 2) y muestra/oculta el nav
    function checkAdmin() {
      if (SESSION_ROL === 2) {
        var seccion = document.getElementById('nav-admin-section');
        if (seccion) seccion.style.display = 'block';
      }
    }

    // Carga usuarios desde la API REST y renderiza la tabla
    async function cargarUsuarios() {
      // Mostrar estado de carga
      document.getElementById('permisos-loading').style.display = 'block';
      document.getElementById('permisos-tabla').style.display = 'none';
      document.getElementById('permisos-empty').style.display = 'none';
      document.getElementById('permisos-error').style.display = 'none';

      try {
        var res = await fetch('api/api_usuarios.php');
        var data = await res.json();

        if (!res.ok || !data.ok) {
          throw new Error(data.error || 'Error desconocido');
        }

        _usuariosTodos = data.usuarios || [];
        renderStats(data.stats);
        renderUsuarios(_usuariosTodos);

      } catch (e) {
        document.getElementById('permisos-loading').style.display = 'none';
        document.getElementById('permisos-error').style.display = 'block';
        document.getElementById('permisos-error-msg').textContent = 'Error al cargar usuarios: ' + e.message;
      }
    }

    // Renderiza las stats de resumen (total, admins, empleados, externos)
    function renderStats(stats) {
      if (!stats) return;
      var ROL_CONFIG = [{
          label: 'Total usuarios',
          valor: stats.total,
          color: 'var(--primary)',
          icon: '👥'
        },
        {
          label: 'Administradores',
          valor: stats.admins,
          color: '#8b5cf6',
          icon: '🔐'
        },
        {
          label: 'Empleados',
          valor: stats.empleados,
          color: '#10b981',
          icon: '💼'
        },
        {
          label: 'Externos',
          valor: stats.externos,
          color: 'var(--text-muted)',
          icon: '🌐'
        },
      ];
      var html = ROL_CONFIG.map(function(s) {
        return '<div style="background:var(--bg-surface);border:1px solid var(--border);border-radius:10px;' +
          'padding:14px 18px;display:flex;align-items:center;gap:12px">' +
          '<span style="font-size:22px">' + s.icon + '</span>' +
          '<div><div style="font-size:22px;font-weight:800;color:' + s.color + '">' + s.valor + '</div>' +
          '<div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px">' + s.label + '</div></div>' +
          '</div>';
      }).join('');
      document.getElementById('permisos-stats').innerHTML = html;
    }

    // Renderiza la tabla de usuarios (acepta el array filtrado o completo)
    function renderUsuarios(usuarios) {
      document.getElementById('permisos-loading').style.display = 'none';
      document.getElementById('permisos-error').style.display = 'none';

      if (!usuarios || usuarios.length === 0) {
        document.getElementById('permisos-tabla').style.display = 'none';
        document.getElementById('permisos-empty').style.display = 'block';
        return;
      }

      document.getElementById('permisos-empty').style.display = 'none';
      document.getElementById('permisos-tabla').style.display = 'block';

      var ROL_BADGE = {
        0: '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:rgba(100,116,139,.12);color:var(--text-muted)">Externo</span>',
        1: '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:rgba(16,185,129,.12);color:#10b981">Empleado</span>',
        2: '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:rgba(139,92,246,.12);color:#8b5cf6">Admin</span>',
      };

      var html = usuarios.map(function(u, i) {
        var bgRow = i % 2 === 0 ? 'transparent' : 'rgba(255,255,255,.015)';
        return '<tr style="border-bottom:1px solid var(--border);background:' + bgRow + '">' +
          '<td style="padding:14px 20px;font-size:14px;font-weight:600;color:var(--text-primary)">' + esc(u.nombre) + '</td>' +
          '<td style="padding:14px 20px;font-size:13px;color:var(--text-muted)">' + esc(u.email) + '</td>' +
          '<td style="padding:14px 20px;text-align:center">' + (ROL_BADGE[u.rol] || ROL_BADGE[0]) + '</td>' +
          '<td style="padding:14px 20px;text-align:center">' +
          '<select id="select-rol-' + u.id + '" style="padding:7px 10px;background:var(--bg-surface);' +
          'border:1px solid var(--border);color:var(--text-primary);border-radius:6px;font-size:13px;cursor:pointer">' +
          '<option value="0"' + (u.rol == 0 ? ' selected' : '') + '>0 — Externo</option>' +
          '<option value="1"' + (u.rol == 1 ? ' selected' : '') + '>1 — Empleado</option>' +
          '<option value="2"' + (u.rol == 2 ? ' selected' : '') + '>2 — Admin</option>' +
          '</select>' +
          '</td>' +
          '<td style="padding:14px 20px;text-align:center">' +
          '<button class="btn btn-primary btn-sm" id="btn-guardar-' + u.id + '" ' +
          'onclick="actualizarRol(' + u.id + ')">Guardar</button>' +
          '</td>' +
          '</tr>';
      }).join('');

      document.getElementById('permisos-tbody').innerHTML = html;
    }

    // Filtra la tabla localmente por nombre o email
    function filtrarUsuarios(q) {
      var query = (q || '').toLowerCase().trim();
      if (!query) {
        renderUsuarios(_usuariosTodos);
        return;
      }
      var filtrados = _usuariosTodos.filter(function(u) {
        return u.nombre.toLowerCase().includes(query) || u.email.toLowerCase().includes(query);
      });
      renderUsuarios(filtrados);
    }

    // Llama a la API para actualizar el rol del usuario con id dado
    async function actualizarRol(id) {
      var select = document.getElementById('select-rol-' + id);
      var btn = document.getElementById('btn-guardar-' + id);
      if (!select || !btn) return;

      var nuevoRol = parseInt(select.value, 10);

      // Estado de carga en el botón
      btn.disabled = true;
      btn.textContent = '...';

      try {
        var res = await fetch('api/api_usuarios.php', {
          method: 'POST',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            id: id,
            rol: nuevoRol
          })
        });
        var data = await res.json();

        if (!res.ok || !data.ok) {
          throw new Error(data.error || 'Error al actualizar');
        }

        toast('✅', data.mensaje || 'Rol actualizado correctamente.');
        // Recargar tabla completa para reflejar badge actualizado
        await cargarUsuarios();

      } catch (e) {
        toast('❌', e.message, 'warn', 5000);
        btn.disabled = false;
        btn.textContent = 'Guardar';
      }
    }

    /* ══════════════════════════════════════════════════════════ */

    init();
  </script>
</body>

</html>