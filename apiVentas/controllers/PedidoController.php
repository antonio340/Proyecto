<?php

require_once __DIR__ . "/../config/database.php"; // incluir la clase Database __DIR__ es una constante que contiene la ruta del directorio actual
require_once __DIR__ . "/../models/Pedido.php";
require_once __DIR__ . "../../middlewares/tokenVerify.php";

class PedidoController {
    private $db;
    private $pedido;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pedido = new Pedido($this->db);
    }
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
    public function createPedidos() {
        $data = json_decode(file_get_contents("php://input"));
        $tokenData = $this->getTokenData(); //se obtiene el id del usuario del token
        if (isset($data->idProducto) && $data->idProducto !== "") {

            $this->pedido->idUsuario = $tokenData['idUsuario']; //se asigna el id del usuario al pedido
            $this->pedido->idProducto = $data->idProducto;

            if ($this->pedido->createPedidos()) {
                http_response_code(201);
                echo json_encode(["message" => "Pedido creado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error al crear el Pedido"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
            echo json_encode($data);
        }
    }
    public function readPedidos() {
        $tokenData = $this->getTokenData(); //se obtiene el id del usuario del token
        $data = json_decode(file_get_contents("php://input"));
        $this->pedido->idUsuario = $tokenData['idUsuario']; //se asigna el id del usuario al pedido
        $stmt = $this->pedido->readPedidos();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $pedidos_arr = [];
            $pedidos_arr["pedidos"] = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $pedido_item = array(
                    "idPedido" => $idPedido,
                    "idUsuario" => $idUsuario,
                    "idProducto" => $idProducto,
                    "nombreProducto" => $nombreProducto,
                    "precioProducto" => $precioProducto
                );
                array_push($pedidos_arr["pedidos"], $pedido_item);
            }
            http_response_code(200);
            echo json_encode($pedidos_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Todavia no tiene pedidos"));
        }
    }
    
    public function deletePedidos() {
        $tokenData = $this->getTokenData(); //se obtiene el id del usuario del token
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->idPedido) && $data->idPedido !== "") {
            $this->pedido->idPedido = $data->idPedido;
            $this->pedido->idUsuario = $tokenData['idUsuario']; //se asigna el id del usuario al pedido

            if ($this->pedido->deletePedidos()) {
                http_response_code(200);
                echo json_encode(["message" => "Pedido eliminado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error al eliminar el pedido, puede que no exista."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
