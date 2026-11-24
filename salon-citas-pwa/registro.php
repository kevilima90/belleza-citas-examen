<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('dashboard.php');
}

$pageTitle = 'Crear cuenta';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmacion = $_POST['confirmacion'] ?? '';

    if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $telefono === '') {
        $error = 'Completa correctamente todos los datos.';
    } elseif (strlen($contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($contrasena !== $confirmacion) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        try {
            // El registro público siempre crea una cuenta de Cliente.
            $rol = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'Cliente' LIMIT 1");
            $rol->execute();
            $rolId = $rol->fetchColumn();

            $insert = $pdo->prepare(
                'INSERT INTO usuarios (nombre, correo, telefono, contrasena, rol_id)
                 VALUES (:nombre, :correo, :telefono, :contrasena, :rol_id)'
            );
            $insert->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'telefono' => $telefono,
                'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
                'rol_id' => $rolId
            ]);
            set_flash('success', 'Tu cuenta fue creada. Ya puedes iniciar sesión.');
            redirect('login.php');
        } catch (PDOException $exception) {
            $error = 'No se pudo crear la cuenta. Verifica que el correo no esté registrado.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center auth-wrap">
    <div class="col-md-8 col-lg-6">
        <div class="card app-card border-0">
            <div class="card-body p-4 p-lg-5">
                <p class="eyebrow mb-2">Registro de clientes</p>
                <h1 class="h3 fw-bold mb-2">Crear una cuenta</h1>
                <p class="text-secondary mb-4">Regístrate para solicitar tus citas en línea.</p>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="nombre">Nombre completo</label>
                        <input class="form-control" id="nombre" name="nombre" maxlength="100" required value="<?= e($_POST['nombre'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label" for="correo">Correo electrónico</label>
                            <input class="form-control" type="email" id="correo" name="correo" required value="<?= e($_POST['correo'] ?? '') ?>">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input class="form-control" id="telefono" name="telefono" maxlength="20" required value="<?= e($_POST['telefono'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="contrasena">Contraseña (mínimo 8 caracteres)</label>
                        <input class="form-control" type="password" id="contrasena" name="contrasena" minlength="8" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="confirmacion">Confirmar contraseña</label>
                        <input class="form-control" type="password" id="confirmacion" name="confirmacion" minlength="8" required>
                    </div>
                    <button class="btn btn-primary btn-lg w-100" type="submit">Crear cuenta</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
