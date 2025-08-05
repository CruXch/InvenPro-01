<?php
// Se incluye el archivo que contiene la función para conectarse a la base de datos
include("../Modelo/conexion.php");
$con = conexion(); // Se crea la conexión

// Verificamos si se presionó el botón "importar" en el formulario
if (isset($_POST['importar'])) {

    // Con esto verificamos que el archivo_csv fue subido y luego en el && se verifica que el archivo_csv no tenga errores
    // El 0 no es un bool aqui, es una constante que se llama "UPLOAD_ERR_OK" Por lo cual tambien se puede poner el nombre si no se quiere poner el 0    
    if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] == 0) {

        // Aqui podemos definir una variable que nos da acceso al archivo de manera temporal, por eso se usa "tmp_name"
        $archivo_temporal = $_FILES['archivo_csv']['tmp_name'];

        // Aqui se define la variable $gestor donde con fopen abrirá el archivo temporal definido antes y tenerlo en modo lectura "r"
        // Se verifica que si esto sale bien se prosigue, por eso se necesita que el proceso completado sea contrario 
        // a FALSE ya que al si el archivo por ejemplo no existe entonces retornará un FALSE
        // fopen() devuelve un recurso especial de tipo resource
        if (($gestor = fopen($archivo_temporal, "r")) !== FALSE) {

            // Con esto leemos y descartamos la primera línea del archivo CSV (los encabezados como id,nombre,etc.)
            fgetcsv($gestor);

            // Con el ciclo while se empieza a recorrer el archivo y se toma la información con fgetcsv donde tambien
            // se puede definir que cada campo tendrá maximo 500 bytes aquí e irá separado por comas
            // El !== FALSE funciona igual que arriba, el ciclo se hará mientras siga sucediendo lo contrario a false
            while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {

                // Si hay menos de 9 columnas, mostramos error y saltamos esa línea
                if (count($datos) < 9) {
                    echo "Línea incompleta. Se requieren al menos 9 columnas.<br>";
                    continue;
                }

                // Aqui empezamos a capturar los datos y darle un puesto dentro del array o conjunto que se crea
                // Los int convierten la cantidad en entero
                // $con->real_escape_string() toma un texto y escapa (protege) caracteres especiales que podrían: 
                // Romper la consulta SQL, Generar errores, Permitir un ataque de inyección SQL si el contenido es malicioso
                $id_producto = (int)$datos[0];
                $nombre = mysqli_real_escape_string($con, $datos[1]); // $nombre = $con->real_escape_string ($datos[1]); = manera con POO. Cualquiera de las dos sirve
                $descripcion = mysqli_real_escape_string($con, $datos[2]);
                $marca = mysqli_real_escape_string($con, $datos[3]);
                $referencia = mysqli_real_escape_string($con, $datos[4]);
                $cantidad = (int)$datos[5];
                $unidad = mysqli_real_escape_string($con, $datos[6]);

                // Tomamos los datos de categoría
                $id_categoria = (int)$datos[7];
                $nombre_categoria = mysqli_real_escape_string($con, $datos[8]);

                // Tomamos los datos de subcategoría si existe
                // Si no existe entonces en ese campo del array se pondrá un null, para eso funciona aqui el ? 
                // Si no se cumple la primera opción se aplica la segunda que es el null
                $id_subcategoria = !empty($datos[9]) ? (int)$datos[9] : null;
                $nombre_subcategoria = !empty($datos[10]) ? mysqli_real_escape_string($con, $datos[10]) : null;

                // Si existe subcategoría y no está en la base de datos, la insertamos
                if ($id_subcategoria && $nombre_subcategoria) {
                    $ver_subcat = mysqli_query($con, "SELECT 1 FROM tbl_subcategorias WHERE id_subcategoria = $id_subcategoria");
                    if (mysqli_num_rows($ver_subcat) == 0) {
                        mysqli_query($con, "INSERT INTO tbl_subcategorias (id_subcategoria, nombre_subcategoria) 
                                            VALUES ($id_subcategoria, '$nombre_subcategoria')");
                    }
                }

                // Si la categoría no existe, la insertamos
                $ver_categoria = mysqli_query($con, "SELECT 1 FROM tbl_categorias WHERE id_categoria = $id_categoria");
                if (mysqli_num_rows($ver_categoria) == 0) {
                    // Creamos la consulta SQL para insertar categoría
                    $query_categoria = "INSERT INTO tbl_categorias (id_categoria, nombre_categoria";
                    $values_categoria = "VALUES ($id_categoria, '$nombre_categoria'";

                    // Si tiene subcategoría asociada a esa categoria, se agrega a la consulta
                    if ($id_subcategoria) {
                        $query_categoria .= ", id_subcategoria";
                        $values_categoria .= ", $id_subcategoria";
                    }

                    // Agregamos el cierre del paréntesis a cada parte de la consulta
                    // y después juntamos las dos partes con "." para crear la consulta completa.
                    $query_categoria .= ") ";
                    $values_categoria .= ")";
                    mysqli_query($con, $query_categoria . $values_categoria); // El "."
                }

                // Verificamos si el producto ya existe en la base de datos
                $check_producto = mysqli_query($con, "SELECT * FROM tbl_productos WHERE id_producto = $id_producto");

                if (mysqli_num_rows($check_producto) > 0) {
                    //Ya existe un producto con este ID
                    $producto_actual = mysqli_fetch_assoc($check_producto);

                    //Validamos si el nombre o la referencia son diferentes al producto existente
                    if (
                        $producto_actual['nombre_producto'] !== $nombre ||
                        $producto_actual['referencia_producto'] !== $referencia
                    ) {
                        echo "Conflicto con ID $id_producto: ya existe con nombre '{$producto_actual['nombre_producto']}' y referencia '{$producto_actual['referencia_producto']}', 
                              pero el archivo trae '$nombre' y '$referencia' que no corresponden al ID registrado.<br>";
                        continue; // Saltamos esta línea sin actualizar
                    }

                    //Si coincide, sumamos el stock y actualizamos info
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

                    // Insertar el ingreso como un registro individual en la tabla tbl_Ingresos_Csv
                    $sql_ingreso = "INSERT INTO tbl_Ingresos_Csv 
                        (marca_ingreso, nombre_ingreso, stock_ingreso, id_producto)
                        VALUES ('$marca','$nombre', $cantidad, $id_producto)";

                    mysqli_query($con, $sql_ingreso);


                } else {
                    //Producto no existe: insertamos como nuevo
                    $sql_insert = "INSERT INTO tbl_productos 
                        (id_producto, nombre_producto, descripcion_producto, marca_producto, referencia_producto, stock_producto, unidad_producto, id_categoria)
                        VALUES ($id_producto, '$nombre', '$descripcion', '$marca', '$referencia', $cantidad, '$unidad', $id_categoria)";

                    mysqli_query($con, $sql_insert);

                    // Insertar el ingreso como un registro individual en la tabla tbl_Ingresos_Csv
                    $sql_ingreso = "INSERT INTO tbl_Ingresos_Csv 
                        (marca_ingreso, nombre_ingreso, stock_ingreso, id_producto)
                        VALUES ('$marca', '$nombre', $cantidad, $id_producto)";

                    mysqli_query($con, $sql_ingreso);
                }

            }

            //Cerramos el archivo CSV después de procesarlo
            fclose($gestor);

            //Mostramos mensaje de éxito poniendo un fragmento de js pero no estará definido en una funcion en el archivo js.        
            echo "<script>
                  alert('Archivo .csv cargado exitosamente'); 
                  window.location.href = '../Vista/3_02_01_listaPlanificacion.php';
                  </script>";
            exit; //Este exit es para cerrar en su totalidad el uso del js aqui.

        } else {
            //Si no se pudo abrir el archivo CSV
            echo "No se pudo abrir el archivo.";
        }

      } else {
        //Si no se subió correctamente el archivo
        echo "No se recibió un archivo CSV válido o hubo un error en el proceso, intente nuevamente.";
      }
}
?>

