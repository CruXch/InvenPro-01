<?php 
include ("../Modelo/conexion.php");
$con = conexion();

// --- Capturamos los datos del formulario enviados por POST ---
$nombresAdmin = $_POST['nombres-admin'];          // Nombres del administrador
$apellidosAdmin = $_POST['apellidos-admin'];      // Apellidos del administrador
$cedulaAdmin = $_POST['cedula-admin'];            // Cédula (será el ID de usuario)
$celularAdmin = $_POST['celular-admin'];          // Teléfono del administrador
$correoAdmin = $_POST['correo-admin'];            // Correo electrónico
$contraAdmin = $_POST['contra-admin'];            // Contraseña ingresada
$confirmarContraAdmin = $_POST['confirmar-contra-admin']; // Confirmación de contraseña
$rolAdmin = 1;                                    // Rol fijo de Administrador en la base de datos

// --- Validación: Verificamos que ambas contraseñas coincidan ---
if ($contraAdmin !== $confirmarContraAdmin){
    // Si no coinciden, mostramos un mensaje y regresamos a la página anterior
    echo "<script>
        alert('Las contraseñas no coinciden, inténtelo de nuevo');
        window.history.back();
    </script>";
    exit;
}

// --- Preparar e insertar el nuevo administrador en la tabla tbl_usuarios ---
$sql = "INSERT INTO tbl_usuarios (id_usuario, contrasena_usuario, nombre_usuario, apellido_usuario, correo_usuario, telefono_usuario, id_rol) 
        VALUES ('$cedulaAdmin','$contraAdmin','$nombresAdmin','$apellidosAdmin','$correoAdmin','$celularAdmin','$rolAdmin')";

// Ejecutamos la consulta de inserción
$consulta = mysqli_query($con, $sql);

// --- Verificamos el resultado de la inserción ---
if ($consulta) {
    // Si todo fue bien, mostramos un mensaje y redirigimos al login
    echo "<script>
        alert('Usuario registrado correctamente');
        window.location.href = '../Vista/1_01_login.html';
    </script>";
    exit;
} else {
    // Si hubo un error, mostramos un mensaje y permitimos al usuario volver atrás
    echo "<script>
        alert('Ocurrió un error al registrar el usuario. Inténtelo nuevamente.');
        window.history.back();
    </script>";
    exit;
}

