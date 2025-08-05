<?php 
include ("../Modelo/conexion.php");
$con = conexion();

$nombresAdmin = $_POST['nombres-admin'];
$apellidosAdmin = $_POST['apellidos-admin'];
$cedulaAdmin = $_POST['cedula-admin'];
$celularAdmin = $_POST['celular-admin'];
$correoAdmin = $_POST['correo-admin'];
$contraAdmin = $_POST['contra-admin'];
$confirmarContraAdmin = $_POST['confirmar-contra-admin'];
$rolAdmin = 1;

// Validación de contraseña
if ($contraAdmin !== $confirmarContraAdmin){
    echo "<script>
        alert('Las contraseñas no coinciden, inténtelo de nuevo');
        window.history.back();
    </script>";
    exit;
}

$sql = "INSERT INTO tbl_usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol) 
        VALUES ('$cedulaAdmin','$contraAdmin','$nombresAdmin','$apellidosAdmin','$correoAdmin','$celularAdmin','$rolAdmin')";

$consulta = mysqli_query($con, $sql);

if ($consulta) {
    echo "<script>
        alert('Usuario registrado correctamente');
        window.location.href = '../Vista/1_01_login.html';
    </script>";
    exit;
} else {
    echo "<script>
        alert('Ocurrió un error al registrar el usuario. Inténtelo nuevamente.');
        window.history.back();
    </script>";
    exit;
}
?>