<?php
session_start();
include '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

// Asegurarse de que la tabla usuarios tenga las columnas requeridas para perfil extendido.
$neededCols = ['descripcion', 'direccion', 'alergias'];
foreach ($neededCols as $col) {
    $colCheck = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE '$col'");
    if (mysqli_num_rows($colCheck) === 0) {
        mysqli_query($con, "ALTER TABLE usuarios ADD COLUMN $col TEXT NULL");
    }
}

// Cargar datos actuales del usuario para mostrar en el formulario
$userEmail = $_SESSION['correo'];
$profileData = mysqli_fetch_assoc(mysqli_query($con, "SELECT nom_com, imagen, telefono, descripcion, direccion, alergias FROM usuarios WHERE correo = '" . mysqli_real_escape_string($con, $userEmail) . "' LIMIT 1"));

$alertMessage = '';
$alertType = 'info';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (isset($_POST['deletePerfil'])) {
    // Eliminar el perfil del usuario autenticado
    $deleteStmt = mysqli_prepare($con, 'DELETE FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($deleteStmt, 's', $userEmail);
    $deleted = mysqli_stmt_execute($deleteStmt);
    mysqli_stmt_close($deleteStmt);

    if ($deleted) {
        // Limpiar sesión y redirigir
        session_unset();
        session_destroy();

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Perfil eliminado. Redirigiendo...']);
            exit();
        }

        header('Location: ../index.php');
        exit();
    } else {
        $alertMessage = 'No se pudo eliminar el perfil. Intente nuevamente.';
        $alertType = 'error';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $alertMessage]);
            exit();
        }
    }
}

if (isset($_POST['updatePerfil'])) {
    $newNombre = mysqli_real_escape_string($con, $_POST['nombre'] ?? '');
    $newTelefono = mysqli_real_escape_string($con, $_POST['telefono'] ?? '');
    $newDescripcion = mysqli_real_escape_string($con, $_POST['descripcion'] ?? '');
    $newDireccion = mysqli_real_escape_string($con, $_POST['direccion'] ?? '');
    $newAlergias = mysqli_real_escape_string($con, $_POST['alergias'] ?? '');

    $newImagen = $profileData['imagen'];
    if (!empty($_FILES['profile-pic']['name']) && $_FILES['profile-pic']['error'] == 0) {
        $check = getimagesize($_FILES['profile-pic']['tmp_name']);
        if ($check !== false) {
            $newImagen = file_get_contents($_FILES['profile-pic']['tmp_name']);
        } else {
            $alertMessage = 'El archivo no es una imagen válida.';
            $alertType = 'error';
        }
    }

    if ($alertMessage === '') {
        $stmt = mysqli_prepare($con, "UPDATE usuarios SET nom_com = ?, imagen = ?, telefono = ?, descripcion = ?, direccion = ?, alergias = ? WHERE correo = ?");
        mysqli_stmt_bind_param($stmt, 'sssssss', $newNombre, $newImagen, $newTelefono, $newDescripcion, $newDireccion, $newAlergias, $userEmail);

        if (mysqli_stmt_execute($stmt)) {
            $alertMessage = 'Perfil actualizado correctamente.';
            $alertType = 'success';
            $_SESSION['usuario'] = $newNombre;
            $_SESSION['imagen'] = $newImagen;
            $profileData['nom_com'] = $newNombre;
            $profileData['telefono'] = $newTelefono;
            $profileData['descripcion'] = $newDescripcion;
            $profileData['direccion'] = $newDireccion;
            $profileData['alergias'] = $newAlergias;
            $profileData['imagen'] = $newImagen;
        } else {
            $alertMessage = 'No se pudo actualizar el perfil. Inténtalo otra vez.';
            $alertType = 'error';
        }

        mysqli_stmt_close($stmt);
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $alertType === 'success',
            'message' => $alertMessage,
            'type' => $alertType
        ]);
        exit();
    }
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sty.css">
    <link rel="stylesheet" href="../css/pefisty.css">
    <link rel="icon" href="../image/placeholder.png">
    <title>Perfil</title>
