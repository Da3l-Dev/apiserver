<?php
include_once __DIR__ . '/../utils/logger.php';
include_once 'models/UserModel.php';
use Firebase\JWT\JWT;

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    // Método para iniciar sesión
    public function loginUser($username, $password) {

        $user = $this->userModel->loginUser($username, $password);

        if ($user) {
            $_SESSION['user'] = $user;

            echo json_encode([
                'status' => 'success',
                'message' => 'Inicio de sesión exitoso',
                'user' => $user
            ]);
        } else {

            
            // Enviar el mensaje de error con código 401
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuario o contraseña incorrectos'
            ]);
        }
    }

    // Método para verificar si el usuario está autenticado
    public function checkAuthentication() {
        
        if (isset($_SESSION['user'])) {

            echo json_encode([
                'authenticated' => true,
                'user' => $_SESSION['user']
            ]);
        } else {

            http_response_code(401);
            echo json_encode([
                'authenticated' => true,
                'message' => 'Usuario no autenticado'
            ]);
        }
    }

    // Método para cerrar sesión
    public function logoutUser() {
        

        session_unset();
        session_destroy();
        
        

        echo json_encode([
            'status' => 'success',
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }
}
