
<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$pageTitle = 'Panel principal';

// Verificar sesión
if (!$user || !isset($user['id'], $user['rol'], $user['nombre'])) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

// Variables iniciales
$citasHoy = 0;
$pendientes = 0;
$ultimasCitas = [];
$serviceCount = 0;

// CONSULTAS SEGÚN EL TIPO DE USUARIO
if ($user['rol'] === 'Peluquero') {

    // Citas de hoy
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM citas
        WHERE id_peluquero = ?
        AND fecha = CURDATE()
    ");

    $stmt->execute([$user['id']]);
    $citasHoy = (int) $stmt->fetchColumn();

    // Solicitudes pendientes
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM citas
        WHERE id_peluquero = ?
        AND estado = 'pendiente'
    ");

    $stmt->execute([$user['id']]);
    $pendientes = (int) $stmt->fetchColumn();

    // Últimas citas del peluquero
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.fecha,
            c.hora,
            c.estado,
            u.nombre AS cliente,
            s.nombre AS servicio
        FROM citas c
        INNER JOIN usuarios u ON u.id = c.id_cliente
        INNER JOIN servicios s ON s.id = c.id_servicio
        WHERE c.id_peluquero = :id
        ORDER BY c.fecha DESC, c.hora DESC
        LIMIT 5
    ");

} else {

    // Citas activas del cliente
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM citas
        WHERE id_cliente = ?
        AND estado IN ('pendiente', 'confirmada')
    ");

    $stmt->execute([$user['id']]);
    $pendientes = (int) $stmt->fetchColumn();

    // Últimas citas del cliente
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.fecha,
            c.hora,
            c.estado,
            p.nombre AS peluquero,
            s.nombre AS servicio
        FROM citas c
        INNER JOIN usuarios p ON p.id = c.id_peluquero
        INNER JOIN servicios s ON s.id = c.id_servicio
        WHERE c.id_cliente = :id
        ORDER BY c.fecha DESC, c.hora DESC
        LIMIT 5
    ");
}

// Obtener últimas citas
$stmt->execute(['id' => $user['id']]);
$ultimasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar servicios activos
$serviceCount = (int) $pdo->query("
    SELECT COUNT(*)
    FROM servicios
    WHERE activo = 1
")->fetchColumn();

// Cargar encabezado
require __DIR__ . '/includes/header.php';
?>

<!-- PANEL PRINCIPAL -->
<section class="hero-panel mb-4">

    <div>
        <p class="eyebrow">Panel de control</p>

        <h1 class="display-6 fw-bold mb-2">
            Hola, <?= e($user['nombre']) ?>
        </h1>

        <p class="mb-0 opacity-75">
            <?= $user['rol'] === 'Peluquero'
                ? 'Administra tu agenda y responde las solicitudes de tus clientes.'
                : 'Reserva tu próxima cita y consulta el estado de tus solicitudes.' ?>
        </p>
    </div>

    <?php if ($user['rol'] === 'Cliente'): ?>

        <a class="btn btn-light btn-lg"
           href="<?= e(APP_URL) ?>/citas/nueva.php">
            Reservar una cita
        </a>

    <?php else: ?>

        <a class="btn btn-light btn-lg"
           href="<?= e(APP_URL) ?>/citas/index.php">
            Ver agenda
        </a>

    <?php endif; ?>

</section>

<!-- TARJETAS INTERACTIVAS -->
<div class="row g-3 mb-4">

    <!-- TARJETA DE CITAS -->
    <div class="col-md-6">

        <a class="stat-card-link"
           href="<?= e(APP_URL) ?>/citas/index.php">

            <div class="stat-card">

                <span class="stat-label">
                    <?= $user['rol'] === 'Peluquero'
                        ? 'Citas para hoy'
                        : 'Citas activas' ?>
                </span>

                <strong>
                    <?= $user['rol'] === 'Peluquero'
                        ? $citasHoy
                        : $pendientes ?>
                </strong>

                <span class="stat-action">
                    Consultar mis citas →
                </span>

            </div>

        </a>

    </div>

    <!-- TARJETA DE SOLICITUDES Y SERVICIOS -->
    <div class="col-md-6">

        <a class="stat-card-link"
           href="<?= e(APP_URL) ?>/<?= $user['rol'] === 'Peluquero'
                ? 'citas/index.php'
                : 'servicios/index.php' ?>">

            <div class="stat-card">

                <span class="stat-label">
                    <?= $user['rol'] === 'Peluquero'
                        ? 'Solicitudes pendientes'
                        : 'Servicios disponibles' ?>
                </span>

                <strong>
                    <?= $user['rol'] === 'Peluquero'
                        ? $pendientes
                        : $serviceCount ?>
                </strong>

                <span class="stat-action">
                    <?= $user['rol'] === 'Peluquero'
                        ? 'Consultar solicitudes →'
                        : 'Ver todos los servicios →' ?>
                </span>

            </div>

        </a>

    </div>

</div>

<!-- ACTIVIDAD RECIENTE -->
<div class="card app-card border-0">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

            <div>
                <p class="eyebrow mb-1">Actividad reciente</p>

                <h2 class="h4 fw-bold mb-0">
                    Últimas citas
                </h2>
            </div>

            <a href="<?= e(APP_URL) ?>/citas/index.php"
               class="btn btn-outline-primary">
                Ver todas
            </a>

        </div>

        <?php if (empty($ultimasCitas)): ?>

            <div class="empty-state">

                <h3 class="h5">
                    Todavía no hay citas
                </h3>

                <p class="text-secondary mb-0">
                    <?= $user['rol'] === 'Cliente'
                        ? 'Reserva tu primera cita para verla aquí.'
                        : 'Las nuevas solicitudes aparecerán en esta sección.' ?>
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>

                            <th>
                                <?= $user['rol'] === 'Peluquero'
                                    ? 'Cliente'
                                    : 'Peluquero' ?>
                            </th>

                            <th>Servicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($ultimasCitas as $cita): ?>

                            <tr>

                                <td>
                                    <?= e(date(
                                        'd/m/Y',
                                        strtotime($cita['fecha'])
                                    )) ?>
                                </td>

                                <td>
                                    <?= e(substr($cita['hora'], 0, 5)) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $user['rol'] === 'Peluquero'
                                            ? $cita['cliente']
                                            : $cita['peluquero']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e($cita['servicio']) ?>
                                </td>

                                <td>
                                    <span class="badge <?= e(status_class($cita['estado'])) ?>">
                                        <?= e(status_label($cita['estado'])) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
