<?php
// app/views/layouts/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Sistema General' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/try.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/ajustes.css">

</head>
<body class="<?= isset($_COOKIE['bw-mode']) && $_COOKIE['bw-mode'] === 'true' ? 'bw-mode' : '' ?>">
    <div class="sidebar">
        <nav class="sidebar-menu-top">
            <a href="<?= BASE_URL ?>/" class="menu-item">Inicio</a>
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="<?= BASE_URL ?>/perfil" class="menu-item">Perfil</a>
                <a href="<?= BASE_URL ?>/ajustes" class="menu-item">Ajustes</a>
            <?php endif; ?>
        </nav>
        <nav class="sidebar-menu-bottom">
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="<?= BASE_URL ?>/logout" class="menu-item logout">Cerrar sesión</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="menu-item">Iniciar sesión</a>
            <?php endif; ?>
        </nav>
    </div>
    <div id="hoverHolder" title="Abrir menú"></div>