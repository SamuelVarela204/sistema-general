/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `taf2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `taf2`;

CREATE TABLE IF NOT EXISTS `detalles_pedido` (
  `id_det_ped` int NOT NULL AUTO_INCREMENT,
  `id_ped` int NOT NULL,
  `id_pro` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_det_ped`),
  KEY `id_ped` (`id_ped`),
  KEY `id_pro` (`id_pro`),
  CONSTRAINT `detalles_ibfk_pedido` FOREIGN KEY (`id_ped`) REFERENCES `pedido` (`id_ped`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ibfk_producto` FOREIGN KEY (`id_pro`) REFERENCES `producto` (`id_pro`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `detalles_pedido` (`id_det_ped`, `id_ped`, `id_pro`, `cantidad`, `precio_unitario`) VALUES
	(1, 1, 1, 2, 6000.00),
	(2, 1, 2, 1, 12500.00),
	(3, 1, 3, 1, 8000.00),
	(4, 2, 1, 1, 6000.00),
	(5, 3, 1, 1, 6000.00),
	(6, 4, 2, 1, 12500.00);

CREATE TABLE IF NOT EXISTS `frutas` (
  `id_fru` int NOT NULL AUTO_INCREMENT,
  `nom_fru` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_fru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `ingredientes_receta` (
  `id_ing_rec` int NOT NULL AUTO_INCREMENT,
  `id_rec` int NOT NULL,
  `id_inv` int NOT NULL,
  `cantidad_necesaria` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_ing_rec`),
  KEY `id_rec` (`id_rec`),
  KEY `id_inv` (`id_inv`),
  CONSTRAINT `ingredientes_ibfk_inventario` FOREIGN KEY (`id_inv`) REFERENCES `inventario` (`id_inv`) ON UPDATE CASCADE,
  CONSTRAINT `ingredientes_ibfk_receta` FOREIGN KEY (`id_rec`) REFERENCES `receta` (`id_rec`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `inventario` (
  `id_inv` int NOT NULL AUTO_INCREMENT,
  `id_pro` int NOT NULL,
  `cantidad_disponible` int NOT NULL DEFAULT '0',
  `ubicacion_bodega` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_inv`),
  KEY `id_pro` (`id_pro`),
  CONSTRAINT `inventario_ibfk_producto` FOREIGN KEY (`id_pro`) REFERENCES `producto` (`id_pro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `pedido` (
  `id_ped` int NOT NULL AUTO_INCREMENT,
  `id_usu` int NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','preparando','enviado','entregado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_ped`),
  KEY `id_usu` (`id_usu`),
  CONSTRAINT `pedido_ibfk_usuarios` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `pedido` (`id_ped`, `id_usu`, `fecha_pedido`, `estado`, `total`) VALUES
	(1, 2, '2026-05-20 07:13:11', 'pendiente', 32500.00),
	(2, 2, '2026-05-20 07:13:50', 'pendiente', 6000.00),
	(3, 1, '2026-05-20 07:14:29', 'pendiente', 6000.00),
	(4, 1, '2026-05-20 07:16:34', 'pendiente', 12500.00);

CREATE TABLE IF NOT EXISTS `producto` (
  `id_pro` int NOT NULL AUTO_INCREMENT,
  `nom_pro` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `categoria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_pro`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `producto` (`id_pro`, `nom_pro`, `descripcion`, `precio`, `stock`, `categoria`) VALUES
	(1, 'Jugo de Naranja 500ml', '100% natural sin azúcar', 6000.00, 50, 'Bebidas'),
	(2, 'Ensalada de Frutas Especial', 'Con helado y queso', 12500.00, 30, 'Platos'),
	(3, 'Porción de Fresas con Crema', 'Fresas frescas del día', 8000.00, 40, 'Postres');

CREATE TABLE IF NOT EXISTS `receta` (
  `id_rec` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `id_usu` int NOT NULL,
  PRIMARY KEY (`id_rec`),
  KEY `id_usu` (`id_usu`),
  CONSTRAINT `receta_ibfk_usuarios` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `roles` (`id_rol`, `nombre_rol`) VALUES
	(1, 'admin'),
	(2, 'cliente');

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usu` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL DEFAULT '2',
  `nom_com` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `usu_con` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `imagen` mediumblob,
  `telefono` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_usu`),
  UNIQUE KEY `correo` (`correo`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `usuarios` (`id_usu`, `id_rol`, `nom_com`, `usu_con`, `imagen`, `telefono`, `correo`, `direccion`, `descripcion`) VALUES
	(1, 1, 'Administrador de Pruebas', '123456', NULL, NULL, 'admin@correo.com', NULL, NULL),
	(2, 2, 'Carlos Mendoza', 'password123', NULL, '3001234567', 'carlos@correo.com', 'Calle 45 # 10-20', NULL);

CREATE TABLE IF NOT EXISTS `usuario_alergias` (
  `id_usu` int NOT NULL,
  `id_fru` int NOT NULL,
  PRIMARY KEY (`id_usu`,`id_fru`),
  KEY `id_fru` (`id_fru`),
  CONSTRAINT `alergias_ibfk_fruta` FOREIGN KEY (`id_fru`) REFERENCES `frutas` (`id_fru`) ON DELETE CASCADE,
  CONSTRAINT `alergias_ibfk_usuario` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `usuario_frutas_favoritas` (
  `id_usu` int NOT NULL,
  `id_fru` int NOT NULL,
  PRIMARY KEY (`id_usu`,`id_fru`),
  KEY `id_fru` (`id_fru`),
  CONSTRAINT `favoritas_ibfk_fruta` FOREIGN KEY (`id_fru`) REFERENCES `frutas` (`id_fru`) ON DELETE CASCADE,
  CONSTRAINT `favoritas_ibfk_usuario` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
