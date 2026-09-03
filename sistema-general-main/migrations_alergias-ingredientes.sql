-- ========================================
-- MIGRACIÓN: Sistema de Alergias e Ingredientes
-- Fecha: 2026-09-01
-- ========================================

USE taf2;

-- PASO 1: Crear tabla de ingredientes (si no existe)
CREATE TABLE IF NOT EXISTS ingredientes (
    id_ing INT NOT NULL AUTO_INCREMENT,
    nombre_ing VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    unidad_medida VARCHAR(20) DEFAULT 'kg',
    stock_actual DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_minimo DECIMAL(10,2) NOT NULL DEFAULT 0,
    precio_unitario DECIMAL(10,2),
    estado VARCHAR(20) NOT NULL DEFAULT 'activo',

    PRIMARY KEY (id_ing)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PASO 2: Insertar ingredientes comunes si la tabla está vacía
INSERT IGNORE INTO ingredientes (id_ing, nombre_ing, descripcion, unidad_medida, stock_actual, precio_unitario, estado) VALUES
(1, 'Naranja', 'Naranjas frescas', 'kg', 50, 2500, 'activo'),
(2, 'Fresa', 'Fresas frescas', 'kg', 30, 8000, 'activo'),
(3, 'Banano', 'Bananos frescos', 'kg', 40, 1500, 'activo'),
(4, 'Manzana', 'Manzanas rojas', 'kg', 35, 3000, 'activo'),
(5, 'Kiwi', 'Kiwis frescos', 'kg', 25, 4000, 'activo'),
(6, 'Mango', 'Mangos frescos', 'kg', 20, 3500, 'activo'),
(7, 'Piña', 'Piñas frescas', 'kg', 15, 5000, 'activo'),
(8, 'Mora', 'Moras frescas', 'kg', 18, 12000, 'activo'),
(9, 'Arándano', 'Arándanos frescos', 'kg', 12, 15000, 'activo'),
(10, 'Cacao', 'Cacao en polvo', 'kg', 10, 25000, 'activo'),
(11, 'Leche', 'Leche descremada', 'L', 100, 2000, 'activo'),
(12, 'Yogurt', 'Yogurt natural', 'kg', 50, 8000, 'activo'),
(13, 'Miel', 'Miel pura', 'kg', 8, 20000, 'activo'),
(14, 'Granola', 'Granola artesanal', 'kg', 20, 12000, 'activo'),
(15, 'Almendra', 'Almendras naturales', 'kg', 5, 35000, 'activo');

-- PASO 3: Agregar columna id_ing a tabla receta si no existe
SET @dbname = DATABASE();
SET @tablename = 'receta';
SET @columnname = 'id_ing_principal';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE (table_name = @tablename) AND (table_schema = @dbname) 
     AND (column_name = @columnname)) > 0,
    "SELECT 1",
    CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN id_ing_principal INT DEFAULT 1")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- PASO 4: Llenar tabla frutas si está vacía
INSERT IGNORE INTO frutas (id_fru, nom_fru) VALUES
(1, 'Naranja'),
(2, 'Fresa'),
(3, 'Banano'),
(4, 'Manzana'),
(5, 'Kiwi'),
(6, 'Mango'),
(7, 'Piña'),
(8, 'Mora'),
(9, 'Arándano'),
(10, 'Cacao'),
(11, 'Leche'),
(12, 'Yogurt');

-- ========================================
-- NOTAS:
-- ========================================
-- 1. Se ha creado tabla 'ingredientes' para gestionar stock global
-- 2. Se han insertado frutas/ingredientes comunes
-- 3. Se puede usar 'id_ing' en recetas para rastrear ingredientes principales
-- 4. El sistema de alergias usa 'usuario_alergias' (ya existente)
-- 5. Los ingredientes de recetas usan 'ingredientes_receta' (ya existente)
