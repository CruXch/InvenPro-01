<?php
// Iniciamos la sesión para poder manipularla y luego cerrarla
session_start();

// Eliminamos todas las variables almacenadas en la sesión
session_unset();

// Destruimos por completo la sesión activa
session_destroy();

// Una vez cerrada la sesión, enviamos al usuario de regreso a la pantalla de login
header("Location: ../Vista/1_01_login.html");

// Cerramos el script para asegurarnos de que no se ejecute nada más
exit;



