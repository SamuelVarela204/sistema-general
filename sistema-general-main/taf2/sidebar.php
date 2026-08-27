<?php 
require_once 'controlador/controlador.php';

// Asegurar que $rol_actual exista
if (!isset($rol_actual)) {
    $rol_actual = $_SESSION['usuario_rol'] ?? 'admin';
}
$productos = $pdo->query("SELECT * FROM producto")->fetchAll();
$usuarios = $pdo->query("SELECT id_usu, nom_com FROM usuarios")->fetchAll();
$pedidos = $pdo->query("SELECT p.id_ped, u.nom_com, p.fecha_pedido, p.estado, p.total FROM pedido p JOIN usuarios u ON p.id_usu = u.id_usu ORDER BY p.id_ped DESC")->fetchAll();
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading">TAF2 - Panel</div>
    <div class="simulador-box">
        <span>Simulando Vista de:</span>
        <select class="form-select" style="padding: 4px; font-size: 0.85rem;" onchange="location = this.value;">
            <option value="?vista=admin" <?= $rol_actual == 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="?vista=inventario" <?= $rol_actual == 'inventario' ? 'selected' : '' ?>>Inventario</option>
            <option value="?vista=gerente" <?= $rol_actual == 'gerente' ? 'selected' : '' ?>>Gerente</option>
        </select>
    </div>
    <div class="menu-sections">
        <a href="/taf2/index.php?vista=<?= $rol_actual ?>" class="menu-link" style="font-weight: 600;">Inicio</a>
        
        <?php if ($rol_actual === 'admin'): ?>
            <div class="section-title">Módulo Admin</div>
            <a href="#" class="menu-link link-admin"> Gestionar Roles</a>
            <a href="../paginas/usuarios.php" class="menu-link link-admin"> Control Usuarios</a>
            <a href="#" class="menu-link link-admin"> Configuración Sistema</a>
        <?php endif; ?>
        
        <?php if ($rol_actual === 'admin' || $rol_actual === 'inventario'): ?>
            <div class="section-title">Módulo Ventas</div>
            <a href="/taf2/paginas/vent.php" class="menu-link link-ventas"> Crear Pedido</a>
            <a href="/taf2/paginas/productos.php" class="menu-link link-ventas"> Lista Productos</a>
            <a href="../paginas/pedidos.php" class="menu-link link-ventas">Pedidos</a>
        <?php endif; ?>
        
        <?php if ($rol_actual === 'admin' || $rol_actual === 'gerente'): ?>
            <div class="section-title">Módulo Gerencial</div>
            <a href="#" class="menu-link link-gerente"> Empleados (solo estado)</a>
            <a href="#" class="menu-link link-gerente"> Control Inventario</a>
            <a href="#" class="menu-link link-gerente"> Gestión de Stock</a>
        <?php endif; ?>
        
    </div>
    <div class="sidebar-footer">
        <div style="font-size: 0.8rem; color: #adb5bd; text-align: center; line-height: 1.4;">
            Conectado como: <br>
            <strong style="color: #fff; font-size: 0.85rem;"><?= htmlspecialchars($_SESSION['nom_com'] ?? 'Admin Pruebas') ?></strong>
        </div>
        <a href="/taf2/procesar.php?action=cerrar_sesion" class="btn-logout"> Cerrar Sesión</a>
    </div>
</div>