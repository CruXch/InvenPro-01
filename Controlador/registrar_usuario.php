<?php
include ("../Modelo/conexion.php");
$con = conexion();

$nombresUsuario = $_POST['nombres-registro'];
$apellidosUsuario = $_POST['apellidos-registro'];
$cedulaUsuario = $_POST['cedula-registro'];
$celularUsuario = $_POST['celular-registro'];
$correoUsuario = $_POST['correo-registro'];
$contraUsuario = $_POST['contra-registro'];
$tipoUsuario = $_POST['tipo-usuario-registro'];

if ($tipoUsuario == 'Contador'){
    $tipoUsuario = 2;
} elseif ($tipoUsuario == 'Jefe'){
    $tipoUsuario = 3;
} else{
    echo "No existe el tipo de usuario seleccionado";
}

$sql = "INSERT INTO Tbl_Usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol)
VALUES ('$cedulaUsuario','$contraUsuario','$nombresUsuario','$apellidosUsuario','$correoUsuario','$celularUsuario','$tipoUsuario')";
$consulta = mysqli_query($con, $sql);

if ($consulta){
    Header ("Location:../Vista/3_01_03_mensajeRegistro.html");
} else{
    echo "Error al registrar el usuario administrador.";
}


?>