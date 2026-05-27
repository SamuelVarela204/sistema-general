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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - Sistema General</title>
    <link rel="stylesheet" type="text/css" href="public/css/try.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
<div class="content">
    <section class="search-page">
        <div class="search-hero">
            <div class="hero-text">
                <span>Encuentra tu sabor</span>
                <h1>Busca recetas</h1>
                <p>Explora jugos, postres y platos frescos.</p>
            </div>
            <div class="search-panel">
                <form action="index.php?page=buscar" method="get" class="search-form">
                    <input type="hidden" name="page" value="buscar">
                    <label class="search-field">
                        <input type="text" name="q" placeholder="Buscar productos, bebidas o postres..." autocomplete="off" value="<?= htmlspecialchars($busqueda) ?>">
                    </label>
                    <button class="submit-btn" type="submit" style=" height: 54px; width: 170px;">Buscar</button>
                </form>
            </div>
        </div>

        <?php if (!empty($busqueda)): ?>
            <div class="results-meta">
                <p class="results-title">Resultados para: <strong><?= htmlspecialchars($busqueda) ?></strong></p>
                <span class="results-badge"><?= count($productos) ?> producto<?= count($productos) === 1 ? '' : 's' ?></span>
            </div>

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
                <div class="empty-state">No se encontraron productos para esta búsqueda <strong><?= htmlspecialchars($busqueda) ?></strong>. Intenta con otra palabra clave o consulta el nombre completo del producto.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">Escribe algo en el buscador para ver resultados rápidos y detallados.</div>
        <?php endif; ?>
    </section>
</div>
</body>
</html>