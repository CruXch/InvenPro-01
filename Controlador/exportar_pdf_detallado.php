<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");

// Carga automática de clases instaladas con Composer (en este caso Dompdf)
require_once __DIR__ . '/../vendor/autoload.php';

// Se importan las clases necesarias de Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// Llama a la función de conexión a la base de datos
$con = conexion();

// Inicia la sesión si aún no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recupera las fechas almacenadas en la sesión (rango de consulta)
$fechaInicio = $_SESSION['fecha_inicio'] ?? null;
$fechaFin    = $_SESSION['fecha_fin'] ?? null;

// Variables iniciales para almacenar los resultados
$filas = [];
$total_productos_asignados = 0;
$total_productos_contados = 0;
$total_diferencias_negativas = 0;
$total_diferencias_positivas = 0;
$discrepancias_negativas = [];
$discrepancias_positivas = [];

$top_negativas = [];
$top_positivas = [];

$mensaje_error = "";
$fecha_actual = date("Y-m-d");

// --- Lógica de consulta y procesamiento ---
if ($fechaInicio && $fechaFin) {
    // Consulta SQL para obtener los registros en el rango de fechas
    $sql = "SELECT 
                ti.id_toma,
                u.nombre_usuario,
                u.apellido_usuario,
                u.id_usuario,
                ti.fecha_toma,
                ic.id_ingreso,
                ic.id_producto,
                ic.nombre_ingreso,
                ic.marca_ingreso,
                ic.stock_ingreso,
                ti.unidades_toma,
                ti.comentarios_toma,
                ti.diferencia_toma
            FROM Tbl_Productos_Usuarios_Toma put
            INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
            INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
            INNER JOIN tbl_Ingresos_Csv ic ON put.id_ingreso = ic.id_ingreso
            WHERE ti.fecha_toma BETWEEN '$fechaInicio' AND '$fechaFin'
            ORDER BY ti.id_toma ASC";

    // Ejecuta la consulta
    $resultado = $con->query($sql);

    // Verifica si hay resultados
    if ($resultado->num_rows > 0) {
        // Recorre todos los registros
        while ($fila = $resultado->fetch_assoc()) {
            $filas[] = $fila; // Guarda cada fila en el arreglo

            $total_productos_asignados++; // Aumenta el total asignado
            if (!empty($fila["unidades_toma"])) {
                $total_productos_contados++; // Aumenta si fue contado
            }

            // Clasifica las diferencias entre SAP y conteo físico
            $diferencia = $fila["diferencia_toma"];
            if ($diferencia < 0) {
                $total_diferencias_negativas++;
                $discrepancias_negativas[] = $fila;
            } elseif ($diferencia > 0) {
                $total_diferencias_positivas++;
                $discrepancias_positivas[] = $fila;
            }
        }

        // Cálculos estadísticos
        $total_diferencias = $total_diferencias_negativas + $total_diferencias_positivas;
        $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
        $porcentaje_diferencias_negativas = ($total_diferencias_negativas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_positivas = ($total_diferencias_positivas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_totales = ($total_diferencias / $total_productos_asignados) * 100;

        // Ordena discrepancias para obtener el top 5 de cada caso
        usort($discrepancias_negativas, fn($a, $b) => $a['diferencia_toma'] <=> $b['diferencia_toma']);
        $top_negativas = array_slice($discrepancias_negativas, 0, 5);

        usort($discrepancias_positivas, fn($a, $b) => $b['diferencia_toma'] <=> $a['diferencia_toma']);
        $top_positivas = array_slice($discrepancias_positivas, 0, 5);

    } else {
        // Si no hay registros
        $mensaje_error = "No hay registros disponibles en ese rango de fechas.";
    }
} else {
    // Si faltan fechas
    $mensaje_error = "Faltan parámetros de fecha.";
}

// --- Generar HTML para el PDF ---
$html = '<h1 style="text-align:center;">Informe Detallado - InvenPro</h1>';
$html .= '<p><strong>Rango de fechas:</strong> '.$fechaInicio.' | '.$fechaFin.'</p> <br>';

if ($mensaje_error) {
    // Muestra mensaje de error en el PDF
    $html .= '<p>'.$mensaje_error.'</p>';
} else {
    // Recorre cada registro y lo agrega al HTML
    foreach ($filas as $fila) {
        $html .= '<p><strong>ID Toma:</strong> '.$fila["id_toma"].'</p>';
        $html .= '<p><strong>Trabajador:</strong> '.$fila["nombre_usuario"].' '.$fila["apellido_usuario"].' (<strong>ID: </strong>'.$fila["id_usuario"].')</p>';
        $html .= '<p><strong>Fecha toma:</strong> '.date("d/m/Y", strtotime($fila["fecha_toma"])).'</p>';
        $html .= '<p><strong>Nombre Producto:</strong> '.$fila["nombre_ingreso"].' | <strong>ID Producto:</strong> '.$fila["id_producto"].' | <strong>Marca:</strong> '.$fila["marca_ingreso"].'</p>';
        $html .= '<p><strong>Cantidad registrada:</strong> '.$fila["unidades_toma"].' | <strong>Unidades SAP:</strong> '.$fila["stock_ingreso"].' | <strong>Diferencia:</strong> '.$fila["diferencia_toma"].'</p>';
        $html .= '<p><strong>Comentarios:</strong> '.$fila["comentarios_toma"].'</p>';
        $html .= '<hr>';
    }

    // Sección de resumen
    $html .= '<h2>Resumen del informe</h2>';
    $html .= '<p><strong>Total asignados:</strong> '.$total_productos_asignados.'</p>';
    $html .= '<p><strong>Total contados:</strong> '.$total_productos_contados.'</p>';
    $html .= '<p><strong>% de ejecución:</strong> '.round($porcentaje_ejecucion,2).'%</p>';
    $html .= '<p><strong>Diferencias negativas:</strong> '.$total_diferencias_negativas.' ('.round($porcentaje_diferencias_negativas,2).'%)</p>';
    $html .= '<p><strong>Diferencias positivas:</strong> '.$total_diferencias_positivas.' ('.round($porcentaje_diferencias_positivas,2).'%)</p>';
    $html .= '<p><strong>Total diferencias:</strong> '.$total_diferencias.' ('.round($porcentaje_diferencias_totales,2).'%)</p> <br>';

    // Top 5 discrepancias negativas
    $html .= '<h3>Top 5 discrepancias negativas</h3>';
    foreach ($top_negativas as $negativo) {
        $html .= '<p>'.$negativo['nombre_ingreso'].' (ID: '.$negativo['id_producto'].') (ID Toma: '.$negativo['id_toma'].') -- SAP: '.$negativo['stock_ingreso'].' | Conteo: '.$negativo['unidades_toma'].' | Diferencia: '.$negativo['diferencia_toma'].'</p>';
    } 

    // Top 5 discrepancias positivas
    $html .= '<h3>Top 5 discrepancias positivas</h3>';
    foreach ($top_positivas as $positivo) {
        $html .= '<p>'.$positivo['nombre_ingreso'].' (ID: '.$positivo['id_producto'].') (ID Toma: '.$positivo['id_toma'].') -- SAP: '.$positivo['stock_ingreso'].' | Conteo: '.$positivo['unidades_toma'].' | Diferencia: +'.$positivo['diferencia_toma'].'</p>';
    }
}

// --- Crear PDF con Dompdf ---
// Se configuran las opciones para permitir carga remota de recursos (como imágenes externas)
$options = new Options();
$options->set('isRemoteEnabled', true);

// Se crea la instancia de Dompdf
$dompdf = new Dompdf($options);

// Se carga el contenido HTML preparado
$dompdf->loadHtml($html);

// Se establece el tamaño y orientación del papel
$dompdf->setPaper('A4', 'portrait');

// Se renderiza el PDF
$dompdf->render();

// Se envía el PDF al navegador para su descarga automática
$dompdf->stream("Informe_Detallado_$fecha_actual.pdf", ["Attachment" => true]);





