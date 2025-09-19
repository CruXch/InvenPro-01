<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Guardamos en variables la información que llega desde el formulario por método POST
$id = $_POST['cedula-registro'];          
$contrasena = $_POST['contra-registro'];  
$nombre = $_POST['nombres-registro'];     
$apellido = $_POST['apellidos-registro']; 
$correo = $_POST['correo-registro'];      
$celular = $_POST['celular-registro'];    
$rol = $_POST['tipo-usuario-registro'];   

// Validamos que el rol enviado sea válido
if ($rol == 'Administrador') {
    // Si el rol es Administrador, lo convertimos al valor que corresponde en la base de datos
    $rol = 1;
} else {
    // Si se envía un rol no válido, mostramos un mensaje de error
    echo "Error elección de rol";
}

// Creamos la consulta para actualizar los datos del usuario con la información recibida
$sql = "UPDATE Tbl_usuarios 
        SET contrasena_usuario='$contrasena', 
            nombre_usuario='$nombre', 
            apellido_usuario='$apellido',
            correo_usuario='$correo', 
            telefono_usuario='$celular', 
            id_rol='$rol' 
        WHERE id_usuario='$id'";

// Ejecutamos la consulta en la base de datos
$consulta = mysqli_query($con, $sql);

// Verificamos si la actualización fue exitosa
if ($consulta) {
    // Si todo salió bien, redirigimos a la lista de administradores
    Header("Location: ../Vista/3_01_08_listaAdmin.php");
} else {
    // Si hubo un error en la consulta, mostramos un mensaje
    echo "Error al actualizar el usuario.";
}

