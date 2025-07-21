<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../InvenPro/Modelo/conexion.php';  // Importa el archivo con la función conexion()

class ConexionTest extends TestCase
{
    public function testConexion()
    {
        $con = conexion();

        // Verifica que la conexión no sea false
        $this->assertNotFalse($con, "La conexión no debe ser false");

        // Verifica que sea una instancia válida de mysqli (PHP 8+)
        $this->assertInstanceOf(mysqli::class, $con);
    }
}
