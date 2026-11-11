-- Script de base de datos: Gestión de productos (MVC)
-- Importar en phpMyAdmin (XAMPP) o ejecutar desde la consola de MySQL.

CREATE DATABASE IF NOT EXISTS evaluacion_mvc
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE evaluacion_mvc;

DROP TABLE IF EXISTS productos;

CREATE TABLE productos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100)  NOT NULL,
    categoria VARCHAR(80)   NOT NULL,
    precio    DECIMAL(10,2) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO productos (nombre, categoria, precio) VALUES
    -- Productos originales (se conservan)
    ('Teclado',            'Accesorios',   85.00),
    ('Monitor',            'Pantallas',   650.00),
    ('Mouse',              'Accesorios',   45.00),
    ('Audífonos',          'Accesorios',  120.00),
    ('Impresora láser',    'Impresoras',  780.50),
    -- Productos adicionales (15)
    ('Laptop 14 pulgadas', 'Computadoras', 2499.90),
    ('Teclado mecánico',   'Accesorios',    180.00),
    ('Monitor curvo 27 pulgadas', 'Pantallas', 980.00),
    ('Webcam HD 1080p',    'Accesorios',   150.00),
    ('Disco SSD 1 TB',     'Almacenamiento', 420.00),
    ('Memoria USB 64 GB',  'Almacenamiento',  45.00),
    ('Router Wi-Fi 6',     'Redes',        260.00),
    ('Cable HDMI 2 m',     'Accesorios',    25.90),
    ('Parlante Bluetooth', 'Audio',        210.00),
    ('Micrófono USB',      'Audio',        135.00),
    ('Tarjeta gráfica 8 GB', 'Componentes', 1890.00),
    ('Lámpara LED de escritorio', 'Iluminación', 89.90),
    ('Silla ergonómica',   'Oficina',      780.00),
    ('Escritorio 120 cm',  'Oficina',      450.00),
    ('Cargador portátil 20000 mAh', 'Energía', 115.00);
