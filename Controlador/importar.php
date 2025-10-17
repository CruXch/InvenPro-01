<?php
include("../Modelo/conexion.php");
$con = conexion();

if (isset($_POST['importar'])) {
    if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === 0) {
        $archivo_temporal = $_FILES['archivo_csv']['tmp_name'];
        $nombre_archivo = $_FILES['archivo_csv']['name'];
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        $tipo_mime = mime_content_type($archivo_temporal); // Detecta el tipo de archivo

        // Validar que el archivo tenga extensión .csv
        if ($extension !== 'csv') {
            echo "<script>
                alert('Error: Solo se permiten archivos con extensión .csv.');
                window.history.back();
            </script>";
            exit;
        }

        // Validar tipo MIME (puedes omitir esto si no es necesario)
        if (!in_array($tipo_mime, ['text/plain', 'text/csv', 'application/vnd.ms-excel'])) {
            echo "<script>
                alert('Error: El archivo subido no parece ser un CSV válido.');
                window.history.back();
            </script>";
            exit;
        }

        // Abrir el archivo CSV
        if (($gestor = fopen($archivo_temporal, "r")) !== FALSE) {
            fgetcsv($gestor); // Saltar encabezados
            $linea = 2;

            while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                if (count($datos) < 9) {
                    echo "<script>
                        alert('Error en la línea $linea: Se requieren al menos 9 columnas.');
                        window.history.back();
                    </script>";
                    fclose($gestor);
                    exit;
                }
                $linea++;

                // Procesar datos
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

                // Insertar subcategoría si no existe
                if ($id_subcategoria && $nombre_subcategoria) {
                    $ver_subcat = mysqli_query($con, "SELECT 1 FROM Tbl_Subcategorias WHERE id_subcategoria = $id_subcategoria");
                    if (mysqli_num_rows($ver_subcat) == 0) {
                        mysqli_query($con, "INSERT INTO Tbl_Subcategorias (id_subcategoria, nombre_subcategoria) 
                                            VALUES ($id_subcategoria, '$nombre_subcategoria')");
                    }
                }

                // Insertar categoría si no existe
                $ver_categoria = mysqli_query($con, "SELECT 1 FROM Tbl_Categorias WHERE id_categoria = $id_categoria");
                if (mysqli_num_rows($ver_categoria) == 0) {
                    $query_categoria = "INSERT INTO Tbl_Categorias (id_categoria, nombre_categoria";
                    $values_categoria = "VALUES ($id_categoria, '$nombre_categoria'";
                    if ($id_subcategoria) {
                        $query_categoria .= ", id_subcategoria";
                        $values_categoria .= ", $id_subcategoria";
                    }
                    $query_categoria .= ")";
                    $values_categoria .= ")";
                    mysqli_query($con, $query_categoria . " " . $values_categoria);
                }

                // Verificar si el producto ya existe
                $check_producto = mysqli_query($con, "SELECT * FROM Tbl_Productos WHERE id_producto = $id_producto");
                if (mysqli_num_rows($check_producto) > 0) {
                    $producto_actual = mysqli_fetch_assoc($check_producto);
                    if (
                        $producto_actual['nombre_producto'] !== $nombre ||
                        $producto_actual['referencia_producto'] !== $referencia
                    ) {
                        echo "<script>
                            alert('Conflicto con ID $id_producto: ya existe con otros datos.');
                            window.history.back();
                        </script>";
                        fclose($gestor);
                        exit;
                    }

                    $nuevo_stock = $producto_actual['stock_producto'] + $cantidad;

                    $sql_update = "UPDATE Tbl_Productos SET 
                        descripcion_producto = '$descripcion',
                        marca_producto = '$marca',
                        referencia_producto = '$referencia',
                        stock_producto = $nuevo_stock,
                        unidad_producto = '$unidad',
                        id_categoria = $id_categoria
                        WHERE id_producto = $id_producto";
                    mysqli_query($con, $sql_update);

                    $sql_ingreso = "INSERT INTO Tbl_Ingresos_Csv 
                        (marca_ingreso, nombre_ingreso, stock_ingreso, id_producto)
                        VALUES ('$marca','$nombre', $cantidad, $id_producto)";
                    mysqli_query($con, $sql_ingreso);
                } else {
                    // Insertar nuevo producto
                    $sql_insert = "INSERT INTO Tbl_Productos 
                        (id_producto, nombre_producto, descripcion_producto, marca_producto, referencia_producto, stock_producto, unidad_producto, id_categoria)
                        VALUES ($id_producto, '$nombre', '$descripcion', '$marca', '$referencia', $cantidad, '$unidad', $id_categoria)";
                    mysqli_query($con, $sql_insert);

                    $sql_ingreso = "INSERT INTO Tbl_Ingresos_Csv 
                        (marca_ingreso, nombre_ingreso, stock_ingreso, id_producto)
                        VALUES ('$marca', '$nombre', $cantidad, $id_producto)";
                    mysqli_query($con, $sql_ingreso);
                }
            }

            fclose($gestor);

            // Si todo fue exitoso, mostrar mensaje de éxito
            echo "<script>
                alert('Archivo .csv cargado exitosamente'); 
                window.location.href = '../Vista/3_02_01_listaPlanificacion.php';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('No se pudo abrir el archivo.'); 
                window.history.back();
            </script>";
            exit;
        }
    } else {
        echo "<script>
            alert('No se recibió un archivo válido o hubo un error en la subida.'); 
            window.history.back();
        </script>";
        exit;
    }
}

