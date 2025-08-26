<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$con = conexion();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fechaInicio = $_SESSION['fecha_inicio'] ?? null;
$fechaFin    = $_SESSION['fecha_fin'] ?? null;

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
            WHERE ti.fecha_toma BETWEEN '$fechaInicio' AND '$fechaFin'
            ORDER BY ti.id_toma ASC";

    $resultado = $con->query($sql);

    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $filas[] = $fila;

            $total_productos_asignados++;
            if (!empty($fila["unidades_toma"])) {
                $total_productos_contados++;
            }

            $diferencia = $fila["diferencia_toma"];
            if ($diferencia < 0) {
                $total_diferencias_negativas++;
                $discrepancias_negativas[] = $fila;
            } elseif ($diferencia > 0) {
                $total_diferencias_positivas++;
                $discrepancias_positivas[] = $fila;
            }
        }

        $total_diferencias = $total_diferencias_negativas + $total_diferencias_positivas;
        $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
        $porcentaje_diferencias_negativas = ($total_diferencias_negativas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_positivas = ($total_diferencias_positivas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_totales = ($total_diferencias / $total_productos_asignados) * 100;

        // Ordenar discrepancias
        usort($discrepancias_negativas, fn($a, $b) => $a['diferencia_toma'] <=> $b['diferencia_toma']);
        $top_negativas = array_slice($discrepancias_negativas, 0, 5);

        usort($discrepancias_positivas, fn($a, $b) => $b['diferencia_toma'] <=> $a['diferencia_toma']);
        $top_positivas = array_slice($discrepancias_positivas, 0, 5);

    } else {
        $mensaje_error = "No hay registros disponibles en ese rango de fechas.";
    }
} else {
    $mensaje_error = "Faltan parámetros de fecha.";
}

// --- Generar HTML para el PDF ---
$html = '<h1 style="text-align:center;">Informe Simple - InvenPro</h1>';
$html .= '<p><strong>Rango de fechas:</strong> '.$fechaInicio.' | '.$fechaFin.'</p><br>';

if ($mensaje_error) {
    $html .= '<p>'.$mensaje_error.'</p>';
} else {
    $html .= '<h2>Resumen del informe</h2>';
    $html .= '<p><strong>Total asignados:</strong> '.$total_productos_asignados.'</p>';
    $html .= '<p><strong>Total contados:</strong> '.$total_productos_contados.'</p>';
    $html .= '<p><strong>% de ejecución:</strong> '.round($porcentaje_ejecucion,2).'%</p>';
    $html .= '<p><strong>Diferencias negativas:</strong> '.$total_diferencias_negativas.' ('.round($porcentaje_diferencias_negativas,2).'%)</p>';
    $html .= '<p><strong>Diferencias positivas:</strong> '.$total_diferencias_positivas.' ('.round($porcentaje_diferencias_positivas,2).'%)</p>';
    $html .= '<p><strong>Total diferencias:</strong> '.$total_diferencias.' ('.round($porcentaje_diferencias_totales,2).'%)</p><br>';

    $html .= '<h3>Top 5 discrepancias negativas</h3>';
    if (count($top_negativas) > 0) {
        foreach ($top_negativas as $negativo) {
            $html .= '<p>'.$negativo['nombre_ingreso'].' (ID: '.$negativo['id_producto'].') (ID Toma: '.$negativo['id_toma'].') -- SAP: '.$negativo['stock_ingreso'].' | Conteo: '.$negativo['unidades_toma'].' | Diferencia: '.$negativo['diferencia_toma'].'</p>';
        }
    } else {
        $html .= '<p>No se encontraron discrepancias negativas.</p>';
    }

    $html .= '<h3>Top 5 discrepancias positivas</h3>';
    if (count($top_positivas) > 0) {
        foreach ($top_positivas as $positivo) {
            $html .= '<p>'.$positivo['nombre_ingreso'].' (ID: '.$positivo['id_producto'].') (ID Toma: '.$positivo['id_toma'].') -- SAP: '.$positivo['stock_ingreso'].' | Conteo: '.$positivo['unidades_toma'].' | Diferencia: +'.$positivo['diferencia_toma'].'</p>';
        }
    } else {
        $html .= '<p>No se encontraron discrepancias positivas.</p>';
    }
}

// --- Crear PDF con Dompdf ---
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Informe_Simple_$fecha_actual.pdf", ["Attachment" => true]);
