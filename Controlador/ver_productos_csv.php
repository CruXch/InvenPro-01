<?php
include("../Modelo/conexion.php");
$con = conexion();

// Verifica si se recibió una fecha por el método GET
if (isset($_GET['fecha'])) {
    // Decodifica la fecha recibida desde la URL
    $fechaRecibida = urldecode($_GET['fecha']);

    // Escapa la fecha para evitar inyecciones SQL
    $fechaEscapada = mysqli_real_escape_string($con, $fechaRecibida);

    // Consulta para obtener productos ingresados en la fecha indicada
    $sql = "SELECT p.nombre_producto, i.marca_ingreso, i.stock_ingreso
            FROM tbl_Ingresos_Csv i
            INNER JOIN tbl_productos p ON i.id_producto = p.id_producto
            WHERE i.fecha_ingreso = '$fechaEscapada'";

    $resultado = mysqli_query($con, $sql);

    // Crea un objeto DateTime a partir de la fecha recibida
    $fecha = new DateTime($fechaRecibida);

    // Establece el idioma español para el formato de fecha
    setlocale(LC_TIME, 'spanish'); // Nota: válido en Windows con XAMPP

    // Formatea la fecha en estilo largo con día, mes, año y hora
    $fechaFormateada = strftime("%A, %e de %B de %Y - %I:%M %p", $fecha->getTimestamp());

    // Muestra el encabezado con la fecha formateada
    echo "<h2>Productos ingresados el: " . ucfirst($fechaFormateada) . "</h2>";

    // Construye una tabla HTML para mostrar los resultados
    echo "<table border='1'>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th>Stock</th>
            </tr>";

    // Recorre los resultados y los imprime en filas de la tabla
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
                <td>" . htmlspecialchars($fila['nombre_producto']) . "</td>
                <td>" . htmlspecialchars($fila['marca_ingreso']) . "</td>
                <td>" . htmlspecialchars($fila['stock_ingreso']) . "</td>
              </tr>";
    }

    // Cierra la tabla
    echo "</table>";

} else {
    // Si no se recibe la fecha por GET, muestra un mensaje de error
    echo "No se recibió una fecha válida.";
}



