<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';

// Obtener ID del producto desde la URL
$id_pro = (int)($_GET['id'] ?? 0);

if ($id_pro <= 0) {
    redirigir('index.php?page=taf2&view=productos');
}

// Obtener detalles del producto
$producto = obtenerDetalleProducto($con, $id_pro);

if (!$producto) {
    redirigir('index.php?page=taf2&view=productos?error=Producto+no+encontrado');
}

// Obtener recetas parecidas
$id_cat = (int)($producto['id_cat'] ?? 0);
$recetas_parecidas = $id_cat > 0 ? obtenerRecetasParecidas($con, $id_cat, $id_pro, 3) : [];

// Obtener ingredientes del producto (si es receta)
$ingredientes = $id_pro > 0 ? obtenerIngredientesReceta($con, $id_pro) : [];
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <a href="index.php?page=taf2&view=productos" style="color: #7b0030; text-decoration: none; font-weight: bold;">
                ← Volver a Productos
            </a>
        </div>
    </section>

    <!-- Detalles del Producto -->
    <section class="taf2-card">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            <!-- Información Principal -->
            <div>
                <h1 style="font-size: 2rem; margin: 0 0 15px 0; color: #212529;">
                    <?= htmlspecialchars($producto['nom_pro']) ?>
                </h1>
                
                <p style="font-size: 1.1rem; color: #666; line-height: 1.6; margin-bottom: 20px;">
                    <?= htmlspecialchars($producto['descripcion'] ?? 'Sin descripción') ?>
                </p>

                <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 8px 0;"><strong>Categoría:</strong> <?= htmlspecialchars($producto['nombre_cat'] ?? 'Sin categoría') ?></p>
                    <p style="margin: 8px 0;"><strong>Precio:</strong> <span style="font-size: 1.3rem; color: #28a745;">$<?= number_format((float)$producto['precio'], 2) ?></span></p>
                    <p style="margin: 8px 0;"><strong>Stock Disponible:</strong> <?= (int)$producto['stock'] ?> unidades</p>
                </div>

                <button style="padding: 12px 30px; background: #7b0030; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1rem;">
                    Agregar al Carrito
                </button>
            </div>

            <!-- Imagen/Información secundaria -->
            <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                <div style="text-align: center; color: #999;">
                    <p style="font-size: 3rem; margin: 0;">🍽️</p>
                    <p>Imagen del producto</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ingredientes -->
    <?php if (!empty($ingredientes)): ?>
    <section class="taf2-card">
        <h2>Ingredientes Utilizados</h2>
        <table class="taf2-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ingrediente</th>
                    <th>Cantidad Necesaria</th>
                    <th>Stock Disponible</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredientes as $ing): ?>
                <tr>
                    <td><?= htmlspecialchars($ing['nombre_ing']) ?></td>
                    <td><?= number_format((float)$ing['cantidad_necesaria'], 2) ?></td>
                    <td>
                        <span style="color: <?= $ing['stock_actual'] < $ing['cantidad_necesaria'] ? '#e74c3c' : '#28a745' ?>; font-weight: bold;">
                            <?= number_format((float)$ing['stock_actual'], 2) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($ing['unidad_medida']) ?></td>
                    <td>
                        <?php if ($ing['stock_actual'] >= $ing['cantidad_necesaria']): ?>
                            <span style="background: #d1e7dd; color: #0f5132; padding: 5px 10px; border-radius: 3px;">✓ Disponible</span>
                        <?php else: ?>
                            <span style="background: #f8d7da; color: #842029; padding: 5px 10px; border-radius: 3px;">✗ Stock bajo</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>

    <!-- Productos Similares -->
    <?php if (!empty($recetas_parecidas)): ?>
    <section class="taf2-card">
        <h2>Productos Similares en la Categoría</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($recetas_parecidas as $similar): ?>
            <a href="index.php?page=taf2&view=detalle&id=<?= (int)$similar['id_pro'] ?>" style="text-decoration: none; color: inherit;">
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s; background: #f9f9f9;"
                     onmouseover="this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'; this.style.background='#fff';"
                     onmouseout="this.style.boxShadow=''; this.style.background='#f9f9f9';">
                    <div style="font-size: 2rem; text-align: center; margin-bottom: 10px;">🍽️</div>
                    <h4 style="margin: 10px 0; font-size: 1rem; color: #212529;">
                        <?= htmlspecialchars($similar['nom_pro']) ?>
                    </h4>
                    <p style="color: #666; font-size: 0.9rem; margin: 8px 0;">
                        <?= htmlspecialchars(substr($similar['descripcion'], 0, 60)) ?>...
                    </p>
                    <p style="color: #28a745; font-weight: bold; margin: 10px 0 0 0;">
                        $<?= number_format((float)$similar['precio'], 2) ?>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Reseñas (Opcional) -->
    <section class="taf2-card">
        <h2>Reseñas</h2>
        <p style="color: #999; font-style: italic;">Las reseñas estarán disponibles próximamente</p>
    </section>
</div>

<style>
.taf2-main section.taf2-card {
    margin-bottom: 30px;
    padding: 25px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.taf2-main h2 {
    color: #212529;
    font-size: 1.5rem;
    margin-bottom: 20px;
    border-bottom: 2px solid #7b0030;
    padding-bottom: 10px;
}

.taf2-main a[href*="volver"] {
    display: inline-block;
    margin-bottom: 20px;
    font-size: 1rem;
}
</style>
