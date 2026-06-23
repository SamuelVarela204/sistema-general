<?php
// app/models/UserModel.php
require_once __DIR__ . '/../core/Database.php';
use PDO;

class UserModel {
    private PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findByEmail(string $email): ?array {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM usuarios u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.correo = :email";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool {
        $query = "SELECT COUNT(*) FROM usuarios WHERE correo = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function createUser(string $nombre, string $email, string $password): bool {
        $query = "INSERT INTO usuarios (nom_com, correo, usu_con, id_rol) 
                  VALUES (:nombre, :email, :password, 5)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updateUser(int $id, string $nombre, ?string $telefono, ?string $direccion, ?string $descripcion, ?string $imagen = null): bool {
        $set = ["nom_com = :nombre"];
        $params = [':nombre' => $nombre, ':id' => $id];

        if ($telefono !== null) {
            $set[] = "telefono = :telefono";
            $params[':telefono'] = $telefono;
        }
        if ($direccion !== null) {
            $set[] = "direccion = :direccion";
            $params[':direccion'] = $direccion;
        }
        if ($descripcion !== null) {
            $set[] = "descripcion = :descripcion";
            $params[':descripcion'] = $descripcion;
        }
        if ($imagen !== null) {
            $set[] = "imagen = :imagen";
            $params[':imagen'] = $imagen;
        }

        $query = "UPDATE usuarios SET " . implode(', ', $set) . " WHERE id_usu = :id";
        $stmt = $this->db->prepare($query);

        return $stmt->execute($params);
    }

    public function getUserById(int $id): ?array {
        $query = "SELECT * FROM usuarios WHERE id_usu = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() ?: null;
    }
}