<?php
// Conexión a la base de datos
require_once ("../Modelo/conexion.php");
$con = conexion();

// Consulta SQL para obtener la cantidad de productos agrupados por fecha de ingreso
// Incluye hora exacta (no solo la fecha) para diferenciar registros
$sql = "SELECT fecha_ingreso, COUNT(*) AS total_productos 
        FROM Tbl_Ingresos_Csv 
        GROUP BY fecha_ingreso 
        ORDER BY fecha_ingreso DESC";

$resultado = mysqli_query($con, $sql);

// Recorremos los registros agrupados por fecha
while ($fila = mysqli_fetch_assoc($resultado)) {
    $fechaCompleta = $fila['fecha_ingreso'];             // Fecha completa con hora exacta
    $fechaCodificada = urlencode($fechaCompleta);        // Codificamos la fecha para usarla en la URL

    // Convertimos la fecha a objeto DateTime
    $fecha = new DateTime($fechaCompleta);

    // Usamos IntlDateFormatter para formatear la fecha en español
    $formatter = new IntlDateFormatter(
        'es_ES', 
        IntlDateFormatter::FULL, 
        IntlDateFormatter::SHORT,
        'America/Bogota',
        IntlDateFormatter::GREGORIAN,
        "EEEE, d 'de' MMMM 'de' yyyy - hh:mm a"
    );

    $formateada = ucfirst($formatter->format($fecha));

    // Generamos el enlace para cada fecha
    echo '<a class="infoConteo" href="3_02_03_asignarProductos.php?fecha=' . $fechaCodificada . '">';
    echo 'Archivo - ' . $formateada;
    echo '</a>';
}





