<?php
session_start();
include("../Modelo/conexion.php");
$con = conexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contraseña'] ?? '';

    if (empty($usuario) || empty($contrasena)){ //Funcion para validar que no hayan campos en blanco
        echo 
        "<script>
        alert('Por favor ingrese su usuario y contraseña');
        window.location.href='../Vista/1_01_login.php';
        </script>";
        exit();
    }

    // Consulta para verificar credenciales
    $query = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, u.contrasena_usuario, r.id_rol, r.nombre_rol 
              FROM Tbl_Usuarios u
              INNER JOIN Tbl_Roles r ON u.id_rol = r.id_rol
              WHERE u.id_usuario = ? AND u.contrasena_usuario = ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param("is", $usuario, $contrasena); // asume que el usuario es numérico
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuarioDatos = $resultado->fetch_assoc();

        // Guardar sesión si lo deseas
        $_SESSION['id_usuario'] = $usuarioDatos['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuarioDatos['nombre_usuario'];
        $_SESSION['apellido_usuario'] = $usuarioDatos['apellido_usuario'];
        $_SESSION['rol'] = $usuarioDatos['nombre_rol'];

        // Redirección por rol con alert
        $rol = strtolower($usuarioDatos['nombre_rol']);

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
                echo "<script>alert('Rol no reconocido'); window.location.href='../Vista/1_01_login.php';</script>";
                break;
        }
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='../Vista/1_01_login.php';</script>";
    }
}

