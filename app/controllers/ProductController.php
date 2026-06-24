<?php
// app/controllers/ProductController.php
require_once __DIR__ . '../models/ProductModel.php';

class ProductController {
    public function search() {
        $searchTerm = $_GET['q'] ?? '';
        $productModel = new ProductModel();
        $productos = $productModel->getAllProducts($searchTerm);
        
        // Cargar la vista con los resultados
        require_once VIEW_PATH . '../views/search.php';
    }
}