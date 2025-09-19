<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Verifica que la URL traiga un parámetro "fecha"
// Si no lo recibe, muestra un mensaje de error y detiene la ejecución
if (!isset($_GET['fecha'])) {
    echo "No se recibió una fecha válida.";
    exit;
}

// Decodifica la fecha recibida en la URL y la limpia para evitar inyección SQL
$fechaRecibida = urldecode($_GET['fecha']);
$fechaEscapada = mysqli_real_escape_string($con, $fechaRecibida);

// Consulta los productos registrados en la fecha seleccionada
// Une las tablas de ingresos y productos para obtener nombre, marca y stock
$sql = "SELECT p.id_producto, p.nombre_producto, i.id_ingreso, i.marca_ingreso, i.stock_ingreso
        FROM tbl_ingresos_csv i
        INNER JOIN tbl_productos p ON i.id_producto = p.id_producto
        WHERE i.fecha_ingreso = '$fechaEscapada'";

// Ejecuta la consulta y guarda el resultado
$resultado = mysqli_query($con, $sql);

// Convierte la fecha recibida a un objeto DateTime para formatearla
$fecha = new DateTime($fechaRecibida);

// Configura la localización a español para mostrar la fecha con nombres de días y meses
setlocale(LC_TIME, 'spanish');

// Da formato a la fecha: ejemplo "Lunes, 16 de Septiembre de 2025 - 03:45 PM"
$fechaFormateada = strftime("%A, %e de %B de %Y - %I:%M %p", $fecha->getTimestamp());

// Asegura que la primera letra esté en mayúscula
$fechaFormateada = ucfirst($fechaFormateada);
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
    <title>Planificar inventarios</title>
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
                          <a href="2_01_menuAdministrador.php"><i class="fa-solid fa-house-user"></i></a>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="botonMenu" id="acordeon1">Registrar usuarios <i class="fa-sharp fa-solid fa-chevron-down" id="icono1"></i></button>
                    <div class="acordeon" id="acordeon1opciones">
                        <a href="3_01_01_registrarUsuarios.php">Registrar nuevo usuario</a>
                        <a href="3_01_04_editarOEliminar.php">Lista de usuarios</a>
                        <a href="3_01_08_listaAdmin.php">Lista de usuarios administrador</a>
                    </div>
                    
                    <button class="botonMenu" id="acordeon2" onclick="pestañaListaPlanificacion()">Planificar inventarios</button>
                </div>

                <div class="linksMenu">
                    <a href="#" target="_blank" class="linkMenu">Documentación</a>
                    <a href="#" target="_blank" class="linkMenu">Soporte</a>
                    <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
                    <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
                </div>

                <div class="contenedorBotonCerrar">
                    <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
                </div>

            </div>
        

            <div class="menu2">
                <a href="3_02_01_listaPlanificacion.php"><i class="fa-solid fa-circle-up fa-rotate-270 iconoVolver"></i></a>

                <h1 class="tituloCentro">Asignar productos</h1>

                    <div class="contenedorBarra">
                        <input class="barraBuscar" type="text" placeholder="Buscar">
                        <div class="botonBuscar">
                            <i class="fa-solid fa-magnifying-glass iconoLupa"></i>
                        </div>
                        <div class="contenedorBotonFiltrar">
                            <button class="botonFiltrar"><i class="fa-solid fa-filter"></i>Filtrar</button>
                        </div>
                    </div>
                    
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
                                    <th>Asignar</th>
                                    <th>Información</th>
                                </tr>
                            
                                <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                <td><?= htmlspecialchars($fila['id_producto']) ?></td>
                                <td><?= htmlspecialchars($fila['nombre_producto']) ?></td>
                                <td><?= htmlspecialchars($fila['marca_ingreso']) ?></td>
                                <td><?= htmlspecialchars($fila['stock_ingreso']) ?></td>
                                <td><input type="radio" name="productos_seleccionados" value="<?= $fila['id_ingreso'] ?>" required></td>
                                <input type="hidden" name="productos_seleccionados_id" value="<?= $fila['id_producto'] ?>">
                                <td><a href="3_02_07_mirarProducto.php?id_producto=<?= $fila['id_producto']?>&fecha=<?= urlencode($fechaRecibida)?>"><i class="fa-solid fa-eye iconoMirar"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                                
                            </table>
                        </div>

                    </div>
                    <button class="ingresar5" type="submit">Siguiente</button>
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

