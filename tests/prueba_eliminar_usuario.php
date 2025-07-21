<?php
require_once "../InvenPro/Modelo/conexion.php"; // si la conexión está en otro archivo
function eliminarUsuario($id) {
    $con = conexion();

    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM Tbl_usuarios WHERE id_usuario='$id'";
    $consulta = mysqli_query($con, $sql);

    if (!$consulta) {
        return false;
    }

    return mysqli_affected_rows($con) > 0;
}


