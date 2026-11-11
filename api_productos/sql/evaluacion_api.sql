-- Script de la evaluación: API RESTful de productos
-- Se puede importar directamente desde phpMyAdmin.

CREATE DATABASE IF NOT EXISTS evaluacion_api
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE evaluacion_api;

DROP TABLE IF EXISTS productos;

CREATE TABLE productos (
    id             INT            NOT NULL AUTO_INCREMENT,
    codigo         VARCHAR(50)    NOT NULL,
    nombre         VARCHAR(100)   NOT NULL,
    descripcion    TEXT           NULL,
    precio         DECIMAL(10,2)  NOT NULL,
    stock          INT            NOT NULL DEFAULT 0,
    fecha_registro TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_productos_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de prueba
INSERT INTO productos (codigo, nombre, descripcion, precio, stock) VALUES
('P001', 'Teclado mecánico',     'Teclado mecánico con switches rojos e iluminación RGB',       59.90, 25),
('P002', 'Mouse inalámbrico',    'Mouse ergonómico de 1600 DPI con receptor USB',               24.50, 40),
('P003', 'Monitor 24 pulgadas',  'Monitor Full HD con panel IPS y entrada HDMI',               389.00, 12),
('P004', 'Auriculares con micrófono', 'Auriculares over-ear con cancelación de ruido básica',   79.99, 18),
('P005', 'Disco SSD 500GB',      'Unidad de estado sólido SATA de 500 GB',                      45.00, 30),
('P006', 'Cámara web HD',        NULL,                                                          32.75,  0);
