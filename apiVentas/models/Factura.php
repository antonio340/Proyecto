<?php
class Factura {
    private $conn;
    private $table_name = "facturas"; // Asegúrate de que coincida con el nombre real de la tabla

    public $idFactura;
    public $idUsuario;
    public $subtotalFactura;
    public $ivaFactura;
    public $totalFactura;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Recupera la factura para un usuario
    public function getFacturaByUsuario() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idUsuario = :idUsuario LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        $stmt->bindParam(":idUsuario", $this->idUsuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea una nueva factura, incluyendo subtotal, IVA y total
    public function createFactura() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET idUsuario = :idUsuario, subtotalFactura = :subtotalFactura, ivaFactura = :ivaFactura, totalFactura = :totalFactura";
        $stmt = $this->conn->prepare($query);

        $this->idUsuario       = htmlspecialchars(strip_tags($this->idUsuario));
        $this->subtotalFactura = htmlspecialchars(strip_tags($this->subtotalFactura));
        $this->ivaFactura      = htmlspecialchars(strip_tags($this->ivaFactura));
        $this->totalFactura    = htmlspecialchars(strip_tags($this->totalFactura));

        $stmt->bindParam(":idUsuario", $this->idUsuario);
        $stmt->bindParam(":subtotalFactura", $this->subtotalFactura);
        $stmt->bindParam(":ivaFactura", $this->ivaFactura);
        $stmt->bindParam(":totalFactura", $this->totalFactura);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Actualiza la factura existente
    public function updateFactura() {
        $query = "UPDATE " . $this->table_name . " 
                  SET subtotalFactura = :subtotalFactura, ivaFactura = :ivaFactura, totalFactura = :totalFactura 
                  WHERE idFactura = :idFactura";
        $stmt = $this->conn->prepare($query);

        $this->idFactura       = htmlspecialchars(strip_tags($this->idFactura));
        $this->subtotalFactura = htmlspecialchars(strip_tags($this->subtotalFactura));
        $this->ivaFactura      = htmlspecialchars(strip_tags($this->ivaFactura));
        $this->totalFactura    = htmlspecialchars(strip_tags($this->totalFactura));

        $stmt->bindParam(":subtotalFactura", $this->subtotalFactura);
        $stmt->bindParam(":ivaFactura", $this->ivaFactura);
        $stmt->bindParam(":totalFactura", $this->totalFactura);
        $stmt->bindParam(":idFactura", $this->idFactura);

        return $stmt->execute();
    }

    // Calcula el subtotal sumando los precios de los productos en los pedidos del usuario
    public function calculateSubtotal() {
        $query = "SELECT SUM(p.precioProducto) as subtotal 
                  FROM pedidos pe 
                  INNER JOIN productos p ON pe.idProducto = p.idProducto 
                  WHERE pe.idUsuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        $stmt->bindParam(":idUsuario", $this->idUsuario);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return (!empty($data['subtotal'])) ? $data['subtotal'] : 0; // Retorna 0 si no hay pedidos
    }
}
?>
