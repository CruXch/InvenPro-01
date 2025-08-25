<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tipo_informe'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
    $tipoInforme = $_POST['tipo_informe'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    // Guardamos los parámetros en sesión para que la vista los use
    $_SESSION['tipo_informe'] = $tipoInforme;
    $_SESSION['fecha_inicio'] = $fechaInicio;
    $_SESSION['fecha_fin'] = $fechaFin;

    // Redirigimos a la vista correspondiente
    if ($tipoInforme === "detallado") {
        header("Location: ../Vista/5_02_confirmarInforme.php");
    } else {
        header("Location: ../Vista/5_03_confirmarInforme2.php");
    }
    exit;
} else {
    echo "<p>Error: faltan parámetros.</p>";
}

$con->close();



