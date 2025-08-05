<?php
include("../Modelo/conexion.php");
$con = conexion();

if (isset($_GET['fecha'])) {
    $fechaRecibida = urldecode($_GET['fecha']);
    $fechaEscapada = mysqli_real_escape_string($con, $fechaRecibida);

    $sql = "SELECT p.nombre_producto, i.marca_ingreso, i.stock_ingreso
            FROM tbl_Ingresos_Csv i
            INNER JOIN tbl_productos p ON i.id_producto = p.id_producto
            WHERE i.fecha_ingreso = '$fechaEscapada'";

    $resultado = mysqli_query($con, $sql);

    // Formatear la fecha en español
    $fecha = new DateTime($fechaRecibida);
    setlocale(LC_TIME, 'spanish'); // Esto es válido en Windows con XAMPP
    $fechaFormateada = strftime("%A, %e de %B de %Y - %I:%M %p", $fecha->getTimestamp());

    echo "<h2>Productos ingresados el: " . ucfirst($fechaFormateada) . "</h2>";

    echo "<table border='1'>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th>Stock</th>
            </tr>";

    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
                <td>" . htmlspecialchars($fila['nombre_producto']) . "</td>
                <td>" . htmlspecialchars($fila['marca_ingreso']) . "</td>
                <td>" . htmlspecialchars($fila['stock_ingreso']) . "</td>
              </tr>";
    }

    echo "</table>";

} else {
    echo "No se recibió una fecha válida.";
}
?>

