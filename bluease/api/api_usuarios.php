<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
 
// Solo admin (rol 2) puede acceder a esta API
if (!isset($_SESSION['rol']) || (int)$_SESSION['rol'] !== 2) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado. Se requiere rol de administrador.']);
    exit();
}
 
require __DIR__ . '/../conexion.php';
 
// ── GET → Listar todos los usuarios ──────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt    = $conexion->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY rol DESC, nombre ASC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $total     = count($usuarios);
        $admins    = 0;
        $empleados = 0;
        $externos  = 0;
        foreach ($usuarios as $u) {
            $r = (int)$u['rol'];
            if ($r === 2)      $admins++;
            elseif ($r === 1)  $empleados++;
            else               $externos++;
        }
 
        echo json_encode([
            'ok'       => true,
            'usuarios' => $usuarios,
            'stats'    => [
                'total'     => $total,
                'admins'    => $admins,
                'empleados' => $empleados,
                'externos'  => $externos,
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error al obtener usuarios: ' . $e->getMessage()]);
    }
    exit();
}
 
// ── POST → Actualizar rol de un usuario ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
 
    $id  = isset($body['id'])  ? (int)$body['id']  : 0;
    $rol = isset($body['rol']) ? (int)$body['rol']  : -1;
 
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de usuario inválido.']);
        exit();
    }
    if (!in_array($rol, [0, 1, 2], true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Rol inválido. Valores permitidos: 0, 1, 2.']);
        exit();
    }
 
    // Verificar que el usuario existe
    $stmt = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if (!$target) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Usuario no encontrado.']);
        exit();
    }
 
    // Un admin no puede quitarse su propio rol
    if ($target['nombre'] === $_SESSION['usuario'] && $rol !== 2) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'No puedes quitarte a ti mismo el rol de administrador.']);
        exit();
    }
 
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET rol = :rol WHERE id = :id");
        $stmt->execute([':rol' => $rol, ':id' => $id]);
 
        $roles  = [0 => 'Externo', 1 => 'Empleado', 2 => 'Admin'];
        echo json_encode([
            'ok'      => true,
            'mensaje' => 'Rol de "' . htmlspecialchars($target['nombre']) . '" actualizado a ' . $roles[$rol] . '.'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error al actualizar el rol: ' . $e->getMessage()]);
    }
    exit();
}
 
http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
