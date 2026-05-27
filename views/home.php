<?php
$con = conectarBD();
$productos = obtenerProductos($con);
?>
<div class="página-completa">
    <div class="marca">
        <div class="logo-circle">
            <span>T&F</span>
        </div>
        <h2>Tropical y fresco</h2>
        <p>Sabores naturales que transforman tu día</p>
    </div>

    <div class="contenido-principal">
        <div class="columna-de-acción">
            <h3 style="text-align: center;">¿Qué buscas hoy?</h3>
            <div style="text-align: center; margin-bottom: 10px;">Encuentra tu bebida favorita</div>

            <div class="search-wrapper">
                <form action="index.php?page=buscar" method="get">
                    <input type="hidden" name="page" value="buscar">
                    <input type="text" name="q" placeholder="Buscar..." autocomplete="off">
                    <button type="submit">Buscar producto</button>
                </form>
            </div>

            <div>
                <a href="index.php?page=login">Iniciar sesión</a>
                <a href="index.php?page=register">Crear Cuenta</a>
            </div>
        </div>
    </div>
</div>
</div>

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