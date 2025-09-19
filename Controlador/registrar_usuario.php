<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Captura los valores enviados por el formulario mediante el método POST
$nombresUsuario   = $_POST['nombres-registro'];
$apellidosUsuario = $_POST['apellidos-registro'];
$cedulaUsuario    = $_POST['cedula-registro'];
$celularUsuario   = $_POST['celular-registro'];
$correoUsuario    = $_POST['correo-registro'];
$contraUsuario    = $_POST['contra-registro'];
$tipoUsuario      = $_POST['tipo-usuario-registro'];

// Convierte el tipo de usuario en un valor numérico para la base de datos
if ($tipoUsuario == 'Contador'){
    $tipoUsuario = 2; // Rol: Contador
} elseif ($tipoUsuario == 'Jefe'){
    $tipoUsuario = 3; // Rol: Jefe
} else{
    echo "No existe el tipo de usuario seleccionado";
}

// Inserta el nuevo usuario en la tabla Tbl_Usuarios
$sql = "INSERT INTO Tbl_Usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol)
VALUES ('$cedulaUsuario','$contraUsuario','$nombresUsuario','$apellidosUsuario','$correoUsuario','$celularUsuario','$tipoUsuario')";
$consulta = mysqli_query($con, $sql);

// Verifica si la consulta fue exitosa
if ($consulta){
    Header ("Location:../Vista/3_01_03_mensajeRegistro.php"); // Redirige a pantalla de éxito
} else{
    echo "Error al registrar el usuario administrador.";
}



