CREATE DATABASE `taf2`;
USE `taf2`;

-- 1. TABLAS INDEPENDIENTES (Sin Claves Foráneas)
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`)
);

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
	(1, 'admin'),
	(2, 'cliente');

CREATE TABLE `frutas` (
  `id_fru` int NOT NULL AUTO_INCREMENT,
  `nom_fru` varchar(100) NOT NULL,
  PRIMARY KEY (`id_fru`)
);

CREATE TABLE `producto` (
  `id_pro` int NOT NULL AUTO_INCREMENT,
  `nom_pro` varchar(225) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  PRIMARY KEY (`id_pro`)
);

INSERT INTO `producto` (`id_pro`, `nom_pro`, `descripcion`, `precio`, `categoria`) VALUES
	(1, 'Jugo de Naranja 500ml', '100% natural sin azúcar', 6000.00, 'Bebidas'),
	(2, 'Ensalada de Frutas Especial', 'Con helado y queso', 12500.00, 'Platos'),
	(3, 'Porción de Fresas con Crema', 'Fresas frescas del día', 8000.00, 'Postres');

-- 2. TABLAS DE PRIMER NIVEL DE DEPENDENCIA
CREATE TABLE `usuarios` (
  `id_usu` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL DEFAULT '2',
  `nom_com` varchar(225) NOT NULL,
  `usu_con` varchar(225) NOT NULL,
  `imagen` mediumblob,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(225) NOT NULL,
  `direccion` varchar(225) DEFAULT NULL,
  `descripcion` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id_usu`),
  UNIQUE KEY `uq_correo` (`correo`),
  FOREIGN KEY (`id_rol`) REFERENCES `roles`(`id_rol`)
);

INSERT INTO `usuarios` (`id_usu`, `id_rol`, `nom_com`, `usu_con`, `imagen`, `telefono`, `correo`, `direccion`, `descripcion`) VALUES
	(1, 1, 'Administrador de Pruebas', '123456', NULL, NULL, 'admin@correo.com', NULL, NULL),
	(2, 2, 'Carlos Mendoza', 'password123', NULL, '3001234567', 'carlos@correo.com', 'Calle 45 # 10-20', NULL);

CREATE TABLE `inventario` (
  `id_inv` int NOT NULL AUTO_INCREMENT,
  `id_pro` int NOT NULL,
  `cantidad_disponible` int NOT NULL DEFAULT '0',
  `ubicacion_bodega` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_inv`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`)
);

INSERT INTO `inventario` (`id_pro`, `cantidad_disponible`) VALUES
(1, 50), (2, 30), (3, 40);

-- 3. TABLAS DE SEGUNDO NIVEL DE DEPENDENCIAS
CREATE TABLE `pedido` (
  `id_ped` int NOT NULL AUTO_INCREMENT,
  `id_usu` int NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','preparando','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id_ped`),
  FOREIGN KEY (`id_usu`) REFERENCES `usuarios`(`id_usu`)
);

INSERT INTO `pedido` (`id_ped`, `id_usu`, `fecha_pedido`, `estado`) VALUES
	(1, 2, '2026-05-20 07:13:11', 'pendiente'),
	(2, 2, '2026-05-20 07:13:50', 'pendiente'),
	(3, 1, '2026-05-20 07:14:29', 'pendiente'),
	(4, 1, '2026-05-20 07:16:34', 'pendiente');

CREATE TABLE `receta` (
  `id_rec` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(225) NOT NULL,
  `descripcion` text,
  `id_usu` int NOT NULL,
  PRIMARY KEY (`id_rec`),
  FOREIGN KEY (`id_usu`) REFERENCES `usuarios`(`id_usu`)
);

-- 4. TABLAS PIVOTE / TRANSACCIONALES
CREATE TABLE `detalles_pedido` (
  `id_det_ped` int NOT NULL AUTO_INCREMENT,
  `id_ped` int NOT NULL,
  `id_pro` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_det_ped`),
  FOREIGN KEY (`id_ped`) REFERENCES `pedido`(`id_ped`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`)
);

INSERT INTO `detalles_pedido` (`id_det_ped`, `id_ped`, `id_pro`, `cantidad`, `precio_unitario`) VALUES
	(1, 1, 1, 2, 6000.00),
	(2, 1, 2, 1, 12500.00),
	(3, 1, 3, 1, 8000.00),
	(4, 2, 1, 1, 6000.00),
	(5, 3, 1, 1, 6000.00),
	(6, 4, 2, 1, 12500.00);

CREATE TABLE `ingredientes_receta` (
  `id_ing_rec` int NOT NULL AUTO_INCREMENT,
  `id_rec` int NOT NULL,
  `id_pro` int NOT NULL,
  `cantidad_necesaria` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_ing_rec`),
  FOREIGN KEY (`id_rec`) REFERENCES `receta`(`id_rec`),
  FOREIGN KEY (`id_pro`) REFERENCES `producto`(`id_pro`)
);

CREATE TABLE `usuario_alergias` (
  `id_usu` int NOT NULL,
  `id_fru` int NOT NULL,
  PRIMARY KEY (`id_usu`,`id_fru`),
  FOREIGN KEY (`id_usu`) REFERENCES `usuarios`(`id_usu`),
  FOREIGN KEY (`id_fru`) REFERENCES `frutas`(`id_fru`)
);

CREATE TABLE `usuario_frutas_favoritas` (
  `id_usu` int NOT NULL,
  `id_fru` int NOT NULL,
  PRIMARY KEY (`id_usu`,`id_fru`),
  FOREIGN KEY (`id_usu`) REFERENCES `usuarios`(`id_usu`),
  FOREIGN KEY (`id_fru`) REFERENCES `frutas`(`id_fru`)
);