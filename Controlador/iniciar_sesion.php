<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();


error_reporting(E_ALL);
ini_set('display_errors', 1);



if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Recuperamos los datos enviados por el formulario de login
    $usuario = $_POST['usuario'] ?? '';       // ID o nombre de usuario
    $contrasena = $_POST['contraseña'] ?? ''; // Contraseña

    // Validamos que los campos no estén vacíos
    if (empty($usuario) || empty($contrasena)){
        echo 
        "<script>
        alert('Por favor ingrese su usuario y contraseña');
        window.location.href='../Vista/1_01_login.html';
        </script>";
        exit(); // Si faltan datos, detenemos el proceso
    }

    // Consulta para verificar credenciales con JOIN a roles
    $query = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, u.contrasena_usuario, r.id_rol, r.nombre_rol 
              FROM Tbl_Usuarios u
              INNER JOIN Tbl_Roles r ON u.id_rol = r.id_rol
              WHERE u.id_usuario = ? AND u.contrasena_usuario = ?";
    
    // Preparamos y ejecutamos la consulta
    $stmt = $con->prepare($query);
    $stmt->bind_param("is", $usuario, $contrasena); // "i" → entero (id), "s" → string (contraseña)
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificamos si existe un único registro con esas credenciales
    if ($resultado->num_rows === 1) {
        $usuarioDatos = $resultado->fetch_assoc();

        // Guardamos los datos del usuario en la sesión
        $_SESSION['id_usuario'] = $usuarioDatos['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuarioDatos['nombre_usuario'];
        $_SESSION['apellido_usuario'] = $usuarioDatos['apellido_usuario'];
        $_SESSION['rol'] = $usuarioDatos['nombre_rol'];

        // Normalizamos el rol a minúsculas para manejar los casos
        $rol = strtolower($usuarioDatos['nombre_rol']);

        // Redirigimos según el rol, mostrando un mensaje de bienvenida
        switch ($rol) {
            case 'administrador':
                echo "<script>alert('Bienvenido Administrador'); window.location.href='../Vista/2_01_menuAdministrador.php';</script>";
                break;
            case 'jefe':
                echo "<script>alert('Bienvenido Jefe'); window.location.href='../Vista/2_03_menuJefe.php';</script>";
                break;
            case 'contador':
                echo "<script>alert('Bienvenido Contador'); window.location.href='../Vista/2_02_menuContador.php';</script>";
                break;
            default:
                echo "<script>alert('Rol no reconocido'); window.location.href='../Vista/1_01_login.html';</script>";
                break;
        }
    } else {
        // Si no se encontró usuario con esas credenciales
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='../Vista/1_01_login.html';</script>";
    }
}

