<?php
require_once __DIR__ . '/includes/auth.php';

// Envía al usuario a la pantalla que corresponde según su sesión.
redirect(current_user() ? 'dashboard.php' : 'login.php');
