<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function(){
            try{
                var k = 'app_grayscale_mode';
                if (localStorage.getItem(k) === 'true') {
                    document.documentElement.classList.add('bw-mode');
                }
            } catch(e) { /* ignore */ }
        })();
    </script>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/taf2.css">
    <link rel="icon" href="public/images/placeholder.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title><?= isset($titulo) ? htmlspecialchars($titulo) . ' - Tropical & Fresh' : 'Tropical & Fresh' ?></title>
</head>

<body>
<?php
    $descripcionPerfil = '';
    if (estaLogueado() && !empty($_SESSION['correo'])) {
        require_once __DIR__ . '/../config/db.php';
        $con = conectarBD();
        $descripcionPerfil = obtenerDescripcionUsuario($con, $_SESSION['correo']);
        mysqli_close($con);
        if (empty($descripcionPerfil) && !empty($_SESSION['descripcion'])) {
            $descripcionPerfil = $_SESSION['descripcion'];
        }
    }
    ?>

    <!-- Sidebar (solo para logueados) -->
    <?php if (estaLogueado()): ?>
        <?php
            $rolSidebar = strtolower((string)($_SESSION['usuario_rol'] ?? ($_SESSION['rol_id'] ?? 'cliente')));
            $sidebarActions = [
                ['label' => 'Perfil', 'href' => 'index.php?page=perfil'],
                ['label' => 'Pedidos', 'href' => 'index.php?page=pedidos'],
                ['label' => 'Recetas', 'href' => 'index.php?page=recetas'],
                ['label' => 'Ajustes', 'href' => 'index.php?page=ajustes'],
            ];

            if ($rolSidebar === 'admin') {
                $sidebarActions = array_merge($sidebarActions, [
                    ['label' => 'Panel TAF2', 'href' => 'index.php?page=taf2'],
                    ['label' => 'Usuarios', 'href' => 'index.php?page=taf2&view=usuarios'],
                    ['label' => 'Productos', 'href' => 'index.php?page=taf2&view=productos'],
                    ['label' => 'Pedidos TAF2', 'href' => 'index.php?page=taf2&view=pedidos'],
                ]);
            } elseif (in_array($rolSidebar, ['inventario', 'gerente'], true)) {
                $sidebarActions = array_merge($sidebarActions, [
                    ['label' => 'Panel TAF2', 'href' => 'index.php?page=taf2'],
                    ['label' => 'Productos', 'href' => 'index.php?page=taf2&view=productos'],
                    ['label' => 'Pedidos TAF2', 'href' => 'index.php?page=taf2&view=pedidos'],
                ]);
            }
        ?>
        <div class="notification-widget" id="notificationWidget">
            <button id="notificationBell" class="notification-bell" aria-label="Abrir notificaciones">
                <span class="bell-icon">🔔</span>
                <span id="notificationCount" class="notification-count">0</span>
            </button>
            <div id="notificationPanel" class="notification-panel hidden" aria-hidden="true">
                <div class="notification-panel-header">
                    <span>Notificaciones</span>
                    <button id="notificationClose" class="notification-close" aria-label="Cerrar">×</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <div class="notification-item empty">No hay notificaciones nuevas.</div>
                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <a href="index.php" class="sidebar-back-button" title="Volver al inicio">←</a>
            <div class="profile-center">
                <?php if (!empty($_SESSION['imagen'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($_SESSION['imagen']) ?>" alt="Perfil">
                <?php else: ?>
                    <img src="public/images/placeholder.png" alt="Perfil">
                <?php endif; ?>
            </div>
            <h1 style="text-align: center; margin-bottom: 6px;"><?= htmlspecialchars($_SESSION['usuario']) ?></h1>
            <p style="text-align: center; margin: 0 0 12px; color: #d8d8d8; font-size: 0.95rem;"><?= htmlspecialchars($rolSidebar === 'admin' ? 'Aplicación admin' : 'Aplicación cliente') ?></p>
            <h1 style="text-align: center; margin-top: 0; margin-bottom: 18px; font-size: 1.05rem; color: #7e7e7e;"> <?= htmlspecialchars($descripcionPerfil ?: 'Perfil sin descripción') ?></h1>
            <nav class="sidebar-menu">
                <?php foreach ($sidebarActions as $action): ?>
                    <a href="<?= htmlspecialchars($action['href']) ?>" class="menu-item"><?= htmlspecialchars($action['label']) ?></a>
                <?php endforeach; ?>
            </nav>
            <nav class="sidebar-menu-bottom">
                <a href="index.php?page=logout" class="menu-item logout">Cerrar sesión</a>
            </nav>
        </div>
        <div id="hoverHolder" title="Abrir menú"></div>
    <?php endif; ?>

    <!-- Contenido específico de cada página -->
    <?= $contenido ?? '' ?>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <p><a href="views/siscop.php" class="footer-link">&copy;</a>Samuel Varela y Diego Garcia — Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>
<script>
(function() {
    // Aplicar apariencia adicional
    const fontSize = localStorage.getItem('fontSize');
    if (fontSize === 'small') document.documentElement.classList.add('font-small');
    if (fontSize === 'large') document.documentElement.classList.add('font-large');
    if (localStorage.getItem('highContrast') === 'true') document.documentElement.classList.add('high-contrast');
    if (localStorage.getItem('noAnimations') === 'true') document.documentElement.classList.add('no-animations');
})();
</script>
</html>