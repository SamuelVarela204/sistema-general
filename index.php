<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$con = conectarBD();
$page = $_GET['page'] ?? 'home';

// Manejar logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

switch ($page) {
    case 'login':
        require_once __DIR__ . '/views/login.php';
        break;
    case 'register':
        require_once __DIR__ . '/views/register.php';
        break;
    case 'perfil':
        require_once __DIR__ . '/views/perfil.php';
        break;
    case 'buscar':
        require_once __DIR__ . '/views/buscar.php';
        break;
    case 'home':
    default:
        $productos = obtenerProductos($con);
        $titulo = 'Inicio';
        ob_start();
?>

<div class="página-completa">
    <!-- Logo y marca -->
    <div class="marca">
        <div class="logo-circle">
            <span>T&F</span>
        </div>
        <h2>Tropical y fresco</h2>
        <p>Sabores naturales que transforman tu día</p>
    </div>

    <div class="contenido-principal">
        <div class="columna-de-acción">
            <h3>¿Qué buscas hoy?</h3>
            <div class="subtítulo">Encuentra tu bebida favorita</div>

            <div class="search-wrapper">
                <form action="index.php?page=buscar" method="get">
                    <input type="hidden" name="page" value="buscar">
                    <input type="text" name="q" placeholder="Buscar..." autocomplete="off">
                    <button type="submit">Buscar producto</button>
                </form>
            </div>

            <div class="botones-dobles">
                <a href="index.php?page=login" class="btn-login">Iniciar sesión</a>
                <a href="index.php?page=register" class="btn-register">Crear Cuenta</a>
            </div>
        </div>
    </div>
</div>

<div class="cards-grid" style="max-width: 1120px; margin: 0 auto;">
        <?php foreach ($productos as $prod): ?>
            <div class="card">
                <div class="thumb">
                    <img src="public/images/placeholder.jfif" alt="<?= htmlspecialchars($prod['nom_pro']) ?>">
                </div>
                <div class="info">
                    <h3><?= htmlspecialchars($prod['nom_pro']) ?></h3>
                    <p><?= htmlspecialchars($prod['descripcion']) ?></p>
                    <span class="tag">$<?= number_format($prod['precio'], 2) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php
        $contenido = ob_get_clean();
        require_once __DIR__ . '/includes/layout.php';
        break;
}
?>