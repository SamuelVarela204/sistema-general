-- ========================================
-- MIGRACIÓN: Implementar Roles y Categorías
-- Fecha: 2026-09-01
-- ========================================

USE taf2;

-- PASO 1: Crear tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id_cat INT NOT NULL AUTO_INCREMENT,
    nombre_cat VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado VARCHAR(20) NOT NULL DEFAULT 'activo',

    PRIMARY KEY (id_cat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PASO 2: Insertar categorías predefinidas
INSERT IGNORE INTO categorias (id_cat, nombre_cat, descripcion, estado) VALUES
(1, 'Bebidas', 'Jugos y bebidas naturales', 'activo'),
(2, 'Platos', 'Platos principales y combinados', 'activo'),
(3, 'Postres', 'Postres y frutas con acompañamientos', 'activo'),
(4, 'Ensaladas', 'Ensaladas frescas y saludables', 'activo'),
(5, 'Productos', 'Productos varios', 'activo');

-- PASO 3: Modificar tabla producto para agregar id_cat
-- Primero verificar si la columna categoria existe
SET @dbname = DATABASE();
SET @tablename = 'producto';
SET @columnname = 'categoria';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE (table_name = @tablename) AND (table_schema = @dbname) 
     AND (column_name = @columnname)) > 0,
    "SELECT 1",
    CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN id_cat INT NOT NULL DEFAULT 1")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- PASO 4: Agregar constraint si no existe
ALTER TABLE producto ADD CONSTRAINT producto_ibfk_categorias 
FOREIGN KEY (id_cat) REFERENCES categorias (id_cat) 
ON UPDATE CASCADE;

-- PASO 5: Actualizar la estructura si es necesario
-- Si aún existe la columna 'categoria' (VARCHAR), primero hacer el migration de datos
-- Mapear categorías antiguas a nuevas
UPDATE producto SET id_cat = 1 WHERE categoria = 'Bebidas' AND id_cat = 1;
UPDATE producto SET id_cat = 2 WHERE categoria = 'Platos' AND id_cat = 1;
UPDATE producto SET id_cat = 3 WHERE categoria = 'Postres' AND id_cat = 1;
UPDATE producto SET id_cat = 4 WHERE categoria = 'Ensaladas' AND id_cat = 1;

-- PASO 6: Eliminar columna categoria si existe (descomentar cuando confirmes que funciona)
-- ALTER TABLE producto DROP COLUMN categoria;

-- ========================================
-- NOTAS IMPORTANTES:
-- ========================================
-- 1. Los roles ya existen en la BD (admin, cliente, inventario, gerente)
-- 2. Se han agregado funciones de validación en controlador.php
-- 3. Se ha agregado validación de permisos en procesar.php
-- 4. La página de productos.php ahora usa un dropdown de categorías
-- 5. Solo los usuarios con rol 'admin' o 'inventario' pueden crear productos
-- 6. Solo los usuarios con rol 'admin' pueden crear usuarios

-- ========================================
-- PERMISOS POR ROL:
-- ========================================
-- ADMIN: Acceso a todo
-- INVENTARIO: Crear/editar productos, crear pedidos
-- GERENTE: Ver reportes y estado de inventario (solo lectura)
-- CLIENTE: Solo ver productos y crear pedidos
