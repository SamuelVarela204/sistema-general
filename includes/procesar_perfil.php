<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

if (!estaLogueado()) {
    respuestaJSON(false, 'No estás logueado');
}

$con = conectarBD();
$userEmail = $_SESSION['correo'];
$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

function columnaExiste($con, $columna)
{
    $columna = mysqli_real_escape_string($con, $columna);
    $resultado = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE '" . $columna . "'");
    return $resultado && mysqli_num_rows($resultado) > 0;
}

// Actualizar perfil
if ($accion === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $alergias = trim($_POST['alergias'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($nombre)) {
        respuestaJSON(false, 'El nombre es obligatorio');
    }

    $imagen = null;
    if (!empty($_FILES['imagen']['tmp_name']) && $_FILES['imagen']['error'] == 0) {
        $check = getimagesize($_FILES['imagen']['tmp_name']);
        if ($check !== false) {
            $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        } else {
            respuestaJSON(false, 'El archivo no es una imagen válida');
        }
    }

    $campos = [
        'nom_com' => $nombre,
        'telefono' => $telefono,
    ];

    if (columnaExiste($con, 'direccion')) {
        $campos['direccion'] = $direccion;
    }
    if (columnaExiste($con, 'alergias')) {
        $campos['alergias'] = $alergias;
    }
    if (columnaExiste($con, 'descripcion')) {
        $campos['descripcion'] = $descripcion;
    }
    if ($imagen !== null && columnaExiste($con, 'imagen')) {
        $campos['imagen'] = $imagen;
    }

    $set = [];
    $types = '';
    $values = [];
    foreach ($campos as $nombreCampo => $valorCampo) {
        $set[] = "$nombreCampo = ?";
        $types .= 's';
        $values[] = $valorCampo;
    }

    if (empty($set)) {
        respuestaJSON(false, 'No hay campos disponibles para actualizar');
    }

    $query = 'UPDATE usuarios SET ' . implode(', ', $set) . ' WHERE correo = ?';
    $types .= 's';
    $values[] = $userEmail;

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$values);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['usuario'] = $nombre;
        if ($imagen !== null) {
            $_SESSION['imagen'] = $imagen;
        }
        if (!empty($descripcion)) {
            $_SESSION['descripcion'] = $descripcion;
        }
        mysqli_stmt_close($stmt);
        respuestaJSON(true, 'Perfil actualizado correctamente');
    }

    respuestaJSON(false, 'Error al actualizar el perfil');
}

// Eliminar perfil
if ($accion === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = mysqli_prepare($con, 'DELETE FROM usuarios WHERE correo = ?');
    mysqli_stmt_bind_param($stmt, 's', $userEmail);

    if (mysqli_stmt_execute($stmt)) {
        session_destroy();
        mysqli_stmt_close($stmt);
        respuestaJSON(true, 'Perfil eliminado correctamente');
    }

    respuestaJSON(false, 'Error al eliminar el perfil');
}

if ($accion === 'actualizar_alergias' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $alergias = trim($_POST['alergias'] ?? '');
    $check = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'alergias'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "ALTER TABLE usuarios ADD COLUMN alergias TEXT DEFAULT NULL");
    }
    $stmt = mysqli_prepare($con, 'UPDATE usuarios SET alergias = ? WHERE correo = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $alergias, $userEmail);
    if (mysqli_stmt_execute($stmt)) {
        respuestaJSON(true, 'Alergias guardadas correctamente');
    } else {
        respuestaJSON(false, 'Error al guardar alergias');
    }
}

if ($accion === 'actualizar_notificaciones' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificaciones = (int)($_POST['notificaciones'] ?? 0);
    // Asegurarse de que la columna exista (si no, crear)
    $check = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'notificaciones'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "ALTER TABLE usuarios ADD COLUMN notificaciones TINYINT(1) DEFAULT 0");
    }
    $stmt = mysqli_prepare($con, 'UPDATE usuarios SET notificaciones = ? WHERE correo = ?');
    mysqli_stmt_bind_param($stmt, 'is', $notificaciones, $userEmail);
    if (mysqli_stmt_execute($stmt)) {
        respuestaJSON(true, 'Preferencia de notificaciones actualizada');
    } else {
        respuestaJSON(false, 'Error al actualizar');
    }
}

respuestaJSON(false, 'Acción no válida');