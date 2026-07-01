<?php
// index.php
require_once 'controlador/controlador.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir rol_actual ANTES de incluir sidebar (CRÍTICO)
$rol_actual = $_SESSION['usuario_rol'] ?? 'admin';

// Obt
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAF2 - Consola de Control</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>

<div class="main-wrapper">
    
    <?php include 'sidebar.php'; ?>

    <div class="content-container">
        
        <nav class="top-navbar">
            <button class="btn-toggle" id="sidebarToggle">☰ Alternar Menú</button>
            <span class="navbar-project-title">Proyecto TAF2 - Consola de Control</span>
            </nav>
</div>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function(e) {
    e.preventDefault();
    document.body.classList.toggle('sidebar-hidden');
});
</script>

</body>
</html>