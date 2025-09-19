<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Validar que se reciban todos los datos esperados por la URL (GET)
// En este caso: id del usuario, fecha y los productos seleccionados con sus IDs
if (!isset($_GET['id_usuario']) || !isset($_GET['fecha']) || !isset($_GET['productos_seleccionados']) || !isset($_GET['productos_seleccionados_id'])) {
    echo "No se recibieron los datos necesarios.";
    exit; // Si falta algún dato, se interrumpe la ejecución
}

// Guardar los valores recibidos en variables para usarlos después
$id = $_GET['id_usuario'];                   // Identificador del usuario
$fecha = $_GET['fecha'];                     // Fecha recibida
$productos = $_GET['productos_seleccionados'];       // Lista de productos seleccionados
$productosId = $_GET['productos_seleccionados_id']; // IDs de los productos seleccionados

// Consulta SQL para obtener los datos del usuario que corresponde al id recibido
$sql = "SELECT * FROM tbl_usuarios WHERE id_usuario='$id'";

// Ejecutar la consulta
$consulta = mysqli_query($con, $sql);

// Obtener la fila con los resultados de la consulta en formato asociativo
$fila = mysqli_fetch_assoc($consulta);

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

                <h1 class="tituloCentro">Información contador</h1>

                    <div class="contenedorConfirmar">
                        <div class="datosConfirmar1"><br>
                            <p class="datos1Fuente">Nombre: <?= $fila['nombre_usuario']?></p>
                            <p class="datos1Fuente">Apellido: <?= $fila['apellido_usuario']?></p>
                            <p class="datos1Fuente">Cédula: <?= $fila['id_usuario']?></p>
                            <p class="datos1Fuente">Correo electrónico: <?= $fila['correo_usuario']?></p>
                            <p class="datos1Fuente">Número de celular: <?= $fila['telefono_usuario']?></p>                            
                        </div>
                    </div>
                    
                    <p class="mensajeConfirmar">Regresar a la pantalla anterior</p>
                    <form action="3_02_04_asignarContador.php" method="post">
                        <input type="hidden" name="productos_seleccionados" value="<?= htmlspecialchars($productos) ?>">
                        <input type="hidden" name="productos_seleccionados_id" value="<?= htmlspecialchars($productosId) ?>">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
                        <button class="botonPdf" type="submit">Atrás</button>
                    </form>
                    <!-- Usamos un formulario con método POST para regresar a la pantalla anterior 
                    y mantener el envío de datos de forma segura (sin exponerlos en la url).
                    (Mejor opción en general, el metodo GET deja ver los datos en la url.)
                    Esto es necesario porque la pantalla "seleccionar contador" originalmente recibe los datos por POST, 
                    y si intentamos volver usando un enlace con GET (por ejemplo, <a href="...">),
                    los datos no estarían disponibles en $_POST y se perderían. -->
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