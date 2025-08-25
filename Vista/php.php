<?php
// Conexión a la base de datos
include("../Modelo/conexion.php");
$con = conexion();

// Verificamos que llegaron los parámetros desde el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tipo_informe'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
    $tipoInforme = $_POST['tipo_informe'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    if ($tipoInforme === "detallado") {
    // ================== INFORME DETALLADO + SIMPLE ==================
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

    $resultado = $con->query($sql);

    if ($resultado->num_rows > 0) {
        // Guardamos todas las filas en un array
        $filas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $filas[] = $fila;
        }

        // ====== Parte Detallada ======
        echo '<h2>Informe detallado de inventario</h2>';
        echo '<table class="tablaInforme" border="1" cellpadding="5">';
        echo '<thead>
                <tr>
                    <th>Id Toma</th>
                    <th>Id Usuario</th>
                    <th>Nombre Usuario</th>
                    <th>Apellido Usuario</th>
                    <th>Fecha Toma</th>
                    <th>Id Producto</th>
                    <th>Nombre Producto</th>
                    <th>Marca Producto</th>
                    <th>Unidades SAP</th>
                    <th>Unidades físicas</th>
                    <th>Diferencia SAP</th>
                    <th>Comentarios</th>
                </tr>
              </thead><tbody>';

        foreach ($filas as $fila) {
            echo '<tr>';
            echo '<td>' . $fila["id_toma"] . '</td>';
            echo '<td>' . $fila["id_usuario"] . '</td>';
            echo '<td>' . $fila["nombre_usuario"] . '</td>';
            echo '<td>' . $fila["apellido_usuario"] . '</td>';
            echo '<td>' . date("d/m/Y", strtotime($fila["fecha_toma"])) . '</td>';
            echo '<td>' . $fila["id_producto"] . '</td>';
            echo '<td>' . $fila["nombre_ingreso"] . '</td>';
            echo '<td>' . $fila["marca_ingreso"] . '</td>';
            echo '<td>' . $fila["stock_ingreso"] . '</td>';
            echo '<td>' . $fila["unidades_toma"] . '</td>';
            echo '<td>' . $fila["diferencia_toma"] . '</td>';
            echo '<td>' . $fila["comentarios_toma"] . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // ====== Parte Resumen (como Informe Simple) ======
        $total_productos_asignados = count($filas);
        $total_productos_contados = 0;
        $total_diferencias_negativas = 0;
        $total_diferencias_positivas = 0;
        $discrepancias_negativas = [];
        $discrepancias_positivas = [];

        foreach ($filas as $fila) {
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

        echo "<h3>Resumen del informe</h3><ul>";
        echo "<li><strong>Total de productos asignados:</strong> $total_productos_asignados</li>";
        echo "<li><strong>Total de productos contados:</strong> $total_productos_contados</li>";
        echo "<li><strong>% de ejecución:</strong> " . round($porcentaje_ejecucion, 2) . "%</li>";
        echo "<li><strong>Total diferencias negativas:</strong> $total_diferencias_negativas (" . round($porcentaje_diferencias_negativas, 2) . "%)</li>";
        echo "<li><strong>Total diferencias positivas:</strong> $total_diferencias_positivas (" . round($porcentaje_diferencias_positivas, 2) . "%)</li>";
        echo "<li><strong>Total diferencias:</strong> $total_diferencias (" . round($porcentaje_diferencias_totales, 2) . "%)</li>";
        echo "</ul>";

        echo "<h3>Top 5 discrepancias negativas</h3><ol>";
        foreach ($top_negativas as $item) {
            echo "<li>{$item['nombre_ingreso']} (ID: {$item['id_producto']}) -- Cantidad SAP: {$item['stock_ingreso']} ~ Conteo físico: {$item['unidades_toma']} | Diferencia: {$item['diferencia_toma']}</li>";
        }
        echo "</ol>";

        echo "<h3>Top 5 discrepancias positivas</h3><ol>";
        foreach ($top_positivas as $item) {
            echo "<li>{$item['nombre_ingreso']} (ID: {$item['id_producto']}) -- Cantidad SAP: {$item['stock_ingreso']} ~ Conteo físico: {$item['unidades_toma']} | Diferencia: +{$item['diferencia_toma']}</li>";
        }
        echo "</ol>";

    } else {
        echo "<p>No hay registros disponibles en ese rango de fechas.</p>";
    }

    } else {
        // ================== INFORME SIMPLE ==================
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

        // Variables de control
        $total_productos_asignados = 0;
        $total_productos_contados = 0;
        $total_diferencias_negativas = 0;
        $total_diferencias_positivas = 0;
        $discrepancias_negativas = [];
        $discrepancias_positivas = [];

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $total_productos_asignados++;
                if (!empty($fila["unidades_toma"])) {
                    $total_productos_contados++;
                }

                $diferencia = $fila["diferencia_toma"];
                if ($diferencia < 0) {
                    $total_diferencias_negativas++;
                    $discrepancias_negativas[] = [
                        'producto' => $fila["nombre_ingreso"],
                        'id' => $fila["id_producto"],
                        'idToma' => $fila["id_toma"],
                        'idIngreso' => $fila["id_ingreso"],
                        'unidadesToma' => $fila["unidades_toma"],
                        'unidadesStock' => $fila["stock_ingreso"],
                        'diferencia' => $diferencia
                    ];
                } elseif ($diferencia > 0) {
                    $total_diferencias_positivas++;
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
            }

            $total_diferencias = $total_diferencias_negativas + $total_diferencias_positivas;
            $porcentaje_ejecucion = ($total_productos_contados / $total_productos_asignados) * 100;
            $porcentaje_diferencias_negativas = ($total_diferencias_negativas / $total_productos_asignados) * 100;
            $porcentaje_diferencias_positivas = ($total_diferencias_positivas / $total_productos_asignados) * 100;
            $porcentaje_diferencias_totales = ($total_diferencias / $total_productos_asignados) * 100;

            usort($discrepancias_negativas, fn($a, $b) => $a['diferencia'] <=> $b['diferencia']);
            $top_negativas = array_slice($discrepancias_negativas, 0, 5);

            usort($discrepancias_positivas, fn($a, $b) => $b['diferencia'] <=> $a['diferencia']);
            $top_positivas = array_slice($discrepancias_positivas, 0, 5);

            echo "<h2>Informe simple de inventario</h2>";
            echo "<ul>";
            echo "<li><strong>Total de productos asignados:</strong> $total_productos_asignados</li>";
            echo "<li><strong>Total de productos contados:</strong> $total_productos_contados</li>";
            echo "<li><strong>% de ejecución:</strong> " . round($porcentaje_ejecucion, 2) . "%</li>";
            echo "<li><strong>Total diferencias negativas:</strong> $total_diferencias_negativas (" . round($porcentaje_diferencias_negativas, 2) . "%)</li>";
            echo "<li><strong>Total diferencias positivas:</strong> $total_diferencias_positivas (" . round($porcentaje_diferencias_positivas, 2) . "%)</li>";
            echo "<li><strong>Total diferencias:</strong> $total_diferencias (" . round($porcentaje_diferencias_totales, 2) . "%)</li>";
            echo "</ul>";

            echo "<h3>Top 5 productos con mayor discrepancia negativa</h3><ol>";
            foreach ($top_negativas as $item) {
                echo "<li>{$item['producto']} (ID: {$item['id']}) -- (ID Toma: {$item['idToma']}) (ID Ingreso: {$item['idIngreso']}) -- 
                Cantidad virtual SAP: {$item['unidadesStock']} ~ Cantidad conteo físico: {$item['unidadesToma']} |--| Diferencia: {$item['diferencia']}</li>";
            }
            echo "</ol>";

            echo "<h3>Top 5 productos con mayor discrepancia positiva</h3><ol>";
            foreach ($top_positivas as $item) {
                echo "<li>{$item['producto']} (ID: {$item['id']}) -- (ID Toma: {$item['idToma']}) (ID Ingreso: {$item['idIngreso']}) -- 
                Cantidad virtual SAP: {$item['unidadesStock']} ~ Cantidad conteo físico: {$item['unidadesToma']} |--| Diferencia: +{$item['diferencia']}</li>";
            }
            echo "</ol>";
        } else {
            echo "<p>No hay registros disponibles en ese rango de fechas.</p>";
        }
    }
} else {
    echo "<p>Error: faltan parámetros.</p>";
}

