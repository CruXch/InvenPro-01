<?php
// Conexión a la base de datos
require_once ("../Modelo/conexion.php");
$con = conexion();

// Consulta SQL para obtener la cantidad de productos agrupados por fecha de ingreso
// Incluye hora exacta (no solo la fecha) para diferenciar registros
$sql = "SELECT fecha_ingreso, COUNT(*) AS total_productos 
        FROM tbl_Ingresos_Csv 
        GROUP BY fecha_ingreso 
        ORDER BY fecha_ingreso DESC";

$resultado = mysqli_query($con, $sql);

// Configuramos el locale en español para que strftime muestre la fecha en español
setlocale(LC_TIME, 'spanish');

// Recorremos los registros agrupados por fecha
while ($fila = mysqli_fetch_assoc($resultado)) {
    $fechaCompleta = $fila['fecha_ingreso'];             // Fecha completa con hora exacta
    $fechaCodificada = urlencode($fechaCompleta);        // Codificamos la fecha para usarla en la URL

    // Convertimos la fecha a objeto DateTime
    $fecha = new DateTime($fechaCompleta);

    // Formateamos la fecha en español (ej: "Lunes, 12 de Febrero de 2025 - 03:45 PM")
    $formateada = strftime("%A, %e de %B de %Y - %I:%M %p", $fecha->getTimestamp());
    $formateada = ucfirst($formateada); // Pone la primera letra en mayúscula

    // Generamos el enlace para cada fecha, que lleva a la pantalla de asignación de productos
    echo '<a class="infoConteo" href="3_02_03_asignarProductos.php?fecha=' . $fechaCodificada . '">';
    echo 'Archivo - ' . $formateada;
    echo '</a>';
}




