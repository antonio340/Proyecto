<?php

// Incluir la conexión a la base de datos
require_once __DIR__ .'/../config/database.php';
// Incluir la librería FPDF
require_once __DIR__ .'/../fpdf/fpdf.php';  
require_once __DIR__ . "../../middlewares/tokenVerify.php";

     function getTokenData() {
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
    $tokenData = getTokenData(); // se obtiene el rol apartir de tokenData
        if (strtolower($tokenData['rolUsuario']) === "cliente") {
            http_response_code(403);
            echo json_encode(["error" => "No tienes permiso para crear productos."]);
            exit;
        }
// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

// Realizar la consulta que relacione facturas con pedidos y productos
$query = "SELECT 
             f.idFactura,
             f.idUsuario,
             f.subtotalFactura,
             f.ivaFactura,
             f.totalFactura,
             p.nombreProducto,
             p.precioProducto
          FROM facturas f
          LEFT JOIN pedidos pe ON f.idUsuario = pe.idUsuario
          LEFT JOIN productos p ON pe.idProducto = p.idProducto
          ORDER BY f.idFactura";
$stmt = $db->prepare($query);
$stmt->execute(); // Se ejecuta la consulta

// Crear una instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Título del reporte
$pdf->SetFont('Arial','B',16); // Fuente Arial, negrita, tamaño 16
$pdf->Cell(0,10,'Reporte de Facturas',0,1,'C'); // Ancho 0 para que ocupe todo el ancho de la página, alto 10, sin borde, salto de línea
$pdf->Ln(5); // Espacio entre el título y la tabla

// Encabezados de la tabla
$pdf->SetFont('Arial','B',12); // Fuente Arial, negrita, tamaño 12
$pdf->Cell(20,10,'Factura',1,0,'C'); // Ancho de la celda 20, alto 10, texto, borde 1, sin salto de línea, alineación centrada
$pdf->Cell(20,10,'Usuario',1,0,'C'); 
$pdf->Cell(30,10,'Subtotal',1,0,'C');
$pdf->Cell(20,10,'IVA',1,0,'C');
$pdf->Cell(30,10,'Total',1,0,'C');
$pdf->Cell(50,10,'Producto',1,0,'C');
$pdf->Cell(30,10,'Precio',1,1,'C');

// Datos del reporte
$pdf->SetFont('Arial','',10);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(20,10,$row['idFactura'],1,0,'C'); // El texto de la celda se obtiene del array asociativo $row
    $pdf->Cell(20,10,$row['idUsuario'],1,0,'C');
    $pdf->Cell(30,10,$row['subtotalFactura'],1,0,'C');
    $pdf->Cell(20,10,$row['ivaFactura'],1,0,'C');
    $pdf->Cell(30,10,$row['totalFactura'],1,0,'C');
    // utf8_decode para evitar problemas con acentos o caracteres especiales
    $pdf->Cell(50,10,utf8_decode($row['nombreProducto']),1,0,'C');
    $pdf->Cell(30,10,$row['precioProducto'],1,1,'C');
}

// Enviar el PDF al navegador y forzar su descarga
$pdf->Output('D', 'reporte_facturas.pdf');
?>
