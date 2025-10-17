<?php
session_start(); // Inicia o reanuda la sesión para usar variables de sesión
include("../Modelo/conexion.php");
$con = conexion();

// Verifica que la petición se haya hecho por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se obtiene el correo guardado en sesión durante el proceso de recuperación
    if (!isset($_SESSION['correo_recuperacion'])) {
        echo "No se encontró información de sesión. Por favor, vuelve a enviar el código.";
        exit;
    }
    $correo = $_SESSION['correo_recuperacion'];

    // Captura el código ingresado por el usuario desde el formulario
    $codigo_ingresado = isset($_POST['codigo2']) ? trim($_POST['codigo2']) : '';

    // Validación: si no se ingresó código
    if (empty($codigo_ingresado)) {
        echo "Por favor, ingresa el código.";
        exit;
    }

    // Prepara la consulta para obtener de la BD el código y su fecha de expiración
    $stmt = $con->prepare("SELECT codigo_recuperacion, expiracion_codigo FROM Tbl_Usuarios WHERE correo_usuario = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si no se encontró ningún usuario con ese correo
    if ($result->num_rows === 0) {
        echo "No se encontró la cuenta asociada.";
        exit;
    }

    // Extrae el resultado en un arreglo asociativo
    $row = $result->fetch_assoc();

    // Compara el código ingresado por el usuario con el guardado en BD
    if ($codigo_ingresado !== $row['codigo_recuperacion']) {
        // Si no coinciden, redirige con parámetro de error
        header("Location: ../Vista/1_03_restablecerContraseña_1.php?error_codigo=1");
        exit;
    }

    // Crear objetos DateTime para comparar fecha actual con la de expiración
    $fecha_actual = new DateTime();
    $fecha_expiracion = new DateTime($row['expiracion_codigo']);

    // Verifica si el código ya expiró
    if ($fecha_actual > $fecha_expiracion) {
        echo "El código ha expirado. Por favor, solicita uno nuevo.";
        exit;
    }

    // Si el código es válido y no expiró, se marca en sesión que fue verificado
    $_SESSION['codigo_verificado'] = true;

    // Redirige a la pantalla para cambiar la contraseña
    header("Location: ../Vista/1_04_restablecerContraseña_2.php");
    exit;
}

