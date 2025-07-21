<?php
include("../InvenPro/Modelo/conexion.php");

function registrarUsuario($nombres, $apellidos, $cedula, $celular, $correo, $contrasena, $tipo) {
    $con = conexion();

    // Asignar ID de rol
    if ($tipo == 'Contador') {
        $tipoUsuario = 2;
    } elseif ($tipo == 'Jefe') {
        $tipoUsuario = 3;
    } else {
        return "Tipo de usuario inválido";
    }

    $sql = "INSERT INTO Tbl_Usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol)
            VALUES ('$cedula','$contrasena','$nombres','$apellidos','$correo','$celular','$tipoUsuario')";
    
    $consulta = mysqli_query($con, $sql);

    if ($consulta) {
        return true;
    } else {
        return false;
    }
}
