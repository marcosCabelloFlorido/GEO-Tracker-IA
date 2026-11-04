<?php
session_start();
require __DIR__ . '/conexion.php';
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $pass    = $_POST['contrasena'];

    try {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = :nombre OR email = :email");
        $stmt->execute([':nombre' => $usuario, ':email' => $usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['contrasena'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['rol']     = (int)$user['rol'];

            if ((int)$user['rol'] === 0) {
                header("Location: https://bluease.io");
            } else {
                header("Location: bluease_tracker_generator_all_08_36_admin.php");
            }
            exit();
        } else {
            $mensaje = "error|Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        error_log("[Bluease Login] " . $e->getMessage());
        $mensaje = "error|Error interno. Inténtalo más tarde.";
    }
}

[$tipoMsg, $textoMsg] = $mensaje ? explode("|", $mensaje, 2) : ["", ""];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bluease — Iniciar sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f5f6f8;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        }

        .logo {
            margin-bottom: 28px;
        }

        .logo img {
            height: 36px;
            width: auto;
            display: block;
        }

        h2 { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .subtitle { font-size: 13px; color: #6b7280; margin-bottom: 24px; }

        .alert {
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-exito { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: border-color 0.2s;
            margin-bottom: 16px;
        }
        input:focus { border-color: #00A86B; }
        input::placeholder { color: #9ca3af; }

        /* Botón brillante (ShinyButton portado a CSS puro), verde de marca */
        @property --gradient-angle { syntax: "<angle>"; initial-value: 0deg; inherits: false; }
        @property --gradient-angle-offset { syntax: "<angle>"; initial-value: 0deg; inherits: false; }
        @property --gradient-percent { syntax: "<percentage>"; initial-value: 5%; inherits: false; }
        @property --gradient-shine { syntax: "<color>"; initial-value: white; inherits: false; }

        button {
            --shiny-bg: #04271a; --shiny-bg-subtle: #065f46; --shiny-fg: #ffffff;
            --shiny-hl: #1ed671; --shiny-hl-subtle: #86efac;
            --animation: gradient-angle linear infinite;
            --duration: 3s;
            --shadow-size: 2px;
            --transition: 800ms cubic-bezier(0.25, 1, 0.5, 1);
            isolation: isolate;
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 12px;
            color: var(--shiny-fg);
            border: 1px solid transparent;
            border-radius: 360px;
            background:
                linear-gradient(var(--shiny-bg), var(--shiny-bg)) padding-box,
                conic-gradient(
                    from calc(var(--gradient-angle) - var(--gradient-angle-offset)),
                    transparent,
                    var(--shiny-hl) var(--gradient-percent),
                    var(--gradient-shine) calc(var(--gradient-percent) * 2),
                    var(--shiny-hl) calc(var(--gradient-percent) * 3),
                    transparent calc(var(--gradient-percent) * 4)
                ) border-box;
            box-shadow: inset 0 0 0 1px var(--shiny-bg-subtle);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
            transition: var(--transition);
            transition-property: --gradient-angle-offset, --gradient-percent, --gradient-shine, box-shadow, opacity;
        }
        button::before, button::after {
            content: ""; pointer-events: none; position: absolute;
            inset-inline-start: 50%; inset-block-start: 50%;
            translate: -50% -50%; z-index: -1;
        }
        button:active:not(:disabled) { translate: 0 1px; }
        button::before {
            --size: calc(100% - var(--shadow-size) * 3);
            --position: 2px;
            --space: calc(var(--position) * 2);
            width: var(--size); height: var(--size);
            background: radial-gradient(circle at var(--position) var(--position), white calc(var(--position) / 4), transparent 0) padding-box;
            background-size: var(--space) var(--space);
            background-repeat: space;
            mask-image: conic-gradient(from calc(var(--gradient-angle) + 45deg), black, transparent 10% 90%, black);
            border-radius: inherit;
            opacity: 0.4;
        }
        button::after {
            --animation: shimmer linear infinite;
            width: 100%; aspect-ratio: 1;
            background: linear-gradient(-50deg, transparent, var(--shiny-hl), transparent);
            mask-image: radial-gradient(circle at bottom, transparent 40%, black);
            opacity: 0.6;
        }
        button, button::before, button::after {
            animation: var(--animation) var(--duration),
                       var(--animation) calc(var(--duration) / 0.4) reverse paused;
            animation-composition: add;
        }
        button:is(:hover, :focus-visible):not(:disabled) {
            --gradient-percent: 20%;
            --gradient-angle-offset: 95deg;
            --gradient-shine: var(--shiny-hl-subtle);
            box-shadow: inset 0 0 0 1px var(--shiny-bg-subtle),
                        inset 0 -1ex 1.4rem 0 color-mix(in srgb, var(--shiny-hl) 55%, transparent);
        }
        button:is(:hover, :focus-visible):not(:disabled),
        button:is(:hover, :focus-visible):not(:disabled)::before,
        button:is(:hover, :focus-visible):not(:disabled)::after { animation-play-state: running; }
        button:disabled { opacity: 0.55; cursor: not-allowed; }
        @keyframes gradient-angle { to { --gradient-angle: 360deg; } }
        @keyframes shimmer { to { rotate: 360deg; } }
        @media (prefers-reduced-motion: reduce) {
            button, button::before, button::after { animation: none !important; }
        }

        .footer { text-align: center; margin-top: 20px; font-size: 13px; color: #6b7280; }
        .footer a { color: #00A86B; font-weight: 600; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }

        .back { display: block; text-align: center; margin-top: 16px; font-size: 12px; color: #9ca3af; text-decoration: none; }
        .back:hover { color: #6b7280; }
    </style>
</head>
<body>
<div class="card">

    <div class="logo">
        <img src="logo.png" alt="Bluease">
    </div>

    <h2>Bienvenido</h2>
    <p class="subtitle">Accede a la plataforma interna.</p>

    <?php if ($tipoMsg): ?>
        <div class="alert alert-<?php echo htmlspecialchars($tipoMsg); ?>">
            <?php echo htmlspecialchars($textoMsg); ?>
        </div>
    <?php endif; ?>

    <form id="formLogin" method="POST" action="login.php">
        <label for="usuario">Usuario o email</label>
        <input type="text" id="usuario" name="usuario" placeholder="tu@email.com" required autocomplete="username">

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required autocomplete="current-password">

        <button type="submit" id="btnLogin">Iniciar sesión</button>
    </form>

    <p class="footer">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    <a href="https://bluease.io" class="back">← Volver a bluease.io</a>
</div>

<script>
    document.getElementById('formLogin').addEventListener('submit', function() {
        var btn = document.getElementById('btnLogin');
        btn.disabled = true;
        btn.textContent = 'Verificando...';
    });
</script>
</body>
</html>
