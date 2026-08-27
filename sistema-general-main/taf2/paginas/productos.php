<?php
require_once '../controlador/controlador.php';

// Consulta para obtener los productos registrados
$productos = $pdo->query("SELECT * FROM producto ORDER BY id_pro DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos - TAF2</title>
    <link rel="stylesheet" href="/taf2/css/style.css">
    <link rel="stylesheet" href="/taf2/css/sidebar.css">
</head>
<body>
<div class="main-wrapper">
    
    <?php include '../sidebar.php'; ?>

    <div class="content-container">
        <nav class="top-navbar">
            <button class="btn-toggle" id="sidebarToggle">☰ Alternar Menú</button>
            <span class="navbar-project-title">Módulo Ventas / Productos</span>
        </nav>

        <div class="page-content">
            <h1 class="main-page-title">Lista de Productos</h1>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert-banner alert-success" style="padding: 12px; background-color: #d1e7dd; color: #0f5132; border-radius: 5px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-banner alert-danger" style="padding: 12px; background-color: #f8d7da; color: #842029; border-radius: 5px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                
                <div class="grid-column-form" style="flex: 1; min-width: 320px;">
                    <div class="custom-card">
                        <div class="card-header-dark" style="background-color: #212529; color: white; padding: 15px; font-weight: bold; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            Registrar Nuevo Producto
                        </div>
                        <div class="card-body">
                            <form action="../procesar.php" method="POST">
                                <input type="hidden" name="action" value="nuevo_producto">

                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre del Producto</label>
                                    <input type="text" name="nom_pro" class="form-input" required placeholder="Ej. Jugo de Naranja Natural" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                </div>

                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Descripción</label>
                                    <input type="text" name="descripcion" class="form-input" placeholder="Ej. Botella de 500ml" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                </div>

                                <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <div class="form-col" style="flex: 1;">
                                        <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Precio ($)</label>
                                        <input type="number" step="0.01" name="precio" class="form-input" required placeholder="5500.00" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                    </div>
                                    <div class="form-col" style="flex: 1;">
                                        <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Stock Inicial</label>
                                        <input type="number" name="stock" class="form-input" required placeholder="24" min="0" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Categoría</label>
                                    <input type="text" name="categoria" class="form-input" required placeholder="Bebidas" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                </div>

                                <button type="submit" class="btn-submit-dark" style="width: 100%; padding: 10px; background-color: #212529; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Guardar Producto</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="grid-column-table" style="flex: 2; min-width: 500px;">
                    <div class="custom-card">
                        <div class="card-header-green">Productos Registrados</div>
                        <div class="card-body">
                            <?php if (empty($productos)): ?>
                                <p class="no-data-text">No hay productos registrados todavía.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="custom-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                                <th>Categoría</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos as $p): ?>
                                                <tr>
                                                    <td><strong>#<?= (int)$p['id_pro'] ?></strong></td>
                                                    <td class="text-nowrap"><?= htmlspecialchars($p['nom_pro'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($p['descripcion'] ?? '') ?></td>
                                                    <td class="table-total-column">$<?= number_format((float)($p['precio'] ?? 0), 2) ?></td>
                                                    <td><?= (int)($p['stock'] ?? 0) ?></td>
                                                    <td><span class="custom-badge badge-warning"><?= htmlspecialchars($p['categoria'] ?? '') ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div> </div> </div> </div> <script>
// Manejo del botón de alternar menú (Sidebar Toggle)
document.getElementById('sidebarToggle').addEventListener('click', function(e) {
    e.preventDefault();
    document.body.classList.toggle('sidebar-hidden');
});
</script>
</body>
</html>