<?php
class FilesModel {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
        customLog("FilesModel ha recibido conexión a la base de datos");
    }

    /**
     * Registrar o actualizar la ruta de una evidencia
     */
    public function registrarRutaEvidencia($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre, $rutaFiles) {
        if ($this->verificarRegistro($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre)) {
            // Actualizar registro existente
            $sqlUpdate = "UPDATE rutafiles 
                          SET rutaFiles = ? 
                          WHERE idEjercicio = ? AND idRamo = ? AND idFuenteFinan = ? 
                          AND idPrograma = ? AND idArea = ? AND idIndicador = ? AND idTrimestre = ?";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$rutaFiles, $idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre]);
            return "Registro actualizado exitosamente.";
        } else {
            // Insertar nuevo registro
            $sqlInsert = "INSERT INTO rutafiles (idEjercicio, idRamo, idFuenteFinan, idPrograma, idArea, idIndicador, idTrimestre, rutaFiles) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([$idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre, $rutaFiles]);
            return "Registro insertado exitosamente.";
        }
    }

    /**
     * Verificar si un registro ya existe en la base de datos
     */
    public function verificarRegistro($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre) {
        $sql = "SELECT COUNT(*) as count FROM rutafiles 
                WHERE idEjercicio = ? AND idRamo = ? AND idFuenteFinan = ? 
                AND idPrograma = ? AND idArea = ? AND idIndicador = ? AND idTrimestre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Obtener la ruta de una evidencia
     */
    public function obtenerRutaEvidencia($idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre) {
        $sql = "SELECT rutaFiles FROM rutafiles 
                WHERE idEjercicio = ? AND idRamo = ? AND idFuenteFinan = ? 
                AND idPrograma = ? AND idArea = ? AND idIndicador = ? AND idTrimestre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEjercicio, $idRamo, $idFuenteFinan, $idPrograma, $idArea, $idIndicador, $idTrimestre]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['rutaFiles'] : null;
    }

    public function eliminarRutaEvidencia($idEjercicio, $idIndicador, $idArea, $idPrograma, $idTrimestre){
        $sql = "DELETE * FROM rutafiles 
                WHERE idEjercicio = ? 
                AND idIndicador = ? 
                AND idArea = ?
                AND idPrograma = ?
                AND idTrimestre";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEjercicio, $idIndicador, $idArea, $idPrograma, $idTrimestre]);
    }

    
}
