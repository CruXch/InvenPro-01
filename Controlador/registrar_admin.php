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

if ($contraAdmin !== $confirmarContraAdmin){
    echo "Las contraseñas no coinciden, intentelo de nuevo";
    exit;
}

$sql = "INSERT INTO tbl_usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol) 
VALUES ('$cedulaAdmin','$contraAdmin','$nombresAdmin','$apellidosAdmin','$correoAdmin','$celularAdmin','$rolAdmin' )";
$consulta = mysqli_query($con, $sql);

if($consulta){
    Header("Location:../Vista/1_02_registroAdministrador.php");
}else{

}

?>