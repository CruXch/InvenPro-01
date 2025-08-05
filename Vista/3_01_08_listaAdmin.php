<?php
include("../Modelo/validar_sesion.php");
include ("../Modelo/conexion.php");
$con = conexion();

$sql = "SELECT * FROM Tbl_usuarios WHERE id_rol='1'";
$consulta = mysqli_query($con, $sql);

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
    <title>Lista Usuarios Administrador</title>
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

                    <h1 class="tituloCentro">Ver o editar usuario Administrador</h1>
                    
                    <div class="contenedorBarra">
                        <input class="barraBuscar" type="text" placeholder="Buscar">
                        <div class="botonBuscar">
                            <i class="fa-solid fa-magnifying-glass iconoLupa"></i>
                        </div>
                        <div class="contenedorBotonFiltrar">
                            <button class="botonFiltrar"><i class="fa-solid fa-filter"></i>Filtrar</button>
                        </div>
                    </div>

                    <div class="contenedorInfoUsuarios">

                        <div class="contenedorTablaUsuarios">
                            <table>
                                <tr>
                                    <th>Cédula</th>
                                    <th>Apellidos</th>
                                    <th>Nombres</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                                <?php while ($fila = mysqli_fetch_array($consulta)): ?>
                                <tr>
                                    <td><?= $fila['id_usuario'] ?></td>
                                    <td><?= $fila['apellido_usuario'] ?></td>
                                    <td><?= $fila['nombre_usuario'] ?></td>
                                    <td><?php if ($fila['id_rol'] == 1){
                                        echo 'Administrador';
                                    }?></td>

                                    <td><a href="3_01_11_mirarAdmin.php?id_usuario=<?= $fila['id_usuario'] ?>"><i class="fa-solid fa-eye iconoMirar"></i></a></td>    
                                    <td><a href="3_01_09_edicionAdmin.php?id_usuario= <?= $fila['id_usuario'] ?>"><i class="fa-solid fa-user-pen iconoEditar"></i></a></td>                                                
                                </tr>
                                <?php endwhile; ?>
                            </table>
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