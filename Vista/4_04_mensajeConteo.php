<?php
include("../Modelo/validar_sesion.php");
include ("../Modelo/conexion.php");
$con = conexion();

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
    <title>Registrar conteos</title>
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
                        <!-- <i class="fa-solid fa-bell campanaMenu"></i> -->
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
                          <a href="2_02_menuContador.php"><i class="fa-solid fa-house-user"></i></a>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="botonMenu"><a href="4_01_listaConteos.php">Registrar conteos</a></button>
                </div>

                <div class="linksMenu1">
                    <a href="https://drive.google.com/drive/folders/1hRCdwiTO1Jt4KA0wqh2Q08sxTA_oYSZ6?usp=sharing" target="_blank" class="linkMenu">Manuales</a>
                    <a href="mailto:SomosInvenpro@gmail.com?subject=Soporte%20técnico" target="_blank" class="linkMenu">Soporte</a>
                    <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
                    <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
                </div>

                <div class="contenedorBotonCerrar1">
                    <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
                </div>

            </div>
        

            <div class="menu2">

                <h1 class="tituloCentro">Registrar conteos de inventarios</h1>               

                <img class="logo2" src="img/logo-mini.png" alt="Logo secundario">

                    <p class="mensajeConfirmar">Toma de inventario enviada exitosamente.
                        ¿Desea volver a la lista de conteos
                        o ir al inicio?</p>

                    <div class="botonesEleccion">
                        <button class="ingresar1"><a href="4_01_listaConteos.php">Lista de conteos</a></button> <br>
                        <button class="ingresar1"><a href="2_02_menuContador.php">Inicio</a></button>
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