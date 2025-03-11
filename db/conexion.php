<?php
/**
 * Este codigo se encarga de establecer una conexion a la base de datos
 */
include_once __DIR__ . '/../utils/logger.php';
class Conexion extends PDO {
    private $host = 'localhost';
    private $db_name = 'cockersy_sieva31';
    private $username = 'dael';
    private $password = 'Da3lox#2022';

    public function __construct() {
        try {
            parent::__construct(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8',
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            // customLog("Conexión a la base de datos exitosa.");
        } catch (PDOException $e) {
            // customLog("Error de conexión a la base de datos: " . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
        }
    }
}
