<?php
// app/views/siscop.php
require_once LAYOUTS_PATH . '/header.php';
$titulo = 'Siscop';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/siscop.css">
<h1>ESTA PAGINA ESTA HECHA POR</h1>
<div class="contenedor-creadores">
    <div class="C-Monika">
        <!-- Ruta relativa desde views/ -->
        <img src="<?= BASE_URL ?>/public/images/sistema de copyright.gif" alt="">
        <h1>SAMUEL VARELA</h1>
    </div>
    <div class="C-Mirko">
        <img src="<?= BASE_URL ?>/public/images/mirk.png" alt="" width="400px" height="400px">
        <h1>DIEGO GARCIA</h1>
    </div>
</div>
<h2>ES ILEGAL USAR ESTA PAGINA SIN SU CONCENTIMIENTO</h2>


<?php
require_once LAYOUTS_PATH . '/footer.php';
?>