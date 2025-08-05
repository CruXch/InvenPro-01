<?php
include("../Modelo/validar_sesion.php");
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
                        <i class="fa-solid fa-bell campanaMenu"></i>
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
                          <a href="2_02_menuContador.php"><i class="fa-solid fa-house-user"></i></a>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="botonMenu"><a href="4_01_listaConteos.php">Registrar conteos</a></button>
                </div>

                <div class="linksMenu1">
                    <a href="#" target="_blank" class="linkMenu">Documentación</a>
                    <a href="#" target="_blank" class="linkMenu">Soporte</a>
                    <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
                    <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
                </div>

                <div class="contenedorBotonCerrar1">
                    <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
                </div>

            </div>
        

            <div class="menu2">

                <h1 class="tituloCentro">Registrar conteos de inventarios</h1>               

                    <div class="contenedorInfoConteos2">

                        <div class="contenedorConteos2">

                            <h2>Jueves 17 Octubre 2024</h2>
                            <p>Id toma: 03</p>
                            <p>Id producto: 00001</p>
                            <p>Nombre producto: Harina 500gr</p>
                            
                            <div class="contenedorLlenar">
                                <label for="cantidadConteo">Cantidad:
                                    <input type="text" name="cantidadConteo" id="cantidadConteo" placeholder="Escribir(unidades,kg,etc.)">
                                </label> 
                                <label for="comentariosConteo">Comentarios:
                                    <textarea id="comentariosConteo" name="comentariosConteo" placeholder="Escribir"></textarea>
                                </label>
                            </div>

                            <p>──────────────────────────────────────────────</p>

                            <p>Id toma: 03</p>
                            <p>Id producto: 00001</p>
                            <p>Nombre producto: Harina 500gr</p>
                            
                            <div class="contenedorLlenar">
                                <label for="cantidadConteo">Cantidad:
                                    <input type="text" name="cantidadConteo" id="cantidadConteo" placeholder="Escribir(unidades,kg,etc.)">
                                </label> 
                                <label for="comentariosConteo">Comentarios:
                                    <textarea id="comentariosConteo" name="comentariosConteo" placeholder="Escribir"></textarea>
                                </label>
                            </div>

                        </div>

                    </div>

                    <div class="botonSiguiente">
                        <button class="ingresar1"><a href="4_03_enviarConteo.php">Siguiente</a></button>
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