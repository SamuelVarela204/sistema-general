<?php
$rol_actual = $_SESSION['usuario_rol'] ?? 'admin';
?>
<nav class="taf2-sidebar">
    <h2>TAF2</h2>
    <a href="index.php?page=taf2">Inicio</a>
    <a href="index.php?page=perfil">Mi Perfil</a>
    <a href="index.php?page=taf2&view=productos">Productos</a>
    <a href="index.php?page=taf2&view=usuarios">Usuarios</a>
    <a href="index.php?page=taf2&view=pedidos">Pedidos</a>
    <a href="index.php?page=logout" class="taf2-logout">Cerrar sesión</a>
</nav>
