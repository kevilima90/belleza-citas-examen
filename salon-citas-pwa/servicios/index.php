
<?php

require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = current_user();
$pageTitle = 'Servicios';

// ==========================================
// OBTENER SERVICIOS ACTIVOS
// ==========================================

$stmt = $pdo->query("
    SELECT
        id,
        nombre,
        descripcion,
        duracion_minutos,
        precio
    FROM servicios
    WHERE activo = 1
    ORDER BY id ASC
");

$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// FOTOGRAFÍAS DE LOS SERVICIOS
// ==========================================

$imagenes = [
    'corte de cabello' =>
    'https://images.pexels.com/photos/3993443/pexels-photo-3993443.jpeg?auto=compress&dpr=1&h=750&w=1260',

    'peinado' =>
    'https://images.pexels.com/photos/7440126/pexels-photo-7440126.jpeg?auto=compress&dpr=1&h=750&w=1260',

    'tinte' =>
    'https://images.pexels.com/photos/3993320/pexels-photo-3993320.jpeg?auto=compress&dpr=1&h=750&w=1260',

    'tratamiento capilar' =>
    'https://images.pexels.com/photos/29189946/pexels-photo-29189946/free-photo-of-woman-receiving-hair-treatment-in-salon.jpeg?auto=compress&dpr=1&h=750&w=1260'
];

// Imagen predeterminada
$imagenPredeterminada = $imagenes['corte de cabello'];

// ==========================================
// CARGAR ENCABEZADO
// ==========================================

require __DIR__ . '/../includes/header.php';

?>

<style>

/* ==========================================
   PÁGINA DE SERVICIOS
========================================== */

.services-page {
    max-width: 1150px;
    margin: auto;
    padding-bottom: 30px;
}

/* ENCABEZADO */

.services-heading {
    margin-bottom: 30px;
}

.services-heading .eyebrow {
    color: #841d43;
    font-weight: 800;
    letter-spacing: 2px;
    font-size: 12px;
}

.services-heading h1 {
    color: #64132f;
    font-size: 34px;
    font-weight: 800;
    margin-bottom: 10px;
}

/* CONTENEDOR */

.services-list {
    background: white;
    border: 1px solid #eadde3;
    border-radius: 22px;
    padding: 10px 30px;
    box-shadow: 0 10px 30px rgba(70,20,45,.07);
}

/* FILAS DE SERVICIOS */

.service-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px;
    align-items: center;
    gap: 30px;
    padding: 28px 5px;
}

.service-row + .service-row {
    border-top: 1px solid #eee5e9;
}

/* INFORMACIÓN */

.service-info {
    min-width: 0;
}

.service-category {
    color: #a66a83;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.service-info h2 {
    color: #45152b;
    font-size: 24px;
    font-weight: 800;
    margin: 10px 0;
}

.service-info p {
    color: #766b72;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 16px;
}

/* PRECIO Y DURACIÓN */

.service-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}

.service-duration {
    color: #817780;
    font-size: 14px;
}

.service-price {
    color: #64132f;
    font-size: 23px;
    font-weight: 850;
}

/* FOTOGRAFÍAS */

.service-photo {
    width: 220px;
    height: 165px;
    overflow: hidden;
    border-radius: 18px;
    background: #f5e8ed;
}

.service-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .35s ease;
}

.service-photo:hover img {
    transform: scale(1.07);
}

/* BOTÓN */

.service-select {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border-radius: 30px;
    background: #64132f;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 750;
    transition: all .25s ease;
}

.service-select:hover {
    background: #8a254b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(100,19,47,.2);
}

/* SERVICIOS VACÍOS */

.services-empty {
    text-align: center;
    padding: 55px 20px;
    color: #766b72;
}

.services-empty h3 {
    color: #64132f;
    font-weight: 800;
    margin-bottom: 10px;
}

/* BOTÓN VOLVER */

.services-back {
    margin-top: 25px;
}

.services-back a {
    border-radius: 25px;
    padding: 10px 22px;
}

/* ==========================================
   DISEÑO RESPONSIVE
========================================== */

