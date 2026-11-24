<?php
/**
 * Conexión centralizada a MySQL mediante PDO.
 * En un hosting, reemplaza estos datos por los entregados por tu proveedor.
 */
require_once __DIR__ . '/config.php';

$dbHost = 'localhost';
$dbName = 'peluqueria_pwa';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $error) {
    // No se muestran credenciales ni detalles internos al visitante.
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa config/database.php y database.sql.');
}
