<?php
// app/controllers/AuthController.php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    public function login() {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['correo'] ?? '';
            $password = $_POST['contrasena'] ?? '';
            
            $userModel = new UserModel();
            $user = $userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['usu_con'])) {
                // Iniciar sesión
                $_SESSION['usuario'] = $user['nom_com'];
                $_SESSION['usuario_id'] = $user['id_usu'];
                $_SESSION['usuario_rol'] = $user['nombre_rol'];
                
                if (!empty($user['imagen'])) {
                    $_SESSION['imagen'] = $user['imagen'];
                }
                
                // Redirigir al home
                header('Location: ' . BASE_URL . '/');
                exit;
            } else {
                $error = 'Credenciales inválidas';
            }
        }
        
        // Cargar la vista
        require_once VIEW_PATH . '/auth/login.php';
    }
    
    public function register() {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['correo'] ?? '';
            $password = $_POST['contrasena'] ?? '';
            
            // Validaciones básicas
            if (empty($nombre) || empty($email) || empty($password)) {
                $error = 'Todos los campos son obligatorios';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                $userModel = new UserModel();
                
                // Verificar si el email ya existe
                if ($userModel->emailExists($email)) {
                    $error = 'Este correo ya está registrado';
                } else {
                    // Registrar el usuario
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $userModel->createUser($nombre, $email, $hashedPassword);
                    
                    // Redirigir al login con éxito
                    header('Location: ' . BASE_URL . '/login?success=1');
                    exit;
                }
            }
        }
        
        require_once VIEW_PATH . '/auth/register.php';
    }
    
    public function logout() {
        // Destruir la sesión
        session_unset();
        session_destroy();
        
        // Redirigir al home
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}