$con->close();
?>



--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ahora puedes usar las variables de sesión sin problema
$tipoInforme = $_SESSION['tipo_informe'] ?? null;
$fechaInicio = $_SESSION['fecha_inicio'] ?? null;
$fechaFin    = $_SESSION['fecha_fin'] ?? null;

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

$resultado = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Estilos de css-->
    <link rel="stylesheet" href="css/estilos.css">
    <!--Iconos de FontAwesome-->
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <title>Informe detallado</title>
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
                <p class="menuFecha"><?= date("d/m/Y - l") ?></p>
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
        <h1 class="tituloCentro">Informe detallado de inventario</h1>

        <?php
        if ($resultado->num_rows > 0) {
            $filas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $filas[] = $fila;
            }

            // ====== TABLA DETALLADA ======
            echo '<table class="tablaInforme" border="1" cellpadding="5">';
            echo '<thead>
                    <tr>
                        <th>Id Toma</th>
                        <th>Id Usuario</th>
                        <th>Nombre Usuario</th>
                        <th>Apellido Usuario</th>
                        <th>Fecha Toma</th>
                        <th>Id Producto</th>
                        <th>Nombre Producto</th>
                        <th>Marca Producto</th>
                        <th>Unidades SAP</th>
                        <th>Unidades físicas</th>
                        <th>Diferencia SAP</th>
                        <th>Comentarios</th>
                    </tr>
                  </thead><tbody>';

            foreach ($filas as $fila) {
                echo '<tr>';
                echo '<td>' . $fila["id_toma"] . '</td>';
                echo '<td>' . $fila["id_usuario"] . '</td>';
                echo '<td>' . $fila["nombre_usuario"] . '</td>';
                echo '<td>' . $fila["apellido_usuario"] . '</td>';
                echo '<td>' . date("d/m/Y", strtotime($fila["fecha_toma"])) . '</td>';
                echo '<td>' . $fila["id_producto"] . '</td>';
                echo '<td>' . $fila["nombre_ingreso"] . '</td>';
                echo '<td>' . $fila["marca_ingreso"] . '</td>';
                echo '<td>' . $fila["stock_ingreso"] . '</td>';
                echo '<td>' . $fila["unidades_toma"] . '</td>';
                echo '<td>' . $fila["diferencia_toma"] . '</td>';
                echo '<td>' . $fila["comentarios_toma"] . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            // ====== PARTE RESUMEN ======
            $total_productos_asignados = count($filas);
            $total_productos_contados = 0;
            $total_diferencias_negativas = 0;
            $total_diferencias_positivas = 0;
            $discrepancias_negativas = [];
            $discrepancias_positivas = [];

            foreach ($filas as $fila) {
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

            echo "<h3>Resumen del informe</h3><ul>";
            echo "<li><strong>Total de productos asignados:</strong> $total_productos_asignados</li>";
            echo "<li><strong>Total de productos contados:</strong> $total_productos_contados</li>";
            echo "<li><strong>% de ejecución:</strong> " . round($porcentaje_ejecucion, 2) . "%</li>";
            echo "<li><strong>Total diferencias negativas:</strong> $total_diferencias_negativas (" . round($porcentaje_diferencias_negativas, 2) . "%)</li>";
            echo "<li><strong>Total diferencias positivas:</strong> $total_diferencias_positivas (" . round($porcentaje_diferencias_positivas, 2) . "%)</li>";
            echo "<li><strong>Total diferencias:</strong> $total_diferencias (" . round($porcentaje_diferencias_totales, 2) . "%)</li>";
            echo "</ul>";

            echo "<h3>Top 5 discrepancias negativas</h3><ol>";
            foreach ($top_negativas as $item) {
                echo "<li>{$item['nombre_ingreso']} (ID: {$item['id_producto']}) -- Cantidad SAP: {$item['stock_ingreso']} ~ Conteo físico: {$item['unidades_toma']} | Diferencia: {$item['diferencia_toma']}</li>";
            }
            echo "</ol>";

            echo "<h3>Top 5 discrepancias positivas</h3><ol>";
            foreach ($top_positivas as $item) {
                echo "<li>{$item['nombre_ingreso']} (ID: {$item['id_producto']}) -- Cantidad SAP: {$item['stock_ingreso']} ~ Conteo físico: {$item['unidades_toma']} | Diferencia: +{$item['diferencia_toma']}</li>";
            }
            echo "</ol>";

        } else {
            echo "<p>No hay registros disponibles en ese rango de fechas.</p>";
        }
        ?>
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
?>
