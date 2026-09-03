<?php
// controlador/controlador.php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar el motor de sesiones de PHP
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../../includes/functions.php';

// =========================================
// AUTO-MIGRACIÓN: Verificar y crear tabla categorias si no existe
// =========================================
try {
    $check = $pdo->query("SHOW TABLES LIKE 'categorias'")->fetch();
    if (!$check) {
        // Crear tabla categorias
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categorias (
                id_cat INT NOT NULL AUTO_INCREMENT,
                nombre_cat VARCHAR(100) NOT NULL UNIQUE,
                descripcion VARCHAR(255),
                estado VARCHAR(20) NOT NULL DEFAULT 'activo',
                PRIMARY KEY (id_cat)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Insertar categorías predefinidas
        $pdo->exec("
            INSERT IGNORE INTO categorias (id_cat, nombre_cat, descripcion, estado) VALUES
            (1, 'Bebidas', 'Jugos y bebidas naturales', 'activo'),
            (2, 'Platos', 'Platos principales y combinados', 'activo'),
            (3, 'Postres', 'Postres y frutas con acompañamientos', 'activo'),
            (4, 'Ensaladas', 'Ensaladas frescas y saludables', 'activo'),
            (5, 'Productos', 'Productos varios', 'activo');
        ");
        
        // Verificar si la columna id_cat ya existe
        $columnas = $pdo->query("DESCRIBE producto")->fetchAll();
        $tiene_id_cat = false;
        
        foreach ($columnas as $col) {
            if ($col['Field'] === 'id_cat') {
                $tiene_id_cat = true;
                break;
            }
        }
        
        // Si no existe id_cat, agregarla
        if (!$tiene_id_cat) {
            $pdo->exec("ALTER TABLE producto ADD COLUMN id_cat INT NOT NULL DEFAULT 1");
            
            // Agregar constraint
            try {
                $pdo->exec("ALTER TABLE producto ADD CONSTRAINT producto_ibfk_categorias 
                    FOREIGN KEY (id_cat) REFERENCES categorias (id_cat) 
                    ON UPDATE CASCADE");
            } catch (Exception $e) {
                // El constraint ya existe, ignorar
            }
        }
    }
} catch (Exception $e) {
    // Log silencioso si hay error, la aplicación sigue funcionando
}

// Protección de páginas - redirigir usando TAF2_URL
$pagina_actual = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['usuario_id']) && $pagina_actual !== 'login.php' && $pagina_actual !== 'procesar.php') {
    // Redirección manual para TAF2 ya que BASE_URL apunta a la raíz
    $basePath = TAF2_URL;
    $url = $basePath . '/login.php';
    header("Location: $url");
    exit;
}

// Rol sidebar
$rol_actual = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : 'cliente';

// =========================================
// FUNCIONES DE CONTROL DE ACCESO
// =========================================

/**
 * Verifica si el usuario actual tiene permiso para realizar una acción
 * @param string $rol_requerido El rol necesario (o array de roles permitidos)
 * @return bool True si tiene permiso, False si no
 */
function verificar_permiso($rol_requerido) {
    if (!isset($_SESSION['usuario_rol'])) {
        return false;
    }
    
    $rol_usuario = $_SESSION['usuario_rol'];
    
    if (is_array($rol_requerido)) {
        return in_array($rol_usuario, $rol_requerido, true);
    }
    
    return $rol_usuario === $rol_requerido;
}

/**
 * Verifica permisos y redirige si no los tiene
 * @param string|array $rol_requerido El rol necesario (o array de roles)
 * @param string $url_redireccion URL de redirección si no tiene permiso
 */
function requiere_permiso($rol_requerido, $url_redireccion = '../index.php') {
    if (!verificar_permiso($rol_requerido)) {
        header("Location: $url_redireccion?error=No+tienes+permiso+para+acceder+a+esta+página");
        exit;
    }
}

/**
 * Obtiene todas las categorías disponibles
 * @return array Array de categorías
 */
function obtener_categorias() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id_cat, nombre_cat FROM categorias WHERE estado = 'activo' ORDER BY nombre_cat ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Obtiene el nombre de una categoría por su ID
 * @param int $id_cat ID de la categoría
 * @return string Nombre de la categoría
 */
function obtener_nombre_categoria($id_cat) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT nombre_cat FROM categorias WHERE id_cat = ?");
        $stmt->execute([$id_cat]);
        $cat = $stmt->fetch();
        return $cat ? $cat['nombre_cat'] : 'Desconocida';
    } catch (PDOException $e) {
        return 'Error';
    }
}
?>