<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('Cliente');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('citas/nueva.php');
}
verify_csrf();

$user = current_user();
$servicioId = filter_input(INPUT_POST, 'id_servicio', FILTER_VALIDATE_INT);
$peluqueroId = filter_input(INPUT_POST, 'id_peluquero', FILTER_VALIDATE_INT);
$fecha = trim($_POST['fecha'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');

if (!$servicioId || !$peluqueroId || !$fecha || !$hora) {
    set_flash('danger', 'Completa todos los campos obligatorios.');
    redirect('citas/nueva.php');
}
if ($fecha < date('Y-m-d')) {
    set_flash('danger', 'No puedes reservar una fecha que ya pasó.');
    redirect('citas/nueva.php');
}

$service = $pdo->prepare('SELECT id FROM servicios WHERE id = ? AND activo = 1');
$service->execute([$servicioId]);
$staff = $pdo->prepare(
    "SELECT u.id FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
     WHERE u.id = ? AND r.nombre = 'Peluquero' AND u.activo = 1"
);
$staff->execute([$peluqueroId]);

if (!$service->fetch() || !$staff->fetch()) {
    set_flash('danger', 'El servicio o peluquero seleccionado no está disponible.');
    redirect('citas/nueva.php');
}

// Evita reservar el mismo horario para el mismo peluquero si la cita sigue activa.
$check = $pdo->prepare(
    "SELECT COUNT(*) FROM citas
     WHERE id_peluquero = :peluquero AND fecha = :fecha AND hora = :hora
     AND estado IN ('pendiente','confirmada')"
);
$check->execute(['peluquero' => $peluqueroId, 'fecha' => $fecha, 'hora' => $hora]);

if ((int)$check->fetchColumn() > 0) {
    set_flash('warning', 'Ese horario ya está ocupado. Selecciona otra hora.');
    redirect('citas/nueva.php');
}

$insert = $pdo->prepare(
    "INSERT INTO citas (id_cliente, id_peluquero, id_servicio, fecha, hora, estado, observaciones)
     VALUES (:cliente, :peluquero, :servicio, :fecha, :hora, 'pendiente', :observaciones)"
);
$insert->execute([
    'cliente' => $user['id'],
    'peluquero' => $peluqueroId,
    'servicio' => $servicioId,
    'fecha' => $fecha,
    'hora' => $hora,
    'observaciones' => $observaciones ?: null
]);

set_flash('success', 'Tu solicitud fue enviada. El peluquero debe confirmarla.');
redirect('citas/index.php');
