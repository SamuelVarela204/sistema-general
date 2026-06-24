<?php
// app/views/search.php
require_once LAYOUTS_PATH . '/header.php';
$titulo = 'Búsqueda';
$busqueda = $_GET['q'] ?? '';
?>

<main class="spc" style="max-width: 1100px; width: 100%; margin: 0 auto; padding: 20px 16px; position: relative; margin-top: 30px;">
    <h1 style="text-align: center;"><strong>RESULTADOS DE BÚSQUEDA</strong></h1>
    
    <?php if (empty($busqueda)): ?>
        <p style="text-align: center;">No se realizó ninguna búsqueda.</p>
    <?php elseif (empty($productos)): ?>
        <p style="text-align: center;">No se encontraron productos para "<?= htmlspecialchars($busqueda) ?>"</p>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="card">
                    <div class="thumb">
                        <img src="<?= BASE_URL ?>/public/images/placeholder.png" alt="<?= htmlspecialchars($producto['nom_pro']) ?>">
                    </div>
                    <div class="info">
                        <h3><?= htmlspecialchars($producto['nom_pro']) ?></h3>
                        <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                        <span class="tag">$<?= number_format($producto['precio'], 2) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php
require_once VIEW_PATH . '/footer.php';