CREATE DATABASE IF NOT EXISTS taf2;
USE taf2;

-- =========================================
-- TABLA: roles
-- =========================================

CREATE TABLE IF NOT EXISTS roles (
    id_rol INT NOT NULL AUTO_INCREMENT,
    nombre_rol VARCHAR(50) NOT NULL,

    PRIMARY KEY (id_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO roles (id_rol, nombre_rol) VALUES
(1, 'admin'),
(2, 'cliente'),
(3, 'inventario'),
(4, 'gerente');


-- =========================================
-- TABLA: usuarios
-- =========================================

CREATE TABLE IF NOT EXISTS usuarios (
    id_usu INT NOT NULL AUTO_INCREMENT,
    id_rol INT NOT NULL DEFAULT 2,
    nom_com VARCHAR(225) NOT NULL,
    usu_con VARCHAR(225) NOT NULL,
    imagen MEDIUMBLOB,
    telefono VARCHAR(15),
    correo VARCHAR(225) NOT NULL,
    direccion VARCHAR(225),
    descripcion VARCHAR(225),
    estado VARCHAR(20) NOT NULL DEFAULT 'activo',

    PRIMARY KEY (id_usu),
    UNIQUE KEY correo (correo),
    KEY id_rol (id_rol),

    CONSTRAINT usuarios_ibfk_roles
        FOREIGN KEY (id_rol)
        REFERENCES roles (id_rol)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO usuarios
(id_usu, id_rol, nom_com, usu_con, imagen, telefono, correo, direccion, descripcion, estado)
VALUES
(1, 1, 'Administrador de Pruebas', '123456', NULL, NULL,
 'admin@correo.com', NULL, 'Rol administrativo', 'activo'),

(2, 2, 'Carlos Mendoza', 'cliente123', NULL, '3001234567',
 'carlos@correo.com', 'Calle 45 # 10-20', 'Cliente frecuente', 'activo'),

(3, 3, 'María López', 'inventario123', NULL, '3012345678',
 'inventario@correo.com', 'Carrera 8 # 12-34', 'Encargada de inventario', 'activo'),

(4, 4, 'Sofía Ramírez', 'gerente123', NULL, '3034567890',
 'gerente@correo.com', 'Calle 90 # 15-06', 'Gerente de operaciones', 'activo');


-- =========================================
-- TABLA: frutas
-- =========================================

CREATE TABLE IF NOT EXISTS frutas (
    id_fru INT NOT NULL AUTO_INCREMENT,
    nom_fru VARCHAR(100) NOT NULL,

    PRIMARY KEY (id_fru)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================
-- TABLA: categorias
-- =========================================

CREATE TABLE IF NOT EXISTS categorias (
    id_cat INT NOT NULL AUTO_INCREMENT,
    nombre_cat VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado VARCHAR(20) NOT NULL DEFAULT 'activo',

    PRIMARY KEY (id_cat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categorias (id_cat, nombre_cat, descripcion, estado) VALUES
(1, 'Bebidas', 'Jugos y bebidas naturales', 'activo'),
(2, 'Platos', 'Platos principales y combinados', 'activo'),
(3, 'Postres', 'Postres y frutas con acompañamientos', 'activo'),
(4, 'Ensaladas', 'Ensaladas frescas y saludables', 'activo'),
(5, 'Productos', 'Productos varios', 'activo');


-- =========================================
-- TABLA: producto
-- =========================================

CREATE TABLE IF NOT EXISTS producto (
    id_pro INT NOT NULL AUTO_INCREMENT,
    nom_pro VARCHAR(225) NOT NULL,
    descripcion VARCHAR(100),
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    id_cat INT NOT NULL,

    PRIMARY KEY (id_pro),
    KEY id_cat (id_cat),

    CONSTRAINT producto_ibfk_categorias
        FOREIGN KEY (id_cat)
        REFERENCES categorias (id_cat)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT IGNORE INTO producto
(id_pro, nom_pro, descripcion, precio, stock, id_cat)
VALUES
(1, 'Jugo de Naranja 500ml',
 '100% natural sin azúcar',
 6000.00, 50, 1),

(2, 'Ensalada de Frutas Especial',
 'Con helado y queso',
 12500.00, 30, 2),

(3, 'Porción de Fresas con Crema',
 'Fresas frescas del día',
 8000.00, 40, 3);


-- =========================================
-- TABLA: inventario
-- =========================================

CREATE TABLE IF NOT EXISTS inventario (
    id_inv INT NOT NULL AUTO_INCREMENT,
    id_pro INT NOT NULL,
    cantidad_disponible INT NOT NULL DEFAULT 0,
    ubicacion_bodega VARCHAR(100),

    PRIMARY KEY (id_inv),
    KEY id_pro (id_pro),

    CONSTRAINT inventario_ibfk_producto
        FOREIGN KEY (id_pro)
        REFERENCES producto (id_pro)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================
-- TABLA: pedido
-- =========================================

CREATE TABLE IF NOT EXISTS pedido (
    id_ped INT NOT NULL AUTO_INCREMENT,
    id_usu INT NOT NULL,
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    estado ENUM(
        'pendiente',
        'preparando',
        'enviado',
        'entregado',
        'cancelado'
    ) NOT NULL DEFAULT 'pendiente',

    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    PRIMARY KEY (id_ped),
    KEY id_usu (id_usu),

    CONSTRAINT pedido_ibfk_usuarios
        FOREIGN KEY (id_usu)
        REFERENCES usuarios (id_usu)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO pedido
(id_ped, id_usu, fecha_pedido, estado, total)
VALUES
(1, 2, '2026-05-20 07:13:11', 'pendiente', 32500.00),
(2, 2, '2026-05-20 07:13:50', 'pendiente', 6000.00),
(3, 1, '2026-05-20 07:14:29', 'pendiente', 6000.00),
(4, 1, '2026-05-20 07:16:34', 'pendiente', 12500.00);


-- =========================================
-- TABLA: detalles_pedido
-- =========================================

CREATE TABLE IF NOT EXISTS detalles_pedido (
    id_det_ped INT NOT NULL AUTO_INCREMENT,
    id_ped INT NOT NULL,
    id_pro INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_det_ped),
    KEY id_ped (id_ped),
    KEY id_pro (id_pro),

    CONSTRAINT detalles_ibfk_pedido
        FOREIGN KEY (id_ped)
        REFERENCES pedido (id_ped)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT detalles_ibfk_producto
        FOREIGN KEY (id_pro)
        REFERENCES producto (id_pro)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO detalles_pedido
(id_det_ped, id_ped, id_pro, cantidad, precio_unitario)
VALUES
(1, 1, 1, 2, 6000.00),
(2, 1, 2, 1, 12500.00),
(3, 1, 3, 1, 8000.00),
(4, 2, 1, 1, 6000.00),
(5, 3, 1, 1, 6000.00),
(6, 4, 2, 1, 12500.00);


-- =========================================
-- TABLA: receta
-- =========================================

CREATE TABLE IF NOT EXISTS receta (
    id_rec INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(225) NOT NULL,
    descripcion TEXT,
    id_usu INT NOT NULL,

    PRIMARY KEY (id_rec),
    KEY id_usu (id_usu),

    CONSTRAINT receta_ibfk_usuarios
        FOREIGN KEY (id_usu)
        REFERENCES usuarios (id_usu)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================
-- TABLA: ingredientes_receta
-- =========================================

CREATE TABLE IF NOT EXISTS ingredientes_receta (
    id_ing_rec INT NOT NULL AUTO_INCREMENT,
    id_rec INT NOT NULL,
    id_inv INT NOT NULL,
    cantidad_necesaria DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_ing_rec),
    KEY id_rec (id_rec),
    KEY id_inv (id_inv),

    CONSTRAINT ingredientes_ibfk_inventario
        FOREIGN KEY (id_inv)
        REFERENCES inventario (id_inv)
        ON UPDATE CASCADE,

    CONSTRAINT ingredientes_ibfk_receta
        FOREIGN KEY (id_rec)
        REFERENCES receta (id_rec)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================
-- TABLA: global_settings
-- =========================================

CREATE TABLE IF NOT EXISTS global_settings (
    id INT NOT NULL,
    glob_wall MEDIUMBLOB DEFAULT NULL,
    glob_mime VARCHAR(50) DEFAULT NULL,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO global_settings (id)
VALUES (1);


-- =========================================
-- TABLA: usuario_alergias
-- =========================================

CREATE TABLE IF NOT EXISTS usuario_alergias (
    id_usu INT NOT NULL,
    id_fru INT NOT NULL,

    PRIMARY KEY (id_usu, id_fru),
    KEY id_fru (id_fru),

    CONSTRAINT alergias_ibfk_fruta
        FOREIGN KEY (id_fru)
        REFERENCES frutas (id_fru)
        ON DELETE CASCADE,

    CONSTRAINT alergias_ibfk_usuario
        FOREIGN KEY (id_usu)
        REFERENCES usuarios (id_usu)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================
-- TABLA: usuario_frutas_favoritas
-- =========================================

CREATE TABLE IF NOT EXISTS usuario_frutas_favoritas (
    id_usu INT NOT NULL,
    id_fru INT NOT NULL,

    PRIMARY KEY (id_usu, id_fru),
    KEY id_fru (id_fru),

    CONSTRAINT favoritas_ibfk_fruta
        FOREIGN KEY (id_fru)
        REFERENCES frutas (id_fru)
        ON DELETE CASCADE,

    CONSTRAINT favoritas_ibfk_usuario
        FOREIGN KEY (id_usu)
        REFERENCES usuarios (id_usu)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;