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
    <title>Restablecer contraseña</title>
</head>
<body>

<?php if (isset($_GET['error_codigo']) && $_GET['error_codigo'] == 1): ?>
    <script>
        alert("El código ingresado es incorrecto.");
    </script>
<?php endif; ?>


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

    <!-- Formulario para Enviar Código -->
    <form action="../Controlador/enviar_codigo.php" method="post" id="form-enviar-codigo">
        <span class="mensaje2">Se enviará un código al correo electrónico que fue registrado para comprobar su identidad.</span> <br>
        <input class="campo2" type="text" name="correo" placeholder="Correo electrónico" required> <br>
        
        <!-- Botón Enviar Código -->
        <button class="ingresar1" type="submit" name="accion" value="enviar">Enviar código</button> <br><br>
    </form>

    <br><br>

    <!-- Formulario para Ingresar el Código y Continuar -->
    <form action="../Controlador/verificar_codigo.php" method="post" id="form-verificar-codigo">
        <input class="campo2" type="text" name="codigo2" placeholder="Código" required> <br>
        <button class="ingresar1" type="submit">Siguiente</button> <br>
    </form>
</div>


    <footer>
        <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>