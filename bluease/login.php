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

        button {
            width: 100%;
            padding: 11px;
            background: #1ed671;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
        }
        button:hover:not(:disabled) { background: #1191db; }
        button:disabled { background: #d1d5db; cursor: not-allowed; }

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
