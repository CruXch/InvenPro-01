<?php
function conexion() {
    // Parámetros de conexión
    $host = "localhost";   // Servidor donde está la BD (en XAMPP es localhost)
    $user = "root";        // Usuario de la BD (en XAMPP es root)
    $pass = "";            // Contraseña del usuario (en XAMPP es "" que significa vacio)
    $bd   = "bd_invenpro"; // Nombre de la base de datos

    // Crea la conexión con los datos de arriba
    $conectar = mysqli_connect($host, $user, $pass, $bd);

    // Verificar si hubo error en la conexión
    if (!$conectar) {
        die("Error en la conexión: " . mysqli_connect_error());
        // mysqli_connect_error devuelve el mensaje del error
        // die() detiene la ejecución del script y muestra el mensaje
    }

    // Configurar la zona horaria en PHP
    // Esto asegura que todas las funciones date() o DateTime usen la hora correcta
    date_default_timezone_set('America/Bogota');

    // Configurar la zona horaria en MySQL
    // Esto hace que NOW(), CURRENT_TIMESTAMP, etc. también usen la misma zona horaria
    mysqli_query($conectar, "SET time_zone = '-05:00'");

    // Retornar el objeto de conexión para que se use en las consultas
    return $conectar;
}

//Función para la fecha en las pantallas de los usuarios con los dias de la semana en español
function fechaConDia() {
    $dias = [
        'Sunday' => 'Domingo',
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado'
    ];

    return date("d/m/Y") . " - " . $dias[date("l")];
}


