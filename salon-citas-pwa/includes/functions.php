<?php
/**
 * Funciones reutilizables para presentación, mensajes y protección CSRF.
 */
require_once __DIR__ . '/../config/config.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    $saved = $_SESSION['csrf_token'] ?? '';
    if (!$sent || !$saved || !hash_equals($saved, $sent)) {
        http_response_code(419);
        exit('La solicitud expiró o no es válida. Regresa e inténtalo nuevamente.');
    }
}

function status_label(string $status): string {
    $labels = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
        'completada' => 'Completada'
    ];
    return $labels[$status] ?? ucfirst($status);
}

function status_class(string $status): string {
    $classes = [
        'pendiente' => 'text-bg-warning',
        'confirmada' => 'text-bg-success',
        'rechazada' => 'text-bg-danger',
        'cancelada' => 'text-bg-secondary',
        'completada' => 'text-bg-primary'
    ];
    return $classes[$status] ?? 'text-bg-secondary';
}
