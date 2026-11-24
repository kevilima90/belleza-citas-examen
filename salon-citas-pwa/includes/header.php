<?php
$user = current_user();
$flash = get_flash();
$isAuthPage = $isAuthPage ?? false;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#64132f">
    <meta name="description" content="Sistema web de reservas y administración de citas para peluquería.">
    <title><?= e($pageTitle ?? APP_NAME) ?> | <?= e(APP_NAME) ?></title>
    <link rel="manifest" href="<?= e(APP_URL) ?>/manifest.json">
    <link rel="icon" href="<?= e(APP_URL) ?>/assets/icons/icon-192.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(APP_URL) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body data-app-url="<?= e(APP_URL) ?>" class="<?= $isAuthPage ? 'auth-page-body' : 'app-page-body' ?>">
<?php if (!$isAuthPage): ?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(APP_URL) ?>/dashboard.php">Belleza Citas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($user): ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= e(APP_URL) ?>/dashboard.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(APP_URL) ?>/citas/index.php">Citas</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(APP_URL) ?>/servicios/index.php">Servicios</a></li>
                    <?php if ($user['rol'] === 'Cliente'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(APP_URL) ?>/citas/nueva.php">Reservar cita</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="navbar-text small"><?= e($user['nombre']) ?> · <?= e($user['rol']) ?></span>
                    <button id="installAppBtn" class="btn btn-sm btn-outline-light" type="button" hidden>Instalar app</button>
                    <form method="post" action="<?= e(APP_URL) ?>/logout.php" class="m-0">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-light" type="submit">Salir</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="ms-auto d-flex gap-2">
                    <a class="btn btn-outline-light btn-sm" href="<?= e(APP_URL) ?>/login.php">Iniciar sesión</a>
                    <a class="btn btn-light btn-sm" href="<?= e(APP_URL) ?>/registro.php">Crear cuenta</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="<?= $isAuthPage ? 'auth-main' : 'container py-4' ?>">
    <?php if ($flash && !$isAuthPage): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