</head>
<body>
    <form action="../index.php" class="back-form">
        <button class="back-btn" type="submit">← Volver</button>
    </form>
    <button id="editarPerfilBtn" type="button" class="submit-btn" style="position: absolute; top: 10px; right: 10px;">Editar perfil</button>
    <main class="spc" style="width: 1300px ; height: 1100px auto;">
        <div>
            <h1><strong>-----PERFIL-----</strong></h1><br>
        </div>
        <section style="display:flex; gap:100px; align-items:center; flex-wrap:wrap; width:100%; max-width:800px;">
            <div class="cirp" style="width:150px; height:150px; background-image: url('data:image/jpeg;base64,<?php echo $_SESSION['imagen'] ? base64_encode($_SESSION['imagen']) : ''; ?>'); background-size: cover; background-position: center;"></div>
            <div class="profile-info" style="flex:1; min-width:220px;">
                <h1 style="margin:0 0 8px 0;"><?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                <p style="margin:4px 0;"><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($_SESSION['correo']); ?></p>
                <p  style="margin:4px 0;"><strong>Bebida favorita:</strong> Malteada de vainilla</p>
            </div>
        </section>

        <section style="width:100%; max-width:800px; margin-top:20px;">
            <h2 style="margin:0 0 8px 0; font-size:1.05em;">Descripción</h2>
            <p style="margin:0; line-height:1.5;"><?php echo htmlspecialchars($profileData['descripcion'] ?: "Hola, soy " . $_SESSION['usuario'] . ". Me gustan las bebidas tropicales."); ?></p>
        </section>

        <!-- Formulario edición perfil -->
        <section id="formEditarPerfil" style="display:none; width:100%; max-width:800px; margin-top:25px; background: rgba(255,255,255,0.85); border-radius:14px; padding:16px; box-shadow:0 8px 18px rgba(0,0,0,0.08);">
            <h2 style="margin:0 0 12px 0;">Editar perfil</h2>
            <form action="" method="post" enctype="multipart/form-data" style="display:grid; gap:10px;">
                <label>Nombre completo:
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($profileData['nom_com']); ?>" required class="input-pastel">
                </label>
                <label>Teléfono:
                    <input type="tel" name="telefono" value="<?php echo htmlspecialchars($profileData['telefono']); ?>" class="input-pastel">
                </label>
                <label>Dirección:
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($profileData['direccion']); ?>" class="input-pastel">
                </label>
                <label>Alergias:
                    <input type="text" name="alergias" value="<?php echo htmlspecialchars($profileData['alergias']); ?>" class="input-pastel">
                </label>
                <label>Descripción:
                    <textarea name="descripcion" rows="3" class="input-pastel"><?php echo htmlspecialchars($profileData['descripcion']); ?></textarea>
                </label>
                <label>Imagen de perfil:
                    <input type="file" name="profile-pic" accept="image/*" class="input-pastel">
                </label>
                <button type="submit" name="updatePerfil" class="submit-btn">Guardar cambios</button>
            </form>
        </section>

        <div>
            <h1 style="margin: 4px 0;"><strong>----BEBIDAS FAVORITAS----</strong></h1>
        </div>
        <section>
                <div class="cards-grid">
        <div class="card">
            <div class="thumb"><img src="../image/placeholder.jfif" alt="Item 1"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="../image/placeholder.jfif" alt="Item 2"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="../image/placeholder.jfif" alt="Item 3"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="../image/placeholder.jfif" alt="Item 4"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        </section>
        <button id="borrarPerfilBtn" class="delet-buttons">Borrar Perfil</button>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Controlador para abrir SweetAlert2 y mostrar el formulario de edición.
        document.getElementById('editarPerfilBtn').addEventListener('click', async function (event) {
            event.preventDefault();

            const formHtml = `
                <div style="display:flex; flex-direction:column; gap:10px; text-align:left; align-items:center; justify-content:center;">
                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Nombre completo</label>
                        <input id="swal-nombre" class="input-pastel" style="width:100%;" placeholder="Nombre completo" value="${encodeHTML('<?php echo addslashes($profileData['nom_com'])?>')}" />
                    </div>

                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Teléfono</label>
                        <input id="swal-telefono" class="input-pastel" style="width:100%;" type="tel" placeholder="Teléfono" value="${encodeHTML('<?php echo addslashes($profileData['telefono'])?>')}" />
                    </div>

                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Dirección</label>
                        <input id="swal-direccion" class="input-pastel" style="width:100%;" placeholder="Dirección" value="${encodeHTML('<?php echo addslashes($profileData['direccion'])?>')}" />
                    </div>

                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Alergias</label>
                        <input id="swal-alergias" class="input-pastel" style="width:100%;" placeholder="Alergias" value="${encodeHTML('<?php echo addslashes($profileData['alergias'])?>')}" />
                    </div>

                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Descripción</label>
                        <textarea id="swal-descripcion" class="input-pastel" style="width:100%;" rows="3" placeholder="Descripción">${encodeHTML('<?php echo addslashes($profileData['descripcion'])?>')}</textarea>
                    </div>

                    <div style="width:100%; max-width:520px;">
                        <label style="font-weight:600; display:block; margin-bottom:4px;">Imagen de perfil</label>
                        <input id="swal-profile-pic" class="input-pastel" style="width:100%;" type="file" accept="image/*" />
                    </div>
                </div>
            `;

            const { value: formValues } = await Swal.fire({
                title: 'Editar perfil',
                html: formHtml,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                width: '650px',
                preConfirm: () => {
                    return {
                        nombre: document.getElementById('swal-nombre').value,
                        telefono: document.getElementById('swal-telefono').value,
                        direccion: document.getElementById('swal-direccion').value,
                        alergias: document.getElementById('swal-alergias').value,
                        descripcion: document.getElementById('swal-descripcion').value,
                        imageFile: document.getElementById('swal-profile-pic').files[0] || null
                    };
                }
            });

            if (!formValues) return;

            const data = new FormData();
            data.append('updatePerfil', '1');
            data.append('nombre', formValues.nombre);
            data.append('telefono', formValues.telefono);
            data.append('direccion', formValues.direccion);
            data.append('alergias', formValues.alergias);
            data.append('descripcion', formValues.descripcion);
            if (formValues.imageFile) {
                data.append('profile-pic', formValues.imageFile);
            }

            const response = await fetch(window.location.href, {
                method: 'POST',
                body: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            Swal.fire({
                icon: result.success ? 'success' : 'error',
                title: result.success ? 'Perfil actualizado' : 'Error',
                text: result.message,
                timer: result.success ? 1600 : 2800,
                timerProgressBar: true
            }).then(() => {
                if (result.success) window.location.reload();
            });
        });

        // Eliminar perfil mediante SweetAlert2
        document.getElementById('borrarPerfilBtn').addEventListener('click', async function (event) {
            event.preventDefault();
            const confirmResult = await Swal.fire({
                title: '¿Eliminar perfil?',
                text: 'Esta acción no se puede deshacer. Todos los datos se eliminarán.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!confirmResult.isConfirmed) return;

            const data = new FormData();
            data.append('deletePerfil', '1');

            const response = await fetch(window.location.href, {
                method: 'POST',
                body: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            Swal.fire({
                icon: result.success ? 'success' : 'error',
                title: result.success ? 'Perfil eliminado' : 'Error',
                text: result.message,
                timer: 1800,
                timerProgressBar: true
            }).then(() => {
                if (result.success) window.location.href = '../index.php';
            });
        });

        function encodeHTML(str) {
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#39;');
        }
    </script>

</body>
</html>