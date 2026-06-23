<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Ajustes · Modo Blanco y Negro</title>
    <link rel="stylesheet" href="../public/css/ajustes.css">
</head>

<body>
<?php
        // Obtener alergias y notificaciones del usuario logueado
        $alergiasUsuario = '';
        $notificacionesUsuario = false;
        if (estaLogueado()) {
            $con = conectarBD();
            $alergiasCol = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'alergias'");
            $notificacionesCol = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'notificaciones'");
            $columnas = [];
            if ($alergiasCol && mysqli_num_rows($alergiasCol) > 0) {
                $columnas[] = 'alergias';
            }
            if ($notificacionesCol && mysqli_num_rows($notificacionesCol) > 0) {
                $columnas[] = 'notificaciones';
            }

            if (!empty($columnas)) {
                $query = 'SELECT ' . implode(', ', $columnas) . ' FROM usuarios WHERE correo = ?';
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, 's', $_SESSION['correo']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    if (in_array('alergias', $columnas, true)) {
                        $alergiasUsuario = $row['alergias'] ?? '';
                    }
                    if (in_array('notificaciones', $columnas, true)) {
                        $notificacionesUsuario = (bool)($row['notificaciones'] ?? false);
                    }
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($con);
        }
        ?>

    <div class="settings-container">

        <div class="page-header">
            <h1>⚙️ Panel de ajustes</h1>
            <p>Personaliza la experiencia visual de la aplicación</p>
        </div>

        <!-- Tarjeta de configuración principal: blanco y negro -->
        <div class="settings-card">
            <div class="setting-item">
                <div class="setting-info">
                    <h2>tema negro
                        <span class="status-badge" id="statusLabel">Desactivado</span>
                    </h2>
                    <p class="desc">Activa el filtro de escala de grises en toda la interfaz. Ideal para reducir distracciones visuales o simular una vista monocromática.</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="grayscaleToggle" aria-label="Activar modo blanco y negro">
                    <span class="slider"></span>
                </label>
            </div>
            <div style="margin-top: 0.9rem; font-size: 0.75rem; color: #5b7a9a; background: #eef3fa; padding: 0.4rem 1rem; border-radius: 40px; display: inline-block;">
                El ajuste se guarda automáticamente en tu navegador
            </div>
        </div>

        <!-- Área de vista previa / demo del aplicativo (elementos llenos de color) -->
        <div class="preview-area">
            <div class="preview-title">
                <h2>Vista previa del aplicativo</h2>
            </div>
            <p style="margin-bottom: 1rem; color: #2c4e6e;">Componentes de ejemplo: tarjetas, botones, badges y degradados. Al activar el modo B/N todo se volverá monocromático.</p>
            <!-- Tarjeta: Alergias -->
            <div class="settings-card">
                <div class="setting-item">
                    <div class="setting-info">
                        <h2>Mis alergias</h2>
                        <p class="desc">Indica tus alergias para filtrar productos que no puedas consumir.</p>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <textarea id="alergiasInput" rows="3" style="width: 100%; padding: 12px; border-radius: 20px; border: 1px solid #ccc; font-family: inherit;"><?= htmlspecialchars($alergiasUsuario) ?></textarea>
                    <button id="guardarAlergiasBtn" class="submit-btn" style="margin-top: 1rem; width: auto; padding: 0.6rem 2rem;">Guardar alergias</button>
                </div>
            </div>

            <!-- Tarjeta: Notificaciones -->
            <div class="settings-card">
                <div class="setting-item">
                    <div class="setting-info">
                        <h2>Notificaciones</h2>
                        <p class="desc">Recibe correos con promociones y novedades.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="notificacionesToggle" <?= $notificacionesUsuario ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Tarjeta: Apariencia adicional -->
            <div class="settings-card">
                <div class="setting-info">
                    <h2>Apariencia adicional</h2>
                    <p class="desc">Ajusta el tamaño de fuente, el contraste y las animaciones.</p>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1.5rem;">
                    <div>
                        <label>Tamaño de fuente:</label>
                        <select id="fontSizeSelect">
                            <option value="small">Pequeño</option>
                            <option value="normal" selected>Normal</option>
                            <option value="large">Grande</option>
                        </select>
                    </div>
                    <div>
                        <label>Alto contraste:</label>
                        <label class="toggle-switch" style="width: 50px; height: 28px; margin-left: 0.5rem;">
                            <input type="checkbox" id="highContrastToggle">
                            <span class="slider" style="height: 28px;"></span>
                        </label>
                    </div>
                    <div>
                        <label>Desactivar animaciones:</label>
                        <label class="toggle-switch" style="width: 50px; height: 28px; margin-left: 0.5rem;">
                            <input type="checkbox" id="noAnimationsToggle">
                            <span class="slider" style="height: 28px;"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const rootElement = document.documentElement;
            rootElement.classList.add('bw-mode');
            (function() {
                // Elementos del DOM
                const toggleCheckbox = document.getElementById('grayscaleToggle');
                const statusSpan = document.getElementById('statusLabel');
                const rootElement = document.documentElement;

                // Clave para localStorage
                const STORAGE_KEY = 'app_grayscale_mode';

                // Función para actualizar el estado visual y guardar
                function setGrayscaleMode(enabled) {
                    try {
                        if (enabled) {
                            rootElement.classList.add('bw-mode');
                            if (document.body) document.body.classList.add('bw-mode');
                            if (toggleCheckbox) toggleCheckbox.checked = true;
                            if (statusSpan) statusSpan.textContent = 'Activado';
                            // Guardar preferencia
                            localStorage.setItem(STORAGE_KEY, 'true');
                        } else {
                            rootElement.classList.remove('bw-mode');
                            if (document.body) document.body.classList.remove('bw-mode');
                            if (toggleCheckbox) toggleCheckbox.checked = false;
                            if (statusSpan) statusSpan.textContent = 'Desactivado';
                            localStorage.setItem(STORAGE_KEY, 'false');
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }

                // Cargar configuración guardada al iniciar
                function loadInitialMode() {
                    const saved = localStorage.getItem(STORAGE_KEY);
                    if (saved === 'true') {
                        setGrayscaleMode(true);
                    } else if (saved === 'false') {
                        setGrayscaleMode(false);
                    } else {
                        // Si no hay preferencia guardada, por defecto desactivado
                        setGrayscaleMode(false);
                    }
                }

                // Evento de cambio del toggle
                if (toggleCheckbox) {
                    toggleCheckbox.addEventListener('change', (e) => {
                        const isChecked = e.target.checked;
                        setGrayscaleMode(isChecked);
                    });
                }

                // Inicialización
                loadInitialMode();

                // Opcional: asegurar que si se cambia la clase desde fuera (solo por consistencia)
                // pero nuestro método central es el correcto.
                // Además por si se pierde sincronía, actualizar estado al recargar cambios manualmente
                window.addEventListener('storage', (event) => {
                    if (event.key === STORAGE_KEY) {
                        const newValue = event.newValue === 'true';
                        // actualizar interfaz cuando otra pestaña modifique el modo
                        if (newValue && !rootElement.classList.contains('bw-mode')) {
                            setGrayscaleMode(true);
                        } else if (!newValue && rootElement.classList.contains('bw-mode')) {
                            setGrayscaleMode(false);
                        }
                        // sincronizar checkbox
                        if (toggleCheckbox) toggleCheckbox.checked = newValue;
                    }
                });
            })();
            // Guardar alergias
            document.getElementById('guardarAlergiasBtn')?.addEventListener('click', async () => {
                const alergias = document.getElementById('alergiasInput').value;
                const res = await fetch('includes/procesar_perfil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'accion=actualizar_alergias&alergias=' + encodeURIComponent(alergias)
                });
                const data = await res.json();
                Swal.fire({
                    icon: data.exito ? 'success' : 'error',
                    title: data.mensaje
                });
            });

            // Guardar notificaciones
            document.getElementById('notificacionesToggle')?.addEventListener('change', async (e) => {
                const notificaciones = e.target.checked ? 1 : 0;
                const res = await fetch('includes/procesar_perfil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'accion=actualizar_notificaciones&notificaciones=' + notificaciones
                });
                const data = await res.json();
                if (!data.exito) Swal.fire('Error', data.mensaje, 'error');
            });

            // Apariencia adicional (localStorage)
            function aplicarApariencia() {
                const fontSize = localStorage.getItem('fontSize') || 'normal';
                const highContrast = localStorage.getItem('highContrast') === 'true';
                const noAnimations = localStorage.getItem('noAnimations') === 'true';

                document.documentElement.classList.remove('font-small', 'font-large', 'high-contrast', 'no-animations');
                if (fontSize === 'small') document.documentElement.classList.add('font-small');
                if (fontSize === 'large') document.documentElement.classList.add('font-large');
                if (highContrast) document.documentElement.classList.add('high-contrast');
                if (noAnimations) document.documentElement.classList.add('no-animations');
            }

            document.getElementById('fontSizeSelect')?.addEventListener('change', (e) => {
                localStorage.setItem('fontSize', e.target.value);
                aplicarApariencia();
            });
            document.getElementById('highContrastToggle')?.addEventListener('change', (e) => {
                localStorage.setItem('highContrast', e.target.checked);
                aplicarApariencia();
            });
            document.getElementById('noAnimationsToggle')?.addEventListener('change', (e) => {
                localStorage.setItem('noAnimations', e.target.checked);
                aplicarApariencia();
            });

            // Cargar valores guardados en los controles
            document.getElementById('fontSizeSelect').value = localStorage.getItem('fontSize') || 'normal';
            document.getElementById('highContrastToggle').checked = localStorage.getItem('highContrast') === 'true';
            document.getElementById('noAnimationsToggle').checked = localStorage.getItem('noAnimations') === 'true';
            aplicarApariencia();
        </script>
        <?php
        // Obtener alergias y notificaciones del usuario logueado
        $alergiasUsuario = '';
        $notificacionesUsuario = false;
        if (estaLogueado()) {
            $con = conectarBD();
            $alergiasCol = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'alergias'");
            $notificacionesCol = mysqli_query($con, "SHOW COLUMNS FROM usuarios LIKE 'notificaciones'");
            $columnas = [];
            if ($alergiasCol && mysqli_num_rows($alergiasCol) > 0) {
                $columnas[] = 'alergias';
            }
            if ($notificacionesCol && mysqli_num_rows($notificacionesCol) > 0) {
                $columnas[] = 'notificaciones';
            }

            if (!empty($columnas)) {
                $query = 'SELECT ' . implode(', ', $columnas) . ' FROM usuarios WHERE correo = ?';
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, 's', $_SESSION['correo']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    if (in_array('alergias', $columnas, true)) {
                        $alergiasUsuario = $row['alergias'] ?? '';
                    }
                    if (in_array('notificaciones', $columnas, true)) {
                        $notificacionesUsuario = (bool)($row['notificaciones'] ?? false);
                    }
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($con);
        }
        ?>
</body>

</html>