@media (max-width: 768px) {

    .services-heading h1 {
        font-size: 28px;
    }

    .services-list {
        padding: 8px 18px;
    }

    .service-row {
        grid-template-columns: minmax(0, 1fr) 150px;
        gap: 15px;
        padding: 22px 0;
    }

    .service-photo {
        width: 150px;
        height: 140px;
    }

    .service-info h2 {
        font-size: 20px;
    }

    .service-info p {
        font-size: 14px;
    }

}

@media (max-width: 480px) {

    .services-list {
        padding: 8px 12px;
    }

    .service-row {
        grid-template-columns: minmax(0, 1fr) 110px;
        gap: 12px;
        padding: 20px 0;
    }

    .service-photo {
        width: 110px;
        height: 125px;
    }

    .service-info h2 {
        font-size: 17px;
    }

    .service-info p {
        font-size: 12px;
        line-height: 1.5;
    }

    .service-price {
        font-size: 18px;
    }

    .service-duration {
        font-size: 12px;
    }

    .service-details {
        flex-wrap: wrap;
    }

    .service-select {
        padding: 10px 12px;
        font-size: 12px;
        width: 100%;
    }

}

/* EVITAR IMÁGENES DESBORDADAS */

.service-photo img {
    max-width: 100%;
}

</style>

<!-- ==========================================
     CONTENIDO PRINCIPAL
========================================== -->

<div class="services-page">

    <!-- ENCABEZADO -->

    <div class="services-heading">

        <p class="eyebrow">
            NUESTRA CARTA
        </p>

        <h1>
            Servicios disponibles
        </h1>

        <p class="text-secondary">
            Descubre nuestros tratamientos y reserva
            el momento perfecto para ti.
        </p>

    </div>

    <!-- LISTADO -->

    <div class="services-list">

        <?php if (empty($servicios)): ?>

            <div class="services-empty">

                <h3>
                    No hay servicios disponibles
                </h3>

                <p>
                    Actualmente no tenemos servicios publicados.
                    Vuelve a consultar más tarde.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($servicios as $servicio): ?>

                <?php

                // Normalizar nombre del servicio
                $nombre = mb_strtolower(
                    trim($servicio['nombre']),
                    'UTF-8'
                );

                // Buscar fotografía correspondiente
                $imagen = $imagenes[$nombre] ?? $imagenPredeterminada;

                ?>

                <article class="service-row">

                    <!-- INFORMACIÓN DEL SERVICIO -->

                    <div class="service-info">

                        <span class="service-category">
                            BELLEZA Y CUIDADO
                        </span>

                        <h2>
                            <?= e($servicio['nombre']) ?>
                        </h2>

                        <p>
                            <?= e($servicio['descripcion'] ?? 'Servicio profesional de belleza y cuidado personal.') ?>
                        </p>

                        <!-- PRECIO Y DURACIÓN -->

                        <div class="service-details">

                            <span class="service-duration">
                                <?= (int) $servicio['duracion_minutos'] ?>
                                minutos
                            </span>

                            <strong class="service-price">
                                $<?= number_format(
                                    (float) $servicio['precio'],
                                    2
                                ) ?>
                            </strong>

                        </div>

                        <!-- BOTÓN DE RESERVA -->

                        <?php if ($user['rol'] === 'Cliente'): ?>

                            <a
                                class="service-select"
                                href="<?= e(APP_URL) ?>/citas/nueva.php?id_servicio=<?= (int) $servicio['id'] ?>"
                            >
                                Elegir este servicio →
                            </a>

                        <?php elseif ($user['rol'] === 'Peluquero'): ?>

                            <span class="text-secondary small">
                                Servicio disponible para reservas de clientes.
                            </span>

                        <?php endif; ?>

                    </div>

                    <!-- FOTOGRAFÍA -->

                    <div class="service-photo">

                        <img
                            src="<?= e($imagen) ?>"
                            alt="<?= e($servicio['nombre']) ?>"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='<?= e($imagenPredeterminada) ?>';"
                        >

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <!-- VOLVER AL PANEL -->

    <div class="services-back">

        <a
            href="<?= e(APP_URL) ?>/dashboard.php"
            class="btn btn-outline-primary"
        >
            ← Volver al panel principal
        </a>

    </div>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
