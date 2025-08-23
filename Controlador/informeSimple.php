<?php
// Conexión a la base de datos
include ("../Modelo/conexion.php");
$con = conexion();

// Consulta para obtener toda la información necesaria de las tablas relacionadas
$sql = "SELECT 
    ti.id_toma,
    u.nombre_usuario,
    u.id_usuario,
    ti.fecha_toma,
    ti.unidades_toma,
    ti.comentarios_toma,
    ti.diferencia_toma,
    ic.id_ingreso,
    ic.id_producto,
    ic.nombre_ingreso,
    ic.marca_ingreso,
    ic.stock_ingreso
FROM Tbl_Productos_Usuarios_Toma put
INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
INNER JOIN tbl_Ingresos_Csv ic ON put.id_ingreso = ic.id_ingreso
ORDER BY ti.id_toma ASC";

$resultado = $con->query($sql);

// Inicializamos variables para contar y guardar datos importantes
$total_productos_asignados = 0;       // Total de productos que deben contarse
$total_productos_contados = 0;        // Productos que efectivamente tienen un conteo registrado

$total_diferencias_negativas = 0;    // Cantidad de productos con diferencia negativa (menos en físico)
$total_diferencias_positivas = 0;    // Cantidad de productos con diferencia positiva (más en físico)

$discrepancias_negativas = [];       // Arreglo para guardar productos con diferencias negativas
$discrepancias_positivas = [];       // Arreglo para guardar productos con diferencias positivas

if ($resultado->num_rows > 0) {
    // Recorremos cada fila del resultado para procesar la información
    while ($fila = $resultado->fetch_assoc()) {
        $total_productos_asignados++;  // Contamos que hay un producto más para analizar

        // Si hay unidades contadas (no vacío), aumentamos el conteo de productos contados
        if (!empty($fila["unidades_toma"])) {
            $total_productos_contados++;
        }

        // Obtenemos la diferencia precalculada (conteo físico - stock)
        $diferencia = $fila["diferencia_toma"];

        // Si la diferencia es negativa (menos en conteo físico)
        if ($diferencia < 0) {
            $total_diferencias_negativas++;  // Contamos una diferencia negativa

            // Guardamos la información para luego mostrar el Top 5
            $discrepancias_negativas[] = [
                'producto' => $fila["nombre_ingreso"],
                'id' => $fila["id_producto"],
                'idToma' => $fila["id_toma"],
                'idIngreso' => $fila["id_ingreso"],
                'unidadesToma' => $fila["unidades_toma"],
                'unidadesStock' => $fila["stock_ingreso"],
                'diferencia' => $diferencia
            ];
        } 
        // Si la diferencia es positiva (más en conteo físico)
        elseif ($diferencia > 0) {
            $total_diferencias_positivas++;  // Contamos una diferencia positiva

            // Guardamos info para el Top 5 de diferencias positivas
            $discrepancias_positivas[] = [
                'producto' => $fila["nombre_ingreso"],
                'id' => $fila["id_producto"],
                'idToma' => $fila["id_toma"],
                'idIngreso' => $fila["id_ingreso"],
                'unidadesToma' => $fila["unidades_toma"],
                'unidadesStock' => $fila["stock_ingreso"],
                'diferencia' => $diferencia
            ];
        }
        // Si la diferencia es cero, no la contamos ni guardamos
    }

    // Calculamos total de productos con diferencias (positivas + negativas)
    $total_diferencias = $total_diferencias_negativas + $total_diferencias_positivas;

    // Calculamos porcentajes basándonos en el total de productos asignados
    $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
    $porcentaje_diferencias_negativas = ($total_diferencias_negativas / $total_productos_asignados) * 100;
    $porcentaje_diferencias_positivas = ($total_diferencias_positivas / $total_productos_asignados) * 100;
    $porcentaje_diferencias_totales = ($total_diferencias / $total_productos_asignados) * 100;

    // Ordenamos las discrepancias negativas de la más grande en valor absoluto (más negativa) a la menos
    usort($discrepancias_negativas, function($a, $b) {
        return $a['diferencia'] <=> $b['diferencia']; // Orden ascendente (ej: -20, -10, -5)
    });
    $top_negativas = array_slice($discrepancias_negativas, 0, 5); // Tomamos solo las 5 peores negativas

    // Ordenamos las discrepancias positivas de mayor a menor
    usort($discrepancias_positivas, function($a, $b) {
        return $b['diferencia'] <=> $a['diferencia']; // Orden descendente (ej: 20, 15, 10)
    });
    $top_positivas = array_slice($discrepancias_positivas, 0, 5); // Tomamos las 5 mayores positivas

    // Mostramos el resumen de resultados en HTML
    echo "<h2>Informe simple de inventario</h2>";
    echo "<ul>";
    echo "<li><strong>Total de productos asignados para contar:</strong> $total_productos_asignados</li>";
    echo "<li><strong>Total de productos contados:</strong> $total_productos_contados</li>";
    echo "<li><strong>% de ejecución productos contados:</strong> " . round($porcentaje_ejecucion, 2) . "%</li>";
    echo "<li><strong>Total de productos con diferencias negativas:</strong> $total_diferencias_negativas</li>";
    echo "<li><strong>% de productos con diferencias negativas:</strong> " . round($porcentaje_diferencias_negativas, 2) . "%</li>";
    echo "<li><strong>Total de productos con diferencias positivas:</strong> $total_diferencias_positivas</li>";
    echo "<li><strong>% de productos con diferencias positivas:</strong> " . round($porcentaje_diferencias_positivas, 2) . "%</li>";
    echo "<li><strong>Total de productos con diferencias (negativas + positivas):</strong> $total_diferencias</li>";
    echo "<li><strong>% de productos con diferencias totales:</strong> " . round($porcentaje_diferencias_totales, 2) . "%</li>";
    echo "</ul>";

    // Mostramos el Top 5 de productos con más diferencias negativas
    echo "<h3>Top 5 productos con mayor discrepancia negativa</h3>";
    echo "<ol>";
    foreach ($top_negativas as $item) {
        echo "<li>{$item['producto']} (ID: {$item['id']}) -- (ID Toma: {$item['idToma']}) (ID Ingreso: {$item['idIngreso']}) -- 
        Cantidad virtual SAP: {$item['unidadesStock']} ~ Cantidad conteo físico: {$item['unidadesToma']} |--| Diferencia: {$item['diferencia']}</li>";
    }
    echo "</ol>";

    // Mostramos el Top 5 de productos con más diferencias positivas
    echo "<h3>Top 5 productos con mayor discrepancia positiva</h3>";
    echo "<ol>";
    foreach ($top_positivas as $item) {
        echo "<li>{$item['producto']} (ID: {$item['id']}) -- (ID Toma: {$item['idToma']}) (ID Ingreso: {$item['idIngreso']}) -- 
        Cantidad virtual SAP: {$item['unidadesStock']} ~ Cantidad conteo físico: {$item['unidadesToma']} |--| Diferencia: +{$item['diferencia']}</li>";
    }
    echo "</ol>";

} else {
    // En caso de que no existan registros en la consulta
    echo "<p>No hay registros disponibles.</p>";
}

// Cerramos la conexión a la base de datos
$con->close();
?>


