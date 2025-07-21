<?php
include ("../Modelo/conexion.php");
$con = conexion();

$id = $_POST['cedula-registro'];
$contrasena = $_POST['contra-registro'];
$nombre = $_POST['nombres-registro'];
$apellido = $_POST['apellidos-registro'];
$correo = $_POST['correo-registro'];
$celular = $_POST['celular-registro'];
$rol = $_POST['tipo-usuario-registro'];

if ($rol == 'Administrador'){
    $rol = 1;
} else{
    echo "Error elección de rol";
}

$sql = "UPDATE Tbl_usuarios SET contrasena_usuario='$contrasena', nombre_usuario='$nombre', apellido_usuario='$apellido',
correo_usuario='$correo', telefono_usuario='$celular', id_rol='$rol' WHERE id_usuario='$id'";
$consulta = mysqli_query($con, $sql);

if($consulta){
    Header ("Location: ../Vista/3_01_08_listaAdmin.php");
} else{
    echo "Error al actualizar el usuario.";
}