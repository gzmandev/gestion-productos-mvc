<?php
/**
 * Funciones auxiliares generales de la aplicación.
 */

// Escapa texto para imprimirlo de forma segura en HTML
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Guarda un mensaje para mostrarlo en la siguiente página (patrón PRG)
function setFlash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Redirige a una acción del index.php y detiene la ejecución
function redirigir(string $accion): void
{
    header('Location: index.php?accion=' . urlencode($accion));
    exit;
}
