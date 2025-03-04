<?php
include_once __DIR__ . '/../utils/logger.php';
include_once 'models/AdminModel.php';

class AdminController{
    private $adminModel;

    public function __construct($db) {
        $this->adminModel = new AdminModel($db);
    }

    public function obtenerAreas(){
        try{
            $areasData = $this->adminModel->obtenerAreas();

            if ($areasData) {
                echo json_encode([
                    "status" => "success",
                    "data" => $areasData
                ]);
            }
            else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No se encontraron datos de la MIR"
                ]);
            }

        }catch(Exception $e){
            
        }
    }

    public function obtenerLogroAreas($idArea) {
        try {
    
            // Obtener los logros desde el modelo usando el ID del área
            $logros = $this->adminModel->obtenerLogros($idArea);
    
            // Verificar si se obtuvieron datos
            if ($logros) {
                // Devolver una respuesta exitosa
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "status" => "success",
                    "data" => $logros
                ]);
            } else {
                // Devolver un error 404 si no se encontraron datos
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No se encontraron logros para el área especificada."
                ]);
            }
        } catch (Exception $e) {
            // Manejar excepciones y devolver un error 500
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "message" => "Ocurrió un error en el servidor: " . $e->getMessage()
            ]);
        }
    }

    public function obtenerSeguimiento($idArea, $year){
        try{
            $seguimiento = $this->adminModel->obtenerSeguimientoAreas($idArea, $year);
            
            // Verificar si se obtuvieron datos
            if ($seguimiento) {
                // Devolver una respuesta exitosa
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "status" => "success",
                    "data" => $seguimiento
                ]);
            } else {
                // Devolver un error 404 si no se encontraron datos
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No se encontraron logros para el área especificada."
                ]);
            }
        }catch(Exception $e){

        }
    }

}
?>