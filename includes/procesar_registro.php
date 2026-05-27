<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regi'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['correo'] ?? '');
    $password = $_POST['contrasena'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = null;

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        redirigir('index.php?page=register&error=empty_fields');
    }

    if (!esEmailValido($email)) {
        redirigir('index.php?page=register&error=invalid_email');
    }

    if (strlen($password) < 6) {
        redirigir('index.php?page=register&error=weak_password');
    }

    // Verificar si el email ya existe
    $stmt = mysqli_prepare($con, 'SELECT id_usu FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        redirigir('index.php?page=register&error=email_exists');
    }
    mysqli_stmt_close($stmt);

    // Procesar imagen si existe
    if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile-pic']['error'] !== UPLOAD_ERR_OK) {
            redirigir('index.php?page=register&error=upload_failed');
        }

        $check = getimagesize($_FILES['profile-pic']['tmp_name']);
        if ($check === false) {
            redirigir('index.php?page=register&error=invalid_image');
        }

        $imagen = file_get_contents($_FILES['profile-pic']['tmp_name']);
    }

    // Establecer descripción por defecto si está vacía
    if (empty($descripcion)) {
        $descripcion = 'perfil sin descripcion';
    }

    // Hashear contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if ($imagen !== null) {
        $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (nom_com, correo, usu_con, descripcion, imagen) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssss', $nombre, $email, $passwordHash, $descripcion, $imagen);
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (nom_com, correo, usu_con, descripcion) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssss', $nombre, $email, $passwordHash, $descripcion);
    }

    if ($stmt && mysqli_stmt_execute($stmt)) {
        $_SESSION['usuario'] = $nombre;
        $_SESSION['correo'] = $email;
        $_SESSION['descripcion'] = $descripcion;
        if ($imagen !== null) {
            $_SESSION['imagen'] = $imagen;
        }
        mysqli_stmt_close($stmt);
        redirigir('index.php');
    } else {
        redirigir('index.php?page=register&error=register_failed');
    }

    mysqli_stmt_close($stmt);
} else {
    redirigir('index.php?page=register');
}

mysqli_close($con);
