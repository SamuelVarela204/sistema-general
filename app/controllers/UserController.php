<?php
// app/controllers/UserController.php
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    public function profile() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['usuario_id']);
        
        if (!$user) {
            die('Usuario no encontrado');
        }
        
        require_once VIEW_PATH . '/user/profile.php';
    }
    
    public function settings() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        // Puedes cargar datos adicionales si necesitas (ej. alergias, notificaciones)
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['usuario_id']);
        
        if (!$user) {
            die('Usuario no encontrado');
        }
        
        require_once VIEW_PATH . '/user/settings.php';
    }
    
    public function updateProfile() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }
        
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['usuario_id']);
        
        if (!$user) {
            die('Usuario no encontrado');
        }
        
        // Obtener datos del formulario
        $nombre = $_POST['nombre'] ?? $user['nom_com'];
        $telefono = $_POST['telefono'] ?? $user['telefono'];
        $direccion = $_POST['direccion'] ?? $user['direccion'];
        $descripcion = $_POST['descripcion'] ?? $user['descripcion'];
        $imagen = $user['imagen']; // Mantener imagen existente
        
        // Procesar nueva imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                $imageData = file_get_contents($_FILES['imagen']['tmp_name']);
                $imagen = base64_encode($imageData);
            }
        }
        
        // Actualizar usuario
        if ($userModel->updateUser(
            $_SESSION['usuario_id'],
            $nombre,
            $telefono,
            $direccion,
            $descripcion,
            $imagen
        )) {
            // Actualizar sesión
            $_SESSION['usuario'] = $nombre;
            if ($imagen) {
                $_SESSION['imagen'] = $imagen;
            }
            
            header('Location: ' . BASE_URL . '/perfil?success=1');
        } else {
            header('Location: ' . BASE_URL . '/perfil?error=1');
        }
        exit;
    }
}