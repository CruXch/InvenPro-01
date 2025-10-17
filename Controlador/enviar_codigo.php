<?php
// Cargar las clases de PHPMailer necesarias para enviar correos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir los archivos de PHPMailer según la estructura del proyecto
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Iniciamos la sesión para poder usar variables de sesión
session_start();
include("../Modelo/conexion.php");
$con = conexion();

// Validamos que el formulario haya sido enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos el correo enviado por el formulario y eliminamos espacios al inicio y fin
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';

    // Validamos que el correo tenga un formato válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo no válido.";
        exit;
    }

    // Revisamos si el correo existe en la base de datos
    $stmt = $con->prepare("SELECT id_usuario FROM Tbl_Usuarios WHERE correo_usuario = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si no existe ninguna cuenta con ese correo, mostramos un mensaje
    if ($result->num_rows === 0) {
        echo "No existe ninguna cuenta con ese correo.";
        exit;
    }

    // Generamos un código aleatorio de 6 dígitos para la recuperación
    $codigo = rand(100000, 999999);
    // Definimos la fecha y hora de expiración del código (30 minutos a partir de ahora)
    $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // Guardamos el código y su expiración en la base de datos para el usuario correspondiente
    $stmt = $con->prepare("UPDATE Tbl_Usuarios SET codigo_recuperacion = ?, expiracion_codigo = ? WHERE correo_usuario = ?");
    $stmt->bind_param("sss", $codigo, $expiracion, $correo);

    // Ejecutamos la consulta para guardar el código
    if ($stmt->execute()) {
        // Guardamos el correo en sesión para usarlo en los siguientes pasos del restablecimiento
        $_SESSION['correo_recuperacion'] = $correo;

        // Creamos el objeto PHPMailer para enviar el correo
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP para enviar correos usando Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'somosinvenpro@gmail.com';   // Correo Gmail que enviará el código
            $mail->Password   = 'ztynpwatvxqgeeqt';          // Contraseña de aplicación de Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Configuración del remitente y destinatario
            $mail->setFrom('somosinvenpro@gmail.com', 'InvenPro');
            $mail->addAddress($correo); // Enviamos el correo al usuario

            // Configuramos el contenido del correo
            $mail->Subject = 'Código para restablecer contraseña InvenPro';
            $mail->Body    = "Hola! Tu código para restablecer tu contraseña es: $codigo\n\nEste código expirará en 30 minutos.";
            $mail->isHTML(false); // Enviamos en texto plano

            // Intentamos enviar el correo
            $mail->send();

            // Si el correo se envió correctamente, redirigimos a la pantalla de ingreso del código
            header("Location: ../Vista/1_03_restablecerContraseña_1.php?codigo_enviado=1");
            exit;

        } catch (Exception $e) {
            // Si hubo un error al enviar el correo, mostramos un mensaje con la información del error
            echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }

    } else {
        // Si no se pudo guardar el código en la base de datos, mostramos un mensaje de error
        echo "Error al guardar el código en la base de datos.";
    }
}
?>




