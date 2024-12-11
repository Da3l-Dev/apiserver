<?php

include_once __DIR__ . '/../utils/logger.php';
include_once 'models/FilesModel.php';

class FilesController {
    private $filesModel;

    public function __construct($db) {
        $this->filesModel = new FilesModel($db);
    }

    /**
     * Subir un archivo de evidencia
     */
    public function subirEvidenciaRuta() {
        try {
            // Validar que todos los parámetros requeridos están presentes
            $parametrosRequeridos = ['idEjercicio', 'idPrograma', 'idArea', 'idIndicador', 'idTrimestre'];
            foreach ($parametrosRequeridos as $param) {
                if (!isset($_POST[$param]) || empty($_POST[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            // Obtener los datos del POST
            $idEjercicio = $_POST['idEjercicio'];
            $idPrograma = $_POST['idPrograma'];
            $idArea = $_POST['idArea'];
            $idIndicador = $_POST['idIndicador'];
            $idTrimestre = $_POST['idTrimestre'];

            // Validar si el archivo fue subido correctamente
            if (!isset($_FILES['rutaFiles']) || $_FILES['rutaFiles']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No se pudo subir el archivo.');
            }

            // Obtener información del archivo
            $archivo = $_FILES['rutaFiles'];
            $nombreArchivoOriginal = basename($archivo['name']);
            
            // Saneamiento del nombre del archivo (solo reemplazar espacios por guiones bajos)
            $nombreArchivo = $this->sanitizeFileName($nombreArchivoOriginal);
            
            // Directorio raíz donde se guardarán los archivos
            $directorioRaiz = __DIR__ . "/../cedulas_evidencias/";

            // Crear la ruta de subcarpetas dinámicamente
            $rutaCarpeta = $directorioRaiz . $idEjercicio . "/" . "programa" . $idPrograma . "/" . "area_" . $idArea . "/" . "indicador" . $idIndicador . "/" . "trim_" . $idTrimestre . "/";

            // Validar extensión del archivo (solo PDF)
            $extensionesPermitidas = ['pdf'];
            $extensionArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            if (!in_array($extensionArchivo, $extensionesPermitidas)) {
                throw new Exception('Tipo de archivo no permitido. Solo se permiten archivos PDF.');
            }

            // Crear el directorio si no existe
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);  // Crear las carpetas de forma recursiva
            }

            // Ruta completa donde se guardará el archivo
            $rutaDestino = $rutaCarpeta . $nombreArchivo;

            // Si el archivo ya existe, sobrescribirlo
            if (file_exists($rutaDestino)) {
                unlink($rutaDestino); // Elimina el archivo existente
            }

            // Mover el archivo a la carpeta de destino solo si no hubo errores
            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new Exception('Error al guardar el archivo en el servidor.');
            }

            // Ruta relativa para guardar en la base de datos
            $rutaFiles = "/cedulas_evidencias/" . $idEjercicio . "/" . "programa" . $idPrograma . "/" . "area_" . $idArea . "/" . "indicador" . $idIndicador . "/" . "trim_" . $idTrimestre . "/" . $nombreArchivo;

            // Registrar o actualizar la ruta del archivo en la base de datos
            $resultado = $this->filesModel->registrarRutaEvidencia($idEjercicio, null, null, $idPrograma, $idArea, $idIndicador, $idTrimestre, $rutaFiles);

            // Respuesta exitosa
            echo json_encode([
                "success" => true,
                "message" => $resultado,
                "ruta" => $rutaFiles
            ]);
        } catch (Exception $e) {
            // Manejar errores
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Método para sanear el nombre del archivo
     */
    private function sanitizeFileName($nombreArchivo) {
        // Reemplazar espacios por guiones bajos, solo si el archivo tiene espacios
        if (strpos($nombreArchivo, ' ') !== false) {
            $nombreArchivo = str_replace(' ', '_', $nombreArchivo);
        }
    
        // Eliminar caracteres no alfanuméricos, acentos y tildes
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9\._-]/', '', $nombreArchivo);
    
        // Convertir todo a minúsculas
        $nombreArchivo = strtolower($nombreArchivo);
    
        return $nombreArchivo;
    }

    /**
     * Obtener la ruta de la evidencia
     */
    public function obtenerEvidenciaRuta() {
        try {
            // Validar parámetros requeridos
            $parametrosRequeridos = ['idEjercicio', 'idPrograma', 'idArea', 'idIndicador', 'idTrimestre'];
            foreach ($parametrosRequeridos as $param) {
                if (!isset($_GET[$param]) || empty($_GET[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            // Obtener parámetros
            $idEjercicio = $_GET['idEjercicio'];
            $idPrograma = $_GET['idPrograma'];
            $idArea = $_GET['idArea'];
            $idIndicador = $_GET['idIndicador'];
            $idTrimestre = $_GET['idTrimestre'];

            // Obtener la ruta del archivo
            $rutaRelativa = $this->filesModel->obtenerRutaEvidencia($idEjercicio, $idPrograma, $idArea, $idIndicador, $idTrimestre);

            if (!$rutaRelativa) {
                throw new Exception('No se encontró la evidencia para el trimestre solicitado.');
            }

            // Respuesta exitosa
            echo json_encode([
                "success" => true,
                "ruta" => $rutaRelativa
            ]);
        } catch (Exception $e) {
            // Manejar errores
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar la ruta del archivo
     */
    public function eliminarRutaArchivo() {
        try {
            // Definir los parámetros requeridos
            $parametrosRequeridos = ['idEjercicio', 'idPrograma', 'idArea', 'idIndicador', 'idTrimestre'];

            // Verificar si todos los parámetros existen y no están vacíos
            foreach ($parametrosRequeridos as $param) {
                if (!isset($_GET[$param]) || empty($_GET[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            // Obtener los parámetros
            $idEjercicio = $_GET['idEjercicio'];
            $idPrograma = $_GET['idPrograma'];
            $idArea = $_GET['idArea'];
            $idIndicador = $_GET['idIndicador'];
            $idTrimestre = $_GET['idTrimestre'];

            // Obtener la ruta relativa del archivo desde la base de datos
            $rutaRelativa = $this->filesModel->obtenerRutaEvidencia($idEjercicio, $idPrograma, $idArea, $idIndicador, $idTrimestre);

            if (!$rutaRelativa) {
                throw new Exception('No se encontró la evidencia para el trimestre solicitado.');
            }

            // Eliminar el archivo físico en el servidor
            $rutaCompleta = __DIR__ . "/..". $rutaRelativa;

            // Verificar si el archivo existe antes de intentar eliminarlo
            if (file_exists($rutaCompleta)) {
                if (!unlink($rutaCompleta)) {
                    throw new Exception('No se pudo eliminar el archivo del servidor.');
                }
            } else {
                throw new Exception('El archivo no existe en el servidor.');
            }

            // Llamar al método eliminarRutaEvidencia pasando los parámetros correctos para eliminar la ruta en la base de datos
            $this->filesModel->eliminarRutaEvidencia($idEjercicio, $idPrograma, $idArea, $idIndicador, $idTrimestre);

            // Enviar respuesta de éxito
            echo json_encode([
                "success" => true,
                "message" => "Ruta de evidencia y archivo eliminados con éxito."
            ]);
        } catch (Exception $e) {
            // Manejar errores
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
