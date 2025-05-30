<?php

class Producto {
    private $conn;
    private $table_name = "productos"; //nombre de la tabla

    //atributos de la tabla productos
    public $idProducto; 
    public $nombreProducto;
    public $precioProducto;


    public function __construct($db) {
        $this->conn = $db; //se obtiene la conexion con la base de datos
    }

    public function createProductos() {
        // se prepara la consulta para insertar un nuevo producto
        $query = "INSERT INTO " . $this->table_name . " SET nombreProducto=:nombreProducto,
         precioProducto=:precioProducto";
        $stmt = $this->conn->prepare($query);
        // se sanitizan los datos para evitar inyecciones SQL
        $stmt->nombreProducto = htmlspecialchars(strip_tags($this->nombreProducto));
        $stmt->precioProducto = htmlspecialchars(strip_tags($this->precioProducto));
        // se vinculan los parametros a la consulta
        $stmt->bindParam(":nombreProducto", $stmt->nombreProducto);
        $stmt->bindParam(":precioProducto", $stmt->precioProducto);

        if ($stmt->execute()) { 
            return true; // si la consulta se ejecuta correctamente devolver verdadero
        }else {
            return false; // si no se ejecuta correctamente, devolver falso
        }
    }
    public function readProductos() { //solo obtiene todos los productos, no recibe nada
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function readProducto($idProducto) { //obtiene un producto por su idProducto
        $query = "SELECT * FROM " . $this->table_name . " WHERE idProducto = :idProducto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idProducto", $idProducto);
        $stmt->execute();
        return $stmt;
    }
    public function updateProductos() { //actualiza un alumno, recibe los datos del alumno a actualizar
        $query = "UPDATE " . $this->table_name . " SET nombreProducto = :nombreProducto,
        precioProducto = :precioProducto WHERE idProducto = :idProducto";
        $stmt = $this->conn->prepare($query);
        // se sanitizan los datos para evitar inyecciones SQL
        $stmt->idProducto = htmlspecialchars(strip_tags($this->idProducto));
        $stmt->nombreProducto = htmlspecialchars(strip_tags($this->nombreProducto));
        $stmt->precioProducto = htmlspecialchars(strip_tags($this->precioProducto));
        // se vinculan los parametros a la consulta
        $stmt->bindParam(":idProducto",  $stmt->idProducto);
        $stmt->bindParam(":nombreProducto", $stmt->nombreProducto);
        $stmt->bindParam(":precioProducto", $stmt->precioProducto);

        if ($stmt->execute()) { //si se ejecuta la consulta
            if ($stmt->rowCount() > 0) { //revisa si se actualizó al menos un registro
                return true; // Se actualizó al menos un registro
            } else {
                return false; // No se actualizó ningún registro (posible no existencia)
            }
        } else {
            return false; // Error en la ejecución de la consulta
        }

    }
    public function deleteProductos() { //elimina un alumno, recibe el noControl del alumno a eliminar
        $query = "DELETE FROM " . $this->table_name . " WHERE idProducto = :idProducto";
        $stmt = $this->conn->prepare($query);
        $stmt->noControl = htmlspecialchars(strip_tags($this->idProducto));
        $stmt->bindParam(":idProducto", $this->idProducto);
        if ($stmt->execute()) {//si se ejecuta la consulta
            if ($stmt->rowCount() > 0) { //revisa si se eliminó al menos un registro
                return true; // Se eliminó al menos un registro
            } else {
                return false; // No se eliminó ningún registro (posible no existencia)
            }
        } else {
            return false; // Error en la ejecución de la consulta
        }
    }
}
