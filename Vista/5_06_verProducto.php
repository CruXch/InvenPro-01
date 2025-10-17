<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// FORZAR UTF-8 en la conexión (por si acaso)
mysqli_set_charset($con, "utf8mb4");

// FORZAR UTF-8 en el header HTTP
header('Content-Type: text/html; charset=utf-8');

// Verifica que la URL traiga un parámetro "fecha"
if (!isset($_GET['fecha'])) {
    echo "No se recibió una fecha válida.";
    exit;
}

// Decodifica la fecha recibida en la URL y la limpia para evitar inyección SQL
$fechaRecibida = urldecode($_GET['fecha']);
$fechaEscapada = mysqli_real_escape_string($con, $fechaRecibida);

// Consulta los productos registrados en la fecha seleccionada
$sql = "SELECT p.id_producto, p.nombre_producto, i.id_ingreso, i.marca_ingreso, i.stock_ingreso
        FROM Tbl_Ingresos_Csv i
        INNER JOIN Tbl_Productos p ON i.id_producto = p.id_producto
        WHERE i.fecha_ingreso = '$fechaEscapada'";

$resultado = mysqli_query($con, $sql);

$fecha = new DateTime($fechaRecibida);

$formatter = new IntlDateFormatter(
    'es_ES', 
    IntlDateFormatter::FULL, 
    IntlDateFormatter::SHORT,
    'America/Bogota',
    IntlDateFormatter::GREGORIAN,
    "EEEE, d 'de' MMMM 'de' yyyy - hh:mm a"
);

$fechaFormateada = ucfirst($formatter->format($fecha));
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
    <title>Informacion de productos</title>
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
                    <button class="botonMenu"><a href="5_05_infoProductos.php">Información productos SAP</a></button>
                </div>

                <div class="linksMenu">
                    <a href="https://drive.google.com/drive/folders/1hRCdwiTO1Jt4KA0wqh2Q08sxTA_oYSZ6?usp=sharing" target="_blank" class="linkMenu">Manuales</a>
                    <a href="mailto:SomosInvenpro@gmail.com?subject=Soporte%20técnico" target="_blank" class="linkMenu">Soporte</a>
                    <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
                    <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
                </div>

                <div class="contenedorBotonCerrar">
                    <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
                </div>

            </div>
        

            <div class="menu2">
                <a href="5_05_infoProductos.php"><i class="fa-solid fa-circle-up fa-rotate-270 iconoVolver"></i></a>

                <h1 class="tituloCentro">Información productos de SAP</h1>
                    
                    <div class="contenedorInfoUsuarios">
                        <form action="3_02_04_asignarContador.php" method="POST">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fechaRecibida) ?>">

                        <div class="contenedorTablaUsuarios">
                            <table>
                                <tr>
                                    <th>Id</th>
                                    <th>Producto</th>
                                    <th>Marca</th>
                                    <th>Stock</th>
                                    <th>Información</th>
                                </tr>
                            
                                <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                <td><?= htmlspecialchars($fila['id_producto']) ?></td>
                                <td><?= htmlspecialchars($fila['nombre_producto']) ?></td>
                                <td><?= htmlspecialchars($fila['marca_ingreso']) ?></td>
                                <td><?= htmlspecialchars($fila['stock_ingreso']) ?></td>
                                <td><a href="5_07_detalleProducto.php?id_producto=<?= $fila['id_producto']?>&fecha=<?= urlencode($fechaRecibida)?>"><i class="fa-solid fa-eye iconoMirar"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                                
                            </table>
                        </div>

                    </div>

                    </form>

                    </div>
                    
                    
            </div>

        </div>

        <div class="menuFooter">
            <footer>
                <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
            </footer>
        </div>

        <script src="js/script.js"></script>
</body>
</html>