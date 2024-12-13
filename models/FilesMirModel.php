<?php

class FilesMirModel {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // Registrar o actualizar la ruta del archivo
    public function registrarRutaMir($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre, $rutaFiles) {
        if ($this->verificarRegistroMir($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre)) {
            // Actualizar registro existente
            $sqlUpdate = "UPDATE rutafilesmir 
                          SET rutaFiles = ? 
                          WHERE ejercicio = ? 
                          AND idRamo = ? 
                          AND idFuenteFinan = ? 
                          AND idPrograma = ? 
                          AND idArea = ? 
                          AND idTrimestre = ?";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$rutaFiles, $idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre]);
            return "MIR actualizado exitosamente.";
        } else {
            // Insertar nuevo registro
            $sqlInsert = "INSERT INTO rutafilesmir (ejercicio, idRamo, idFuenteFinan, idPrograma, idArea, idTrimestre, rutaFiles) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([$idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre, $rutaFiles]);
            return "MIR insertado exitosamente.";
        }
    }

    // Verificar si ya existe un registro en la base de datos
    public function verificarRegistroMir($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre) {
        $sql = "SELECT COUNT(*) as count FROM rutafilesmir 
                WHERE ejercicio = ? 
                AND idRamo = ? 
                AND idFuenteFinan = ? 
                AND idPrograma = ? 
                AND idArea = ? 
                AND idTrimestre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idTrimestre]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Obtener todas las rutas asociadas al area
    public function obtenerMirFirmadas($idArea){
        $sql = "SELECT rutaFiles, idTrimestre as trim FROM rutafilesmir WHERE idArea = ? ORDER BY idTrimestre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idArea]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}
