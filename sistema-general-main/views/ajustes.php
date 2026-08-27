    






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

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background: transparent; /* permitir fondo global en lugar de fondo propio */
            color: #1a2c3e;
            line-height: 1.5;
            transition: filter 0.25s ease;
        }

        /* modo oscuro (tema negro) se aplica directamente al body */
        body.bw-mode {
            /* No cambiar el fondo de la página; solo ajustar color de texto si es necesario */
            color: #cbd5e1;
        }

        /* Tonos de tarjetas y elementos para modo oscuro */
        /* Mantener las tarjetas y componentes con su fondo por defecto; en bw-mode se aplica una capa encima del fondo global */

        body.bw-mode .status-badge {
            background: #111827;
            color: #cbd5e1;
        }

        /* Forzar tonos neutros en barras y chips que usan estilos inline */
        body.bw-mode .card-color-bar {
            background: linear-gradient(90deg, #111827, #374151) !important;
        }

        body.bw-mode .color-chip {
            background: #374151 !important;
            border-color: rgba(255, 255, 255, 0.03) !important;
        }

        body.bw-mode .btn {
            background-color: #1f2937 !important;
            color: #e6eef8 !important;
            box-shadow: none;
        }

        body.bw-mode .btn-primary {
            background: #2563eb !important;
            color: white !important;
        }

        body.bw-mode .btn-success {
            background: #15803d !important;
        }

        body.bw-mode .btn-warning {
            background: #b45309 !important;
        }

        body.bw-mode .btn-outline {
            background: transparent;
            border-color: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
        }

        body.bw-mode .badge,
        body.bw-mode .badge-blue,
        body.bw-mode .badge-pink {
            background: #374151 !important;
            color: #e6eef8 !important;
        }

        body.bw-mode hr {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.03), transparent);
        }

        body.bw-mode .page-header h1 {
            background: none;
            color: #e6eef8;
            -webkit-background-clip: unset;
        }

        /* Overlay en modo B/N: oscurece el fondo global pero NO cambia los fondos de las tarjetas */
        body.bw-mode::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.28);
            pointer-events: none;
            z-index: 0;
        }

        /* Asegurar que el contenido principal y las tarjetas estén por encima del overlay */
        .settings-container,
        .settings-card,
        .preview-area,
        .demo-card,
        .gradient-box {
            position: relative;
            z-index: 1;
            /* mantener sus fondos por defecto para legibilidad */
            background-clip: padding-box;
        }

        /* Contenedor principal estilo dashboard */
        .settings-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 1.5rem;
        }

        /* Header */
        .page-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1e3c5c, #2b5b8b);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            letter-spacing: -0.3px;
        }

        .page-header p {
            color: #4a627a;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Tarjeta de ajustes principal */
        .settings-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(0px);
            border-radius: 2rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.02);
            padding: 1.8rem 2rem;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.2s;
        }

        .setting-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .setting-info {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .setting-info h2 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .setting-info h2 span {
            font-size: 1.6rem;
        }

        .setting-info .desc {
            color: #5c6f87;
            font-size: 0.9rem;
            max-width: 28rem;
        }

        /* Toggle Switch personalizado (moderno) */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 68px;
            height: 34px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.25s ease;
            border-radius: 34px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.25s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        input:checked+.slider {
            background-color: #2c6e9e;
        }

        input:checked+.slider:before {
            transform: translateX(34px);
        }

        /* estado hover para feedback */
        .slider:hover {
            background-color: #b9c2d0;
        }

        input:checked+.slider:hover {
            background-color: #1f5880;
        }

        .status-badge {
            background: #eef2f8;
            border-radius: 100px;
            padding: 0.3rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1f4e6e;
            margin-left: 0.75rem;
            white-space: nowrap;
        }

        /* zona de preview: muestras con colores vivos */
        .preview-area {
            margin-top: 1rem;
        }

        .preview-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
            border-left: 5px solid #3b82f6;
            padding-left: 1rem;
        }

        .preview-title h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #0b3550;
        }

        .preview-title small {
            font-size: 0.8rem;
            color: #5d6f88;
            background: #e9edf2;
            padding: 0.2rem 0.7rem;
            border-radius: 30px;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* tarjetas de demostración multicolor */
        .demo-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.1);
            transition: transform 0.15s ease, box-shadow 0.2s;
        }

        .demo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        }

        .card-color-bar {
            height: 12px;
            width: 100%;
        }

        .card-content {
            padding: 1.3rem 1.2rem 1.5rem;
        }

        .card-content h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-content p {
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .chip-colors {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin: 0.5rem 0;
        }

        .color-chip {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* botones y elementos adicionales coloridos */
        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1rem 0 1.5rem;
        }

        .btn {
            padding: 0.65rem 1.3rem;
            border-radius: 60px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.1s linear;
            background-color: #eef2ff;
            color: #1f3a5f;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: #2266b9;
            color: white;
        }

        .btn-success {
            background: #2b7d4c;
            color: white;
        }

        .btn-warning {
            background: #e68a2e;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1.5px solid #4f7ea0;
            color: #1e5a7d;
        }

        .gradient-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            color: white;
            font-weight: bold;
            margin-top: 0.8rem;
        }

        .inline-colorful {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .badge {
            background: #ffd966;
            padding: 0.25rem 1rem;
            border-radius: 40px;
            font-weight: 600;
            color: #5e3a00;
        }

        .badge-blue {
            background: #1e88e5;
            color: white;
        }

        .badge-pink {
            background: #ec489a;
            color: white;
        }

        hr {
            margin: 1.8rem 0;
            border: 0;
            height: 1px;
            background: linear-gradient(to right, #cfdfed, transparent);
        }

        footer {
            text-align: center;
            font-size: 0.8rem;
            color: #6c7e96;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dfe6ef;
        }

        @media (max-width: 640px) {
            .settings-container {
                padding: 1rem;
            }

            .settings-card {
                padding: 1.3rem;
            }

            .setting-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .status-badge {
                margin-left: 0;
                margin-top: 8px;
                display: inline-block;
            }

            .preview-title h3 {
                font-size: 1.2rem;
            }
        }
    </style>




    <div class="settings-container">

        <div class="page-header">
            <h1>Panel de ajustes</h1>
            <p>Personaliza la experiencia visual de la aplicación</p>
        </div>

        <!-- Tarjeta de configuración principal: blanco y negro -->
        <div class="settings-card">
            <div class="setting-item">
                <div class="setting-info">
                    <h2>
                        tema negro
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
                <h3>Vista previa del aplicativo</h3>
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


