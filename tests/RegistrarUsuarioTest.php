<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . 'prueba_registrar_usuario.php';

class RegistrarUsuarioTest extends TestCase {
    public function testRegistroCorrecto() {
        $resultado = registrarUsuario(
            'Juan',
            'Pérez',
            '12345678',
            '3001234567',
            'juan@example.com',
            '1234',
            'Contador'
        );

        $this->assertTrue($resultado);
    }

    public function testTipoUsuarioInvalido() {
        $resultado = registrarUsuario(
            'Ana',
            'Gómez',
            '87654321',
            '3007654321',
            'ana@example.com',
            '1234',
            'Desconocido'
        );

        $this->assertEquals("Tipo de usuario inválido", $resultado);
    }
}

