<?php
require_once '../controlador/controlador.php';

// Proteger esta página - solo admin
requiere_permiso('admin', '../index.php');

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre_cat ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - TAF2</title>
    <link rel="stylesheet" href="/taf2/css/style.css">
    <link rel="stylesheet" href="/taf2/css/sidebar.css">
</head>
<body>
<div class="main-wrapper">
    
    <?php include '../sidebar.php'; ?>

    <div class="content-container">
        <nav class="top-navbar">
            <button class="btn-toggle" id="sidebarToggle">☰ Alternar Menú</button>
            <span class="navbar-project-title">Módulo Admin / Categorías</span>
        </nav>

        <div class="page-content">
            <h1 class="main-page-title">Gestión de Categorías</h1>

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
                            Crear Nueva Categoría
                        </div>
                        <div class="card-body">
                            <form action="../procesar.php" method="POST">
                                <input type="hidden" name="action" value="nueva_categoria">

                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre de la Categoría</label>
                                    <input type="text" name="nombre_cat" class="form-input" required placeholder="Ej. Frutas Tropicales" maxlength="100" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                </div>

                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Descripción</label>
                                    <textarea name="descripcion" class="form-input" placeholder="Descripción breve de la categoría" maxlength="255" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-height: 80px; resize: vertical;"></textarea>
                                </div>

                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; margin-bottom: 5px; font-weight: 600;">Estado</label>
                                    <select name="estado" class="form-input" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-submit-dark" style="width: 100%; padding: 10px; background-color: #212529; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Crear Categoría</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="grid-column-table" style="flex: 2; min-width: 500px;">
                    <div class="custom-card">
                        <div class="card-header-green">Categorías Registradas</div>
                        <div class="card-body">
                            <?php if (empty($categorias)): ?>
                                <p class="no-data-text">No hay categorías registradas todavía.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="custom-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categorias as $cat): ?>
                                                <tr>
                                                    <td><strong>#<?= (int)$cat['id_cat'] ?></strong></td>
                                                    <td><?= htmlspecialchars($cat['nombre_cat']) ?></td>
                                                    <td><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') ?></td>
                                                    <td>
                                                        <span class="custom-badge <?= $cat['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?>">
                                                            <?= htmlspecialchars(ucfirst($cat['estado'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="/taf2/procesar.php?action=eliminar_categoria&id_cat=<?= (int)$cat['id_cat'] ?>" 
                                                           class="btn-small" 
                                                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')"
                                                           style="padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 3px; font-size: 0.85rem; cursor: pointer; text-decoration: none;">
                                                            Eliminar
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div> 
        </div> 
    </div> 
</div> 

<script>
// Manejo del botón de alternar menú (Sidebar Toggle)
document.getElementById('sidebarToggle').addEventListener('click', function(e) {
    e.preventDefault();
    document.body.classList.toggle('sidebar-hidden');
});
</script>
</body>
</html>
