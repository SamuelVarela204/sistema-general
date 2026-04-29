<?php
// Si no está logueado, redirigir al login
if (!estaLogueado()) {
    redirigir('index.php?page=login');
}

$titulo = 'Mi Perfil';
ob_start();

// Obtener datos actualizados del usuario
$userEmail = $_SESSION['correo'];
$con = conectarBD();
$query = mysqli_query($con, "SELECT nom_com, imagen, telefono, descripcion, direccion, alergias FROM usuarios WHERE correo = '" . mysqli_real_escape_string($con, $userEmail) . "' LIMIT 1");
$userData = mysqli_fetch_assoc($query);
?>

<main class="spc" style="width: 1300px; height: auto;">
    <div>
        <h1><strong>-----PERFIL-----</strong></h1><br>
    </div>
    
    <section class="perfil-header">
        <div class="cirp perfil-foto" style="background-image: url('data:image/jpeg;base64,<?php echo $_SESSION['imagen'] ? base64_encode($_SESSION['imagen']) : ''; ?>');"></div>
        <div class="profile-info" style="flex:1; min-width:220px;">
            <h1 style="margin:0 0 8px 0;"><?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
            <p style="margin:4px 0;"><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($_SESSION['correo']); ?></p>
            <p style="margin:4px 0;"><strong>Bebida favorita:</strong> Malteada de vainilla</p>
        </div>
    </section>

    <button id="borrarPerfilBtn" class="delet-buttons" style="margin-top: 20px;">Borrar Perfil</button>
</main>

<script>
document.getElementById('borrarPerfilBtn')?.addEventListener('click', async () => {
    const confirm = await Swal.fire({
        title: '¿Eliminar perfil?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar'
    });
    if (confirm.isConfirmed) {
        const res = await fetch('index.php?page=eliminar_perfil', { method: 'POST' });
        const result = await res.json();
        Swal.fire({ icon: 'success', title: result.message, timer: 1500 }).then(() => {
            window.location.href = 'index.php';
        });
    }
});
</script>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>