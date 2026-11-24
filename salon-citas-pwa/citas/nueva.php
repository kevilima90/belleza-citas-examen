
<?php

require_once __DIR__ . '/../includes/auth.php';
require_role('Cliente');

$pageTitle = 'Reservar cita';

$user = current_user();

// ======================================
// OBTENER PELUQUEROS ACTIVOS
// ======================================

$stmt = $pdo->query("
    SELECT
        u.id,
        u.nombre
    FROM usuarios u
    INNER JOIN roles r ON r.id = u.rol_id
    WHERE r.nombre = 'Peluquero'
    AND u.activo = 1
    ORDER BY u.nombre ASC
");

$peluqueros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================================
// OBTENER SERVICIOS ACTIVOS
// ======================================

$stmt = $pdo->query("
    SELECT
        id,
        nombre,
        descripcion,
        duracion_minutos,
        precio
    FROM servicios
    WHERE activo = 1
    ORDER BY nombre ASC
");

$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================================
// RECIBIR SERVICIO SELECCIONADO
// ======================================

$servicioSeleccionado = filter_var(
    $_GET['id_servicio'] ?? 0,
    FILTER_VALIDATE_INT
);

if (!$servicioSeleccionado || $servicioSeleccionado < 1) {
    $servicioSeleccionado = 0;
}

// ======================================
// COMPROBAR SERVICIO SELECCIONADO
// ======================================

$servicioElegido = null;

foreach ($servicios as $servicio) {

    if ((int) $servicio['id'] === (int) $servicioSeleccionado) {

        $servicioElegido = $servicio;
        break;

    }

}

// ======================================
// CARGAR ENCABEZADO
// ======================================

require __DIR__ . '/../includes/header.php';

?>

<!-- ENCABEZADO -->
<div class="row justify-content-center">

    <div class="col-lg-9">

        <div class="mb-4">

            <p class="eyebrow">NUEVA RESERVA</p>

            <h1 class="h2 fw-bold">
                Agenda tu cita
            </h1>

            <p class="text-secondary">
                Completa los datos para reservar tu servicio de belleza.
            </p>

        </div>

        <!-- FORMULARIO -->
        <div class="card app-card border-0">

            <div class="card-body p-4 p-lg-5">

                <?php if (empty($peluqueros) || empty($servicios)): ?>

                    <div class="alert alert-warning">

                        <strong>Atención:</strong>

                        Aún no hay peluqueros o servicios activos.
                        Contacta con el establecimiento.

                    </div>

                <?php else: ?>

                    <!-- SERVICIO SELECCIONADO -->
                    <?php if ($servicioElegido): ?>

                        <div class="alert alert-success mb-4">

                            <strong>Servicio seleccionado:</strong>

                            <?= e($servicioElegido['nombre']) ?>

                            <br>

                            Duración:
                            <?= (int) $servicioElegido['duracion_minutos'] ?>
                            minutos

                            <br>

                            Precio:
                            $<?= number_format(
                                (float) $servicioElegido['precio'],
                                2
                            ) ?>

                        </div>

                    <?php endif; ?>

                    <!-- FORMULARIO DE RESERVA -->
                    <form
                        method="POST"
                        action="<?= e(APP_URL) ?>/citas/guardar.php"
                        id="bookingForm"
                    >

                        <?= csrf_field() ?>

                        <!-- SERVICIO -->
                        <div class="mb-4">

                            <label
                                class="form-label"
                                for="id_servicio"
                            >
                                Selecciona tu servicio
                            </label>

                            <select
                                class="form-select"
                                id="id_servicio"
                                name="id_servicio"
                                required
                            >

                                <option value="">
                                    Selecciona un servicio
                                </option>

                                <?php foreach ($servicios as $servicio): ?>

                                    <option
                                        value="<?= (int) $servicio['id'] ?>"
                                        <?= (int) $servicio['id'] === (int) $servicioSeleccionado
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= e($servicio['nombre']) ?>

                                        · $<?= number_format(
                                            (float) $servicio['precio'],
                                            2
                                        ) ?>

                                        · <?= (int) $servicio['duracion_minutos'] ?>
                                        min

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- PELUQUERO -->
                        <div class="mb-4">

                            <label
                                class="form-label"
                                for="id_peluquero"
                            >
                                Selecciona tu peluquero
                            </label>

                            <select
                                class="form-select"
                                id="id_peluquero"
                                name="id_peluquero"
                                required
                            >

                                <option value="">
                                    Selecciona un peluquero
                                </option>

                                <?php foreach ($peluqueros as $peluquero): ?>

                                    <option
                                        value="<?= (int) $peluquero['id'] ?>"
                                    >
                                        <?= e($peluquero['nombre']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- FECHA Y HORA -->
                        <div class="row">

                            <!-- FECHA -->
                            <div class="col-md-6 mb-4">

                                <label
                                    class="form-label"
                                    for="fecha"
                                >
                                    Fecha de tu cita
                                </label>

                                <input
                                    class="form-control"
                                    type="date"
                                    id="fecha"
                                    name="fecha"
                                    min="<?= date('Y-m-d') ?>"
                                    required
                                >

                            </div>

                            <!-- HORA -->
                            <div class="col-md-6 mb-4">

                                <label
                                    class="form-label"
                                    for="hora"
                                >
                                    Hora disponible
                                </label>

                                <select
                                    class="form-select"
                                    id="hora"
                                    name="hora"
                                    required
                                >

                                    <option value="">
                                        Selecciona una hora
                                    </option>

                                    <?php

                                    $horarios = [
                                        '09:00',
                                        '10:00',
                                        '11:00',
                                        '12:00',
                                        '14:00',
                                        '15:00',
                                        '16:00',
                                        '17:00'
                                    ];

                                    foreach ($horarios as $hora):

                                    ?>

                                        <option value="<?= e($hora) ?>">
                                            <?= e($hora) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="mb-4">

                            <label
                                class="form-label"
                                for="observaciones"
                            >
                                Observaciones (opcional)
                            </label>

                            <textarea
                                class="form-control"
                                id="observaciones"
                                name="observaciones"
                                rows="4"
                                maxlength="500"
                                placeholder="Escribe algún detalle que debamos conocer."
                            ></textarea>

                        </div>

                        <!-- BOTONES -->
                        <div class="d-grid gap-3">

                            <button
                                class="btn btn-primary btn-lg"
                                type="submit"
                            >
                                Confirmar solicitud de cita
                            </button>

                            <a
                                href="<?= e(APP_URL) ?>/servicios/index.php"
                                class="btn btn-outline-primary"
                            >
                                Volver a los servicios
                            </a>

                        </div>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
