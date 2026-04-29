<?php
// Este archivo envuelve todas las páginas
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/sty.css">
    <link rel="icon" href="public/images/placeholder.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title><?= $titulo ?? 'TAF - Bebidas' ?></title>
</head>

<body>

    <!-- Botones de sesión -->
    <div class="corner-buttons">
        <?php if (!estaLogueado()): ?>
            <a href="index.php?page=login"><button type="button" class="regINI-buttons">Iniciar sesión</button></a>
            <a href="index.php?page=register"><button type="button" class="regINI-buttons">Registro</button></a>
        <?php else: ?>
            <form action="index.php?page=logout" method="post">
                <button type="submit" class="regINI-buttons">Cerrar sesión</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Sidebar (solo para logueados) -->
    <?php if (estaLogueado()): ?>
        <div class="sidebar" id="sidebar">
            <div class="profile-center">
                <?php if (!empty($_SESSION['imagen'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($_SESSION['imagen']) ?>" alt="Perfil">
                <?php else: ?>
                    <img src="public/images/placeholder.jfif" alt="Perfil">
                <?php endif; ?>
            </div>
            <h2 style="text-align: center;"><?= htmlspecialchars($_SESSION['usuario']) ?></h2>
            <nav class="sidebar-menu">
                <a href="index.php?page=perfil" class="menu-item">Perfil</a>
                <a href="index.php?page=pedidos" class="menu-item">Pedidos</a>
                <a href="index.php?page=recetas" class="menu-item">Recetas</a>
                <a href="index.php?page=ajustes" class="menu-item">Ajustes</a>
            </nav>
        </div>
        <div id="hoverHolder" title="Abrir menú"></div>
    <?php endif; ?>

    <!-- Contenido específico de cada página -->
    <?= $contenido ?>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <p><a href="index.php?page=copyright" class="footer-link">&copy;</a>2025 Samuel Varela — Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>

</html>