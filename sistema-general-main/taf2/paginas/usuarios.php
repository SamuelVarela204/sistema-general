<?php
require_once '../controlador/controlador.php';

try {
    // 1. Quitamos la restricción estricta del WHERE para asegurarnos de traer todos los usuarios y comprobar que el JOIN funciona.
    // 2. Usamos alias claros (id_usu, nom_com, correo, nombre_rol, estado) para que PHP los reconozca sin errores.
    $query = "SELECT u.id_usu, u.nom_com, u.correo, r.nombre_rol, u.estado 
              FROM usuarios u 
              INNER JOIN roles r ON u.id_rol = r.id_rol 
              ORDER BY u.id_usu DESC";
              
    $usuarios = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $usuarios = [];
    $error_db = "Error en la consulta: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Usuarios - TAF2</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<div class="main-wrapper">
    
    <?php include '../sidebar.php'; ?>

    <div class="content-container">
        <nav class="top-navbar">
            <button class="btn-toggle" id="sidebarToggle">Alternar Menu</button>
            <span class="navbar-project-title">Modulo Admin / Usuarios</span>
        </nav>

        <div class="page-content">
            <h1 class="main-page-title">Gestion de Personal</h1>

            <?php if (isset($error_db)): ?>
                <div style="padding: 12px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px;">
                    <?= htmlspecialchars($error_db) ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 25px; flex-wrap: wrap; align-items: flex-start;">
                
                <div style="flex: 1; min-width: 320px;">
                    <div class="custom-card">
                        <div class="card-header-dark">Registrar Nuevo Usuario</div>
                        <div class="card-body">
                            <form action="../procesar.php" method="POST">
                                <input type="hidden" name="action" value="nuevo_usuario">

                                <div style="margin-bottom: 15px;">
                               0.
                               3     <label style="display: block; margin-bottom: 6px; font-weight: 600;">Nombre Completo</label>
                                    <input type="text" name="nom_com" class="form-input" required placeholder="Ej. Carlos Mendoza">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 6px; font-weight: 600;">Correo Electronico</label>
                                    <input type="email" name="correo" class="form-input" required placeholder="ejemplo@correo.com">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 6px; font-weight: 600;">Contraseña</label>
                                    <input type="password" name="usu_con" class="form-input" required placeholder="********">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 6px; font-weight: 600;">Asignar Rol</label>
                                    <select name="id_rol" class="form-input" required style="width: 100%; height: 45px; background: #fff;">
                                        <option value="" disabled selected>Seleccione un rol...</option>
                                        <option value="3">Inventario</option>
                                        <option value="4">Gerente</option>
                                    </select>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 6px; font-weight: 600;">Estado Inicial</label>
                                    <select name="estado" class="form-input" required style="width: 100%; height: 45px; background: #fff;">
                                        <option value="activo" selected>Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-logout" style="width: 100%; padding: 12px; background-color: #c2185b; color: #fff;">Crear Usuario</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div style="flex: 2; min-width: 500px;">
                    <div class="custom-card">
                        <div class="card-header-green">Personal Registrado</div>
                        <div class="card-body">
                            <?php if (empty($usuarios)): ?>
                                <p>No hay usuarios registrados en la base de datos todavía.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="custom-table" style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                                <th style="padding: 12px; text-align: left;">ID</th>
                                                <th style="padding: 12px; text-align: left;">Nombre</th>
                                                <th style="padding: 12px; text-align: left;">Correo</th>
                                                <th style="padding: 12px; text-align: left;">Rol Asignado</th>
                                                <th style="padding: 12px; text-align: left;">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($usuarios as $u): ?>
                                                <tr style="border-bottom: 1px solid #dee2e6;">
                                                    <td style="padding: 12px;"><strong>#<?= (int)$u['id_usu'] ?></strong></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($u['nom_com'] ?? '') ?></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($u['correo'] ?? '') ?></td>
                                                    <td style="padding: 12px;">
                                                        <span class="custom-badge" style="background-color: #f8bbd0; color: #c2185b; text-transform: uppercase; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">
                                                            <?= htmlspecialchars($u['nombre_rol'] ?? 'no asignado') ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px;">
                                                        <?php 
                                                        // Validamos el estado. Si viene vacío en los registros antiguos, por defecto mostramos ACTIVO
                                                        $estado_actual = strtolower($u['estado'] ?? 'activo');
                                                        if ($estado_actual == 'inactivo'): 
                                                        ?>
                                                            <span class="custom-badge" style="background-color: #f8d7da; color: #842029; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">INACTIVO</span>
                                                        <?php else: ?>
                                                            <span class="custom-badge" style="background-color: #d1e7dd; color: #0f5132; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">ACTIVO</span>
                                                        <?php endif; ?>
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
</body>
</html>