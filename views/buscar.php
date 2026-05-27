<?php
if (!isset($con)) {
    $con = conectarBD();
}

$titulo = 'Búsqueda';
$busqueda = $_GET['q'] ?? '';
$productos = [];

if (!empty($busqueda)) {
    $productos = obtenerProductos($con, $busqueda);
}

?>
<div class="content">
    <div class="search-container">
        <div class="search-box">
            <form action="index.php?page=buscar" method="get">
                <input type="hidden" name="page" value="buscar">
                <input type="text" name="q" placeholder="Buscar..." class="tab" autocomplete="off" value="<?= htmlspecialchars($busqueda) ?>">
                <button class="but" type="submit">Buscar</button>
            </form>
        </div>
    </div>

    <?php if (!empty($busqueda)): ?>
        <h2>Resultados para: <strong><?= htmlspecialchars($busqueda) ?></strong></h2>

        <?php if (!empty($productos)): ?>
            <div class="cards-grid">
                <?php foreach ($productos as $prod): ?>
                    <div class="card">
                        <div class="thumb">
                            <img src="public/images/placeholder.png" alt="<?= htmlspecialchars($prod['nom_pro']) ?>">
                        </div>
                        <div class="info">
                            <h3><?= htmlspecialchars($prod['nom_pro']) ?></h3>
                            <p><?= htmlspecialchars($prod['descripcion']) ?></p>
                            <span class="tag">$<?= number_format($prod['precio'], 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 40px; font-size: 18px;">No se encontraron productos.</p>
        <?php endif; ?>
    <?php else: ?>
        <p style="text-align: center; padding: 40px; font-size: 18px;">Ingresa un término de búsqueda.</p>
    <?php endif; ?>
</div>
