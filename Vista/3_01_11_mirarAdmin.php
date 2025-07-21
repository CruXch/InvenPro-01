<?php
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
    <title>Ver usuario administrador</title>
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
                              <p class="nombres">David Fernando</p>
                              <p class="nombres">Pérez Ortiz</p>
                              <p class="cargo">Administrador</p>
                            </div>
                    </div>
                    <div class="menuFechaIcono">
                        <p class="menuFecha">17/10/2024 - Jueves</p>
                        <div class="menuIcono">
                          <a href="#"><i class="fa-solid fa-house-user"></i></a>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="botonMenu">Registrar usuarios <i class="fa-sharp fa-solid fa-chevron-down"></i></button>
                    <button class="botonMenu">Planificar inventarios <i class="fa-sharp fa-solid fa-chevron-down"></i></button>
                </div>

                <div class="linksMenu">
                    <a href="#" target="_blank" class="linkMenu">Documentación</a>
                    <a href="#" target="_blank" class="linkMenu">Soporte</a>
                    <a href="#" target="_blank" class="linkMenu">Quienes somos</a>
                    <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
                </div>

                <div class="contenedorBotonCerrar">
                    <button class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></button>
                </div>

            </div>
        

            <div class="menu2">

                    <h1 class="tituloCentro">Información usuario Administrador</h1>
                    
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
                        <a class="botonEliminarUsu" href="3_01_08_listaAdmin.php">Atras</a>
                    </div>
                    
            </div>

        </div>

        <div class="menuFooter">
            <footer>
                <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
            </footer>
        </div>

</body>
</html>