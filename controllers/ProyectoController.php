<?php

include_once __DIR__ . '/../utils/logger.php';
include_once 'models/ProyectoModel.php';

class ProyectoController{
    private $proyectoModel;

    public function __construct($db) {
        $this->proyectoModel = new ProyectoModel($db);
    }


    // Metodo para obtener los datos del proyecto de Area

    public function obtenerDatosProyecto($idArea, $year) {

        $proyectData = $this->proyectoModel->obtenerDatosProyecto($idArea,$year);

        if ($proyectData) {
            echo json_encode([
                "status" => "success",
                "data" => $proyectData
            ]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "Datos del Proyecto no encontrado"
            ]);
        }
    }
    
    // Metodo para obtener las variables del area
    public function obtenerDatosVariables($idArea, $year){
        $variablesData = $this->proyectoModel->obtenerDatosVariables($idArea, $year);

        if($variablesData){
            echo json_encode([
                "status" => "success",
                "data" => $variablesData
            ]);
        }else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "Variables no encontradas"
            ]);
        }

    }

    // Metodo para obtener las metas programadas del area
    public function obtenerDatosMetasProgramadas($idArea, $year){
        $metaProgData = $this->proyectoModel->obtenerDatosDeMetasProgramadas($idArea,$year);
        if($metaProgData){
            echo json_encode([
                "status" => "success",
                "data" => $metaProgData
            ]);
        }else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "Metas Programadas no encontradas"
            ]);
        }
    }

    public function obtenerSeguimiento($idArea){
        $seguimientoData = $this->proyectoModel->obtenerDatosSeguimiento($idArea);
        if($seguimientoData){
            echo json_encode([
                "status" => "success",
                "data" => $seguimientoData
            ]);
        }else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "data" => "Seguimiento no encontrado"
            ]);
        }
    }

    public function obtenerMetaAlcanzadas($idArea){
        $metasAlcazadasData = $this->proyectoModel->obtenerDatosMetasAlcanzadas($idArea);
        if($metasAlcazadasData){
            echo json_encode([
                "status" => "success",
                "data" => $metasAlcazadasData
            ]);
        }else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "Metas Alcanzadas no encontradas"
            ]);
        }
    }

    public function obtenerTrimActivoArea($idArea){
        $trimActivo = $this->proyectoModel->obtenerTrimActivoArea($idArea);
        if($trimActivo){
            echo json_encode([
                "status" => "success",
                "data" => $trimActivo
            ]);
        }else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "Trimestre Activo no obtenido"
            ]);
        }
    }

    public function obtenerLogrosArea($idEjercicio, $idArea, $idTrimestre, $idIndicador) {
        $logros = $this->proyectoModel->obtenerLogros($idEjercicio, $idArea, $idTrimestre, $idIndicador);
    
        if ($logros) {
            echo json_encode([
                "status" => "success",
                "data" => $logros
            ]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "No existe un logro actual"
            ]);
        }
    }    


    public function registrarLogro(){
        try {
            // Definir los parámetros requeridos
            $parametrosRequeridos = [
                'idEjercicio', 
                'idRamo', 
                'idFuenteFinan', 
                'idPrograma', 
                'idArea', 
                'idIndicador', 
                'idTrimestre',
                'logro',
                'causa',
                'efecto',
                'obs1',
                'obs2'
            ];

            // Validar parámetros requeridos
            foreach ($parametrosRequeridos as $param) {
                if ($param !== 'logro'&& (!isset($_POST[$param]) || empty($_POST[$param]))) {
                    throw new Exception("Falta el parámetro requerido o está vacío: $param");
                }
            }

            // Obtener los datos del POST
            $idEjercicio = $_POST['idEjercicio'];
            $idRamo = $_POST['idRamo'];
            $idFuenteFinan = $_POST['idFuenteFinan'];
            $idPrograma = $_POST['idPrograma'];
            $idArea = $_POST['idArea'];
            $idIndicador = $_POST['idIndicador'];
            $idTrimestre = $_POST['idTrimestre'];
            $logro = $_POST['logro'];
            $causa = $_POST['causa'] ?? null; // Opcionales
            $efecto = $_POST['efecto'] ?? null;
            $obs1 = $_POST['obs1'] ?? null;
            $obs2 = $_POST['obs2'] ?? null;

            // Llamar al modelo para registrar el logro
            $logroAlta = $this->proyectoModel->registrarLogroArea(
                $idEjercicio, 
                $idRamo, 
                $idFuenteFinan, 
                $idPrograma, 
                $idArea, 
                $idIndicador, 
                $idTrimestre, 
                $logro, 
                $causa, 
                $efecto, 
                $obs1, 
                $obs2
            );

            // Verificar la respuesta del modelo
            if ($logroAlta) {
                echo json_encode([
                    "status" => "success",
                    "data" => $logroAlta
                ]);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "message" => "No se pudo registrar el logro."
                ]);
            }
        } catch (Exception $e) {
            // Manejo de errores y envío de respuesta
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function obtenerMejoraSub($idArea, $idIndicador){
        $mejora = $this->proyectoModel->obtenerAspectosMejora($idArea,$idIndicador);

        if ($mejora) {
            echo json_encode([
                "status" => "success",
                "data" => $mejora
            ]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "No existe un logro actual"
            ]);
        }
    }

    public function obtenerMir($idArea) {
        $mirData = $this->proyectoModel->obtenerMir($idArea);
    
        if ($mirData) {
            echo json_encode([
                "status" => "success",
                "data" => $mirData
            ]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "message" => "No se encontraron datos de la MIR"
            ]);
        }
    }    
}