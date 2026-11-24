<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('citas/index.php');
}
verify_csrf();

$user = current_user();
$citaId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$newStatus = $_POST['estado'] ?? '';

if (!$citaId) {
    set_flash('danger', 'La cita indicada no es válida.');
    redirect('citas/index.php');
}

$stmt = $pdo->prepare('SELECT id, id_cliente, id_peluquero, estado, fecha FROM citas WHERE id = ?');
$stmt->execute([$citaId]);
$cita = $stmt->fetch();

if (!$cita) {
    set_flash('danger', 'No se encontró la cita.');
    redirect('citas/index.php');
}

if ($user['rol'] === 'Cliente') {
    // El cliente solo puede cancelar sus propias citas futuras activas.
    if ((int)$cita['id_cliente'] !== (int)$user['id']
        || !in_array($newStatus, ['cancelada'], true)
        || !in_array($cita['estado'], ['pendiente','confirmada'], true)
        || $cita['fecha'] < date('Y-m-d')) {
        http_response_code(403);
        exit('No tienes permiso para modificar esta cita.');
    }
} elseif ($user['rol'] === 'Peluquero') {
    // El peluquero solo administra las citas que le fueron asignadas.
    $allowed = [
        'pendiente' => ['confirmada','rechazada'],
        'confirmada' => ['completada']
    ];
    if ((int)$cita['id_peluquero'] !== (int)$user['id']
        || !in_array($newStatus, $allowed[$cita['estado']] ?? [], true)) {
        http_response_code(403);
        exit('No tienes permiso para modificar esta cita.');
    }
} else {
    http_response_code(403);
    exit('Rol no autorizado.');
}

$update = $pdo->prepare('UPDATE citas SET estado = :estado, actualizado_en = NOW() WHERE id = :id');
$update->execute(['estado' => $newStatus, 'id' => $citaId]);

set_flash('success', 'El estado de la cita fue actualizado.');
redirect('citas/index.php');
