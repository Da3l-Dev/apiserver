<?php
/**
 * Este modelo esta encaragado de hacer consultas a la BD para obtener datos importantes del usurio
 */
include_once __DIR__ . '/../utils/logger.php';

class UserModel {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
        customLog("UserModel inicializado con conexión a la base de datos.");
    }


    // Funcion para poder inciar session y oibtener los datos del usuario
    public function loginUser($username, $psw) {
        customLog("Intentando iniciar sesión con usuario: $username");
    
        $stmt = $this->pdo->prepare("SELECT * FROM usuario, unidadoperativa WHERE usuario = :usuario AND usuario.idArea = unidadoperativa.idArea");
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        customLog("Resultado de la consulta para $username: " . print_r($user, true)); // Log del resultado de la consulta
    
        if ($user && $user['contraInicio'] == $psw) {
            customLog("Contraseña correcta para usuario: $username");
            // Eliminar el campo privado antes de devolver los datos
            unset($user['contraInicio']);
    
            return $user;
        } else {
            return false;
        }
    }    
}