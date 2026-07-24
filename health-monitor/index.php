<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirigir a dashboard si ya está logueado
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Redirigir a login por defecto
header('Location: login.php');
exit();
?>