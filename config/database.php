<?php
/**
 * Conexión a MySQL mediante PDO (patrón singleton simple).
 * Ajuste las credenciales si su XAMPP usa otra configuración.
 */
class Database
{
    private const HOST = 'localhost';
    private const NOMBRE = 'evaluacion_mvc';
    private const USUARIO = 'root';
    private const CLAVE = '';

    private static ?PDO $conexion = null;

    public static function getConnection(): PDO
    {
        if (self::$conexion === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::NOMBRE . ';charset=utf8mb4';
            try {
                self::$conexion = new PDO($dsn, self::USUARIO, self::CLAVE, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $ex) {
                die('Error de conexión con la base de datos. Verifique que MySQL esté activo '
                    . 'y que la base de datos evaluacion_mvc haya sido importada.');
            }
        }
        return self::$conexion;
    }
}
