<?php
// Auto-migración: Ejecuta la migración si es necesario
require_once 'taf2/conexion.php';

// Verificar si la tabla categorias existe
$check = $pdo->query("SHOW TABLES LIKE 'categorias'");
$tabla_existe = $check->rowCount() > 0;

if (!$tabla_existe) {
    echo "<div style='background:#fff3cd; padding:15px; margin:20px; border-radius:5px;'>";
    echo "<h2>🔄 Ejecutando migración automática...</h2>";
    
    try {
        // Crear tabla categorias
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categorias (
                id_cat INT NOT NULL AUTO_INCREMENT,
                nombre_cat VARCHAR(100) NOT NULL UNIQUE,
                descripcion VARCHAR(255),
                estado VARCHAR(20) NOT NULL DEFAULT 'activo',
                PRIMARY KEY (id_cat)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        echo "✅ Tabla 'categorias' creada<br>";
        
        // Insertar categorías
        $pdo->exec("
            INSERT IGNORE INTO categorias (id_cat, nombre_cat, descripcion, estado) VALUES
            (1, 'Bebidas', 'Jugos y bebidas naturales', 'activo'),
            (2, 'Platos', 'Platos principales y combinados', 'activo'),
            (3, 'Postres', 'Postres y frutas con acompañamientos', 'activo'),
            (4, 'Ensaladas', 'Ensaladas frescas y saludables', 'activo'),
            (5, 'Productos', 'Productos varios', 'activo');
        ");
        echo "✅ Categorías predefinidas insertadas<br>";
        
        // Verificar si ya existe la columna id_cat en producto
        $columnas = $pdo->query("DESCRIBE producto")->fetchAll();
        $tiene_id_cat = false;
        
        foreach ($columnas as $col) {
            if ($col['Field'] === 'id_cat') {
                $tiene_id_cat = true;
                break;
            }
        }
        
        if (!$tiene_id_cat) {
            // Agregar columna id_cat
            $pdo->exec("ALTER TABLE producto ADD COLUMN id_cat INT NOT NULL DEFAULT 1");
            echo "✅ Columna 'id_cat' agregada a tabla 'producto'<br>";
            
            // Agregar constraint
            try {
                $pdo->exec("ALTER TABLE producto ADD CONSTRAINT producto_ibfk_categorias 
                    FOREIGN KEY (id_cat) REFERENCES categorias (id_cat) 
                    ON UPDATE CASCADE");
                echo "✅ Relación de clave foránea creada<br>";
            } catch (Exception $e) {
                echo "⚠️  Constraint ya existe<br>";
            }
        } else {
            echo "ℹ️  Columna 'id_cat' ya existe<br>";
        }
        
        echo "<p style='color:green; font-weight:bold;'>✅ ¡Migración completada exitosamente!</p>";
        echo "<p>Recargando página en 3 segundos...</p>";
        echo "<script>setTimeout(() => location.reload(), 3000);</script>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red;'><strong>❌ Error en migración:</strong><br>";
        echo htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";
} else {
    // La tabla ya existe, no hacer nada
}
?>
