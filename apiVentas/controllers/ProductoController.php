<?php

require_once __DIR__ . "/../config/database.php"; // incluir la clase Database __DIR__ es una constante que contiene la ruta del directorio actual
require_once __DIR__ . "/../models/Producto.php";
// Incluimos el middleware para la verificación del token
require_once __DIR__ . "../../middlewares/tokenVerify.php";

class ProductosController {
    private $db;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
    }

    // Función auxiliar para obtener los datos del token
    private function getTokenData() {
        $token = getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Token no proporcionado."]);
            exit;
        }
        $tokenData = verifyToken($token);
        if (!$tokenData) {
            http_response_code(401);
            echo json_encode(["error" => "Token inválido o expirado."]);
            exit;
        }
        return $tokenData;
    }

    

    public function readProductos() {
        $stmt = $this->producto->readProductos();
        $num = $stmt->rowCount(); 

        if ($num > 0) {
            $productos_arr = [];
            $productos_arr["productos"] = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $producto_item = array(
                    "idProducto" => $idProducto,
                    "nombreProducto" => $nombreProducto,
                    "precioProducto" => $precioProducto
                );
                array_push($productos_arr["productos"], $producto_item);
            }
            http_response_code(200);
            echo json_encode($productos_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron productos"));
        }
    }

    public function readProducto($idProducto) {
        $stmt = $this->producto->readProducto($idProducto);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Producto no encontrado"));
        }
    }

    public function createProductos() {
        // Verificamos el rol del usuario si es cliente, no puede crear productos
        $tokenData = $this->getTokenData(); // se obtiene el rol apartir de tokenData
        if (strtolower($tokenData['rolUsuario']) === "cliente") {
            http_response_code(403);
            echo json_encode(["error" => "No tienes permiso para crear productos."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->nombreProducto) &&
            isset($data->precioProducto) &&
            $data->nombreProducto !== "" &&
            $data->precioProducto !== "") {

            $this->producto->nombreProducto = $data->nombreProducto;
            $this->producto->precioProducto = $data->precioProducto;

            if ($this->producto->createProductos()) {
                http_response_code(201);
                echo json_encode(["message" => "Producto creado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error al crear el Producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
            echo json_encode($data);
        }
    }

    public function updateProductos() {
        // Verificamos el rol del usuario si es cliente, no puede actualizar productos
        $tokenData = $this->getTokenData(); // se obtiene el rol apartir de tokenData
        if (strtolower($tokenData['rolUsuario']) === "cliente") {
            http_response_code(403);
            echo json_encode(["error" => "No tienes permiso para actualizar productos."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->idProducto) && isset($data->nombreProducto) &&
            isset($data->precioProducto) &&
            $data->idProducto !== "" && $data->nombreProducto !== "" &&
            $data->precioProducto !== "") {

            $this->producto->idProducto = $data->idProducto;
            $this->producto->nombreProducto = $data->nombreProducto;
            $this->producto->precioProducto = $data->precioProducto;

            if ($this->producto->updateProductos()) {
                http_response_code(200);
                echo json_encode(["message" => "Producto actualizado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error al actualizar el producto, puede que no exista."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function deleteProductos() {
        // Verificamos el rol del usuario si es cliente, no puede borrar productos
        $tokenData = $this->getTokenData(); // se obtiene el rol apartir de tokenData
        if (strtolower($tokenData['rolUsuario']) === "cliente") {
            http_response_code(403);
            echo json_encode(["error" => "No tienes permiso para borrar productos."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->idProducto) && $data->idProducto !== "") {
            $this->producto->idProducto = $data->idProducto;

            if ($this->producto->deleteProductos()) {
                http_response_code(200);
                echo json_encode(["message" => "Producto eliminado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error al eliminar el producto, puede que no exista."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
