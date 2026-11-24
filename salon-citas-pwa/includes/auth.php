<?php
/**
 * Control de acceso por sesión y por rol.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function current_user(): ?array {
    return $_SESSION['usuario'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        set_flash('warning', 'Inicia sesión para continuar.');
        redirect('login.php');
    }
}

function require_role(string ...$roles): void {
    require_login();
    $currentRole = current_user()['rol'] ?? '';
    if (!in_array($currentRole, $roles, true)) {
        http_response_code(403);
        exit('No tienes permisos para acceder a esta sección.');
    }
}
