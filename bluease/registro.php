<?php
session_start();
require __DIR__ . '/conexion.php';
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = htmlspecialchars(trim($_POST['usuario']));
    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pass_raw = $_POST['contrasena'];

    $errores = [];
    if (strlen($nombre) <= 4)        $errores[] = "El nombre debe tener más de 4 caracteres.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email no válido.";
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $pass_raw))
        $errores[] = "Contraseña: mínimo 8 caracteres, una mayúscula, un número y un símbolo.";

    if (empty($errores)) {
        try {
            $pass_hash = password_hash($pass_raw, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (:n, :e, :p, 0)");
            $stmt->execute([':n' => $nombre, ':e' => $email, ':p' => $pass_hash]);
            $mensaje = "exito|Cuenta creada. Ya puedes iniciar sesión.";
        } catch (PDOException $e) {
            $mensaje = $e->getCode() == 23000
                ? "error|Ese usuario o email ya está registrado."
                : "error|Error interno. Inténtalo más tarde.";
        }
    } else {
        $mensaje = "error|" . implode(" ", $errores);
    }
}

[$tipoMsg, $textoMsg] = $mensaje ? explode("|", $mensaje, 2) : ["", ""];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bluease — Registro</title>
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

        .hint { font-size: 11px; color: #9ca3af; margin-top: -12px; margin-bottom: 16px; }

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

    <h2>Crear cuenta</h2>
    <p class="subtitle">Regístrate para acceder a la plataforma.</p>

    <?php if ($tipoMsg): ?>
        <div class="alert alert-<?php echo htmlspecialchars($tipoMsg); ?>">
            <?php echo htmlspecialchars($textoMsg); ?>
        </div>
    <?php endif; ?>

    <form id="formRegistro" method="POST" action="registro.php" autocomplete="on">
        <label for="usuario">Nombre</label>
        <input type="text" id="usuario" name="usuario" placeholder="Tu nombre" required autocomplete="username"
            value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="tu@email.com" required autocomplete="email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required autocomplete="new-password">
        <p class="hint">Mínimo 8 caracteres, una mayúscula, un número y un símbolo</p>

        <button type="submit" id="btnSubmit">Crear cuenta</button>
    </form>

    <p class="footer">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    <a href="https://bluease.io" class="back">← Volver a bluease.io</a>
</div>

<script>
    document.getElementById('formRegistro').addEventListener('submit', function() {
        var btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.textContent = 'Creando cuenta...';
    });
</script>
</body>
</html>
