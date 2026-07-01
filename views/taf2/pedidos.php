<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';
$datos = obtenerDatosTaf2($con);
$pedidos = $datos['pedidos'];
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <h1>Pedidos</h1>
            <p>Revisa el historial de ventas y el estado de los pedidos para mantener tu operación al día.</p>
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
    $editarPedido = null;
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar_pedido') {
        $idEdicion = (int)$_GET['id'];
        if ($idEdicion > 0) {
            $stmt = mysqli_prepare($con, 'SELECT id_ped, estado, total FROM pedido WHERE id_ped = ? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'i', $idEdicion);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editarPedido = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($stmt);
        }
    }
    ?>

    <?php if ($editarPedido): ?>
        <section class="taf2-card">
            <h3>Editar pedido #<?= (int)$editarPedido['id_ped'] ?></h3>
            <form action="index.php?page=taf2&view=pedidos&action=actualizar_pedido" method="POST" class="taf2-form">
                <input type="hidden" name="id_ped" value="<?= (int)$editarPedido['id_ped'] ?>">
                <select name="estado" required>
                    <option value="pendiente" <?= $editarPedido['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="preparando" <?= $editarPedido['estado'] === 'preparando' ? 'selected' : '' ?>>Preparando</option>
                    <option value="enviado" <?= $editarPedido['estado'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                    <option value="entregado" <?= $editarPedido['estado'] === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                    <option value="cancelado" <?= $editarPedido['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
                <input type="number" step="0.01" name="total" value="<?= number_format((float)$editarPedido['total'], 2, '.', '') ?>" placeholder="Total" required>
                <button type="submit" class="taf2-btn">Guardar cambios</button>
                <a href="index.php?page=taf2&view=pedidos" class="taf2-btn" style="background:#7b0030;">Cancelar</a>
            </form>
        </section>
    <?php endif; ?>

    <section class="taf2-card">
        <table class="taf2-table">
            <thead>
                <tr><th>ID</th><th>Cliente</th><th>Estado</th><th>Total</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= (int)$pedido['id_ped'] ?></td>
                        <td><?= htmlspecialchars($pedido['nom_com']) ?></td>
                        <td><?= htmlspecialchars($pedido['estado']) ?></td>
                        <td>$<?= number_format((float)$pedido['total'], 2) ?></td>
                        <td>
                            <a href="index.php?page=taf2&view=pedidos&action=editar_pedido&id=<?= (int)$pedido['id_ped'] ?>" class="taf2-btn" style="padding:8px 12px;font-size:0.9rem;">Editar</a>
                            <form action="index.php?page=taf2&view=pedidos&action=eliminar_pedido" method="POST" style="display:inline-block; margin-left:8px;">
                                <input type="hidden" name="id_ped" value="<?= (int)$pedido['id_ped'] ?>">
                                <button type="submit" class="taf2-btn" style="background:#e74c3c;">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
