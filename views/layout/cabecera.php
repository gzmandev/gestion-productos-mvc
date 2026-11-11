<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Gestión de productos') ?> - MVC</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark navbar-marca">
    <div class="container">
        <span class="navbar-brand d-flex align-items-center">
            <i class="bi bi-box-seam me-2"></i>Gestión de Productos
        </span>
        <span class="navbar-text small">MVC · PHP · PDO</span>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <div class="card tarjeta">
        <div class="card-body p-4 p-lg-5">
            <h1 class="titulo-principal mb-4">GESTIÓN DE PRODUCTOS - MVC</h1>

            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <?php
                    $clase = ['exito' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['tipo']] ?? 'info';
                    $icono = ['exito' => 'bi-check-circle-fill', 'error' => 'bi-exclamation-triangle-fill', 'info' => 'bi-info-circle-fill'][$flash['tipo']] ?? 'bi-info-circle-fill';
                ?>
                <div class="alert alert-<?= $clase ?> d-flex align-items-center" role="alert">
                    <i class="bi <?= $icono ?> me-2"></i>
                    <div><?= e($flash['texto']) ?></div>
                </div>
            <?php endif; ?>
