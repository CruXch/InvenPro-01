<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

// Verificamos si la sesión de código ha sido verificada
if (!isset($_SESSION['codigo_verificado']) || $_SESSION['codigo_verificado'] !== true) {
    // Si no está verificado, redirigimos de vuelta
    header("Location: 1_03_restablecerContraseña_1.php");
    exit;
}
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
    <title>Restablecer contraseña</title>
</head>
<body>

    <header>
        <nav>
            <div>
                <a href="1_01_login.html"><img width="300px" src="img/logo.png" alt="Logo InvenPro"></a>
            </div>
            <div class="iconosNav">
                <a href="#" target="_blank"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
            </div>
            <div class="listaNav">
                <ul>
                    <li><a href="https://drive.google.com/drive/folders/1hRCdwiTO1Jt4KA0wqh2Q08sxTA_oYSZ6?usp=sharing" target="_blank">Manuales</a></li>
                    <li><a href="mailto:SomosInvenpro@gmail.com?subject=Soporte%20técnico" target="_blank">Soporte</a></li>
                    <li><a href="1_00_index.html" target="_blank">Empresa</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="formulario">
        <h1>Restablecer Contraseña</h1>
        <form action="../Controlador/cambiar_contra.php" method="post" id="restablecer-contra">

            <input class="campo2" type="password" name="nueva-contra" id="nueva-contra" placeholder="Nueva contraseña"> <br>
            <input class="campo2" type="password" name="confirmar-contra" id="confirmar-contra" placeholder="Confirmar contraseña"> <br> <br> <br>
            <button class="ingresar1" type="submit">Aceptar</button> <br> <br>

        </form>
    </div>

    <footer>
        <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>