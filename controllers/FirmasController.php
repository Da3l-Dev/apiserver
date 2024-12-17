<?php

include_once 'models/FirmasModel.php';

class FirmasController{
    private $firmaModel;

    public function __construct($db){
        $this->firmaModel = new FirmasModel($db);
    }

    public function obtenerFirmas($idProyecto){
        try{
            $firmas = $this->firmaModel->obtenerFirmasProyecto($idProyecto);

            if($firmas){
                echo json_encode([
                    "status" => "success",
                    "data" => $firmas
                ]);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No existen mir subidas"
                ]);
            }

        }catch (Exception $e){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function registrarFirmaProyecto(){
        try{
            $parametrosRequeridos = [
                'idProyecto', 
                'cPaterno', 
                'cMaterno', 
                'cNombre', 
                'idCargo', 
                'cCelular', 
                'cTelefono',
                'cExtension',
                'cEmail',
                'cTitulo',
                'cCargo',
                'cCurp'
            ];

            foreach($parametrosRequeridos as $param){
                if ($param !== 'firmasou'&& (!isset($_POST[$param]) || empty($_POST[$param]))) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            $idProyecto = $_POST['idProyecto'];
            $cPaterno = $_POST['cPaterno'];
            $cMaterno = $_POST['cMaterno'];
            $cNombre = $_POST['cNombre'];
            $idCargo = $_POST['idCargo'];
            $cCelular = $_POST['cCelular'];
            $cTelefono = $_POST['cTelefono'];
            $cExtension = $_POST['cExtension'];
            $cEmail = $_POST['cEmail'];
            $cTitulo = $_POST['cTitulo'];
            $cCargo = $_POST['cCargo'];
            $cCurp = $_POST['cCurp'];

            $firmAlta = $this->firmaModel->agregarFirma(
                $idProyecto,
                $cPaterno,
                $cMaterno,
                $cNombre,
                $idCargo,
                $cCelular,
                $cTelefono,
                $cExtension,
                $cEmail,
                $cTitulo,
                $cCargo,
                $cCurp
            );

            if ($firmAlta) {
                echo json_encode([
                    "status" => "success",
                    "data" => $firmAlta
                ]);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No se pudo registrar el logro."
                ]);
            }

        }catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function editarFirmaProyecto(){
        try{
            $parametrosRequeridos = [
                'idProyecto', 
                'cPaterno', 
                'cMaterno', 
                'cNombre', 
                'idCargo', 
                'cCelular', 
                'cTelefono',
                'cExtension',
                'cEmail',
                'cTitulo',
                'cCargo',
                'cCurp'
            ];
    
            foreach($parametrosRequeridos as $param){
                if (!isset($_POST[$param]) || empty($_POST[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }
    
            $idProyecto = $_POST['idProyecto'];
            $cPaterno = $_POST['cPaterno'];
            $cMaterno = $_POST['cMaterno'];
            $cNombre = $_POST['cNombre'];
            $idCargo = $_POST['idCargo'];
            $cCelular = $_POST['cCelular'];
            $cTelefono = $_POST['cTelefono'];
            $cExtension = $_POST['cExtension'];
            $cEmail = $_POST['cEmail'];
            $cTitulo = $_POST['cTitulo'];
            $cCargo = $_POST['cCargo'];
            $cCurp = $_POST['cCurp'];
    
            // Llama al modelo para editar la firma
            $firmAlta = $this->firmaModel->editarFirma(
                $idProyecto,
                $cPaterno,
                $cMaterno,
                $cNombre,
                $idCargo,
                $cCelular,
                $cTelefono,
                $cExtension,
                $cEmail,
                $cTitulo,
                $cCargo,
                $cCurp
            );
    
            echo json_encode([
                "status" => "success",
                "message" => $firmAlta
            ]);
    
        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
    

    public function eliminarFirmaProyecto() {
        try {
            // Parametros requeridos
            $parametrosRequeridos = ['idProyecto', 'idCargo', 'cCurp'];
    
            foreach ($parametrosRequeridos as $param) {
                if (!isset($_GET[$param]) || empty($_GET[$param])) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }
    
            $idProyecto = $_GET['idProyecto'];
            $idCargo = $_GET['idCargo'];
            $cCurp = $_GET['cCurp'];
    
            // Verificar si existe la firma antes de eliminarla
            $firmaExistente = $this->firmaModel->verificarFirmaExistente($idProyecto, $idCargo, $cCurp);
            if (!$firmaExistente) {
                throw new Exception("No existe una firma con los parámetros proporcionados.");
            }
    
            // Llamar a eliminar firma
            $this->firmaModel->eliminarFirma($idProyecto, $idCargo, $cCurp);
    
            // Enviar respuesta de éxito
            echo json_encode([
                "success" => true,
                "message" => "Firma eliminada con éxito."
            ]);
    
        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }    
}