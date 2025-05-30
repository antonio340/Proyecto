<?php
// Archivo: controllers/UsuarioController.php
require_once __DIR__ . '/../config/database.php'; // incluir la clase Database __DIR__ es una constante que contiene la ruta del directorio actual
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/Usuario.php';


use \Firebase\JWT\JWT;

class UsuarioController {
    private $db;
    private $usuario;
    private $secretKey = "123123o"; // Define una clave secreta fuerte

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }
    
    public function login() {
        $data = json_decode(file_get_contents("php://input")); // Obtiene los datos del cuerpo de la solicitud en formato JSON
        // Verifica si los datos necesarios están presentes
        if (!isset($data->nombreUsuario) || !isset($data->claveUsuario)) { // Verifica si los datos necesarios están presentes
            // Si faltan datos, devuelve un error 400 (Bad Request)
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos."]);
            return;
        }

        $this->usuario->nombreUsuario = $data->nombreUsuario; // asigna el nombre de usuario ingresado por el json al objeto Usuario

        $usuario = $this->usuario->getUserByNombre(); // ejecuta el metodo getUserByNombre() de la clase Usuario
        if ($usuario) { // Si el usuario existe, verificamos la contraseña
            if ($data->claveUsuario === $usuario['claveUsuario']) {
                // Datos a incluir en el token
                $tokenData = [
                    'idUsuario'       => $usuario['idUsuario'],
                    'nombreUsuario'   => $usuario['nombreUsuario'],
                    'rolUsuario'      => $usuario['rolUsuario'],
                    'iat'             => time(),           // Fecha de emisión
                    'exp'             => time() + 3600       // Expira en 1 hora (puedes ajustar el tiempo)
                ];

                $token = JWT::encode($tokenData, $this->secretKey, 'HS256');
                echo json_encode(["token" => $token]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Credenciales incorrectas."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado."]);
        }
    }
}
?>
