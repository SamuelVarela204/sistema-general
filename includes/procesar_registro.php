<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regi'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['correo'] ?? '');
    $password = $_POST['contrasena'] ?? '';
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
    if (!empty($_FILES['profile-pic']['tmp_name'])) {
        $imagen = file_get_contents($_FILES['profile-pic']['tmp_name']);
    }

    // Hashear contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (nom_com, correo, usu_con, imagen) VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'ssss', $nombre, $email, $passwordHash, $imagen);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['usuario'] = $nombre;
        $_SESSION['correo'] = $email;
        $_SESSION['imagen'] = $imagen;
        redirigir('index.php?page=perfil');
    } else {
        redirigir('index.php?page=register&error=register_failed');
    }

    mysqli_stmt_close($stmt);
} else {
    redirigir('index.php?page=register');
}

mysqli_close($con);
