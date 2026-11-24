<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('dashboard.php');
}

$pageTitle = 'Iniciar sesión';
$error = '';
$isAuthPage = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT u.id, u.nombre, u.correo, u.contrasena, r.nombre AS rol
         FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.correo = :correo AND u.activo = 1 LIMIT 1'
    );
    $stmt->execute(['correo' => $correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        session_regenerate_id(true);
        unset($usuario['contrasena']);
        $_SESSION['usuario'] = $usuario;
        redirect('dashboard.php');
    }

    $error = 'Correo o contraseña incorrectos. Verifica los datos e inténtalo nuevamente.';
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout">
    <aside class="auth-visual">
        <div class="auth-visual-photo" aria-hidden="true"></div>
        <div class="auth-visual-shade" aria-hidden="true"></div>

        <div class="visual-brand">
            <div class="visual-monogram"><span>BC</span><i>✦</i></div>
            <div class="visual-brand-name">Belleza Citas</div>
            <div class="visual-brand-caption"><span></span>SISTEMA DE CITAS<span></span></div>
        </div>

        <div class="visual-copy">
            <p class="visual-kicker">BIENVENIDO A BELLEZA CITAS</p>
            <h1>Tu momento de belleza, organizado en un <em>solo lugar.</em></h1>
            <p class="visual-description">
                Reserva servicios, administra horarios y consulta tus citas de forma sencilla.
            </p>
        </div>

        <div class="visual-features">
            <div class="visual-feature">
                <span class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M7.5 3v4M16.5 3v4M3.5 9.5h17M8 14l2.5 2.5 5-5"/></svg>
                </span>
                <span>Reserva<br>tus servicios</span>
            </div>
            <div class="visual-feature">
                <span class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l4 2.5"/></svg>
                </span>
                <span>Administra<br>tus horarios</span>
            </div>
            <div class="visual-feature">
                <span class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 21c-4.5-4.2-7.5-8-7.5-11.2A4.2 4.2 0 0 1 12 7.4a4.2 4.2 0 0 1 7.5 2.4C19.5 13 16.5 16.8 12 21Z"/><path d="M12 7.5c-1.8 2.2-2.7 4.2-2.7 6M12 7.5c1.8 2.2 2.7 4.2 2.7 6"/></svg>
                </span>
                <span>Consulta<br>tus citas</span>
            </div>
        </div>

        <div class="visual-bottom">
            <span class="bottom-line"></span>
            <span>EXPERIENCIA · BELLEZA · BIENESTAR</span>
        </div>
    </aside>

    <section class="auth-workspace">
        <div class="auth-right-content">
            <section class="login-card">
                <div class="login-brand">
                    <div class="login-monogram">BC<span>✦</span></div>
                    <div class="login-brand-name">Belleza Citas</div>
                    <div class="login-brand-rule"><span></span><span></span></div>
                </div>

                <h2>Iniciar sesión</h2>
                <p class="login-subtitle">Ingresa a tu cuenta para continuar</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger login-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" id="loginForm" class="login-form">
                    <?= csrf_field() ?>
                    <div class="login-field">
                        <label for="correo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                            Correo electrónico
                        </label>
                        <input type="email" id="correo" name="correo" required autocomplete="username"
                               placeholder="Ingresa tu correo electrónico"
                               value="<?= e($_POST['correo'] ?? '') ?>">
                    </div>

                    <div class="login-field">
                        <label for="contrasena">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
                            Contraseña
                        </label>
                        <div class="password-wrap">
                            <input type="password" id="contrasena" name="contrasena" required
                                   autocomplete="current-password" placeholder="Ingresa tu contraseña">
                            <button class="password-toggle" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.7"/><path class="eye-slash" d="m4 4 16 16"/></svg>
                            </button>
                        </div>
                    </div>

                    <button class="login-submit" type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M13 3h5a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-5"/></svg>
                        Entrar al sistema
                    </button>
                </form>

                <div class="login-register">
                    ¿Aún no tienes cuenta?
                    <a href="<?= e(APP_URL) ?>/registro.php">Crear cuenta de cliente</a>
                </div>
            </section>

            <section class="demo-panel">
                <div class="demo-heading">
                    <span class="demo-heading-line"></span>
                    <span class="demo-heading-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M16 20v-1.5a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4V20M9 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 20v-1.5a4 4 0 0 0-3-3.87M16 2.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <h3>Usuarios de prueba</h3>
                    <span class="demo-heading-line"></span>
                </div>

                <div class="demo-cards">
                    <article class="demo-account demo-staff">
                        <div class="demo-account-heading">
                            <span class="demo-account-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="m8.2 8.2 7.6 7.6M15.8 8.2 8.2 15.8M3 21 21 3"/></svg>
                            </span>
                            <div><h4>Acceso peluquero</h4><small>Administración de agenda</small></div>
                        </div>
                        <div class="demo-credential"><span>Correo:</span><code>peluquero@demo.com</code></div>
                        <div class="demo-credential"><span>Contraseña:</span><code>Peluquero123!</code></div>
                        <button class="demo-use" type="button" data-fill-demo
                                data-demo-email="peluquero@demo.com" data-demo-password="Peluquero123!">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
                            Usar cuenta de peluquero
                        </button>
                    </article>

                    <article class="demo-account demo-client">
                        <div class="demo-account-heading">
                            <span class="demo-account-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
                            </span>
                            <div><h4>Acceso cliente</h4><small>Reservas y seguimiento de citas</small></div>
                        </div>
                        <div class="demo-credential"><span>Correo:</span><code>cliente@demo.com</code></div>
                        <div class="demo-credential"><span>Contraseña:</span><code>Cliente123!</code></div>
                        <button class="demo-use" type="button" data-fill-demo
                                data-demo-email="cliente@demo.com" data-demo-password="Cliente123!">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
                            Usar cuenta de cliente
                        </button>
                    </article>
                </div>

                <div class="demo-notice">
                    <span class="notice-info">i</span>
                    <p>El docente puede usar estas cuentas para revisar y calificar el sistema.</p>
                </div>
            </section>

            <div class="auth-footer">
                <span>© <?= date('Y') ?> Belleza Citas</span>
                <span>Aplicación web progresiva · Proyecto académico</span>
            </div>
        </div>
    </section>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
