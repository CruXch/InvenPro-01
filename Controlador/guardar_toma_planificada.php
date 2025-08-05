<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Asegúrate de que existan los valores esperados
if (!isset($_POST['fecha']) || !isset($_POST['productos_seleccionados']) || !isset($_POST['contadores_asignados'])) {
    echo "Faltan datos necesarios.";
    exit;
}

$fecha = $_POST['fecha'];
$id_producto = $_POST['productos_seleccionados'][0];  // [0] para que verifique que es el primer id en caso de ser un array con varios productos
$id_usuario = $_POST['contadores_asignados'][0];      // [0] para que verifique que es el primer id en caso de ser un array con varios contadores
                                                      //Para escalar a mas productos a la vez o contadores se debe implementar un foreach, entre otras cosas.

// Insertar toma de inventario
$sql = "INSERT INTO Tbl_Tomas_De_Inventario (fecha_toma, unidades_toma, comentarios_toma, diferencia_toma)
        VALUES (?, NULL, '', NULL)";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();

$id_toma = $stmt->insert_id; // Guarda el ID de la toma recién insertada

if ($id_toma) {
    // 1️⃣ Insertar en Tbl_Productos_Tomas_De_Inventario
    $sql1 = "INSERT INTO Tbl_Productos_Tomas_De_Inventario (id_producto, id_toma) VALUES (?, ?)";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("ii", $id_producto, $id_toma);
    $stmt1->execute();

    // 2️⃣ Insertar en Tbl_Productos_Usuarios
    $sql2 = "INSERT INTO Tbl_Productos_Usuarios (id_producto, id_usuario) VALUES (?, ?)";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("ii", $id_producto, $id_usuario);
    $stmt2->execute();

    // Redirigir si todo fue exitoso
    Header("Location: ../Vista/3_02_06_mensajeToma.php");
} else {
    echo "Error al guardar la toma.";
}





