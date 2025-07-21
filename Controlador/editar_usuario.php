<?php
include ("../Modelo/conexion.php");
$con = conexion();

$id = $_POST['cedula-registro'];
$contrasena = $_POST['contra-registro'];
$nombre = $_POST['nombres-registro'];
$apellido = $_POST['apellidos-registro'];
$telefono = $_POST['celular-registro'];
$correo = $_POST['correo-registro'];
$rol = $_POST['tipo-usuario-registro'];

if ($rol == 'Contador'){
    $rol = 2;
} elseif ($rol == 'Jefe'){
    $rol = 3;
} else{
    echo "Error elección de rol";
}

$sql = "UPDATE Tbl_usuarios SET contrasena_usuario='$contrasena', nombre_usuario='$nombre', 
apellido_usuario='$apellido', telefono_usuario='$telefono', correo_usuario='$correo', id_rol='$rol' 
WHERE id_usuario = '$id'";
$consulta = mysqli_query($con, $sql);

if ($consulta){
    Header ("Location: ../Vista/3_01_04_editarOEliminar.php");
} else{
    echo "Error al actualizar el usuario.";
}
?>