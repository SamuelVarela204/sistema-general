<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions.php';

$con = conectarBD();

// =========================================
// AUTO-MIGRACIÓN: Crear tabla categorias si no existe
// =========================================
$checkTable = mysqli_query($con, "SHOW TABLES LIKE 'categorias'");
if (mysqli_num_rows($checkTable) == 0) {
    // Crear tabla categorias
    mysqli_query($con, "
        CREATE TABLE IF NOT EXISTS categorias (
            id_cat INT NOT NULL AUTO_INCREMENT,
            nombre_cat VARCHAR(100) NOT NULL UNIQUE,
            descripcion VARCHAR(255),
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            PRIMARY KEY (id_cat)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Insertar categorías predefinidas
    mysqli_query($con, "
        INSERT IGNORE INTO categorias (id_cat, nombre_cat, descripcion, estado) VALUES
        (1, 'Bebidas', 'Jugos y bebidas naturales', 'activo'),
        (2, 'Platos', 'Platos principales y combinados', 'activo'),
        (3, 'Postres', 'Postres y frutas con acompañamientos', 'activo'),
        (4, 'Ensaladas', 'Ensaladas frescas y saludables', 'activo'),
        (5, 'Productos', 'Productos varios', 'activo');
    ");
    
    // Verificar si ya existe la columna id_cat en producto
    $columnas = mysqli_query($con, "DESCRIBE producto");
    $tiene_id_cat = false;
    
    while ($col = mysqli_fetch_assoc($columnas)) {
        if ($col['Field'] === 'id_cat') {
            $tiene_id_cat = true;
            break;
        }
    }
    
    if (!$tiene_id_cat) {
        // Agregar columna id_cat
        mysqli_query($con, "ALTER TABLE producto ADD COLUMN id_cat INT NOT NULL DEFAULT 1");
        
        // Intentar agregar constraint (puede fallar silenciosamente si ya existe)
        mysqli_query($con, "ALTER TABLE producto ADD CONSTRAINT producto_ibfk_categorias 
            FOREIGN KEY (id_cat) REFERENCES categorias (id_cat) 
            ON UPDATE CASCADE");
    }
}

$pagina_actual = basename($_SERVER['PHP_SELF']);
$logueado = !empty($_SESSION['usuario']) || !empty($_SESSION['correo']) || !empty($_SESSION['usuario_id']);
if (!$logueado && $pagina_actual !== 'login.php' && $pagina_actual !== 'procesar.php') {
    redirigir('index.php?page=login');
}

$rol_actual = $_SESSION['usuario_rol'] ?? 'cliente';

function obtenerDatosTaf2($con) {
    $productos = [];
    $usuarios = [];
    $pedidos = [];
    $categorias = [];

    if (tablaExiste($con, 'producto')) {
        $productos = mysqli_query($con, 'SELECT * FROM producto ORDER BY id_pro DESC');
        $productos = $productos ? mysqli_fetch_all($productos, MYSQLI_ASSOC) : [];
    }

    if (tablaExiste($con, 'usuarios')) {
        $usuarios = mysqli_query(
            $con,
            'SELECT u.id_usu, u.nom_com, u.correo, u.estado, u.id_rol, IFNULL(r.nombre_rol, "cliente") AS nombre_rol
             FROM usuarios u
             LEFT JOIN roles r ON u.id_rol = r.id_rol
             ORDER BY u.id_usu DESC'
        );
        $usuarios = $usuarios ? mysqli_fetch_all($usuarios, MYSQLI_ASSOC) : [];
    }

    if (tablaExiste($con, 'pedido') && tablaExiste($con, 'usuarios')) {
        $pedidos = mysqli_query($con, 'SELECT p.id_ped, u.nom_com, p.fecha_pedido, p.estado, p.total FROM pedido p JOIN usuarios u ON p.id_usu = u.id_usu ORDER BY p.id_ped DESC');
        $pedidos = $pedidos ? mysqli_fetch_all($pedidos, MYSQLI_ASSOC) : [];
    }

    // Obtener categorías
    if (tablaExiste($con, 'categorias')) {
        $categorias = mysqli_query($con, "SELECT id_cat, nombre_cat FROM categorias WHERE estado = 'activo' ORDER BY nombre_cat ASC");
        $categorias = $categorias ? mysqli_fetch_all($categorias, MYSQLI_ASSOC) : [];
    }

    return compact('productos', 'usuarios', 'pedidos', 'categorias');
}

// =========================================
// FUNCIONES DE ALERGIAS
// =========================================

/**
 * Obtiene todas las frutas disponibles
 */
function obtenerFrutas($con) {
    if (!tablaExiste($con, 'frutas')) {
        return [];
    }
    $result = mysqli_query($con, "SELECT id_fru, nom_fru FROM frutas ORDER BY nom_fru ASC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

/**
 * Obtiene las alergias del usuario actual
 */
function obtenerAlergiasUsuario($con, $id_usu) {
    if (!tablaExiste($con, 'usuario_alergias')) {
        return [];
    }
    $stmt = mysqli_prepare($con, "
        SELECT ua.id_fru, f.nom_fru 
        FROM usuario_alergias ua
        JOIN frutas f ON ua.id_fru = f.id_fru
        WHERE ua.id_usu = ?
        ORDER BY f.nom_fru ASC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $id_usu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $alergias = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $alergias;
}

/**
 * Obtiene frutas no alérgenas (frutas sin alergia del usuario)
 */
function obtenerFrutasDisponibles($con, $id_usu) {
    if (!tablaExiste($con, 'frutas')) {
        return [];
    }
    
    $stmt = mysqli_prepare($con, "
        SELECT f.id_fru, f.nom_fru 
        FROM frutas f
        WHERE f.id_fru NOT IN (
            SELECT id_fru FROM usuario_alergias WHERE id_usu = ?
        )
        ORDER BY f.nom_fru ASC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $id_usu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $frutas = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $frutas;
}

// =========================================
// FUNCIONES DE INGREDIENTES
// =========================================

/**
 * Obtiene todos los ingredientes disponibles
 */
function obtenerIngredientes($con) {
    if (!tablaExiste($con, 'ingredientes')) {
        return [];
    }
    $result = mysqli_query($con, "SELECT * FROM ingredientes WHERE estado = 'activo' ORDER BY nombre_ing ASC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

/**
 * Obtiene ingredientes de una receta específica
 */
function obtenerIngredientesReceta($con, $id_rec) {
    if (!tablaExiste($con, 'ingredientes_receta')) {
        return [];
    }
    $stmt = mysqli_prepare($con, "
        SELECT ir.id_ing_rec, ir.id_rec, ir.id_inv, ir.cantidad_necesaria,
               i.nombre_ing, i.unidad_medida, i.stock_actual
        FROM ingredientes_receta ir
        JOIN inventario i ON ir.id_inv = i.id_inv
        WHERE ir.id_rec = ?
        ORDER BY i.nombre_ing ASC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $id_rec);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ingredientes = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $ingredientes;
}

// =========================================
// FUNCIONES DE RECOMENDACIONES
// =========================================

/**
 * Obtiene productos/recetas parecidas (de la misma categoría)
 */
function obtenerRecetasParecidas($con, $id_cat, $id_producto_actual = null, $limite = 3) {
    if (!tablaExiste($con, 'producto')) {
        return [];
    }
    
    $query = "SELECT * FROM producto WHERE id_cat = ?";
    $params = [$id_cat];
    $tipos = 'i';
    
    if ($id_producto_actual) {
        $query .= " AND id_pro != ?";
        $params[] = $id_producto_actual;
        $tipos .= 'i';
    }
    
    $query .= " LIMIT ?";
    $params[] = $limite;
    $tipos .= 'i';
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, $tipos, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $similares = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $similares;
}

/**
 * Obtiene detalles completos de un producto
 */
function obtenerDetalleProducto($con, $id_pro) {
    if (!tablaExiste($con, 'producto')) {
        return null;
    }
    
    $stmt = mysqli_prepare($con, "
        SELECT p.*, c.nombre_cat
        FROM producto p
        LEFT JOIN categorias c ON p.id_cat = c.id_cat
        WHERE p.id_pro = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, 'i', $id_pro);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $producto = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    return $producto;
}
?>
