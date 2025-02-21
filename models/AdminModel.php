<?php
class AdminModel{
    private $pdo;

    // Conexion a la base de datos al AdminModel
    public function __construct($db) {
        $this->pdo = $db;
    }


    public function obtenerAreas(){
        $sql = "SELECT * FROM usuario , unidadoperativa WHERE tipoUsuario = 5  AND usuario.idArea = unidadoperativa.idArea";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    // Obtener Logros con datos vitales

    public function obtenerLogros($idArea) {
        try {
            // Definir la consulta SQL
             $sql = "SELECT logros.*, ficha.idComponente, ficha.idActividad, ficha.numCA 
                    FROM logros 
                    JOIN (
                        SELECT idIndicador, idComponente, idActividad, numCA 
                        FROM fichatecnica 
                        WHERE idArea = :idArea 
                        GROUP BY idIndicador 
                    ) AS ficha ON ficha.idIndicador = logros.idIndicador 
                    WHERE logros.idArea = :idArea;";
    
            // Preparar la consulta
            $stmt = $this->pdo->prepare($sql);
    
            // Vincular el parámetro
            $stmt->bindParam(':idArea', $idArea, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            $stmt->execute();
    
            // Obtener los resultados
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Retornar los resultados
            return $result;
        } catch (PDOException $e) {
            // Loguear el error (opcional)
            error_log("Error en obtenerLogros: " . $e->getMessage());
            return false; // Retornar false en caso de error
        }
    }
}
?>