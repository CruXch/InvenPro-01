<?php
// Iniciamos la sesión para poder trabajar con variables de sesión en este archivo
session_start();
include("../Modelo/conexion.php");
$con = conexion();

// Verificamos si el código de recuperación fue validado anteriormente
if (!isset($_SESSION['codigo_verificado']) || $_SESSION['codigo_verificado'] !== true) {
    // Si no está validado, enviamos al usuario de vuelta a la pantalla donde se pide el código
    header("Location: 1_03_restablecerContraseña_1.php");
    exit; // Detenemos la ejecución del script para evitar que siga
}

// Validamos que la petición haya llegado por medio de un formulario (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardamos las contraseñas ingresadas en variables
    $nueva_contra = isset($_POST['nueva-contra']) ? $_POST['nueva-contra'] : '';
    $confirmar_contra = isset($_POST['confirmar-contra']) ? $_POST['confirmar-contra'] : '';

    // Revisamos que ambas contraseñas sean iguales
    if ($nueva_contra !== $confirmar_contra) {
        // Si no coinciden, mostramos una alerta y regresamos al formulario
        echo "<script>alert('Las contraseñas no coinciden.');</script>";
        echo "<script>window.location.href = '../Vista/1_04_restablecerContraseña_2.php';</script>";
        exit; // Salimos para que no siga ejecutando el resto del código
    }

    // Revisamos que exista en la sesión el correo de recuperación
    if (!isset($_SESSION['correo_recuperacion'])) {
        // Si no está, mostramos un mensaje de error y detenemos la ejecución
        echo "No se encontró información de sesión. Por favor, vuelve a enviar el código.";
        exit;
    }
    // Guardamos el correo desde la sesión
    $correo = $_SESSION['correo_recuperacion'];

    // Creamos la consulta para actualizar la contraseña del usuario en la base de datos
    $stmt = $con->prepare("UPDATE tbl_usuarios SET contrasena_usuario = ? WHERE correo_usuario = ?");
    $stmt->bind_param("ss", $nueva_contra, $correo);

    // Ejecutamos la consulta
    if ($stmt->execute()) {
        // Si se actualizó correctamente, eliminamos las variables de sesión usadas
        unset($_SESSION['codigo_verificado']);
        unset($_SESSION['correo_recuperacion']);

        // Y redirigimos al usuario a una pantalla de confirmación
        header("Location: ../Vista/1_05_contraseñaRestablecida.html");
        exit;
    } else {
        // Si ocurre un error en la base de datos, mostramos un mensaje de error
        echo "Hubo un error al actualizar la contraseña. Por favor, intenta de nuevo.";
    }
}




