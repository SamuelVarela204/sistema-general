<?php
// Si no está logueado, redirigir al login
if (!estaLogueado()) {
    redirigir('index.php?page=login');
}

$titulo = 'Mi Perfil';

// Obtener datos actualizados del usuario
$userEmail = $_SESSION['correo'];
$con = conectarBD();

function columnaExiste($con, $columna)
{
    $columna = mysqli_real_escape_string($con, $columna);
    $resultado = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE '" . $columna . "'");
    return $resultado && mysqli_num_rows($resultado) > 0;
}

function obtenerCamposUsuario($con, $correo)
{
    $campos = ['nom_com', 'imagen', 'telefono'];
    foreach (['direccion', 'alergias', 'descripcion'] as $campo) {
        if (columnaExiste($con, $campo)) {
            $campos[] = $campo;
        }
    }

    $query = 'SELECT ' . implode(', ', $campos) . ' FROM usuarios WHERE correo = ? LIMIT 1';
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $correo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $datos ?: [];
}

$userData = obtenerCamposUsuario($con, $userEmail);
?>

<style>

</style>

<main class="spc" style="max-width: 1100px; width: 100%; margin: 0 auto; padding: 20px 16px; position: relative; margin-top: 30px;">
    <div style="text-align: center;">
        <h1><strong>MI PERFIL</strong></h1>
    </div>

    <section class="perfil-header">
        <div class="cirp perfil-foto" style="<?php if (!empty($userData['imagen'])): ?>background-image: url('data:image/jpeg;base64,<?php echo base64_encode($userData['imagen']); ?>');<?php endif; ?>"></div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($userData['nom_com'] ?: $_SESSION['usuario']); ?></h1>
            <div class="profile-grid">
                <div class="profile-item"><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($_SESSION['correo']); ?></div>
                <?php if (!empty($userData['telefono'])): ?>
                    <div class="profile-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($userData['telefono']); ?></div>
                <?php endif; ?>
                <?php if (!empty($userData['direccion'])): ?>
                    <div class="profile-item"><strong>Dirección:</strong> <?php echo htmlspecialchars($userData['direccion']); ?></div>
                <?php endif; ?>
                <?php if (!empty($userData['alergias'])): ?>
                    <div class="profile-item"><strong>Alergias:</strong> <?php echo htmlspecialchars($userData['alergias']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="profile-summary">
        <h2 style="margin:0 0 10px 0;">Descripción</h2>
        <p style="margin:0; line-height: 1.75;">
            <?php echo htmlspecialchars($userData['descripcion'] ?: "Perfil sin descripción."); ?>
        </p>
    </section>

    <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 30px;">
        <button id="editarPerfilBtn" type="button" class="submit-btn" style="width: 140px; height: 56px;">Editar perfil</button>
        <button id="borrarPerfilBtn" type="button" class="delet-buttons mi-boton" style="width: 140px; height: 56px;">Borrar Perfil</button>
        <script>
            const sonidoHover = document.getElementById('sonido-hover');
            const botonBorrar = document.getElementById('borrarPerfilBtn');

            botonBorrar?.addEventListener('mouseenter', () => {
                if (!sonidoHover) return;
                sonidoHover.currentTime = 0;
                sonidoHover.play().catch(() => {
                    // Algunos navegadores requieren interacción previa para permitir sonido
                });
            });
        </script>
    </div>
</main>

<script>
    const defaultNombre = <?php echo json_encode($userData['nom_com'] ?: $_SESSION['usuario']); ?>;
    const defaultTelefono = <?php echo json_encode($userData['telefono'] ?? ''); ?>;
    const defaultDireccion = <?php echo json_encode($userData['direccion'] ?? ''); ?>;
    const defaultAlergias = <?php echo json_encode($userData['alergias'] ?? ''); ?>;
    const defaultDescripcion = <?php echo json_encode($userData['descripcion'] ?? ''); ?>;

    // Editar perfil con SweetAlert2
    document.getElementById('editarPerfilBtn')?.addEventListener('click', async () => {
        const {
            value: formValues
        } = await Swal.fire({
            title: 'Editar Perfil',
            html: `
            <form id="editForm" style="display: grid; gap: 12px; max-width: 450px; margin: 0 auto;">
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Nombre</span>
                    <input type="text" id="nombre" name="nombre" value="${defaultNombre}" class="swal2-input">
                </label>
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Teléfono</span>
                    <input type="tel" id="telefono" name="telefono" value="${defaultTelefono}" class="swal2-input">
                </label>
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Dirección</span>
                    <input type="text" id="direccion" name="direccion" value="${defaultDireccion}" class="swal2-input">
                </label>
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Alergias</span>
                    <input type="text" id="alergias" name="alergias" value="${defaultAlergias}" class="swal2-input">
                </label>
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Descripción</span>
                    <textarea id="descripcion" class="swal2-textarea" style="min-height:100px;">${defaultDescripcion}</textarea>
                </label>
                <label style="display: grid; gap: 4px;">
                    <span style="font-weight: 600; text-align: center; font-size: 0.9rem;">Foto de perfil</span>
                    <input type="file" id="imagen" name="imagen" accept="image/*" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </label>
            </form>
        `,
            width: '650px',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Guardar cambios',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const nombre = document.getElementById('nombre').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                const direccion = document.getElementById('direccion').value.trim();
                const alergias = document.getElementById('alergias').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const imagen = document.getElementById('imagen').files[0];

                if (!nombre) {
                    Swal.showValidationMessage('El nombre es obligatorio');
                    return false;
                }

                return {
                    nombre,
                    telefono,
                    direccion,
                    alergias,
                    descripcion,
                    imagen
                };
            }
        });

        if (formValues) {
            const formData = new FormData();
            formData.append('accion', 'actualizar');
            formData.append('nombre', formValues.nombre);
            formData.append('telefono', formValues.telefono);
            formData.append('direccion', formValues.direccion);
            formData.append('alergias', formValues.alergias);
            formData.append('descripcion', formValues.descripcion);
            if (formValues.imagen) {
                formData.append('imagen', formValues.imagen);
            }

            try {
                const res = await fetch('includes/procesar_perfil.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.exito) {
                    await Swal.fire({
                        icon: 'success',
                        title: result.mensaje,
                        timer: 1500
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.mensaje
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al actualizar el perfil'
                });
            }
        }
    });

    // Eliminar perfil
    document.getElementById('borrarPerfilBtn')?.addEventListener('click', async () => {
        const confirm = await Swal.fire({
            title: '¿Eliminar perfil?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');

            try {
                const res = await fetch('includes/procesar_perfil.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.exito) {
                    await Swal.fire({
                        icon: 'success',
                        title: result.mensaje,
                        timer: 1500
                    });
                    window.location.href = 'index.php';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.mensaje
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el perfil'
                });
            }
        }
    });
</script>