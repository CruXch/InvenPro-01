<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$con = conexion();
$con->set_charset("utf8");  // Importante para que la conexión maneje utf8

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

if ($fechaInicio && $fechaFin) {
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
                ti.estado_toma,
                ti.diferencia_toma
            FROM Tbl_Productos_Usuarios_Toma put
            INNER JOIN Tbl_Tomas_De_Inventario ti ON put.id_toma = ti.id_toma
            INNER JOIN Tbl_Usuarios u ON put.id_usuario = u.id_usuario
            INNER JOIN Tbl_Ingresos_Csv ic ON put.id_ingreso = ic.id_ingreso
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

// Generar HTML para PDF con estructura correcta y codificación
$html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Informe Detallado - InvenPro</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1, h2, h3, p {
            font-family: "DejaVu Sans", sans-serif;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }
        p {
            margin: 4px 0;
        }
    </style>
</head>
<body>';

$html .= '<h1 style="text-align:center;">Informe Detallado - InvenPro</h1>';
$html .= '<p><strong>Rango de fechas:</strong> ' . htmlspecialchars($fechaInicio) . ' | ' . htmlspecialchars($fechaFin) . '</p><br>';

if ($mensaje_error) {
    $html .= '<p>' . htmlspecialchars($mensaje_error) . '</p>';
} else {
    foreach ($filas as $fila) {
        $html .= '<p><strong>Estado de la toma:</strong> ' . htmlspecialchars($fila["estado_toma"]) . '</p>';
        $html .= '<p><strong>ID Toma:</strong> ' . htmlspecialchars($fila["id_toma"]) . '</p>';
        $html .= '<p><strong>Trabajador:</strong> ' . htmlspecialchars($fila["nombre_usuario"]) . ' ' . htmlspecialchars($fila["apellido_usuario"]) . ' (<strong>ID: </strong>' . htmlspecialchars($fila["id_usuario"]) . ')</p>';
        $html .= '<p><strong>Fecha toma:</strong> ' . date("d/m/Y", strtotime($fila["fecha_toma"])) . '</p>';
        $html .= '<p><strong>Nombre Producto:</strong> ' . htmlspecialchars($fila["nombre_ingreso"]) . ' | <strong>ID Producto:</strong> ' . htmlspecialchars($fila["id_producto"]) . ' | <strong>Marca:</strong> ' . htmlspecialchars($fila["marca_ingreso"]) . '</p>';
        $html .= '<p><strong>Cantidad registrada:</strong> ' . htmlspecialchars($fila["unidades_toma"]) . ' | <strong>Cantidad en SAP:</strong> ' . htmlspecialchars($fila["stock_ingreso"]) . ' | <strong>Diferencia con cantidad SAP:</strong> ' . htmlspecialchars($fila["diferencia_toma"]) . '</p>';
        $html .= '<p><strong>Comentarios:</strong> ' . htmlspecialchars($fila["comentarios_toma"]) . '</p>';
        $html .= '<hr>';
    }

    $html .= '<h2>Resumen del informe</h2>';
    $html .= '<p><strong>Total asignados:</strong> ' . $total_productos_asignados . '</p>';
    $html .= '<p><strong>Total contados:</strong> ' . $total_productos_contados . '</p>';
    $html .= '<p><strong>% de ejecución:</strong> ' . round($porcentaje_ejecucion, 2) . '%</p>';
    $html .= '<p><strong>Diferencias negativas:</strong> ' . $total_diferencias_negativas . ' (' . round($porcentaje_diferencias_negativas, 2) . '%)</p>';
    $html .= '<p><strong>Diferencias positivas:</strong> ' . $total_diferencias_positivas . ' (' . round($porcentaje_diferencias_positivas, 2) . '%)</p>';
    $html .= '<p><strong>Total diferencias:</strong> ' . $total_diferencias . ' (' . round($porcentaje_diferencias_totales, 2) . '%)</p><br>';

    $html .= '<h3>Top 5 discrepancias negativas</h3>';
    if (count($top_negativas) > 0) {
        foreach ($top_negativas as $negativo) {
            $html .= '<p>' . htmlspecialchars($negativo['nombre_ingreso']) . ' (ID: ' . htmlspecialchars($negativo['id_producto']) . ') (ID Toma: ' . htmlspecialchars($negativo['id_toma']) . ') -- SAP: ' . htmlspecialchars($negativo['stock_ingreso']) . ' | Conteo: ' . htmlspecialchars($negativo['unidades_toma']) . ' | Diferencia: ' . htmlspecialchars($negativo['diferencia_toma']) . '</p>';
        }
    } else {
        $html .= '<p>No se encontraron discrepancias negativas.</p>';
    }

    $html .= '<h3>Top 5 discrepancias positivas</h3>';
    if (count($top_positivas) > 0) {
        foreach ($top_positivas as $positivo) {
            $html .= '<p>' . htmlspecialchars($positivo['nombre_ingreso']) . ' (ID: ' . htmlspecialchars($positivo['id_producto']) . ') (ID Toma: ' . htmlspecialchars($positivo['id_toma']) . ') -- SAP: ' . htmlspecialchars($positivo['stock_ingreso']) . ' | Conteo: ' . htmlspecialchars($positivo['unidades_toma']) . ' | Diferencia: +' . htmlspecialchars($positivo['diferencia_toma']) . '</p>';
        }
    } else {
        $html .= '<p>No se encontraron discrepancias positivas.</p>';
    }
}

$html .= '</body></html>';

// Convertir a encoding compatible con Dompdf
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->setDefaultFont('DejaVu Sans');
$options->setChroot(__DIR__);

// Limpiar buffer de salida para evitar corrupción
if (ob_get_length()) {
    ob_end_clean();
}

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header("Content-Type: application/pdf");
header('Content-Disposition: attachment; filename="Informe_Detallado_'.$fecha_actual.'.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

echo $dompdf->output();
exit;










