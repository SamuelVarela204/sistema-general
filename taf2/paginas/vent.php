<?php
require_once '../controlador/controlador.php';

// Iniciar sesión si no está iniciada (necesario para el sidebar)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir rol para el sidebar
$rol_actual = $_SESSION['usuario_rol'] ?? 'admin';

$productos = $pdo->query("SELECT * FROM producto")->fetchAll();
$usuarios = $pdo->query("SELECT id_usu, nom_com FROM usuarios")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta - TAF2</title>
    <!-- RUTA CORREGIDA: Usamos ../ para subir un nivel desde la carpeta 'paginas' -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        /* Estilos específicos solo para las filas dinámicas de este formulario */
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .btn-add {
            background-color: #e0f2fe;
            color: #0369a1;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .btn-add:hover { background-color: #bae6fd; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include '../sidebar.php'; ?>
        
        <div class="content-container">
            <nav class="top-navbar">
                <button class="btn-toggle" onclick="toggleSidebar()">☰ Alternar Menú</button>
                <span style="font-weight: bold; color: #4b5563;">Proyecto TAF2 - Consola de Control</span>
            </nav>
            
            <div class="page-content">
                <div class="custom-card" style="max-width: 800px; margin: 0 auto;">
                    <div class="card-header-blue">
                        Registrar Nueva Venta / Pedido
                    </div>
                    <div class="card-body">
                        <!-- RUTA CORREGIDA en el action -->
                        <form action="../procesar.php" method="POST">
                            <input type="hidden" name="action" value="nuevo_pedido">
                            
                            <div class="form-group">
                                <label class="form-label">Seleccionar Cliente</label>
                                <select name="id_usu" class="form-select" required>
                                    <option value="">-- Elija un usuario comprador --</option>
                                    <?php foreach($usuarios as $u): ?>
                                        <option value="<?= $u['id_usu'] ?>"><?= htmlspecialchars($u['nom_com']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <label class="form-label" style="margin: 0;">Productos en el Pedido</label>
                                    <button type="button" class="btn-add" id="btn-add-item">+ Añadir Fila</button>
                                </div>
                                
                                <div id="contenedor-items">
                                    <div class="item-row item-producto">
                                        <select name="productos[]" class="form-select" style="flex: 2;" required>
                                            <option value="">-- Seleccionar Producto --</option>
                                            <?php foreach($productos as $p): ?>
                                                <option value="<?= $p['id_pro'] ?>"><?= htmlspecialchars($p['nom_pro']) ?> ($<?= number_format($p['precio'], 2) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="number" name="cantidades[]" class="form-input" style="flex: 1;" min="1" value="1" required>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-submit">Procesar Compra de Artículos</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            // Usamos la clase CSS que ya definimos en style.css
            document.body.classList.toggle('sidebar-hidden');
        }

        document.getElementById('btn-add-item').addEventListener('click', function() {
            const contenedor = document.getElementById('contenedor-items');
            const primeraFila = contenedor.querySelector('.item-producto');
            if(primeraFila) {
                const nuevaFila = primeraFila.cloneNode(true);
                nuevaFila.querySelector('select').value = "";
                nuevaFila.querySelector('input').value = "1";
                contenedor.appendChild(nuevaFila);
            }
        });
    </script>
</body>
</html>