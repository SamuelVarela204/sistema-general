<?php
// Debug script para verificar el estado de la BD
require_once 'taf2/conexion.php';

echo "<h2>Estado de la Base de Datos TAF2</h2>";
echo "<hr>";

// 1. Verificar si existe la tabla categorias
echo "<h3>1. Verificando tabla 'categorias'...</h3>";
try {
    $check = $pdo->query("SHOW TABLES LIKE 'categorias'")->fetch();
    if ($check) {
        echo "✅ Tabla 'categorias' EXISTE<br>";
        
        // Contar registros
        $count = $pdo->query("SELECT COUNT(*) as cnt FROM categorias")->fetch();
        echo "   Registros: " . $count['cnt'] . "<br>";
        
        // Listar categorías
        $cats = $pdo->query("SELECT * FROM categorias")->fetchAll();
        if (!empty($cats)) {
            echo "   <ul>";
            foreach ($cats as $cat) {
                echo "<li>" . htmlspecialchars($cat['nombre_cat']) . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "❌ Tabla 'categorias' NO EXISTE<br>";
        echo "<strong style='color:red;'>SOLUCIÓN: Ejecuta la migración SQL en phpMyAdmin</strong>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";

// 2. Verificar estructura de tabla producto
echo "<h3>2. Verificando columnas de tabla 'producto'...</h3>";
try {
    $columns = $pdo->query("DESCRIBE producto")->fetchAll();
    $tiene_id_cat = false;
    $tiene_categoria = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'id_cat') {
            $tiene_id_cat = true;
            echo "✅ Columna 'id_cat' EXISTE<br>";
        }
        if ($col['Field'] === 'categoria') {
            $tiene_categoria = true;
            echo "⚠️  Columna 'categoria' (ANTIGUA) aún existe<br>";
        }
    }
    
    if (!$tiene_id_cat) {
        echo "❌ Columna 'id_cat' NO EXISTE<br>";
        echo "<strong style='color:red;'>SOLUCIÓN: Ejecuta la migración SQL en phpMyAdmin</strong>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";

// 3. Verificar función obtener_categorias()
echo "<h3>3. Verificando función obtener_categorias()...</h3>";
try {
    if (function_exists('obtener_categorias')) {
        $categorias = obtener_categorias();
        if (!empty($categorias)) {
            echo "✅ Función funciona correctamente<br>";
            echo "   Categorías cargadas: " . count($categorias);
        } else {
            echo "⚠️  Función retorna vacío<br>";
        }
    } else {
        echo "❌ Función obtener_categorias() NO existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en función: " . $e->getMessage();
}

echo "<hr>";

// 4. Test de conexión
echo "<h3>4. Estado de conexión PDO...</h3>";
try {
    $test = $pdo->query("SELECT 1")->fetch();
    echo "✅ Conexión a BD: EXITOSA<br>";
    echo "   Base de datos actual: " . getenv('DB_NAME') ?? 'taf2';
} catch (PDOException $e) {
    echo "❌ Conexión a BD: FALLÓ<br>";
    echo "   Error: " . $e->getMessage();
}

echo "<hr>";
echo "<p style='background:#fffacd; padding:10px; border-radius:5px;'>";
echo "<strong>⚠️ ACCIÓN REQUERIDA:</strong><br>";
echo "Si ves errores de tabla 'categorias' o columna 'id_cat', debes ejecutar la migración SQL:<br>";
echo "<code style='background:#eee; padding:5px;'>migrations_2026-09-01.sql</code>";
echo "</p>";
?>
