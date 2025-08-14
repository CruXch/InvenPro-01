<?php
// Conexión a la base de datos
include ("../Modelo/conexion.php");
$con = conexion();

// Consulta con JOIN entre tablas
// Consulta SQL con JOIN sobre la tabla puente
$sql = "SELECT 
            ti.id_toma,
            u.nombre_usuario,
            u.id_usuario,
            ti.fecha_toma,
            p.id_producto,
            p.nombre_producto,
            ti.unidades_toma,
            ti.comentarios_toma,
            ti.diferencia_toma,
            ic.nombre_ingreso,       -- Agregado desde tbl_Ingresos_Csv
            ic.marca_ingreso,        -- Agregado desde tbl_Ingresos_Csv
            ic.stock_ingreso        -- Agregado desde tbl_Ingresos_Csv
        FROM Tbl_Productos_Usuarios_Toma put
        INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
        INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
        INNER JOIN Tbl_Productos p ON put.id_producto = p.id_producto
        LEFT JOIN tbl_Ingresos_Csv ic ON p.id_producto = ic.id_producto    -- JOIN con tbl_Ingresos_Csv
        ORDER BY ti.fecha_toma ASC";

$resultado = $con->query($sql);

// Generar tabla HTML
if ($resultado->num_rows > 0) {
    echo '<table class="tablaInforme">';
    echo '<thead>
            <tr>
                <th>Id Toma</th>
                <th>Usuario</th>
                <th>Id Usuario</th>
                <th>Fecha</th>
                <th>Id Producto</th>
                <th>Producto</th>
                <th>Unidades</th>
                <th>Comentarios</th>
                <th>Diferencia SAP</th>
                <th>NOMBRE INGRESO</th>
                <th>MARCA INGRESO</th>
                <th>STOCK INGRESO</th>
            </tr>
          </thead>';
    echo '<tbody>';
    while ($fila = $resultado->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $fila["id_toma"] . '</td>';
        echo '<td>' . $fila["nombre_usuario"] . '</td>';
        echo '<td>' . $fila["id_usuario"] . '</td>';
        echo '<td>' . date("d/m/Y", strtotime($fila["fecha_toma"])) . '</td>';
        echo '<td>' . $fila["id_producto"] . '</td>';
        echo '<td>' . $fila["nombre_producto"] . '</td>';
        echo '<td>' . $fila["unidades_toma"] . '</td>';
        echo '<td>' . $fila["comentarios_toma"] . '</td>';
        echo '<td>' . $fila["diferencia_toma"] . '</td>';
        echo '<td>' . $fila["nombre_ingreso"] . '</td>';
        echo '<td>' . $fila["marca_ingreso"] . '</td>';
        echo '<td>' . $fila["stock_ingreso"] . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo "<p>No hay registros disponibles.</p>";
}

$con->close();

/*  

SELECT 
    ti.id_toma,
    u.nombre_usuario,
    u.id_usuario,
    ti.fecha_toma,
    p.id_producto,
    p.nombre_producto,
    ti.unidades_toma,
    ti.comentarios_toma,
    ti.diferencia_toma,
    ic.nombre_ingreso,
    ic.marca_ingreso,
    ic.stock_ingreso
FROM Tbl_Productos_Usuarios_Toma put
INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
INNER JOIN Tbl_Productos p ON put.id_producto = p.id_producto
INNER JOIN tbl_Ingresos_Csv ic ON put.id_ingreso = ic.id_ingreso  -- Relacionamos con el ingreso específico
ORDER BY ti.fecha_toma ASC;


*/
?>





