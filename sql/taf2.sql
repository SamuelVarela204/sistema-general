CREATE DATABASE IF NOT EXISTS `taf2`;
USE `taf2`;

-- 1. TABLAS INDEPENDIENTES
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` enum('admin','gerente','cajero','cliente') NOT NULL,
  PRIMARY KEY (`id_rol`)
);

INSERT INTO `roles` (`nombre_rol`) VALUES 
('admin'), ('gerente'), ('cajero'), ('cliente');

CREATE TABLE `categorias` (
  `id_cat` int NOT NULL AUTO_INCREMENT,
  `nombre_cat` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cat`)
);

INSERT INTO `categorias` (`nombre_cat`) VALUES 
('Bebidas'), ('Platos'), ('Postres'), ('Insumos'), ('Empaques');

CREATE TABLE `unidades_medida` (
  `id_uni` int NOT NULL AUTO_INCREMENT,
  `nombre_uni` varchar(20) NOT NULL,
  PRIMARY KEY (`id_uni`)
);

INSERT INTO `unidades_medida` (`nombre_uni`) VALUES 
('ml'), ('gramos'), ('unidades'), ('porciones');

-- 2. PRODUCTOS E INSUMOS (Unificado para recetas e inventario)
CREATE TABLE `producto` (
  `id_pro` int NOT NULL AUTO_INCREMENT,
  `nom_pro` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_cat` int NOT NULL,
  `tipo` enum('insumo','producto_final') NOT NULL DEFAULT 'producto_final',
  PRIMARY KEY (`id_pro`),
  FOREIGN KEY (`id_cat`) REFERENCES `categorias`(`id_cat`)
);

INSERT INTO `producto` (`nom_pro`, `descripcion`, `precio`, `id_cat`, `tipo`) VALUES
('Jugo de Naranja 500ml', '100% natural sin azúcar', 6000.00, 1, 'producto_final'),
('Ensalada de Frutas Especial', 'Con helado y queso', 12500.00, 2, 'producto_final'),
('Naranja', 'Fruta fresca para jugos', 0.00, 4, 'insumo'),
('Vaso 500ml', 'Vaso desechable', 0.00, 5, 'insumo');

-- 3. USUARIOS
CREATE TABLE `usuarios` (
  `id_usu` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL DEFAULT 5, -- 5 = cliente por defecto
  `nom_com` varchar(100) NOT NULL,
  `usu_con` varchar(255) NOT NULL, -- Aumentado para password_hash()
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usu`),
  UNIQUE KEY `uq_correo` (`correo`),
  FOREIGN KEY (`id_rol`) REFERENCES `roles`(`id_rol`)
);

-- 4. INVENTARIO (Con alertas de stock mínimo)
CREATE TABLE `inventario` (
  `id_inv` int NOT NULL AUTO_INCREMENT,
  `id_pro` int NOT NULL,
  `cantidad_disponible` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_uni` int NOT NULL,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ubicacion_bodega` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_inv`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`),
  FOREIGN KEY (`id_uni`) REFERENCES `unidades_medida`(`id_uni`)
);

INSERT INTO `inventario` (`id_pro`, `cantidad_disponible`, `id_uni`, `stock_minimo`) VALUES
(3, 50.00, 2, 10.00), -- 50 Naranjas, mínimo 10
(4, 100.00, 3, 20.00); -- 100 Vasos, mínimo 20

-- 5. RECETAS Y PRODUCCIÓN
CREATE TABLE `receta` (
  `id_rec` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `porcion_base` varchar(50) DEFAULT NULL,
  `tiempo_preparacion` int DEFAULT NULL COMMENT 'En minutos',
  `id_usu_creador` int NOT NULL,
  `es_estandar` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_rec`),
  FOREIGN KEY (`id_usu_creador`) REFERENCES `usuarios`(`id_usu`)
);

CREATE TABLE `ingredientes_receta` (
  `id_ing_rec` int NOT NULL AUTO_INCREMENT,
  `id_rec` int NOT NULL,
  `id_pro` int NOT NULL,
  `cantidad_necesaria` decimal(10,2) NOT NULL,
  `id_uni` int NOT NULL,
  PRIMARY KEY (`id_ing_rec`),
  FOREIGN KEY (`id_rec`) REFERENCES `receta`(`id_rec`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`),
  FOREIGN KEY (`id_uni`) REFERENCES `unidades_medida`(`id_uni`)
);

-- 6. VENTAS Y CAJA (POS)
CREATE TABLE `pedido` (
  `id_ped` int NOT NULL AUTO_INCREMENT,
  `id_usu_cliente` int DEFAULT NULL, -- Puede ser null si es venta mostrador sin registro
  `id_usu_cajero` int NOT NULL, -- Quien procesó la venta (Requisito SENA)
  `fecha_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','preparando','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') DEFAULT 'efectivo',
  `tipo_factura` enum('fisica','digital','ninguna') DEFAULT 'ninguna',
  PRIMARY KEY (`id_ped`),
  FOREIGN KEY (`id_usu_cliente`) REFERENCES `usuarios`(`id_usu`),
  FOREIGN KEY (`id_usu_cajero`) REFERENCES `usuarios`(`id_usu`)
);

CREATE TABLE `detalles_pedido` (
  `id_det_ped` int NOT NULL AUTO_INCREMENT,
  `id_ped` int NOT NULL,
  `id_pro` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `notas_personalizacion` varchar(255) DEFAULT NULL COMMENT 'Ej: sin azúcar, extra fruta',
  PRIMARY KEY (`id_det_ped`),
  FOREIGN KEY (`id_ped`) REFERENCES `pedido`(`id_ped`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`)
);

-- 7. MERMAS (Requisito crítico para perecederos)
CREATE TABLE `mermas` (
  `id_mer` int NOT NULL AUTO_INCREMENT,
  `id_pro` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `id_uni` int NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usu_registro` int NOT NULL,
  PRIMARY KEY (`id_mer`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`),
  FOREIGN KEY (`id_uni`) REFERENCES `unidades_medida`(`id_uni`),
  FOREIGN KEY (`id_usu_registro`) REFERENCES `usuarios`(`id_usu`)
);

-- 8. PREFERENCIAS DE USUARIO
CREATE TABLE `usuario_alergias` (
  `id_usu` int NOT NULL,
  `id_pro` int NOT NULL, -- Cambiado de frutas a producto para mayor flexibilidad
  PRIMARY KEY (`id_usu`,`id_pro`),
  FOREIGN KEY (`id_usu`) REFERENCES `usuarios`(`id_usu`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`)
);