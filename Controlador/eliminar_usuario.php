<?php
include ("../Modelo/conexion.php");
$con = conexion();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtenemos el id del usuario que llega por la URL mediante el método GET
$id = $_GET['id_usuario'];

// En lugar de eliminar el registro, actualizamos el estado a 0 (inactivo)
$sql = "UPDATE Tbl_Usuarios SET estado_usuario = 0 WHERE id_usuario = '$id'";

// Ejecutamos la consulta en la base de datos
$consulta = mysqli_query($con, $sql);

// Verificamos si la "eliminación" (inactivación) fue exitosa
if ($consulta) {
   // Redirigimos de nuevo a la pantalla de edición/eliminación
   Header("Location: ../Vista/3_01_04_editarOEliminar.php");
} else {
   // En caso de error, muestra el siguiente mensaje
   echo "Error al inactivar el usuario.";
}



