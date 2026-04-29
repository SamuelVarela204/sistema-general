<?php
function obtenerProductos($con, $busqueda = '') {
    if ($busqueda) {
        $busqueda = "%$busqueda%";
        $stmt = mysqli_prepare($con, "SELECT id_pro, nom_pro, descripcion, precio FROM producto WHERE nom_pro LIKE ? OR descripcion LIKE ? LIMIT 20");
        mysqli_stmt_bind_param($stmt, 'ss', $busqueda, $busqueda);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($con, "SELECT id_pro, nom_pro, descripcion, precio FROM producto LIMIT 20");
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function estaLogueado() {
    return isset($_SESSION['usuario']);
}

function redirigir($url) {
    header("Location: $url");
    exit;
}
?>