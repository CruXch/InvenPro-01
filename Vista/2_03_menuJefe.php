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
    <title>Menú Jefe</title>
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

                <div class="contenedorBotonCerrar1">
                    <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
                </div>

            </div>
        

            <div class="menu2">

                <div class="fotoMenu">
                    <img class="fotoBanner" src="img/imagen-banner.jpg" alt="Foto-Banner">
                </div>

                <div class="infoMenu">
                    <h2 class="tituloInfo">Noticias - Actualizaciones Versión 1.0</h2>
                    <p class="contenidoInfo">- Se agregó el manual de usuario en el enlace "Manuales".</p> 
                    <p class="contenidoInfo">- En el módulo de administrador ahora al eliminar a un usuario Contador o Jefe, en realidad se está desactivando el usuario, así no se perderá la información de dicho usuario.</p> 
                    <p class="contenidoInfo">- El usuario tipo Administrador ya no podrá ver la contraseña de los usuarios tipo Contador y jefe.</p> 
                    <p class="contenidoInfo">- El usuario tipo Administrador ahora podrá cambiar el rol de un usuario tipo Contador a Jefe o viceversa.</p> 
                    <p class="contenidoInfo">- En el módulo de planificación de inventario al momento de ver la información de un producto se cambió el nombre de “Stock” a “Historial Stock Acumulado” para un mejor entendimiento de a que se refiere esta línea de información que suma todas las existencias que hayan entrado al sistema con el mismo id.</p> 
                    <p class="contenidoInfo">- Se arregló el problema de que las “ñ” y caracteres como tildes no se vean bien cuando se observa la información de un producto al momento de estar planificando una toma de inventario o generando un informe.</p> 
                    <p class="contenidoInfo">- Se agregó una nueva función al módulo del usuario Jefe, ahora podrá ver la información de los archivos .csv importados así como también la información de los productos de dichas listas.</p> 
                    <p class="contenidoInfo">- La fecha mínima en la búsqueda de fecha inicial para generar reporte se fijó en Agosto 1 del 2025.</p> 
                    <p class="contenidoInfo">- Al momento de generar un informe detallado se agregó una línea de información adicional que avisa si una toma esta “completada” o “pendiente”.</p> 
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