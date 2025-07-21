<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . 'prueba_subir_csv.php';

class SubirCSVTest extends TestCase {
    public function testImportacionExitosa() {
        $rutaCSV = __DIR__ . '/ejemplo.csv'; // crea este archivo de prueba
        $resultado = importarCSVDesdeRuta($rutaCSV);
        $this->assertEquals("Importación completada correctamente.", $resultado);
    }

    public function testArchivoNoExiste() {
        $resultado = importarCSVDesdeRuta(__DIR__ . '/no_existe.csv');
        $this->assertEquals("Archivo no encontrado.", $resultado);
    }
}


