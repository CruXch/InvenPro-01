<?php
include("../Modelo/validar_sesion.php");
include("../Modelo/conexion.php");
$con = conexion();

// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si vienen los datos por POST (desde 4_02)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cantidadConteo'], $_POST['comentariosConteo'], $_POST['id_toma'], $_POST['id_producto'], $_POST['nombre_producto'])) {
    $_SESSION['datos_toma'] = [
        'cantidad' => $_POST['cantidadConteo'],
        'comentarios' => $_POST['comentariosConteo'],
        'id_toma' => $_POST['id_toma'],
        'id_producto' => $_POST['id_producto'],
        'nombre_producto' => $_POST['nombre_producto']
    ];
}

// Si se hizo clic en “Sí” (confirmación)
if (isset($_POST['confirmar_toma']) && isset($_SESSION['datos_toma'])) {
    $datos = $_SESSION['datos_toma'];
    $id_toma = $datos['id_toma'];
    $id_producto = $datos['id_producto'];
    $cantidad = $datos['cantidad'];
    $comentarios = $datos['comentarios'];

    // 1. Obtener el stock actual más reciente de ese producto
    $sqlStock = "SELECT stock_ingreso FROM tbl_Ingresos_Csv WHERE id_producto = ? ORDER BY fecha_ingreso DESC LIMIT 1";
    $stmtStock = $con->prepare($sqlStock);
    $stmtStock->bind_param("i", $id_producto);
    $stmtStock->execute();
    $resultadoStock = $stmtStock->get_result();

    if ($filaStock = $resultadoStock->fetch_assoc()) {
        $stock_actual = $filaStock['stock_ingreso'];
    } else {
        $stock_actual = 0; // Si no hay stock registrado
    }
    $stmtStock->close();

    // 2. Calcular diferencia
    $diferencia = $cantidad - $stock_actual;

    // 3. Actualizar la tabla de tomas de inventario
    $sql = "UPDATE Tbl_Tomas_De_Inventario 
            SET unidades_toma = ?, comentarios_toma = ?, estado_toma = 'completada', diferencia_toma = ?
            WHERE id_toma = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("isii", $cantidad, $comentarios, $diferencia, $id_toma);

    if ($stmt->execute()) {
        unset($_SESSION['datos_toma']);
        header("Location: 4_04_mensajeConteo.php");
        exit;
    } else {
        echo "Error al guardar los datos: " . $con->error;
    }
}

// Si no hay datos en sesión, redirigir al paso anterior sin datos
if (!isset($_SESSION['datos_toma'])) {
    header("Location: 4_02_tarjetaConteo.php");
    exit;
}

$datos = $_SESSION['datos_toma'];

// Configurar idioma español para la fecha
setlocale(LC_TIME, 'spanish');
$fecha_actual = strftime('%d/%m/%Y - %A');
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

        <div class="contenedorConfirmar">
            <div class="datosConfirmar1">
                <p class="datos1Fuente">Fecha actual: <?= ucfirst($fecha_actual) ?></p>
                <p class="datos1Fuente">Id toma: <?= htmlspecialchars($datos['id_toma']) ?></p>
                <p class="datos1Fuente">Id producto: <?= htmlspecialchars($datos['id_producto']) ?></p>
                <p class="datos1Fuente">Nombre producto: <?= htmlspecialchars($datos['nombre_producto']) ?></p>
                <p class="datos2Fuente">Cantidad: <?= htmlspecialchars($datos['cantidad']) ?> Unidades.</p>
                <p class="datos2Fuente">Comentarios: <?= htmlspecialchars($datos['comentarios']) ?></p>
            </div>
        </div>

        <div class="contenedorOpcion">
            <p class="mensajeConfirmar">¿Desea terminar y enviar la siguiente toma de inventario?</p>
            <form method="POST">
                <div class="botonesSiNo">
                    <button type="submit" name="confirmar_toma" class="botonSi-No">Sí</button>
                    <a class="botonSi-No" href="4_02_tarjetaConteo.php?id_toma=<?= $datos['id_toma'] ?>&id_producto=<?= $datos['id_producto'] ?>&nombre_producto=<?= urlencode($datos['nombre_producto']) ?>">No</a>
                </div>
            </form>
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

