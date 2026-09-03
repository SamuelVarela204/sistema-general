<?php
require_once 'controlador/controlador.php';

if (!isset($rol_actual)) {
    $rol_actual = $_SESSION['usuario_rol'] ?? 'admin';
}
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading">TAF2 - Panel</div>

    <div class="simulador-box">
        <span>Vista actual</span>
        <select class="form-select" onchange="location = this.value;">
            <option value="?vista=admin" <?= $rol_actual == 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="?vista=inventario" <?= $rol_actual == 'inventario' ? 'selected' : '' ?>>Inventario</option>
            <option value="?vista=gerente" <?= $rol_actual == 'gerente' ? 'selected' : '' ?>>Gerente</option>
        </select>
    </div>

    <div class="menu-sections">
        <div class="menu-group">
            <a href="/taf2/index.php?vista=<?= $rol_actual ?>" class="menu-link active-link">🏠 Inicio</a>
            <a href="/index.php?page=perfil" class="menu-link">👤 Mi perfil</a>
        </div>

        <?php if ($rol_actual === 'admin'): ?>
            <div class="menu-group">
                <div class="section-title">Administración</div>
                <a href="/taf2/paginas/categorias.php" class="menu-link">🏷️ Categorías</a>
                <a href="/taf2/paginas/usuarios.php" class="menu-link">👥 Usuarios</a>
                <a href="#" class="menu-link">⚙️ Configuración</a>
            </div>
        <?php endif; ?>

        <?php if ($rol_actual === 'admin' || $rol_actual === 'inventario'): ?>
            <div class="menu-group">
                <div class="section-title">Ventas</div>
                <a href="/taf2/paginas/vent.php" class="menu-link">🛒 Crear pedido</a>
                <a href="/taf2/paginas/productos.php" class="menu-link">📦 Productos</a>
                <a href="/index.php?page=taf2&view=pedidos" class="menu-link">📋 Pedidos</a>
            </div>
        <?php endif; ?>

        <?php if ($rol_actual === 'admin' || $rol_actual === 'gerente'): ?>
            <div class="menu-group">
                <div class="section-title">Gerencial</div>
                <a href="#" class="menu-link">👨‍💼 Empleados</a>
                <a href="#" class="menu-link">📊 Inventario</a>
                <a href="#" class="menu-link">📦 Stock</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <div class="user-summary">
            Conectado como:<br>
            <strong><?= htmlspecialchars($_SESSION['nom_com'] ?? 'Admin Pruebas') ?></strong>
        </div>
        <a href="/taf2/procesar.php?action=cerrar_sesion" class="btn-logout">Cerrar sesión</a>
    </div>
</div>