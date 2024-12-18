<?php

class FirmasModel{
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }


    // Obtener Firmas del proyecto

    public function obtenerFirmasProyecto($idProyecto){
        $sql = "SELECT * FROM firmasuo, 
                        catcargos 
                WHERE firmasuo.idProyecto = ?
                AND firmasuo.idCargo = catcargos.idPuesto";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idProyecto]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function agregarFirma(
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
    ){
        $sqlInsert = "INSERT INTO firmasuo
                        (idProyecto, cPaterno, cMaterno, cNombre, idCargo,
                            cCelular, cTelefono, cExtension, cEmail,
                            cTitulo, cCargo, cCurp 
                        )
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?);";
        $stmtInsert = $this->pdo->prepare($sqlInsert);
        $stmtInsert->execute([
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
        ]);
        
        return "Firma registrada correctamente";
    }
    public function editarFirma(
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
    ){
        $sqlUpdate = "UPDATE firmasuo
                        SET cPaterno = ?,
                            cMaterno = ?,
                            cNombre = ?,
                            cCelular = ?,
                            cTelefono = ?,
                            cExtension = ?,
                            cEmail = ?,
                            cTitulo = ?,
                            cCargo = ?
                        WHERE idProyecto = ? AND
                            idCargo = ? AND
                            cCurp = ?";
        $stmtUpdate = $this->pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            $cPaterno,
            $cMaterno,
            $cNombre,
            $cCelular,
            $cTelefono,
            $cExtension,
            $cEmail,
            $cTitulo,
            $cCargo,
            $idProyecto,
            $idCargo,
            $cCurp
        ]);
    
        // Verifica si se afectaron filas
        if ($stmtUpdate->rowCount() > 0) {
            return "Firma actualizada correctamente";
        } else {
            return "No se encontró ningún registro para actualizar";
        }
    }
    
    public function eliminarFirma($idProyecto, $idCargo, $cCurp) {
        try {
            // Consulta de eliminación
            $sqlDelete = "DELETE FROM firmasuo WHERE idProyecto = ? AND idCargo = ? AND cCurp = ?";
            
            $stmt = $this->pdo->prepare($sqlDelete);
            $stmt->execute([$idProyecto, $idCargo, $cCurp]);

            // Comprobar si se eliminó algún registro
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                // Si no se eliminó nada, podría ser por parámetros incorrectos
                throw new Exception("No se encontró ningún registro para eliminar.");
            }
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error con la base de datos
            throw new Exception("Error al eliminar la firma: " . $e->getMessage());
        }
    }

    public function verificarFirmaExistente($idProyecto, $idCargo, $cCurp) {
        $sqlCheck = "SELECT COUNT(*) FROM firmasuo WHERE idProyecto = ? AND idCargo = ? AND cCurp = ?";
        $stmt = $this->pdo->prepare($sqlCheck);
        $stmt->execute([$idProyecto, $idCargo, $cCurp]);
        
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    }
    

    public function obtenerCatCargos() {
        try {

            $sql = "SELECT * FROM catcargos";
            $stmt = $this->pdo->prepare($sql); // Asumiendo que $this->db es una instancia de PDO
    
            $stmt->execute();
    
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $resultados;
    
        } catch (PDOException $e) {
            error_log("Error en obtenerCatCargos: " . $e->getMessage());
            return [];
        }
    }
    
}