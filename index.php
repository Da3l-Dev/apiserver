<?php
/**
 * Este código contiene los endpoints para obtener los datos desde la API
 */
include __DIR__ . "/utils/logger.php";
session_start();

include 'db/conexion.php';
include 'controllers/UserController.php';
include 'controllers/ProyectoController.php';
include 'controllers/FilesCedulaController.php';

// Inicializa la conexion a la base de datos y les asigna esta conexion a los modelos
$pdo = new conexion();
$userController = new UserController($pdo);
$proyectoController = new ProyectoController($pdo);
$filesController = new FilesCedulaController($pdo);

// Log del método de solicitud y URL solicitada
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
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

// Manejador de las peticiones del servidor
switch ($path) {

    // Ruta para obtener todos los usuarios
    case '/user/all':
        customLog("Ruta '/user/all' detectada.");
        if ($method === 'GET') {
            $userController->getAllUsers();
        }
        break;

    // Ruta para obtener un usuario por ID
    case '/user/id':
        customLog("Ruta '/user/id' detectada.");
        if ($method === 'GET' && isset($_GET['id'])) {
            $id = intval($_GET['id']); // Obtener y convertir el id a entero
            customLog("ID recibido: $id");
            $userController->getUserById($id);
        } else {
            customLog("Error: ID de usuario no proporcionado o método no válido");
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "ID de usuario no proporcionado o método no válido"]);
        }
        break;

    // Ruta para iniciar sesión
    case '/login':
        customLog("Ruta '/login' detectada.");
        if ($method === 'POST' && !empty($params['username']) && !empty($params['password'])) {
            $username = $params['username'];
            $password = $params['password'];
            customLog("Intentando iniciar sesión con usuario: $username");
            $userController->loginUser($username, $password);
        } else {
            customLog("Error: Parámetros de inicio de sesión no proporcionados correctamente");
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Parámetros de inicio de sesión no proporcionados correctamente"]);
        }
        break;

    // Ruta para verificar la autenticación
    case '/check-auth':
        customLog("Ruta '/check-auth' detectada.");
        if ($method === 'GET') {
            $userController->checkAuthentication();
        }
        break;

    // Ruta para cerrar sesión
    case '/logout':
        customLog("Ruta '/logout' detectada.");
        if ($method === 'POST') {
            $userController->logoutUser();
        }
        break;

    // Rutas del proyecto
    case '/proyecto/datos':
        if ($method === 'GET' && isset($_GET['idArea']) && isset($_GET['Year'])) {
            $idArea = intval($_GET['idArea']);
            $Year = intval($_GET['Year']);
            $proyectoController->obtenerDatosProyecto($idArea, $Year);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea o Year"]);
        }
        break;

    case '/proyecto/variables':
        if ($method === 'GET' && isset($_GET['idArea']) && isset($_GET['Year'])) {
            $idArea = intval($_GET['idArea']);
            $Year = intval($_GET['Year']);
            $proyectoController->obtenerDatosVariables($idArea, $Year);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea o Year"]);
        }
        break;

    case '/proyecto/metasProg':
        if ($method === 'GET' && isset($_GET['idArea']) && isset($_GET['Year'])) {
            $idArea = intval($_GET['idArea']);
            $Year = intval($_GET['Year']);
            $proyectoController->obtenerDatosMetasProgramadas($idArea, $Year);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea o Year"]);
        }
        break;

    case '/proyecto/metasAlcanzada':
        if ($method === 'GET' && isset($_GET['idArea'])) {
            $idArea = intval($_GET['idArea']);
            $proyectoController->obtenerMetaAlcanzadas($idArea);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea"]);
        }
        break;

    case '/proyecto/seguimiento':
        if ($method === 'GET' && isset($_GET['idArea'])) {
            $idArea = intval($_GET['idArea']);
            $proyectoController->obtenerSeguimiento($idArea);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea"]);
        }
        break;

    case '/proyecto/trimActivo':
        if ($method === 'GET' && isset($_GET['idArea'])) {
            $idArea = intval($_GET['idArea']);
            $proyectoController->obtenerTrimActivoArea($idArea);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea"]);
        }
        break;

    case '/proyecto/logros':
        if ($method === 'GET' && isset($_GET['idEjercicio']) && isset($_GET['idArea']) && isset($_GET['idTrim']) && isset($_GET['idIndicador'])) {
            $idEjercicio = intval($_GET['idEjercicio']);
            $idArea = intval($_GET['idArea']);
            $idTrimestre = intval($_GET['idTrim']);
            $idIndicador = intval($_GET['idIndicador']);
            $proyectoController->obtenerLogrosArea($idEjercicio, $idArea, $idTrimestre, $idIndicador);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros idArea"]);
        }
        break;

    case '/proyecto/subirLogro':
        if ($method === 'POST') {
            $proyectoController->registrarLogro($params);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/proyecto/mejoras':
        if($method === 'GET' && isset($_GET['idArea']) && isset($_GET['idIndicador'])){
            $idArea = intval($_GET['idArea']);
            $idIndicador = intval($_GET['idIndicador']);
            $proyectoController->obtenerMejoraSub($idArea,$idIndicador);
        }else{
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Faltan parámetros"]);
        }
        break;

    // Rutas de archivos
    case '/files/subirEvidencia':
        if ($method === 'POST') {
            $filesController->subirEvidenciaRuta($params);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/files/obtenerEvidenciaRuta':
        if ($method === 'GET') {
            $filesController->obtenerEvidenciaRuta();
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/files/eliminarRuta':
        if ($method === 'DELETE') {  // Cambiado de 'GET' a 'DELETE'
            $filesController->eliminarRutaArchivo($params);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;
        

    // Endpoint no encontrado
    default:
        customLog("Error: Endpoint no encontrado - Ruta: $path");
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Endpoint no encontrado"]);
        break;
}
