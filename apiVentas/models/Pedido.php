<?php

class Pedido {
    private $conn;
    private $table_name = "pedidos"; //nombre de la tabla

    //atributos de la tabla productos
    public $idPedido; 
    public $idUsuario;
    public $idProducto;


    public function __construct($db) {
        $this->conn = $db; //se obtiene la conexion con la base de datos
    }

    public function createPedidos() {
        // Verificar si el idProducto existe en la tabla productos
        $checkQuery = "SELECT COUNT(*) FROM productos WHERE idProducto = :idProducto";
        $checkStmt = $this->conn->prepare($checkQuery);
        $this->idProducto = htmlspecialchars(strip_tags($this->idProducto));
        $checkStmt->bindParam(":idProducto", $this->idProducto);
        $checkStmt->execute();
        if ($checkStmt->fetchColumn() == 0) {
            // El producto no existe
            return false;
        }

        // Insertar el pedido si el producto existe
        $query = "INSERT INTO " . $this->table_name . " SET idUsuario=:idUsuario, idProducto=:idProducto";
        $stmt = $this->conn->prepare($query);
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        // Vincular parámetros
        $stmt->bindParam(":idUsuario", $this->idUsuario);
        $stmt->bindParam(":idProducto", $this->idProducto);

        if ($stmt->execute()) { 
            return true;
        } else {
            return false;
        }
    }
    public function readPedidos() { // obtiene los pedidos de un usuario junto con el nombre del producto
        $query = "SELECT p.*, pr.precioProducto ,pr.nombreProducto 
                  FROM " . $this->table_name . " p
                  INNER JOIN productos pr ON p.idProducto = pr.idProducto
                  WHERE p.idUsuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        $stmt->bindParam(":idUsuario", $this->idUsuario);
        $stmt->execute();
        return $stmt;
    }

    public function deletePedidos() { // elimina un pedido, recibe el idPedido y el idUsuario
        $query = "DELETE FROM " . $this->table_name . " WHERE idPedido = :idPedido AND idUsuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $this->idPedido = htmlspecialchars(strip_tags($this->idPedido));
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        $stmt->bindParam(":idPedido", $this->idPedido);
        $stmt->bindParam(":idUsuario", $this->idUsuario);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
