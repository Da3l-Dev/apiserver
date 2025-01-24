<?php

include_once __DIR__ . '/../utils/logger.php';
include_once 'models/FilesMirModel.php';

class FilesMirController {
    private $filesMirModel;

    public function __construct($db) {
        $this->filesMirModel = new FilesMirModel($db);
    }

    // Funcion general para subir un archivo PDF de la MIR FIRMADA
    public function subirMirEvidencia() {
        try {
            $parametrosRequeridos = ['idEjercicio', 'idRamo', 'idFuenteFinan', 'idPrograma', 'idArea', 'idTrimestre'];

            // Verificar si los parámetros necesarios están presentes
            foreach ($parametrosRequeridos as $param) {
                if (!isset($_POST[$param]) || empty($_POST[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            // Obtener los parámetros del POST
            $idEjercicio = $_POST['idEjercicio'];
            $idRamo = $_POST['idRamo'];
            $idArea = $_POST['idArea'];
            $idFuenteFinan = $_POST['idFuenteFinan'];
            $idPrograma = $_POST['idPrograma'];
            $idTrimestre = $_POST['idTrimestre'];

            // Verificar si el archivo fue enviado correctamente
            if (!isset($_FILES['rutaFiles']) || $_FILES['rutaFiles']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No se pudo subir el archivo.');
            }

            $archivo = $_FILES['rutaFiles'];
            $nombreArchivoOriginal = basename($archivo['name']);
            $nombreArchivo = $this->sanitizeFileName($nombreArchivoOriginal);

            // Directorio donde se guardarán los archivos
            $directorioRaiz = __DIR__ . "/../MirFirmadas";
            $rutaCarpeta = $directorioRaiz . "/" . $idEjercicio . "/programa" . $idPrograma . "/area_" . $idArea . "/trim_" . $idTrimestre . "/";

            // Asegurarse de que el directorio exista
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);
            }

            // Comprobar la extensión del archivo (solo PDF permitido)
            $extensionesPermitidas = ['pdf'];
            $extencionArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (!in_array($extencionArchivo, $extensionesPermitidas)) {
                throw new Exception('Tipo de archivo no permitido. Solo se permiten archivos PDF.');
            }

            // Ruta completa donde se guardará el archivo
            $rutaDestino = $rutaCarpeta . $nombreArchivo;

            // Eliminar el archivo si ya existe
            if (file_exists($rutaDestino)) {
                unlink($rutaDestino);
            }

            // Mover el archivo al destino
            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new Exception('Error al guardar el archivo en el servidor.');
            }

            // Registrar la ruta del archivo en la base de datos
            $rutaFiles = "/MirFirmadas/" . $idEjercicio . "/programa" . $idPrograma . "/area_" . $idArea . "/trim_" . $idTrimestre . "/" . $nombreArchivo;
            $resultado = $this->filesMirModel->registrarRutaMir($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre, $rutaFiles);

            // Respuesta exitosa
            echo json_encode([
                "success" => true,
                "message" => $resultado,
                "ruta" => $rutaFiles
            ]);

        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    // Función para normalizar el nombre del archivo
    private function sanitizeFileName($nombreArchivo) {
        // Reemplazar espacios por guiones bajos, eliminar caracteres no alfanuméricos
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9\._-]/', '', str_replace(' ', '_', $nombreArchivo));
        return strtolower($nombreArchivo);
    }


    public function obtenerMirArea($idArea){
        $rutas = $this->filesMirModel->obtenerMirFirmadas($idArea);

        if($rutas){
            echo json_encode([
                "status" => "success",
                "data" => $rutas
            ]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "No existen mir subidas"
            ]);
        }
    }
}
