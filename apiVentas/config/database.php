<?php
//archivo para la conexion a la base de datos
class Database {
    private $host = "localhost";
    private $db_name = "ventas";
    private $username = "root";
    private $password = "";
    public $conn;
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            //echo "Conexión exitosa a la base de datos";
        } catch(\Throwable $th) {
            echo "Connection error: " . $th->getMessage();
        }

        return $this->conn;
    }
}