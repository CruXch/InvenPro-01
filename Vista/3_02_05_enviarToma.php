<?php
// Verifica que el usuario tenga sesión activa antes de acceder
include("../Modelo/validar_sesion.php");

// Incluye la conexión a la base de datos
include("../Modelo/conexion.php");
$con = conexion();

// Validación: comprobar que todos los datos esperados fueron enviados por POST
if (!isset($_POST['fecha']) || !isset($_POST['productos_seleccionados']) || !isset($_POST['productos_seleccionados_id']) || !isset($_POST['contadores_asignados'])) {
    echo "Faltan datos necesarios para generar el resumen.";
    exit;
}

// Escapar la fecha recibida para evitar inyección SQL
$fecha = mysqli_real_escape_string($con, $_POST['fecha']);

// Asegurarse de que los productos sean un array (si no, convertirlo en array)
$productos = $_POST['productos_seleccionados'];
if (!is_array($productos)) {
    $productos = [$productos];
}

// Asegurarse de que los IDs de productos sean un array (si no, convertirlos)
$productosId = $_POST['productos_seleccionados_id'];
if (!is_array($productosId)) {
    $productosId = [$productosId];
}

// Asegurarse de que los contadores sean un array (si no, convertirlos)
$contadores = $_POST['contadores_asignados'];
if (!is_array($contadores)) {
    $contadores = [$contadores];
}

// -------------------- OBTENER NOMBRES DE LOS PRODUCTOS --------------------

// Arreglo para guardar la información de los productos seleccionados
$productosIdInfo = [];

// Recorrer todos los IDs de productos seleccionados
foreach ($productosId as $id_producto) {
    // Escapar el valor para seguridad
    $id_escapado = mysqli_real_escape_string($con, $id_producto);

    // Consultar el nombre del producto en la base de datos
    $sqlProductoId = "SELECT nombre_producto FROM tbl_productos WHERE id_producto = '$id_escapado'";
    $resProductoId = mysqli_query($con, $sqlProductoId);

    // Si se encontró el producto, guardarlo en el arreglo
    if ($fila = mysqli_fetch_assoc($resProductoId)) {
        $productosIdInfo[] = [
            'id' => $id_producto,
            'nombre' => $fila['nombre_producto']
        ];
    }
}

// -------------------- OBTENER NOMBRES DE LOS CONTADORES --------------------

// Arreglo para guardar la información de los contadores seleccionados
$contadoresInfo = [];

// Recorrer todos los IDs de contadores seleccionados
foreach ($contadores as $id_contador) {
    // Escapar el valor para seguridad
    $id_escapado = mysqli_real_escape_string($con, $id_contador);

    // Consultar el nombre y apellido del contador
    $sqlContador = "SELECT nombre_usuario, apellido_usuario FROM Tbl_Usuarios WHERE id_usuario = '$id_escapado'";
    $resContador = mysqli_query($con, $sqlContador);

    // Si se encontró, concatenar nombre y apellido y guardarlo en el arreglo
    if ($fila = mysqli_fetch_assoc($resContador)) {
        $contadoresInfo[] = $fila['nombre_usuario'] . " " . $fila['apellido_usuario'];
    }
}

// -------------------- FORMATEAR FECHA PARA MOSTRAR --------------------

// Crear objeto de fecha a partir de la fecha recibida
$fechaObj = new DateTime($fecha);

// Configurar idioma español para mostrar la fecha
setlocale(LC_TIME, 'spanish');

// Dar formato amigable a la fecha (ejemplo: "Lunes, 15 de Septiembre de 2025")
$fechaFormateada = strftime("%A, %e de %B de %Y", $fechaObj->getTimestamp());

// Poner en mayúscula la primera letra
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
            <h1 class="tituloCentro">Planificar inventarios</h1>
            
            <div class="contenedorConfirmar">
                <div class="datosConfirmar1">
                    <p class="datos2Fuente">Fecha ingreso del producto: <?= htmlspecialchars($fechaFormateada) ?></p>
                    <p class="datos2Fuente">Id toma: (se generará al confirmar)</p>

                    <h2 class="datos2Fuente">Producto asignado:</h2>
                    <ul>
                        <?php foreach ($productosIdInfo as $productoId): ?>
                            <p class="datos2Fuente"><strong>ID:</strong> <?= htmlspecialchars($productoId['id']) ?> - <?= htmlspecialchars($productoId['nombre']) ?></p>
                        <?php endforeach; ?>
                    </ul>

                    <h2 class="datos2Fuente">Contador asignado:</h2>
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
                    <form method="POST" action="../Controlador/guardar_toma_planificada.php">
                        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
                        <?php foreach ($productos as $producto): ?>
                            <input type="hidden" name="productos_seleccionados[]" value="<?= htmlspecialchars($producto) ?>">
                        <?php endforeach; ?>
                        <?php foreach ($productosId as $productoId): ?>
                            <input type="hidden" name="productos_seleccionados_id[]" value="<?= htmlspecialchars($productoId) ?>">
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
                            <input type="hidden" name="productos_seleccionados" value="<?= htmlspecialchars($producto) ?>">
                        <?php endforeach; ?>
                        <?php foreach ($productosId as $productoId): ?>
                            <input type="hidden" name="productos_seleccionados_id" value="<?= htmlspecialchars($productoId) ?>">
                        <?php endforeach; ?>
                        <?php foreach ($contadores as $contador): ?>
                            <input type="hidden" name="contadores_asignados" value="<?= htmlspecialchars($contador) ?>">
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


