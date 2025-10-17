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

// Validamos el rol recibido y lo convertimos a su valor numérico en la base de datos
if ($rol == 'Contador') {
    $rol = 2; // Si es Contador, asignamos 2
} elseif ($rol == 'Jefe') {
    $rol = 3; // Si es Jefe, asignamos 3
} elseif ($rol == 'Administrador') {
    $rol = 1; // Si es Admin, asignamos 1
}else {
    // Si no coincide con ninguno de los roles válidos, mostramos un error
    echo "Error elección de rol";
}

// Creamos la consulta para actualizar los datos del usuario con la información recibida
$sql = "UPDATE Tbl_Usuarios 
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

