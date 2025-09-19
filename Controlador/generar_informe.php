<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

// Verificamos que la petición llegue por POST y que existan los parámetros esperados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tipo_informe'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
    // Capturamos los datos enviados desde el formulario
    $tipoInforme = $_POST['tipo_informe'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    // Guardamos los parámetros en variables de sesión para que puedan ser utilizados en la vista
    $_SESSION['tipo_informe'] = $tipoInforme;
    $_SESSION['fecha_inicio'] = $fechaInicio;
    $_SESSION['fecha_fin'] = $fechaFin;

    // Redirigimos según el tipo de informe seleccionado
    if ($tipoInforme === "detallado") {
        header("Location: ../Vista/5_02_confirmarInforme.php");
    } else {
        header("Location: ../Vista/5_03_confirmarInforme2.php");
    }
    exit; // Importante: detenemos la ejecución después de redirigir
} else {
    // Si faltan parámetros en la petición, mostramos un mensaje de error
    echo "<p>Error: faltan parámetros.</p>";
}

// Cerramos la conexión con la base de datos
$con->close();




