<?php
// Este archivo se mantiene para compatibilidad
// El logout se maneja en index.php
session_start();
session_destroy();
header('Location: index.php');
exit;
