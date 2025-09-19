<?php
// Verifica que el usuario tenga sesión activa antes de acceder
include("../Modelo/validar_sesion.php");

// Incluye el archivo de conexión a la base de datos
include("../Modelo/conexion.php");
$con = conexion();

// Configura el idioma para formatear fechas en español
// (En Windows se usa 'spanish', en Linux puede ser 'es_ES.UTF-8')
setlocale(LC_TIME, 'spanish');

// Obtener el ID del usuario que está logueado desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta SQL para obtener las tomas de inventario asignadas al usuario actual
// Solo se traen aquellas que aún están en estado "pendiente"
$sql = "SELECT 
            tdi.id_toma, 
            tdi.fecha_toma
        FROM 
            Tbl_Asignaciones_Toma ta
        INNER JOIN 
            Tbl_Tomas_De_Inventario tdi ON ta.id_toma = tdi.id_toma
        WHERE 
            ta.id_usuario = ?
            AND tdi.estado_toma = 'pendiente'";

// Preparar la consulta para evitar inyecciones SQL
$stmt = $con->prepare($sql);

// Vincular el parámetro (i = integer) con el id del usuario logueado
$stmt->bind_param("i", $id_usuario);

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado de la ejecución
$resultado = $stmt->get_result();

// Crear un arreglo para almacenar todas las tomas encontradas
$tomas = [];

// Recorrer los resultados y guardarlos en el arreglo
while ($fila = $resultado->fetch_assoc()) {
    $tomas[] = $fila;
}

// Cerrar la consulta preparada
$stmt->close();

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Estilos de css-->
    <link rel="stylesheet" href="css/estilos.css">
    <!--Iconos de FontAwesome-->
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <title>Registrar conteos</title>
</head>
<body>

    <header>
        <div>
            <img width="22%" src="img/logo.png" alt="Logo InvenPro">
        </div>
    </header>
    
    <div class="menu">

        <div class="menu1">

            <div class="usuarioMenu1">
                <div class="menuFotoNombre">
                    <i class="fa-solid fa-bell campanaMenu"></i>
                    <img class="menuFoto" src="img/fotoperfil.png" alt="Foto de perfil">
                    <div class="menuNombres">
                        <p class="nombres"><?= $_SESSION['nombre_usuario'] ?></p>
                        <p class="nombres"><?= $_SESSION['apellido_usuario'] ?></p>
                        <p class="cargo"><?= $_SESSION['rol'] ?></p>
                    </div>
                </div>
                <div class="menuFechaIcono">
                    <p class="menuFecha"><?= fechaConDia() ?></p>
                    <div class="menuIcono">
                        <a href="2_02_menuContador.php"><i class="fa-solid fa-house-user"></i></a>
                    </div>
                </div>
            </div>

            <div>
                <button class="botonMenu"><a href="4_01_listaConteos.php">Registrar conteos</a></button>
            </div>

            <div class="linksMenu1">
                <a href="#" target="_blank" class="linkMenu">Documentación</a>
                <a href="#" target="_blank" class="linkMenu">Soporte</a>
                <a href="1_00_index.html" target="_blank" class="linkMenu">Empresa</a>
                <a href="#" target="_blank" class="linkMenu">Redes sociales</a>
            </div>

            <div class="contenedorBotonCerrar1">
                <a href="../Controlador/cerrar_sesion.php" class="botonCerrar">Cerrar sesión 
                    <i class="fa-sharp fa-solid fa-right-from-bracket botonCerrarIcono"></i>
                </a>
            </div>

        </div>
    

        <div class="menu2">

            <h1 class="tituloCentro">Registrar conteos de inventarios</h1>               

            <div class="contenedorInfoConteos">
                <div class="contenedorConteos">
                    <h2 class="tituloConteo">Tomas de inventario</h2>

                    <?php if (empty($tomas)) : ?>
                        <p class="infoConteo">No tienes tomas asignadas por el momento.</p>
                    <?php else : ?>
                        <?php foreach ($tomas as $toma) : 
                            // Formato de fecha
                            $fecha_formateada = strftime('%A %d de %B de %Y', strtotime($toma['fecha_toma']));
                            $fecha_formateada = ucfirst(utf8_encode($fecha_formateada));
                        ?>
                            <a class="infoConteo" href="4_02_tarjetaConteo.php?id_toma=<?= $toma['id_toma'] ?>">
                                Asignación - Id-<?= str_pad($toma['id_toma'], 2, '0', STR_PAD_LEFT) ?> - <?= $fecha_formateada ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

    <div class="menuFooter">
        <footer>
            <p>Términos de servicio     ©2025 Inventory Project - InvenPro</p>
        </footer>
    </div>

    <script src="js/script.js"></script>

</body>
</html>

