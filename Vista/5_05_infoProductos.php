<?php
include("../Modelo/validar_sesion.php");
require_once ("../Modelo/conexion.php");
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

                    <h1 class="tituloCentro">Información productos de SAP</h1>      

                    <div class="contenedorInfoConteos">

                        <div class="contenedorConteos">
                            <h2 class="tituloConteo">Listas de productos SAP</h2>
                            <?php include("../Controlador/mostrar_grupos_csv_jefe.php"); ?>
                        </div>

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