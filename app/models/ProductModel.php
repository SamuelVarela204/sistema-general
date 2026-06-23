<?php
// app/models/ProductModel.php
require_once __DIR__ . '/../core/Database.php';

class ProductModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllProducts($searchTerm = null)
    {
        $query = "SELECT p.*, c.nombre_cat 
                  FROM producto p
                  JOIN categorias c ON p.id_cat = c.id_cat
                  WHERE p.tipo = 'producto_final'";

        if ($searchTerm) {
            $query .= " AND (p.nom_pro LIKE :term OR p.descripcion LIKE :term)";
        }

        $stmt = $this->db->prepare($query);

        if ($searchTerm) {
            $term = '%' . $searchTerm . '%';
            $stmt->bindParam(':term', $term);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
