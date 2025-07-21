<?php

function conexion(){
$host = "localhost";
$user = "root";
$pass = "";

$bd = "BD_InvenPro";

$conectar = mysqli_connect($host, $user, $pass); 

mysqli_select_db($conectar, $bd);

return $conectar;

}

