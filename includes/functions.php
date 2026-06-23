<?php
function obtenerProductos($con, $busqueda = '')
{
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
    // Si ya es absoluta (empieza con /) o URL completa, usar tal cual
    if (strpos($url, '/') === 0 || preg_match('#^[a-zA-Z][a-zA-Z0-9+.-]*://#', $url)) {
        header("Location: $url");
        exit;
    }
    
    // Calcular la ruta URL base del proyecto una sola vez
    static $baseUrl = null;
    if ($baseUrl === null) {
        $projectRoot = dirname(__DIR__); // Sube desde includes/ a la raíz
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $basePath = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
        $baseUrl = rtrim($basePath, '/');
    }
    
    $url = $baseUrl . '/' . ltrim($url, '/');
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