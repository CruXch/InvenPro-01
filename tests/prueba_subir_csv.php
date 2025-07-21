<?php
require_once("../InvenPro/Modelo/conexion.php");

function importarCSVDesdeRuta($ruta) {
    $con = conexion();

    if (!file_exists($ruta)) {
        return "Archivo no encontrado.";
    }

    if (($gestor = fopen($ruta, "r")) === false) {
        return "No se pudo abrir el archivo.";
    }

    fgetcsv($gestor); // saltamos encabezado

    while (($datos = fgetcsv($gestor, 1000, ";")) !== false) {
        if (count($datos) < 9) {
            continue;
        }

        $id_producto = (int)$datos[0];
        $nombre = mysqli_real_escape_string($con, $datos[1]);
        $descripcion = mysqli_real_escape_string($con, $datos[2]);
        $marca = mysqli_real_escape_string($con, $datos[3]);
        $referencia = mysqli_real_escape_string($con, $datos[4]);
        $cantidad = (int)$datos[5];
        $unidad = mysqli_real_escape_string($con, $datos[6]);
        $id_categoria = (int)$datos[7];
        $nombre_categoria = mysqli_real_escape_string($con, $datos[8]);
        $id_subcategoria = !empty($datos[9]) ? (int)$datos[9] : null;
        $nombre_subcategoria = !empty($datos[10]) ? mysqli_real_escape_string($con, $datos[10]) : null;

        if ($id_subcategoria && $nombre_subcategoria) {
            $ver_subcat = mysqli_query($con, "SELECT 1 FROM tbl_subcategorias WHERE id_subcategoria = $id_subcategoria");
            if (mysqli_num_rows($ver_subcat) == 0) {
                mysqli_query($con, "INSERT INTO tbl_subcategorias (id_subcategoria, nombre_subcategoria) VALUES ($id_subcategoria, '$nombre_subcategoria')");
            }
        }

        $ver_categoria = mysqli_query($con, "SELECT 1 FROM tbl_categorias WHERE id_categoria = $id_categoria");
        if (mysqli_num_rows($ver_categoria) == 0) {
            $query_categoria = "INSERT INTO tbl_categorias (id_categoria, nombre_categoria";
            $values_categoria = "VALUES ($id_categoria, '$nombre_categoria'";
            if ($id_subcategoria) {
                $query_categoria .= ", id_subcategoria";
                $values_categoria .= ", $id_subcategoria";
            }
            $query_categoria .= ")";
            $values_categoria .= ")";
            mysqli_query($con, $query_categoria . " " . $values_categoria);
        }

        $check_producto = mysqli_query($con, "SELECT * FROM tbl_productos WHERE id_producto = $id_producto");

        if (mysqli_num_rows($check_producto) > 0) {
            $producto_actual = mysqli_fetch_assoc($check_producto);

            if (
                $producto_actual['nombre_producto'] !== $nombre ||
                $producto_actual['referencia_producto'] !== $referencia
            ) {
                continue; // conflicto, no actualizar
            }

            $nuevo_stock = $producto_actual['stock_producto'] + $cantidad;

            $sql_update = "UPDATE tbl_productos SET 
                descripcion_producto = '$descripcion',
                marca_producto = '$marca',
                referencia_producto = '$referencia',
                stock_producto = $nuevo_stock,
                unidad_producto = '$unidad',
                id_categoria = $id_categoria
                WHERE id_producto = $id_producto";

            mysqli_query($con, $sql_update);
        } else {
            $sql_insert = "INSERT INTO tbl_productos 
                (id_producto, nombre_producto, descripcion_producto, marca_producto, referencia_producto, stock_producto, unidad_producto, id_categoria)
                VALUES ($id_producto, '$nombre', '$descripcion', '$marca', '$referencia', $cantidad, '$unidad', $id_categoria)";
            mysqli_query($con, $sql_insert);
        }
    }

    fclose($gestor);
    return "Importación completada correctamente.";
}



