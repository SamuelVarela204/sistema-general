<?php
// Subimos un nivel de carpeta para encontrar correctamente el controlador central
require_once '../controlador/controlador.php';

// Consulta para obtener los pedidos (necesaria si esta página se abre de forma independiente)
$pedidos = $pdo->query("SELECT p.id_ped, u.nom_com, p.fecha_pedido, p.estado, p.total 
                        FROM pedido p 
                        JOIN usuarios u ON p.id_usu = u.id_usu 
                        ORDER BY p.id_ped DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - TAF2</title>
    <link rel="stylesheet" href="/taf2/css/style.css">
    <link rel="stylesheet" href="/taf2/css/sidebar.css">
</head>
<body>

<div class="main-wrapper">
    
    <?php include '../sidebar.php'; ?>

    <div class="content-container">
        
        <nav class="top-navbar">
            <button class="btn-toggle" id="sidebarToggle">☰ Alternar Menú</button>
            <span class="navbar-project-title">Proyecto TAF2 - Historial de Ventas</span>
        </nav>

        <div class="page-content">
            <div class="custom-card">
                <div class="card-header-green">
                    Historial General de Pedidos en la Base de Datos
                </div>
                <div class="card-body">
                    <?php if (empty($pedidos)): ?>
                        <p class="no-data-text">No hay pedidos registrados todavía en el sistema.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>ID Pedido</th>
                                        <th>Cliente</th>
                                        <th>Fecha y Hora</th>
                                        <th>Estado</th>
                                        <th>Artículos Comprados</th>
                                        <th>Total Facturado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pedidos as $ped): ?>
                                        <tr>
                                            <td><strong>#<?= $ped['id_ped'] ?></strong></td>
                                            <td><?= htmlspecialchars($ped['nom_com']) ?></td>
                                            <td class="text-nowrap"><?= $ped['fecha_pedido'] ?></td>
                                            <td><span class="custom-badge badge-warning"><?= strtoupper($ped['estado']) ?></span></td>
                                            <td>
                                                <ul class="table-products-list">
                                                <?php 
                                                    $stmtD = $pdo->prepare("SELECT dp.cantidad, pr.nom_pro FROM detalles_pedido dp JOIN producto pr ON dp.id_pro = pr.id_pro WHERE dp.id_ped = ?");
                                                    $stmtD->execute([$ped['id_ped']]);
                                                    $detalles = $stmtD->fetchAll();
                                                    foreach($detalles as $d) {
                                                        echo "<li>" . intval($d['cantidad']) . "x " . htmlspecialchars($d['nom_pro']) . "</li>";
                                                    }
                                                ?>
                                                </ul>
                                            </td>
                                            <td class="table-total-column">$<?= number_format($ped['total'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div> </div> </div> <script>
document.getElementById('sidebarToggle').addEventListener('click', function(e) {
    e.preventDefault();
    document.body.classList.toggle('sidebar-hidden');
});
</script>

</body>
</html>