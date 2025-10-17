<?php
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
    <title>Registrar administrador</title>
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
        <h1>Registro Administrador</h1>
        <form action="../Controlador/registrar_admin.php" method="post">

            <input class="campo3" type="text" name="nombres-admin" id="nombres-admin" placeholder="Nombres" required> 
            <input class="campo3" type="text" name="apellidos-admin" id="apellidos-admin" placeholder="Apellidos" required> 
            <input class="campo3" type="number" name="cedula-admin" id="cedula-admin" placeholder="Cédula" required> 
            <input class="campo3" type="number" name="celular-admin" id="celular-admin" placeholder="Número de celular" required> 
            <input class="campo3" type="text" name="correo-admin" id="correo-admin" placeholder="Correo electrónico" required>  
            <input class="campo3" type="password" name="contra-admin" id="contra-admin" placeholder="Contraseña" required> 
            <input class="campo3" type="password" name="confirmar-contra-admin" id="confirmar-contra-admin" placeholder="Confirmar contraseña" required> <br>

            <button class="ingresar1" type="submit" onclick="return registroAdmin()">Aceptar</button> <br>
        </form>
    </div>

    <footer>
        <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>