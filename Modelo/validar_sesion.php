<?php
// Inicia la sesión solo si aún no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configura las cabeceras HTTP para evitar que el navegador guarde en caché la página
// Esto impide que el usuario pueda volver a las vistas protegidas usando el botón "Atrás"
// después de haber cerrado sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verifica que exista la variable de sesión "id_usuario"
// Si no existe, significa que el usuario no ha iniciado sesión
// y se redirige automáticamente al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Vista/1_01_login.html");
    exit();
}


