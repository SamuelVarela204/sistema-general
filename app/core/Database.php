<?php
// app/core/Database.php
require_once __DIR__ . '../config/database.php';
use PDO;

class Database {
    private PDO $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect(): void { // ← Tipo de retorno
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error de conexión a la base de datos");
        }
    }
    
    public function getConnection(): PDO { // ← Tipo de retorno
        return $this->conn;
    }
}