<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Guardamos en variables la información enviada desde el formulario por método POST
$id = $_POST['cedula-registro'];          // Cédula del usuario que se va a actualizar
$contrasena = $_POST['contra-registro'];  // Nueva contraseña
$nombre = $_POST['nombres-registro'];     // Nombres del usuario
$apellido = $_POST['apellidos-registro']; // Apellidos del usuario
$telefono = $_POST['celular-registro'];   // Número de celular
$correo = $_POST['correo-registro'];      // Correo electrónico
$rol = $_POST['tipo-usuario-registro'];   // Rol elegido (Contador o Jefe)

// Validamos el rol recibido y lo convertimos a su valor numérico en la base de datos
if ($rol == 'Contador') {
    $rol = 2; // Si es Contador, asignamos 2
} elseif ($rol == 'Jefe') {
    $rol = 3; // Si es Jefe, asignamos 3
} else {
    // Si no coincide con ninguno de los roles válidos, mostramos un error
    echo "Error elección de rol";
}

// Creamos la consulta SQL para actualizar los datos del usuario con el id recibido
$sql = "UPDATE Tbl_usuarios 
        SET contrasena_usuario='$contrasena', 
            nombre_usuario='$nombre', 
            apellido_usuario='$apellido', 
            telefono_usuario='$telefono', 
            correo_usuario='$correo', 
            id_rol='$rol' 
        WHERE id_usuario = '$id'";

// Ejecutamos la consulta en la base de datos
$consulta = mysqli_query($con, $sql);

// Verificamos si la actualización se realizó correctamente
if ($consulta) {
    // Si todo salió bien, redirigimos al usuario a la pantalla de edición/eliminación
    Header("Location: ../Vista/3_01_04_editarOEliminar.php");
} else {
    // Si ocurre un error en la consulta, mostramos un mensaje
    echo "Error al actualizar el usuario.";
}

