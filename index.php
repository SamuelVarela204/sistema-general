<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$con = conectarBD();
$page = $_GET['page'] ?? 'home';

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
    case 'logout':
        require_once __DIR__ . '/logout.php';
        break;
    case 'home':
    default:
        $productos = obtenerProductos($con);
        $titulo = 'Inicio';
        ob_start();
?>
        <div class="content">
            <div class="search-container">
                <div class="search-box">
                    <form action="index.php?page=buscar" method="get">
                        <input type="hidden" name="page" value="buscar">
                        <input type="text" name="q" placeholder="Buscar..." class="tab" autocomplete="off">
                        <button class="but" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
            <div class="cards-grid">
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
        </div>
<?php
        $contenido = ob_get_clean();
        require_once __DIR__ . '/includes/layout.php';
        break;
}
?>