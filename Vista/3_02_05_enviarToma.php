<?php
include("../Modelo/conexion.php");
$con = conexion();

if (!isset($_POST['fecha']) || !isset($_POST['productos_seleccionados']) || !isset($_POST['contadores_asignados'])) {
    echo "Faltan datos necesarios para generar el resumen.";
    exit;
}

$fecha = mysqli_real_escape_string($con, $_POST['fecha']);

// Asegurarse de que los productos y contadores sean arrays
$productos = $_POST['productos_seleccionados'];
if (!is_array($productos)) {
    $productos = [$productos];
}

$contadores = $_POST['contadores_asignados'];
if (!is_array($contadores)) {
    $contadores = [$contadores];
}

// Obtener nombres de los productos seleccionados
$productosInfo = [];
foreach ($productos as $id_producto) {
    $id_escapado = mysqli_real_escape_string($con, $id_producto);
    $sqlProducto = "SELECT nombre_producto FROM tbl_productos WHERE id_producto = '$id_escapado'";
    $resProducto = mysqli_query($con, $sqlProducto);
    if ($fila = mysqli_fetch_assoc($resProducto)) {
        $productosInfo[] = [
            'id' => $id_producto,
            'nombre' => $fila['nombre_producto']
        ];
    }
}

// Obtener nombres de los contadores seleccionados
$contadoresInfo = [];
foreach ($contadores as $id_contador) {
    $id_escapado = mysqli_real_escape_string($con, $id_contador);
    $sqlContador = "SELECT nombre_usuario, apellido_usuario FROM Tbl_Usuarios WHERE id_usuario = '$id_escapado'";
    $resContador = mysqli_query($con, $sqlContador);
    if ($fila = mysqli_fetch_assoc($resContador)) {
        $contadoresInfo[] = $fila['nombre_usuario'] . " " . $fila['apellido_usuario'];
    }
}

// Formatear fecha para mostrar
$fechaObj = new DateTime($fecha);
setlocale(LC_TIME, 'spanish');
$fechaFormateada = strftime("%A, %e de %B de %Y", $fechaObj->getTimestamp());
$fechaFormateada = ucfirst($fechaFormateada);
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
                        <p class="nombres">David Fernando</p>
                        <p class="nombres">Pérez Ortiz</p>
                        <p class="cargo">Administrador</p>
                    </div>
                </div>
                <div class="menuFechaIcono">
                    <p class="menuFecha">17/10/2024 - Jueves</p>
                    <div class="menuIcono">
                        <a href="2_01_menuAdministrador.html"><i class="fa-solid fa-house-user"></i></a>
                    </div>
                </div>
            </div>

            <div>
                <button class="botonMenu" id="acordeon1">Registrar usuarios <i class="fa-sharp fa-solid fa-chevron-down" id="icono1"></i></button>
                <div class="acordeon" id="acordeon1opciones">
                    <a href="3_01_01_registrarUsuarios.html">Registrar nuevo usuario</a>
                    <a href="3_01_04_editarOEliminar.html">Lista de usuarios</a>
                    <a href="3_01_08_listaAdmin.html">Lista de usuarios administrador</a>
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
                <a href="1_01_login.html" class="botonCerrar">Cerrar sesión <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i></a>
            </div>
        </div>

        <div class="menu2">
            <h1 class="tituloCentro">Planificar inventarios</h1>
            
            <div class="contenedorConfirmar">
                <div class="datosConfirmar1">
                    <p class="datos2Fuente"><?= htmlspecialchars($fechaFormateada) ?></p>
                    <p class="datos2Fuente">Id toma: (se generará al confirmar)</p>

                    <h2 class="datos2Fuente">Productos asignados:</h2>
                    <ul>
                        <?php foreach ($productosInfo as $producto): ?>
                            <p class="datos2Fuente"><strong>ID:</strong> <?= htmlspecialchars($producto['id']) ?> - <?= htmlspecialchars($producto['nombre']) ?></p>
                        <?php endforeach; ?>
                    </ul>

                    <h2 class="datos2Fuente">Contadores asignados:</h2>
                    <ul>
                        <?php foreach ($contadoresInfo as $contador): ?>
                            <p class="datos2Fuente"><?= htmlspecialchars($contador) ?></p>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="contenedorOpcion">
                <p class="mensajeConfirmar">¿Desea asignar la siguiente toma de inventario y notificar al/los contadores?</p>
                <div class="botonesSiNo">
                    <!-- ✅ Nuevo formulario que enviará la información a guardar_toma.php -->
                    <form method="POST" action="../Controlador/guardar_toma_planificada.php" style="display:inline;">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
                        <?php foreach ($productos as $producto): ?>
                            <input type="hidden" name="productos_seleccionados[]" value="<?= htmlspecialchars($producto) ?>">
                        <?php endforeach; ?>
                        <?php foreach ($contadores as $contador): ?>
                            <input type="hidden" name="contadores_asignados[]" value="<?= htmlspecialchars($contador) ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="botonSi-No">Sí</button>
                    </form>

                    <!-- Botón No: vuelve atrás -->
                    <form method="POST" action="3_02_04_asignarContador.php" style="display:inline;">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
                        <?php foreach ($productos as $producto): ?>
                            <input type="hidden" name="productos_seleccionados[]" value="<?= htmlspecialchars($producto) ?>">
                        <?php endforeach; ?>
                        <?php foreach ($contadores as $contador): ?>
                            <input type="hidden" name="contadores_asignados[]" value="<?= htmlspecialchars($contador) ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="botonSi-No">No</button>
                    </form>
                </div>
            </div>  
        </div>
    </div>

    <div class="menuFooter">
        <footer>
            <p>Términos de servicio ©2025 Inventory Project - InvenPro</p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>


