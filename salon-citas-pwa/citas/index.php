<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = current_user();
$pageTitle = 'Mis citas';

if ($user['rol'] === 'Peluquero') {
    $stmt = $pdo->prepare(
        'SELECT c.*, cli.nombre AS cliente, cli.telefono, s.nombre AS servicio
         FROM citas c
         INNER JOIN usuarios cli ON cli.id = c.id_cliente
         INNER JOIN servicios s ON s.id = c.id_servicio
         WHERE c.id_peluquero = :id
         ORDER BY c.fecha DESC, c.hora DESC'
    );
} else {
    $stmt = $pdo->prepare(
        'SELECT c.*, pel.nombre AS peluquero, s.nombre AS servicio
         FROM citas c
         INNER JOIN usuarios pel ON pel.id = c.id_peluquero
         INNER JOIN servicios s ON s.id = c.id_servicio
         WHERE c.id_cliente = :id
         ORDER BY c.fecha DESC, c.hora DESC'
    );
}
$stmt->execute(['id' => $user['id']]);
$citas = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <p class="eyebrow mb-1">Agenda</p>
        <h1 class="h2 fw-bold mb-0"><?= $user['rol'] === 'Peluquero' ? 'Citas de clientes' : 'Mis citas' ?></h1>
    </div>
    <?php if ($user['rol'] === 'Cliente'): ?>
        <a class="btn btn-primary" href="<?= e(APP_URL) ?>/citas/nueva.php">Nueva reserva</a>
    <?php endif; ?>
</div>

<div class="card app-card border-0">
    <div class="card-body p-3 p-md-4">
        <?php if (!$citas): ?>
            <div class="empty-state">
                <h2 class="h5">No hay citas registradas</h2>
                <p class="text-secondary mb-0">Cuando se genere una reserva, aparecerá en esta lista.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th><?= $user['rol'] === 'Peluquero' ? 'Cliente' : 'Peluquero' ?></th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td class="text-nowrap"><?= e(date('d/m/Y', strtotime($cita['fecha']))) ?><br><span class="small text-secondary"><?= e(substr($cita['hora'], 0, 5)) ?></span></td>
                            <td><?= e($cita[$user['rol'] === 'Peluquero' ? 'cliente' : 'peluquero']) ?></td>
                            <td><?= e($cita['servicio']) ?></td>
                            <td><span class="badge <?= e(status_class($cita['estado'])) ?>"><?= e(status_label($cita['estado'])) ?></span></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                <?php if ($user['rol'] === 'Peluquero' && $cita['estado'] === 'pendiente'): ?>
                                    <?php foreach (['confirmada' => 'Confirmar', 'rechazada' => 'Rechazar'] as $value => $label): ?>
                                        <form method="post" action="actualizar_estado.php">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
                                            <input type="hidden" name="estado" value="<?= e($value) ?>">
                                            <button class="btn btn-sm <?= $value === 'confirmada' ? 'btn-success' : 'btn-outline-danger' ?>" type="submit"><?= e($label) ?></button>
                                        </form>
                                    <?php endforeach; ?>
                                <?php elseif ($user['rol'] === 'Peluquero' && $cita['estado'] === 'confirmada'): ?>
                                    <form method="post" action="actualizar_estado.php">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
                                        <input type="hidden" name="estado" value="completada">
                                        <button class="btn btn-sm btn-primary" type="submit">Marcar completada</button>
                                    </form>
                                <?php elseif ($user['rol'] === 'Cliente' && in_array($cita['estado'], ['pendiente','confirmada'], true) && $cita['fecha'] >= date('Y-m-d')): ?>
                                    <form method="post" action="actualizar_estado.php" class="confirm-action" data-confirm="¿Deseas cancelar esta cita?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Cancelar</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-secondary small">Sin acciones</span>
                                <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
