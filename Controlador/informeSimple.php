<?php
// Conexión a la base de datos
include ("../Modelo/conexion.php");
$con = conexion();

// Consulta SQL con JOIN entre tablas relacionadas
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
    ic.nombre_ingreso,
    ic.marca_ingreso,
    ic.stock_ingreso
FROM Tbl_Productos_Usuarios_Toma put
INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
INNER JOIN Tbl_Productos p ON put.id_producto = p.id_producto
INNER JOIN tbl_Ingresos_Csv ic ON put.id_ingreso = ic.id_ingreso
ORDER BY ti.id_toma ASC";

$resultado = $con->query($sql);

// variables para métricas
$total_productos_asignados = 0;
$total_productos_contados = 0;
$total_diferencias = 0;
$discrepancias = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $total_productos_asignados++;

        // Contar productos con unidades registradas
        if (!empty($fila["unidades_toma"])) {
            $total_productos_contados++;
        }

        // Contar productos con diferencia
        if (abs($fila["diferencia_toma"]) > 0) {
            $total_diferencias++;
        }

        //Guardar discrepancias para el top
        $discrepancias[] = [
            'producto' => $fila["nombre_producto"],
            'id' => $fila["id_producto"],
            'diferencia' => abs($fila["diferencia_toma"])
        ];
    }

    //Cálculos porcentuales
    $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
    $porcentaje_diferencias = ($total_diferencias / $total_productos_asignados) * 100;

    //Ordenar discrepancias y obtener top 5
    usort($discrepancias, function($a, $b) {
        return $b['diferencia'] <=> $a['diferencia'];
    });
    $top_discrepancias = array_slice($discrepancias, 0, 5);

    //Mostrar resumen en HTML
    echo "<h2>Informe simple de inventario</h2>";
    echo "<ul>";
    echo "<li><strong>Total de productos asignados:</strong> $total_productos_asignados</li>";
    echo "<li><strong>Total de productos contados:</strong> $total_productos_contados</li>";
    echo "<li><strong>% de ejecución:</strong> " . round($porcentaje_ejecucion, 2) . "%</li>";
    echo "<li><strong>Total con diferencias:</strong> $total_diferencias</li>";
    echo "<li><strong>% con diferencias:</strong> " . round($porcentaje_diferencias, 2) . "%</li>";
    echo "</ul>";

    echo "<h3>Top 5 productos con mayor discrepancia</h3>";
    echo "<ol>";
    foreach ($top_discrepancias as $item) {
        echo "<li>{$item['producto']} (ID: {$item['id']}) - Diferencia: {$item['diferencia']}</li>";
    }
    echo "</ol>";
} else {
    echo "<p>No hay registros disponibles.</p>";
}

//
$con->close();
?>