<?php
include("../Modelo/validar_sesion.php");
include ("../Modelo/conexion.php");
$con = conexion();

$id = $_GET['id_usuario'];

$sql = "SELECT * FROM Tbl_usuarios WHERE id_usuario='$id'";
$consulta = mysqli_query($con, $sql);

$fila = mysqli_fetch_array($consulta);

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
    <title>Ver usuario</title>
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

                    <h1 class="tituloCentro">Información usuario</h1>
                    
                    <div class="contenedorConfirmar">
                        <div class="datosConfirmar1">
                            <p class="datos2Fuente">Nombre: <?= $fila['nombre_usuario'] ?></p>
                            <p class="datos2Fuente">Apellido: <?= $fila['apellido_usuario'] ?></p>
                            <p class="datos2Fuente">Cédula: <?= $fila['id_usuario']?></p>
                            <p class="datos2Fuente">Contraseña: <?= $fila['contrasena_usuario']?></p>
                            <p class="datos2Fuente">Tipo de usuario: <?php if($fila['id_rol'] == 2){
                                echo "Contador";
                            } else {
                                echo "Jefe";
                            }?></p>
                            <p class="datos2Fuente">Correo electrónico: <?= $fila['correo_usuario']?></p>
                            <p class="datos2Fuente">Número de celular: <?= $fila['telefono_usuario']?></p>                            
                        </div>
                    </div>
                    
                    <p class="mensajeConfirmar">Regresar a la pantalla anterior</p>
                    <div class="botonesSiNo">
                        <a class="botonEliminarUsu" href="3_01_04_editarOEliminar.php">Atras</a>
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