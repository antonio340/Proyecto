<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Factura.php";
// Incluimos el middleware para la verificación del token
require_once __DIR__ . "../../middlewares/tokenVerify.php";


class FacturaController {
    private $db;
    private $factura;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->factura = new Factura($this->db);
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

    // Procesa la factura: crea o actualiza según si ya existe para el idUsuario
    public function processFactura() {
            $data = json_decode(file_get_contents("php://input"));
            $tokenData = $this->getTokenData(); // Obtiene los datos del token para el idUsuario
            $this->factura->idUsuario = $tokenData['idUsuario'];
            // Calcula el subtotal de pedidos (suma de precios)
            $subtotal = $this->factura->calculateSubtotal();
            
            // Calculamos el IVA (16%) y el total (subtotal + IVA)
            $ivaRate = 0.16;
            $iva = $subtotal * $ivaRate;
            $total = $subtotal + $iva;

            // Verifica si ya existe una factura para el usuario
            $existingFactura = $this->factura->getFacturaByUsuario();
            if ($existingFactura) {
                // Actualiza la factura existente
                $this->factura->idFactura       = $existingFactura['idFactura'];
                $this->factura->subtotalFactura = $subtotal;
                $this->factura->ivaFactura      = $iva;
                $this->factura->totalFactura    = $total;
                
                if ($this->factura->updateFactura()) {
                    http_response_code(200);
                    echo json_encode([
                        "message" => "Factura actualizada",
                        "factura" => [
                            "idFactura"       => $existingFactura['idFactura'],
                            "idUsuario"       => $this->factura->idUsuario,
                            "subtotalFactura" => $subtotal,
                            "ivaFactura"      => $iva,
                            "totalFactura"    => $total
                        ]
                    ]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Error al actualizar la factura"]);
                }
            } else {
                // Crea una nueva factura
                $this->factura->subtotalFactura = $subtotal;
                $this->factura->ivaFactura      = $iva;
                $this->factura->totalFactura    = $total;
               
                
                $newId = $this->factura->createFactura();
                if ($newId) {
                    http_response_code(201);
                    echo json_encode([
                        "message" => "Factura creada",
                        "factura" => [
                            "idFactura"       => $newId,
                            "idUsuario"       => $this->factura->idUsuario,
                            "subtotalFactura" => $subtotal,
                            "ivaFactura"      => $iva,
                            "totalFactura"    => $total
                        ]
                    ]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Error al crear la factura"]);
                }
            }
    }
}
?>
