<?php
// Incluimos archivos de validación de sesión y conexión a la base de datos
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recoger fechas de sesión (rango de consulta)
$fechaInicio = $_SESSION['fecha_inicio'] ?? null;
$fechaFin    = $_SESSION['fecha_fin'] ?? null;

// Inicializamos arrays y variables para el informe
$filas = [];                        // Guardará todos los registros consultados
$total_productos_asignados = 0;     // Número total de productos asignados en las tomas
$total_productos_contados = 0;      // Número de productos que sí se contaron
$total_diferencias_negativas = 0;   // Casos donde el conteo fue menor al esperado
$total_diferencias_positivas = 0;   // Casos donde el conteo fue mayor al esperado
$discrepancias_negativas = [];      // Lista de discrepancias negativas detalladas
$discrepancias_positivas = [];      // Lista de discrepancias positivas detalladas

$top_negativas = []; // Top 5 discrepancias negativas
$top_positivas = []; // Top 5 discrepancias positivas

$mensaje_error = ""; // Mensaje de error en caso de que falten datos o no haya resultados

// --- LÓGICA DE CONSULTA Y PROCESAMIENTO ---
if ($fechaInicio && $fechaFin) {
    // Consulta principal: recupera tomas de inventario en el rango de fechas
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
        // Recorremos cada fila obtenida de la consulta
        while ($fila = $resultado->fetch_assoc()) {
            $filas[] = $fila; // Guardamos la fila en el array principal

            // Contar producto como asignado
            $total_productos_asignados++;

            // Si tiene unidades_toma (conteo realizado), sumamos a los contados
            if (!empty($fila["unidades_toma"])) {
                $total_productos_contados++;
            }

            // Analizamos las diferencias
            $diferencia = $fila["diferencia_toma"];
            if ($diferencia < 0) {
                // Caso de diferencia negativa (faltante)
                $total_diferencias_negativas++;
                $discrepancias_negativas[] = $fila;

            } elseif ($diferencia > 0) {
                // Caso de diferencia positiva (sobrante)
                $total_diferencias_positivas++;
                $discrepancias_positivas[] = $fila;
            }
        }

        // Totales y porcentajes generales
        $total_diferencias = $total_diferencias_negativas + $total_diferencias_positivas;
        $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
        $porcentaje_diferencias_negativas = ($total_diferencias_negativas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_positivas = ($total_diferencias_positivas / $total_productos_asignados) * 100;
        $porcentaje_diferencias_totales = ($total_diferencias / $total_productos_asignados) * 100;

        // Ordenar discrepancias negativas de menor a mayor (más faltantes primero)
        usort($discrepancias_negativas, fn($a, $b) => $a['diferencia_toma'] <=> $b['diferencia_toma']);
        $top_negativas = array_slice($discrepancias_negativas, 0, 5); // Tomamos las 5 peores

        // Ordenar discrepancias positivas de mayor a menor (más sobrantes primero)
        usort($discrepancias_positivas, fn($a, $b) => $b['diferencia_toma'] <=> $a['diferencia_toma']);
        $top_positivas = array_slice($discrepancias_positivas, 0, 5); // Tomamos las 5 peores
    } else {
        // Caso en que no haya registros en el rango de fechas
        $mensaje_error = "No hay registros disponibles en ese rango de fechas.";
    }
} else {
    // Caso en que no se recibieron las fechas
    $mensaje_error = "Faltan parámetros de fecha.";
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <title>Generar informes</title>
</head>
<body>

<header>
    <div>
        <img width="22%" src="img/logo.png" alt="Logo InvenPro">
    </div>
</header>

<div class="menu">
    <div class="menu1">
        <div class="usuarioMenu1">
            <div class="menuFotoNombre">
                <img class="menuFoto" src="img/fotoperfil.png" alt="Foto de perfil">
                <div class="menuNombres">
                  <p class="nombres"><?= $_SESSION['nombre_usuario'] ?></p>
                  <p class="nombres"><?= $_SESSION['apellido_usuario'] ?></p>
                  <p class="cargo"><?= $_SESSION['rol'] ?></p>
                </div>
            </div>
            <div class="menuFechaIcono">
                <p class="menuFecha"><?= fechaConDia() ?></p>
                <div class="menuIcono">
                  <a href="2_03_menuJefe.php"><i class="fa-solid fa-house-user"></i></a>
                </div>
            </div>
        </div>

        <div>
            <button class="botonMenu"><a href="5_01_listaInformes.php">Generar informes</a></button>
        </div>

        <div class="linksMenu1">
            <a href="#" target="_blank" class="linkMenu">Documentación</a>
            <a href="#" target="_blank" class="linkMenu">Soporte</a>
            <a href="#" target="_blank" class="linkMenu">Empresa</a>
            <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
        </div>

        <div class="contenedorBotonCerrar1">
            <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">
                Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i>
            </a>
        </div>
    </div>

    <div class="menu2">
        <h1 class="tituloCentro">Informe simple</h1>

        <div class="contenedorConfirmarInforme">
            <div class="datosConfirmarInforme">
                <?php if ($mensaje_error): ?>
                    <p><?= $mensaje_error ?></p>
                <?php else: ?>

                        <p><strong>Total de productos asignados:</strong> <?= $total_productos_asignados ?></p>
                        <p><strong>Total de productos contados:</strong> <?= $total_productos_contados ?></p>
                        <p><strong>% de ejecución:</strong> <?= round($porcentaje_ejecucion, 2) ?>%</p>
                        <p><strong>Total diferencias negativas:</strong> <?= $total_diferencias_negativas ?> (<?= round($porcentaje_diferencias_negativas,2) ?>%)</p>
                        <p><strong>Total diferencias positivas:</strong> <?= $total_diferencias_positivas ?> (<?= round($porcentaje_diferencias_positivas,2) ?>%)</p>
                        <p><strong>Total diferencias:</strong> <?= $total_diferencias ?> (<?= round($porcentaje_diferencias_totales,2) ?>%)</p>
                        <br><br>

                    <p><strong>Top 5 discrepancias negativas</strong></p>

                        <?php foreach ($top_negativas as $negativo): ?>
                            <p><?= $negativo['nombre_ingreso'] ?> (ID: <?= $negativo['id_producto'] ?>) (ID Toma: <?= $negativo['id_toma'] ?>) -- Cantidad SAP: <?= $negativo['stock_ingreso'] ?> -- Cantidad conteo físico: <?= $negativo['unidades_toma'] ?> -- Diferencia: <?= $negativo['diferencia_toma'] ?></p>
                        <?php endforeach; ?>
                        <br><br>

                    <p><strong>Top 5 discrepancias positivas</strong></p>

                        <?php foreach ($top_positivas as $positivo): ?>
                            <p><?= $positivo['nombre_ingreso'] ?> (ID: <?= $positivo['id_producto'] ?>) (ID Toma: <?= $positivo['id_toma'] ?>) -- Cantidad SAP: <?= $positivo['stock_ingreso'] ?> -- Cantidad conteo físico: <?= $positivo['unidades_toma'] ?> -- Diferencia: +<?= $positivo['diferencia_toma'] ?></p>
                        <?php endforeach; ?>
                        <br>

                <?php endif; ?>
            </div>
        </div>

        <a href="../Controlador/exportar_pdf_simple.php" class="botonPdf">Descargar PDF</a>

    </div>
</div>

<div class="menuFooter">
    <footer>
        <p>Términos de servicio ©2025 Inventory Project - InvenPro</p>
    </footer>
</div>

<script src="js/script.js"></script>
</body>
</html>
