<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';
$datos = obtenerDatosTaf2($con);
$usuarios = $datos['usuarios'];

$roles = [];
$rolesResult = mysqli_query($con, 'SELECT id_rol, nombre_rol FROM roles ORDER BY id_rol ASC');
if ($rolesResult) {
    $roles = mysqli_fetch_all($rolesResult, MYSQLI_ASSOC);
}
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <h1>Usuarios</h1>
            <p>Gestiona el personal y los permisos. Accede rápidamente a los datos de las cuentas registradas.</p>
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
    $editarUsuario = null;
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar_usuario') {
        $idEdicion = (int)$_GET['id'];
        if ($idEdicion > 0) {
            $stmt = mysqli_prepare($con, 'SELECT id_usu, nom_com, correo, estado, id_rol FROM usuarios WHERE id_usu = ? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'i', $idEdicion);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editarUsuario = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($stmt);
        }
    }
    ?>

    <section class="taf2-card">
        <h3>Crear nuevo usuario</h3>
        <form action="index.php?page=taf2&view=usuarios&action=nuevo_usuario" method="POST" class="taf2-form">
            <input type="text" name="nom_com" placeholder="Nombre completo" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" minlength="6" required>
            <select name="id_rol" required>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= (int)$rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <button type="submit" class="taf2-btn">Crear usuario</button>
        </form>
    </section>

    <?php if ($editarUsuario): ?>
        <section class="taf2-card">
            <h3>Editar usuario #<?= (int)$editarUsuario['id_usu'] ?></h3>
            <form action="index.php?page=taf2&view=usuarios&action=actualizar_usuario" method="POST" class="taf2-form">
                <input type="hidden" name="id_usu" value="<?= (int)$editarUsuario['id_usu'] ?>">
                <input type="text" name="nom_com" value="<?= htmlspecialchars($editarUsuario['nom_com']) ?>" placeholder="Nombre" required>
                <input type="email" name="correo" value="<?= htmlspecialchars($editarUsuario['correo']) ?>" placeholder="Correo" required>
                <select name="id_rol" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int)$rol['id_rol'] ?>" <?= $editarUsuario['id_rol'] === (int)$rol['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="estado" required>
                    <option value="activo" <?= $editarUsuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $editarUsuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
                <button type="submit" class="taf2-btn">Guardar cambios</button>
                <a href="index.php?page=taf2&view=usuarios" class="taf2-btn" style="background:#7b0030;">Cancelar</a>
            </form>
        </section>
    <?php endif; ?>

    <section class="taf2-card">
        <table class="taf2-table">
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= (int)$usuario['id_usu'] ?></td>
                        <td><?= htmlspecialchars($usuario['nom_com']) ?></td>
                        <td><?= htmlspecialchars($usuario['correo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_rol'] ?? ($usuario['id_rol'] == 1 ? 'admin' : 'cliente')) ?></td>
                        <td><?= htmlspecialchars($usuario['estado'] ?? 'activo') ?></td>
                        <td>
                            <a href="index.php?page=taf2&view=usuarios&action=editar_usuario&id=<?= (int)$usuario['id_usu'] ?>" class="taf2-btn" style="padding:8px 12px;font-size:0.9rem;">Editar</a>
                            <form action="index.php?page=taf2&view=usuarios&action=eliminar_usuario" method="POST" style="display:inline-block; margin-left:8px;">
                                <input type="hidden" name="id_usu" value="<?= (int)$usuario['id_usu'] ?>">
                                <button type="submit" class="taf2-btn" style="background:#e74c3c;">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
