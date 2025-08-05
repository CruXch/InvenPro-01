<?php
session_start();
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: ../Vista/1_01_login.html"); // Redirige al login
exit();

