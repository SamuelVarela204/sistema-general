<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';
require_once __DIR__ . '/../../includes/functions.php';

$datos = obtenerDatosTaf2($con);
$productos = $datos['productos'];
$usuarios = $datos['usuarios'];
$pedidos = $datos['pedidos'];
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <h1>Panel TAF2</h1>
            <p>Bienvenido al panel administrativo integrado. Aquí puedes ver el estado de pedidos, usuarios y productos en un solo lugar.</p>
        </div>
        <div>
            <div class="taf2-avatar">
                <?php if (!empty($_SESSION['imagen'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($_SESSION['imagen']) ?>" alt="Avatar">
                <?php else: ?>
                    <div class="taf2-avatar-fallback"><?= strtoupper(substr($_SESSION['usuario'] ?? 'U', 0, 1)) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="taf2-grid">
        <article class="taf2-card">
            <h3>Productos</h3>
            <p><?= count($productos) ?> registrados</p>
            <a href="index.php?page=taf2&view=productos" class="taf2-btn">Ver productos</a>
        </article>
        <article class="taf2-card">
            <h3>Usuarios</h3>
            <p><?= count($usuarios) ?> usuarios</p>
            <a href="index.php?page=taf2&view=usuarios" class="taf2-btn">Ver usuarios</a>
        </article>
        <article class="taf2-card">
            <h3>Pedidos</h3>
            <p><?= count($pedidos) ?> pedidos</p>
            <a href="index.php?page=taf2&view=pedidos" class="taf2-btn">Ver pedidos</a>
        </article>
    </section>
</div>
