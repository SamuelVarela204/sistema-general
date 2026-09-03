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

    <?php
        // Obtener fondo global desde la base de datos (glob_wall MEDIUMBLOB)
        $bgDataUrl = null;
        try {
            require_once __DIR__ . '/../config/db.php';
            require_once __DIR__ . '/functions.php';
            $conBg = conectarBD();
            $bg = getGlobalBackground($conBg);
            if ($bg && !empty($bg['blob']) && !empty($bg['mime'])) {
                $bgDataUrl = 'data:' . htmlspecialchars($bg['mime'], ENT_QUOTES, 'UTF-8') . ';base64,' . base64_encode($bg['blob']);
            }
            mysqli_close($conBg);
        } catch (Exception $e) {
            $bgDataUrl = null;
        }
    ?>

    <title><?= isset($titulo) ? htmlspecialchars($titulo) . ' - Tropical & Fresh' : 'Tropical & Fresh' ?></title>
    <?php if ($bgDataUrl): ?>
        <style>
            body {
                background-image: url('<?= $bgDataUrl ?>');
                background-size: cover;
                background-position: center center;
                background-attachment: fixed;
            }
        </style>
    <?php endif; ?>
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
            <p style="text-align: center; margin: 0 0 12px; color: #d8d8d8; font-size: 0.95rem;"><?= htmlspecialchars($rolSidebar === 'admin' ? 'Admin - Acceso completo' : 'Cliente') ?></p>
            <h1 style="text-align: center; margin-top: 0; margin-bottom: 18px; font-size: 1.05rem; color: #7e7e7e;"> <?= htmlspecialchars($descripcionPerfil ?: 'Sin descripción') ?></h1>
            
            <nav class="sidebar-menu">
                <!-- Sección común para todos -->
                <div class="menu-section">
                    <span class="section-label">Perfil</span>
                    <a href="index.php?page=perfil" class="menu-item">👤 Perfil</a>
                    <a href="index.php?page=taf2&view=alergias" class="menu-item">⚠️ Mis alergias</a>
                    <a href="index.php?page=ajustes" class="menu-item">⚙️ Ajustes</a>
                </div>

                <!-- Sección de órdenes para todos -->
                <div class="menu-section">
                    <span class="section-label">Compras</span>
                    <a href="index.php?page=pedidos" class="menu-item">📋 Mis pedidos</a>
                    <a href="index.php?page=recetas" class="menu-item">📚 Recetas</a>
                </div>

                <!-- Sección admin -->
                <?php if ($rolSidebar === 'admin'): ?>
                    <div class="menu-section">
                        <span class="section-label">Administración</span>
                        <a href="index.php?page=taf2" class="menu-item">📊 Panel TAF2</a>
                        <a href="index.php?page=taf2&view=usuarios" class="menu-item">👥 Usuarios</a>
                        <a href="index.php?page=taf2&view=productos" class="menu-item">📦 Productos</a>
                        <a href="index.php?page=taf2&view=pedidos" class="menu-item">📈 Todas las órdenes</a>
                    </div>
                <?php elseif (in_array($rolSidebar, ['inventario', 'gerente'], true)): ?>
                    <div class="menu-section">
                        <span class="section-label">Operaciones</span>
                        <a href="index.php?page=taf2" class="menu-item">📊 Panel TAF2</a>
                        <a href="index.php?page=taf2&view=productos" class="menu-item">📦 Productos</a>
                        <a href="index.php?page=taf2&view=pedidos" class="menu-item">📈 Órdenes</a>
                    </div>
                <?php endif; ?>
            </nav>

            <nav class="sidebar-menu-bottom">
                <a href="index.php?page=logout" class="menu-item logout">Cerrar sesión</a>
            </nav>
        </div>
        <div id="hoverHolder" title="Abrir menú"></div>
    <?php endif; ?>

    <!-- Contenido específico de cada página -->
    <div class="main-content">
        <?= $contenido ?? '' ?>
    </div>

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
<?php if (isset($_GET['error']) || isset($_GET['status'])): ?>
<script>
    (function(){
        let message = '';
        let type = 'error';
        const code = '<?= isset($_GET['status']) ? htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>';
        switch (code) {
            case 'usuario_activo_no_borrable':
                message = 'Usuario activo no borrable.';
                type = 'warning';
                break;
            case 'background_set':
                message = 'Fondo actualizado correctamente.';
                type = 'success';
                break;
            case 'background_deleted':
                message = 'Fondo eliminado correctamente.';
                type = 'success';
                break;
            case 'background_delete_fail':
                message = 'No se pudo eliminar el fondo. Intenta de nuevo.';
                break;
            case 'background_invalid':
                message = 'Archivo no válido. Sube una imagen JPG, PNG o WEBP.';
                type = 'warning';
                break;
            case 'background_upload_fail':
                message = 'Error al subir el archivo. Intenta de nuevo.';
                break;
            case 'background_missing':
                message = 'No se seleccionó ningún archivo.';
                type = 'warning';
                break;
            case 'user_not_found':
                message = 'Usuario no encontrado.';
                break;
            case 'wrong_password':
                message = 'Contraseña incorrecta.';
                break;
            case 'empty_fields':
                message = 'Correo y contraseña son obligatorios.';
                type = 'warning';
                break;
            case 'password_reset':
                message = 'Contraseña restablecida con éxito. Inicia sesión.';
                type = 'success';
                break;
            default:
                message = 'Error desconocido.';
        }
        Swal.fire({
            icon: type,
            title: message,
            confirmButtonText: 'Aceptar'
        });
    })();
</script>
<?php endif; ?>
</html>