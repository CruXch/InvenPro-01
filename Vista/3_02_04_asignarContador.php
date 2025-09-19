<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Validar recepción de datos
if (!isset($_POST['fecha']) || !isset($_POST['productos_seleccionados']) || !isset($_POST['productos_seleccionados_id'])) {
    echo "No se recibieron los datos necesarios.";
    exit;
}

$fecha = mysqli_real_escape_string($con, $_POST['fecha']);
$productos = $_POST['productos_seleccionados'];
$productosId = $_POST['productos_seleccionados_id'];

// Consulta para obtener los contadores disponibles
$sql_contadores = "SELECT id_usuario, nombre_usuario, apellido_usuario 
                   FROM Tbl_Usuarios WHERE id_rol = 2";
$resultado_contadores = mysqli_query($con, $sql_contadores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <title>Asignar contadores</title>
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
                        <p class="menuFecha">17/10/2024 - Jueves</p>
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
            <a href="3_02_03_asignarProductos.php?fecha=<?= urlencode($fecha) ?>"><i class="fa-solid fa-circle-up fa-rotate-270 iconoVolver"></i></a>
            <h1 class="tituloCentro">Asignar contador/es</h1>

            <div class="contenedorBarra">
                <input class="barraBuscar" type="text" placeholder="Buscar">
                <div class="botonBuscar">
                    <i class="fa-solid fa-magnifying-glass iconoLupa"></i>
                </div>
                <div class="contenedorBotonFiltrar">
                    <button class="botonFiltrar"><i class="fa-solid fa-filter"></i>Filtrar</button>
                </div>
            </div>

            <div class="contenedorAsignar">
                <form action="3_02_05_enviarToma.php" method="POST">
                    <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">

                    <input type="hidden" name="productos_seleccionados" value="<?= htmlspecialchars($productos) ?>">
                    <input type="hidden" name="productos_seleccionados_id" value="<?= htmlspecialchars($productosId) ?>">

                    <div class="contenedorTablaUsuarios">
                        <table>
                            <tr>
                                <th>Cédula</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Asignar</th>
                                <th>Información</th>
                            </tr>

                            <?php while ($contador = mysqli_fetch_assoc($resultado_contadores)): ?>
                            <tr>
                                <td><?= htmlspecialchars($contador['id_usuario']) ?></td>
                                <td><?= htmlspecialchars($contador['apellido_usuario']) ?></td>
                                <td><?= htmlspecialchars($contador['nombre_usuario']) ?></td>
                                <td>
                                    <input type="radio" name="contadores_asignados" value="<?= htmlspecialchars($contador['id_usuario']) ?>" required>
                                </td>
                                <td>
                                    <a href="3_02_08_mirarContador.php?id_usuario=<?= urlencode($contador['id_usuario']) ?>&productos_seleccionados=<?= urlencode($productos) ?>&productos_seleccionados_id=<?= urlencode($productosId)?>&fecha=<?= urlencode($fecha) ?>">
                                        <i class="fa-solid fa-eye iconoMirar"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
            </div>
            <button class="ingresar5" type="submit">Siguiente</button>
            </form>
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