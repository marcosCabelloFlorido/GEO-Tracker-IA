<?php
$host     = 'localhost';
$dbname   = 'bluease';
$user     = 'root';
$password = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conexion = new PDO($dsn, $user, $password, $opciones);
} catch (PDOException $e) {
    error_log("[Bluease DB] " . $e->getMessage());
    die("Error de conexión con la base de datos.");
}
