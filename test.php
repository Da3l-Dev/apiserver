<?php
include 'db/conexion.php';
include 'controllers/UserController.php';
include 'controllers/ProyectoController.php';
include 'controllers/FilesCedulaController.php';
include 'controllers/FilesMirController.php';
include 'controllers/FirmasController.php';

// Inicializa la conexion a la base de datos y les asigna esta conexion a los modelos
$pdo = new conexion();
$userController = new UserController($pdo);
$proyectoController = new ProyectoController($pdo);
$filesController = new FilesCedulaController($pdo);
$filesMirController = new FilesMirController($pdo);
$firmasController = new FirmasController($pdo);

// Log del método de solicitud y URL solicitada
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = trim($path, '/');
customLog("Solicitud recibida: Método $method, Ruta $path");

// Obtener y logear el cuerpo de la solicitud
$json = file_get_contents('php://input');
$params = json_decode($json, true);

// Configuración de CORS específica para permitir solo el origen del frontend en desarrollo
header('Access-Control-Allow-Origin: http://localhost:4200');  // Especificar el origen permitido
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');  // Especificar encabezados permitidos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Métodos permitidos
header('Access-Control-Allow-Credentials: true');  // Permitir credenciales (cookies, autenticación)
header('Content-Type: application/json; charset=utf-8');  // Tipo de contenido de la respuesta

// Manejo de solicitudes OPTIONS (preflight) para CORS
if ($method === 'OPTIONS') {
    customLog("Solicitud OPTIONS recibida para preflight CORS");
    http_response_code(200);
    exit();  // Responde con éxito sin procesar más
}

echo $path;

?>
