<?php
// Conexión a la base de datos
require_once ("../Modelo/conexion.php");
$con = conexion();

// Consulta que agrupa por fecha exacta de ingreso (con hora)
$sql = "SELECT fecha_ingreso, COUNT(*) AS total_productos 
        FROM tbl_Ingresos_Csv 
        GROUP BY fecha_ingreso 
        ORDER BY fecha_ingreso DESC";

$resultado = mysqli_query($con, $sql);

// Establecer el locale a español para formatear la fecha
setlocale(LC_TIME, 'spanish');

// Recorremos los grupos encontrados
while ($fila = mysqli_fetch_assoc($resultado)) {
    $fechaCompleta = $fila['fecha_ingreso']; // Incluye hora
    $fechaCodificada = urlencode($fechaCompleta); // Para usar en URL

    // Formatear la fecha en español con strftime
    $fecha = new DateTime($fechaCompleta);
    $formateada = strftime("%A, %e de %B de %Y - %I:%M %p", $fecha->getTimestamp());
    $formateada = ucfirst($formateada); // Primera letra en mayúscula

    echo '<a class="infoConteo" href="3_02_03_asignarProductos.php?fecha=' . $fechaCodificada . '">';
    echo 'Archivo - ' . $formateada;
    echo '</a>';
}
?>



