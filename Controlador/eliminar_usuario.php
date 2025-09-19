<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Obtenemos el id del usuario que llega por la URL mediante el método GET
$id = $_GET['id_usuario'];

// Creamos la consulta SQL para eliminar al usuario cuyo id coincida con el recibido
$sql = "DELETE FROM Tbl_usuarios WHERE id_usuario='$id'";

// Ejecutamos la consulta en la base de datos
$consulta = mysqli_query($con, $sql);

// Verificamos si la eliminación fue exitosa
if ($consulta) {
   // Si todo salió bien, redirigimos de nuevo a la pantalla de edición/eliminación
   Header("Location: ../Vista/3_01_04_editarOEliminar.php");
} else {
   // En caso de error, muestra el siguiente mensaje
   echo "Error al eliminar el usuario.";
}

