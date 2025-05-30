<?php
// Configuración de cabeceras para CORS y para indicar que se retorna JSON.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8"); // 

// Incluimos las dependencias necesarias: la configuración de la base de datos y los controladores
require_once "../../config/database.php";




// Obtenemos el método HTTP de la petición
$method = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); // Obtenemos la URI de la petición
$uriComponents = explode("/", trim($uri, "/")); // Separamos la URL en sus componentes

// Ejemplo de rutas
if (in_array("login", $uriComponents)) {
    // Ruta para iniciar sesión
    require_once "../../controllers/UsuarioController.php";
    $usuarioController = new UsuarioController();
    
    if ($method === "POST") {
        $usuarioController->login();
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Solo se permite el método POST para login"]);
    }
}
else if (in_array("productos", $uriComponents)) {
    require_once "../../controllers/ProductoController.php"; // Controlador de productos
    $ProductosController = new ProductosController();
    switch ($method) {
        case "GET":
            if (isset($_GET['noControl'])) {
                $ProductosController->readProducto($_GET['noControl']);
            } else {
                $ProductosController->readProductos();
            }
            break;
        case "POST":
            $ProductosController->createProductos();
            break;
        case "PUT":
            $ProductosController->updateProductos();
            break;
        case "DELETE":
            $ProductosController->deleteProductos();
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} else if (in_array("pedidos", $uriComponents)) {
    require_once "../../controllers/PedidoController.php"; // Controlador de pedidos
    $PedidoController = new PedidoController();
    switch ($method) {
        case "GET":
            $PedidoController->readPedidos();
            break;
        case "POST":
            $PedidoController->createPedidos();
            break;
        case "DELETE":
            $PedidoController->deletePedidos();
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} elseif (in_array("factura", $uriComponents)) {
    require_once "../../controllers/FacturaController.php"; // Controlador de factura
    $facturaController = new FacturaController();
    // Verificamos si el método es GET para procesar la factura
    if ($method === "GET") {
        $facturaController->processFactura();
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Solo se permite el método GET para factura"]);
    }
} elseif (in_array("reporteFacturas", $uriComponents)) {
    // Verificamos si el método es GET para procesar la factura
    if ($method === "GET") {
        require_once "../../reportes/reporteFacturasPDF.php";
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Solo se permite el método GET para los reportes"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}

?>
