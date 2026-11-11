<?php
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Producto.php';
require_once __DIR__ . '/controllers/ProductoController.php';

// Acciones permitidas (lista blanca para evitar llamadas arbitrarias)
$acciones = ['listar', 'buscar', 'nuevo', 'guardar', 'editar', 'actualizar', 'eliminar'];
$accion = $_REQUEST['accion'] ?? 'listar';
if (!in_array($accion, $acciones, true)) {
    $accion = 'listar';
}

$controlador = new ProductoController();
$controlador->$accion();
