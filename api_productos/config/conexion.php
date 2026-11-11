<?php
// Configuración y conexión a la base de datos.
// Las credenciales se definen solo aquí; los demás archivos únicamente
// llaman a obtenerConexion().

define('DB_HOST', 'localhost');
define('DB_NAME', 'evaluacion_api');
define('DB_USER', 'root');
define('DB_PASS', '');        // En XAMPP el usuario root viene sin contraseña
define('DB_CHARSET', 'utf8mb4');

function obtenerConexion(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // los errores lanzan excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,                    // consultas preparadas reales
    ];

    return new PDO($dsn, DB_USER, DB_PASS, $opciones);
}
