<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos el correo de la sesión
    if (!isset($_SESSION['correo_recuperacion'])) {
        echo "No se encontró información de sesión. Por favor, vuelve a enviar el código.";
        exit;
    }
    $correo = $_SESSION['correo_recuperacion'];

    // Código ingresado por el usuario
    $codigo_ingresado = isset($_POST['codigo2']) ? trim($_POST['codigo2']) : '';

    if (empty($codigo_ingresado)) {
        echo "Por favor, ingresa el código.";
        exit;
    }

    // Obtener código y expiración de la base de datos
    $stmt = $con->prepare("SELECT codigo_recuperacion, expiracion_codigo FROM tbl_usuarios WHERE correo_usuario = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "No se encontró la cuenta asociada.";
        exit;
    }

    $row = $result->fetch_assoc();

    // Validar código y expiración
    if ($codigo_ingresado !== $row['codigo_recuperacion']) {
        header("Location: ../Vista/1_03_restablecerContraseña_1.php?error_codigo=1");
        exit;
    }


    $fecha_actual = new DateTime();
    $fecha_expiracion = new DateTime($row['expiracion_codigo']);

    if ($fecha_actual > $fecha_expiracion) {
        echo "El código ha expirado. Por favor, solicita uno nuevo.";
        exit;
    }

    // Código válido y no expirado, permitir cambiar contraseña
    // Podemos guardar algo en sesión para confirmar la verificación y redirigir
    $_SESSION['codigo_verificado'] = true;

    header("Location: ../Vista/1_04_restablecerContraseña_2.php");
    exit;
}
?>
