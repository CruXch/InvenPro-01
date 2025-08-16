<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

$id_toma = $_GET['id_toma'] ?? null;

if (!$id_toma) {
    die("Error: ID de toma no proporcionado.");
}

// Consultar la toma actual
$sql = "SELECT * FROM Tbl_Tomas_De_Inventario WHERE id_toma = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id_toma);
$stmt->execute();
$resultado = $stmt->get_result();
$toma = $resultado->fetch_assoc();

if (!$toma) {
    die("Error: Toma no encontrada.");
}

// Consultar productos relacionados con esta toma
$sqlProductos = "
    SELECT 
        p.id_producto, 
        p.nombre_producto,
        put.id_ingreso
    FROM Tbl_Productos p
    INNER JOIN Tbl_Productos_Usuarios_Toma put 
        ON p.id_producto = put.id_producto
    WHERE put.id_toma = ? AND put.id_usuario = ?";
$stmtProductos = $con->prepare($sqlProductos);
$stmtProductos->bind_param("ii", $id_toma, $_SESSION['id_usuario']);
$stmtProductos->execute();
$resultadoProductos = $stmtProductos->get_result();

$productos = [];
while ($fila = $resultadoProductos->fetch_assoc()) {
    $productos[] = $fila;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
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
            <a href="#" class="linkMenu">Documentación</a>
            <a href="#" class="linkMenu">Soporte</a>
            <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
            <a href="#" class="linkMenu">Redes sociales</a>
        </div>

        <div class="contenedorBotonCerrar1">
            <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">
                Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i>
            </a>
        </div>
    </div>

    <div class="menu2">

        <h1 class="tituloCentro">Registrar conteos de inventarios</h1>

        <form action="4_03_enviarConteo.php" method="POST">

    <div class="contenedorInfoConteos2">
        <div class="contenedorConteos2">
    <h2><?= date("l d F Y", strtotime($toma['fecha_toma'])) ?></h2>
    <p>ID toma: <?= $toma['id_toma'] ?></p>
    <input type="hidden" name="id_toma" value="<?= $id_toma ?>">

    <?php if (!empty($productos)): ?>
        <?php 
            // Como solo habrá un producto, tomamos el primero
            $producto = $productos[0];
        ?>
        <p>ID producto: <?= $producto['id_producto'] ?></p>
        <p>Nombre producto: <?= $producto['nombre_producto'] ?></p>
        
        <!-- Inputs ocultos para enviar datos al backend -->
        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
        <input type="hidden" name="nombre_producto" value="<?= $producto['nombre_producto'] ?>">
        <input type="hidden" name="id_ingreso" value="<?= $producto['id_ingreso'] ?>">

    <?php else: ?>
        <p>No hay productos asociados a esta toma.</p>
    <?php endif; ?>

    <div class="contenedorLlenar">
        <label for="cantidadConteo">Cantidad:
            <input type="number" name="cantidadConteo" id="cantidadConteo" placeholder="Unidades" required>
        </label> 
        <label for="comentariosConteo">Comentarios:
            <textarea id="comentariosConteo" name="comentariosConteo" placeholder="Escribir" required></textarea>
        </label>
    </div>
</div>

    </div>

    <div class="botonSiguiente">
        <button type="submit" class="ingresar1">Siguiente</button>
    </div>
</form>

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

