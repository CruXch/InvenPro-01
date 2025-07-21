<?php
include ("../Modelo/conexion.php");
$con = conexion();

$id = $_GET['id_usuario'];

$sql = "DELETE FROM Tbl_usuarios WHERE id_usuario='$id'";
$consulta = mysqli_query($con, $sql);

if ($consulta){
   Header("Location: ../Vista/3_01_04_editarOEliminar.php");
} else {

}

?>