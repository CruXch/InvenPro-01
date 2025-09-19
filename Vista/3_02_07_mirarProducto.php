<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Validación: asegurarse de que se hayan recibido el id del producto y la fecha
if (!isset($_GET['id_producto']) || !isset($_GET['fecha'])) {
    echo "El id del producto o fecha no han sido recibidos";
    exit;
}

// Guardar el id de producto recibido por la URL
$id = $_GET['id_producto'];

// Guardar la fecha recibida por la URL
$fechaRecibida = $_GET['fecha'];

// Consulta SQL para obtener información detallada del producto
// - Se obtienen todos los campos de la tabla productos (p.*)
// - Se hace INNER JOIN con la tabla categorías (c) para obtener su nombre
// - Se hace LEFT JOIN con subcategorías (s) para que, incluso si el producto no tiene subcategoría, igual se devuelva el registro
//   (si fuera INNER JOIN, los productos sin subcategoría quedarían fuera del resultado)
$sql = "SELECT p.*, 
               c.nombre_categoria, 
               s.id_subcategoria,
               s.nombre_subcategoria
        FROM tbl_productos p
        INNER JOIN tbl_categorias c ON p.id_categoria = c.id_categoria
        LEFT JOIN tbl_subcategorias s ON c.id_subcategoria = s.id_subcategoria 
        WHERE p.id_producto = '$id'";

// Ejecutar la consulta en la base de datos
$consulta = mysqli_query($con, $sql);

// Obtener la fila con los resultados de la consulta
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
    <title>Planificar inventarios</title>
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

                <h1 class="tituloCentro">Información producto</h1>
                    
                    <div class="contenedorConfirmar">
                        <div class="datosConfirmar1">
                            <p class="datos1Fuente">Id: <?= $fila['id_producto'] ?></p>
                            <p class="datos1Fuente">Nombre: <?= $fila['nombre_producto']?></p>
                            <p class="datos1Fuente">Descripción: <?= $fila['descripcion_producto']?></p>
                            <p class="datos1Fuente">Marca: <?= $fila['marca_producto']?></p>
                            <p class="datos1Fuente">Referencia: <?= $fila['referencia_producto']?></p>
                            <p class="datos1Fuente">Stock: <?= $fila['stock_producto']?></p>
                            <p class="datos1Fuente">Unidad: <?= $fila['unidad_producto']?></p>
                            <p class="datos1Fuente">Id Categoria: <?= $fila['id_categoria'] ?></p>
                            <p class="datos1Fuente">Categoria: <?= $fila['nombre_categoria'] ?></p>
                            <p class="datos1Fuente">Id Subcategoria: <?= $fila['id_subcategoria'] ? $fila['id_subcategoria'] : 'No Aplica.' ?></p>                          
                            <p class="datos1Fuente">Subcategoria: <?= $fila['nombre_subcategoria'] ? $fila['nombre_subcategoria'] : 'No Aplica.' ?></p>              
                            <!-- Mostramos el id y nombre de la subcategoría solo si existe.
                            Si el producto no tiene id o subcategoría (es NULL o vacío), mostramos el texto "No Aplica."
                            Esto evita errores al acceder a valores nulos en productos que no tienen subcategoría asignada. -->                     
                        </div>
                    </div>
                    
                    <p class="mensajeConfirmar">Regresar a la pantalla anterior</p>
                    <div class="botonesSiNo">
                        <a class="botonEliminarUsu" href="3_02_03_asignarProductos.php?fecha=<?= urlencode($fechaRecibida) ?>">Atras</a>
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