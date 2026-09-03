<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';
$datos = obtenerDatosTaf2($con);
$productos = $datos['productos'];
$categorias = $datos['categorias'] ?? [];
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <h1>Productos</h1>
            <p>Administra tu catálogo con facilidad. Agrega nuevos artículos y visualiza el inventario disponible.</p>
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

    <?php
    $editarProducto = null;
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar_producto') {
        $idEdicion = (int)$_GET['id'];
        if ($idEdicion > 0) {
            $stmt = mysqli_prepare($con, 'SELECT id_pro, nom_pro, descripcion, precio, stock, categoria FROM producto WHERE id_pro = ? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'i', $idEdicion);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editarProducto = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($stmt);
        }
    }
    ?>

    <?php if ($editarProducto): ?>
        <section class="taf2-card">
            <h3>Editar producto #<?= (int)$editarProducto['id_pro'] ?></h3>
            <form action="index.php?page=taf2&view=productos&action=editar_producto" method="POST" class="taf2-form">
                <input type="hidden" name="id_pro" value="<?= (int)$editarProducto['id_pro'] ?>">
                <input type="text" name="nom_pro" value="<?= htmlspecialchars($editarProducto['nom_pro']) ?>" placeholder="Nombre del producto" required>
                <input type="text" name="descripcion" value="<?= htmlspecialchars($editarProducto['descripcion']) ?>" placeholder="Descripción">
                <input type="number" step="0.01" name="precio" value="<?= number_format((float)$editarProducto['precio'], 2, '.', '') ?>" placeholder="Precio" required>
                <input type="number" name="stock" value="<?= (int)$editarProducto['stock'] ?>" placeholder="Stock" required>
                <?php if (!empty($categorias)): ?>
                    <select name="id_cat" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= (int)$cat['id_cat'] ?>"><?= htmlspecialchars($cat['nombre_cat']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($editarProducto['categoria'] ?? '') ?>" placeholder="Categoría" required>
                <?php endif; ?>
                <button type="submit" class="taf2-btn">Actualizar producto</button>
                <a href="index.php?page=taf2&view=productos" class="taf2-btn" style="background:#7b0030;">Cancelar</a>
            </form>
        </section>
    <?php else: ?>

    <section class="taf2-card">
        <form action="index.php?page=taf2&action=nuevo_producto" method="POST" class="taf2-form">
            <input type="text" name="nom_pro" placeholder="Nombre del producto" required>
            <input type="text" name="descripcion" placeholder="Descripción">
            <input type="number" step="0.01" name="precio" placeholder="Precio" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <?php if (!empty($categorias)): ?>
                <select name="id_cat" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['id_cat'] ?>"><?= htmlspecialchars($cat['nombre_cat']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="text" name="categoria" placeholder="Categoría" required>
            <?php endif; ?>
            <button type="submit">Guardar</button>
        </form>
        <table class="taf2-table">
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= (int)$producto['id_pro'] ?></td>
                        <td><?= htmlspecialchars($producto['nom_pro']) ?></td>
                        <td>$<?= number_format((float)$producto['precio'], 2) ?></td>
                        <td><?= (int)$producto['stock'] ?></td>
                        <td>
                            <a href="index.php?page=taf2&view=detalle&id=<?= (int)$producto['id_pro'] ?>" class="taf2-btn" style="padding:8px 12px;font-size:0.9rem; background: #007bff; margin-right: 5px;">Ver Detalles</a>
                            <a href="index.php?page=taf2&view=productos&action=editar_producto&id=<?= (int)$producto['id_pro'] ?>" class="taf2-btn" style="padding:8px 12px;font-size:0.9rem;">Editar</a>
                            <form action="index.php?page=taf2&view=productos&action=eliminar_producto" method="POST" style="display:inline-block; margin-left:8px;">
                                <input type="hidden" name="id_pro" value="<?= (int)$producto['id_pro'] ?>">
                                <button type="submit" class="taf2-btn" style="background:#e74c3c;">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <?php endif; ?>
</div>
