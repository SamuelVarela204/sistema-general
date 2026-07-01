<?php

/**
 * Comprobar si una tabla existe en la base de datos actual.
 */
function tablaExiste($con, $nombreTabla)
{
    $nombreTabla = mysqli_real_escape_string($con, $nombreTabla);
    $resultado = mysqli_query($con, "SHOW TABLES LIKE '$nombreTabla'");
    return $resultado && mysqli_num_rows($resultado) > 0;
}

/**
 * Crear la tabla de productos si aún no existe.
 */
function asegurarTablaProductos($con)
{
    if (tablaExiste($con, 'producto')) {
        return true;
    }

    $sql = "
        CREATE TABLE IF NOT EXISTS producto (
            id_pro INT NOT NULL AUTO_INCREMENT,
            nom_pro VARCHAR(225) NOT NULL,
            descripcion VARCHAR(100) DEFAULT NULL,
            precio DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            categoria VARCHAR(100) NOT NULL DEFAULT 'General',
            PRIMARY KEY (id_pro)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    return mysqli_query($con, $sql);
}

/**
 * Obtener productos de la BD con búsqueda opcional
 */
function obtenerProductos($con, $busqueda = '')
{
    if (!asegurarTablaProductos($con)) {
        return [];
    }

    if ($busqueda) {
        $busqueda = "%$busqueda%";
        $stmt = mysqli_prepare($con, "SELECT id_pro, nom_pro, descripcion, precio FROM producto WHERE nom_pro LIKE ? OR descripcion LIKE ? LIMIT 20");
        mysqli_stmt_bind_param($stmt, 'ss', $busqueda, $busqueda);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $productos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $productos;
    } else {
        $result = mysqli_query($con, "SELECT id_pro, nom_pro, descripcion, precio FROM producto LIMIT 20");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

/**
 * Obtener descripción de perfil de un usuario
 */
function obtenerDescripcionUsuario($con, $correo)
{
    $stmt = mysqli_prepare($con, 'SELECT descripcion FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $correo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $descripcion = '';
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $descripcion = $row['descripcion'] ?? '';
    }
    mysqli_stmt_close($stmt);
    return $descripcion;
}

/**
 * Verificar si el usuario está logueado
 */
function estaLogueado()
{
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

/**
 * Redirigir a una URL
 */
function redirigir($url)
{
    if (strpos($url, '/') !== 0 && !preg_match('#^[a-zA-Z][a-zA-Z0-9+.-]*://#', $url)) {
        // Usar BASE_URL definido en config
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $url = $baseUrl . '/' . ltrim($url, '/');
    }

    header("Location: $url");
    exit;
}

/**
 * Sanitizar HTML para mostrar
 */
function sanitizar($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function esEmailValido($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generar respuesta JSON
 */
function respuestaJSON($exito, $mensaje, $datos = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'datos' => $datos
    ]);
    exit;
}
