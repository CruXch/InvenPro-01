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
    <title>Editar usuario</title>
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
                    <a href="3_01_04_editarOEliminar.php"><i class="fa-solid fa-circle-up fa-rotate-270 iconoVolver"></i></a>

                    <h1 class="tituloCentro">Editar usuario</h1>
                    
                    <div class="formularioUsuarios">
                        <form action="../Controlador/editar_usuario.php" method="post">

                            <input class="campo4" type="text" name="nombres-registro" id="nombres-registro" value="<?= $fila['nombre_usuario'] ?>" placeholder="Nombres" required> 
                            <input class="campo4" type="text" name="apellidos-registro" id="apellidos-registro" value="<?= $fila['apellido_usuario'] ?>" placeholder="Apellidos" required> 
                            <input class="campo4" readonly type="number" name="cedula-registro" id="cedula-registro" value="<?= $fila['id_usuario'] ?>"> 
                            <input class="campo4" type="number" name="celular-registro" id="celular-registro" value="<?= $fila['telefono_usuario'] ?>" placeholder="Celular" required> 
                            <input class="campo4" type="text" name="correo-registro" id="correo-registro" value="<?= $fila['correo_usuario'] ?>" placeholder="Correo electrónico" required> 

                            <input class="campo4" required list="usuario" id="tipo-usuario-registro" name="tipo-usuario-registro"
                                    value="<?php if ($fila['id_rol'] == 1){
                                        echo 'Administrador';
                                    } elseif ($fila['id_rol'] == 2){
                                        echo 'Contador';
                                    } else {
                                        echo 'Jefe';
                                    }?>"> 
                                <datalist id="usuario-elegido-registro">
                                    <option value="Contador">
                                    <option value="Jefe">
                                </datalist> 

                            <input class="campo4" type="text" name="contra-registro" id="contra-registro" value="<?= $fila['contrasena_usuario'] ?>" placeholder="Contraseña" required> <br>
                            
                            <button class="ingresar3" type="submit" onclick="return editarUsuario()">Siguiente</button> 
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