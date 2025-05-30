<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $idUsuario;
    public $nombreUsuario;
    public $claveUsuario;
    public $rolUsuario;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Consulta para obtener un usuario según su nombre
    public function getUserByNombre() { //obtiene toda la información del usuario según su nombre de usuario
        $query = "SELECT * FROM " . $this->table_name . " WHERE nombreUsuario = :nombreUsuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        // Sanitizamos el dato
        $nombreSanitizado = htmlspecialchars(strip_tags($this->nombreUsuario));
        
        $stmt->bindParam(":nombreUsuario", $nombreSanitizado);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
