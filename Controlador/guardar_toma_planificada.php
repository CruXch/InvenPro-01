<?php
include ("../Modelo/conexion.php");
$con = conexion();

// Verificamos que existan los valores esperados en la petición POST
if (!isset($_POST['productos_seleccionados']) || !isset($_POST['productos_seleccionados']) || !isset($_POST['contadores_asignados'])) {
    echo "Faltan datos necesarios.";
    exit; // Si faltan datos, detenemos la ejecución
}

// Obtenemos la fecha actual al momento de planificar la toma
$fecha = date("Y-m-d");

// Recuperamos los valores enviados desde el formulario (por ahora se toma solo el primero de cada arreglo)
$id_ingreso = $_POST['productos_seleccionados'][0];     
$id_producto = $_POST['productos_seleccionados_id'][0]; 
$id_usuario = $_POST['contadores_asignados'][0];        

// Insertamos un nuevo registro en Tbl_Tomas_De_Inventario
$sql = "INSERT INTO Tbl_Tomas_De_Inventario (fecha_toma, unidades_toma, comentarios_toma, diferencia_toma)
        VALUES (?, NULL, '', NULL)";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();

// Guardamos el ID de la toma recién insertada
$id_toma = $stmt->insert_id;

if ($id_toma) {
    // Relacionamos la toma con el producto, usuario e ingreso en Tbl_Productos_Usuarios_Toma
    $sql2 = "INSERT INTO Tbl_Productos_Usuarios_Toma (id_toma, id_producto, id_usuario, id_ingreso) VALUES (?, ?, ?, ?)";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("iiii", $id_toma, $id_producto, $id_usuario, $id_ingreso);
    $stmt2->execute();

    // Registramos la asignación de la toma al usuario en Tbl_Asignaciones_Toma
    $sql3 = "INSERT INTO Tbl_Asignaciones_Toma (id_toma, id_usuario) VALUES (?, ?)";
    $stmt3 = $con->prepare($sql3);
    $stmt3->bind_param("ii", $id_toma, $id_usuario);
    $stmt3->execute();

    // Redirigimos a la vista de confirmación si todo fue exitoso
    Header("Location: ../Vista/3_02_06_mensajeToma.php");
} else {
    // Si no se pudo crear la toma, mostramos un error
    echo "Error al guardar la toma.";
}







