<?php
/**
 * Configuración general del sistema.
 * Cambia APP_URL si instalas el proyecto en una carpeta o dominio diferente.
 */
define('APP_NAME', 'Belleza Citas');
define('APP_URL', 'http://localhost/salon-citas-pwa');

date_default_timezone_set('America/Guayaquil');

if (session_status() === PHP_SESSION_NONE) {
    // Configuración básica de la sesión para reducir riesgos de acceso indebido.
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax'
    ]);
    session_start();
}
