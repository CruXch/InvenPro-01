<?php
// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegúrate de que las rutas coincidan con tu estructura
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

session_start();
include("../Modelo/conexion.php");
$con = conexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo no válido.";
        exit;
    }

    $stmt = $con->prepare("SELECT id_usuario FROM tbl_usuarios WHERE correo_usuario = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "No existe ninguna cuenta con ese correo.";
        exit;
    }

    $codigo = rand(100000, 999999);
    $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $stmt = $con->prepare("UPDATE tbl_usuarios SET codigo_recuperacion = ?, expiracion_codigo = ? WHERE correo_usuario = ?");
    $stmt->bind_param("sss", $codigo, $expiracion, $correo);

    if ($stmt->execute()) {
        $_SESSION['correo_recuperacion'] = $correo;

        // Envío de correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'somosinvenpro@gmail.com';          // Correo Gmail
            $mail->Password   = 'ztynpwatvxqgeeqt';                 // Contraseña de aplicación (no la normal)
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Remitente y destinatario
            $mail->setFrom('somosinvenpro@gmail.com', 'InvenPro');
            $mail->addAddress($correo);  // Correo del usuario

            // Contenido
            $mail->Subject = 'Código para restablecer contraseña InvenPro';
            $mail->Body    = "Hola! Tu código para restablecer tu contraseña es: $codigo\n\nEste código expirará en 30 minutos.";
            $mail->isHTML(false); // texto plano

            $mail->send();

            // Redirigir después de enviar
            header("Location: ../Vista/1_03_restablecerContraseña_1.php?codigo_enviado=1");
            exit;

        } catch (Exception $e) {
            echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Error al guardar el código en la base de datos.";
    }
}
?>



