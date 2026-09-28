-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 28-09-2026 a las 18:15:48
-- Versión del servidor: 8.0.17
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pizzeria_lanapo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, '130.00', '130.00'),
(2, 1, 2, 1, '140.00', '140.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `insumo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT '5.00',
  `ultima_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `insumo`, `cantidad`, `unidad_medida`, `stock_minimo`) VALUES
(1, 'Masa para Pizza', '60.00', 'Piezas', '10.00'),
(2, 'Queso Mozzarella', '15.00', 'Kg', '3.00'),
(3, 'Pepperoni', '8.00', 'Kg', '2.00'),
(4, 'Cajas para Pizza 12\"', '100.00', 'Piezas', '20.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `categoria` enum('Pizza','Promocion','Bebida','Otro') COLLATE utf8mb4_unicode_ci DEFAULT 'Pizza',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `estado`, `fecha_creacion`) VALUES
(1, 'Pizza Margarita 12\"', 'Salsa de tomate, queso mozzarella y albahaca fresca', '130.00', 'Pizza', 'Activo', '2026-09-08 18:23:24'),
(2, 'Pizza Pepperoni 12\"', 'Salsa de tomate, queso mozzarella y abundante pepperoni', '140.00', 'Pizza', 'Activo', '2026-09-08 18:23:24'),
(3, 'Pizza Hawaiana 12\"', 'Salsa de tomate, queso mozzarella, jamón y piña', '140.00', 'Pizza', 'Activo', '2026-09-08 18:23:24'),
(4, 'Promo 1: 2 Margaritas', 'Paquete de 2 Pizzas Margaritas de 12\"', '219.00', 'Promocion', 'Activo', '2026-09-08 18:23:24'),
(5, 'Promo 2: 2 Pepperonis', 'Paquete de 2 Pizzas Pepperonis de 12\"', '229.00', 'Promocion', 'Activo', '2026-09-08 18:23:24'),
(6, 'Promo 3: 2 Hawaianas', 'Paquete de 2 Pizzas Hawaianas de 12\"', '229.00', 'Promocion', 'Activo', '2026-09-08 18:23:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('Admin','Cajero','Repartidor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cajero',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `rol`, `fecha_creacion`) VALUES
(1, 'Sebastian Admin', 'admin', '$2b$10$yLR6lk8ZzeU3CLPAL2caT.pIrNAKRW9nkbYcKq5eK/J21BSqPAk.S', 'Admin', '2026-09-08 18:13:42'),
(2, 'Diego Cajero', 'diego', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.0q/sW4/p94G0xJqYh.70yM72aK', 'Cajero', '2026-09-08 18:13:42'),
(3, 'Arath Ramirez', 'Arath', '$2y$10$93kH6SkuBQkYXuEmn3lxYuC1DjeeE54.rcp7ZVPGvglWOWVnTr/lW', 'Cajero', '2026-09-08 18:36:41'),
(4, 'Kevin Vilchis', 'Kevin', '$2y$10$FNhwMqZzc9SOb7PsbQTUguvYIdCm2uVsX2X.45X4DMTzEGDcaPBYe', 'Admin', '2026-09-08 19:01:08'),
(5, 'Jose Blancas', 'Jose', '$2y$10$RWzdRs5KNVi8KOgWQZCHUulAwijiaojpAwOMY2SeUhqrUY9RuATPu', 'Admin', '2026-09-08 19:46:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_pedido` enum('Mostrador','Domicilio') COLLATE utf8mb4_unicode_ci DEFAULT 'Mostrador',
  `estatus` enum('En Horno','En Camino','Entregado','Cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'En Horno',
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `tipo_pedido`, `estatus`, `total`, `fecha_venta`) VALUES
(1, 1, 'Mostrador', 'En Horno', '270.00', '2026-09-08 18:34:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
