<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

// Verificamos si el código fue validado previamente
if (!isset($_SESSION['codigo_verificado']) || $_SESSION['codigo_verificado'] !== true) {
    // Si no está verificado, redirigimos de vuelta
    header("Location: 1_03_restablecerContraseña_1.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos las contraseñas
    $nueva_contra = isset($_POST['nueva-contra']) ? $_POST['nueva-contra'] : '';
    $confirmar_contra = isset($_POST['confirmar-contra']) ? $_POST['confirmar-contra'] : '';

    // Verificamos que las contraseñas coincidan
    if ($nueva_contra !== $confirmar_contra) {
        // Si no coinciden, redirigimos con un mensaje de alerta
        echo "<script>alert('Las contraseñas no coinciden.');</script>";
        echo "<script>window.location.href = '../Vista/1_04_restablecerContraseña_2.php';</script>";
        exit;
    }

    // Obtener el correo de la sesión
    if (!isset($_SESSION['correo_recuperacion'])) {
        echo "No se encontró información de sesión. Por favor, vuelve a enviar el código.";
        exit;
    }
    $correo = $_SESSION['correo_recuperacion'];

    // Actualizamos la contraseña en la base de datos (sin encriptarla)
    $stmt = $con->prepare("UPDATE tbl_usuarios SET contrasena_usuario = ? WHERE correo_usuario = ?");
    $stmt->bind_param("ss", $nueva_contra, $correo);

    if ($stmt->execute()) {
        // Limpiamos las variables de sesión
        unset($_SESSION['codigo_verificado']);
        unset($_SESSION['correo_recuperacion']);

        // Redirigimos al login con un mensaje de éxito
        header("Location: ../Vista/1_05_contraseñaRestablecida.html");
        exit;
    } else {
        echo "Hubo un error al actualizar la contraseña. Por favor, intenta de nuevo.";
    }
}
?>


