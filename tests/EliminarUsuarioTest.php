<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . 'prueba_eliminar_usuario.php'; // ajusta la ruta si hace falta

class EliminarUsuarioTest extends TestCase {
    public function testEliminarUsuarioExistente() {
        // Puedes usar un ID que sepas que existe en tu BD de pruebas
        $resultado = eliminarUsuario('12345678');
        $this->assertTrue($resultado);
    }

    public function testEliminarUsuarioInexistente() {
        // Un ID que seguramente no exista para probar el caso negativo
        $resultado = eliminarUsuario('99999999');
        $this->assertFalse($resultado);
    }
}
?>

