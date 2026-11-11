<?php
/**
 * Modelo: contiene únicamente las consultas SQL sobre la tabla productos.
 * Todas las consultas usan sentencias preparadas (PDO).
 */
class Producto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listar(): array
    {
        $sql = 'SELECT id, nombre, categoria, precio FROM productos ORDER BY id ASC';
        return $this->db->query($sql)->fetchAll();
    }

    // Busca por código (si el término es numérico) o por nombre parcial
    public function buscar(string $termino): array
    {
        $sql = 'SELECT id, nombre, categoria, precio FROM productos
                WHERE id = :id OR nombre LIKE :nombre
                ORDER BY id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'     => ctype_digit($termino) ? (int) $termino : 0,
            ':nombre' => '%' . $termino . '%',
        ]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nombre, categoria, precio FROM productos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $producto = $stmt->fetch();
        return $producto ?: null;
    }

    public function crear(string $nombre, string $categoria, string $precio): int
    {
        $sql = 'INSERT INTO productos (nombre, categoria, precio)
                VALUES (:nombre, :categoria, :precio)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'    => $nombre,
            ':categoria' => $categoria,
            ':precio'    => $precio,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, string $nombre, string $categoria, string $precio): bool
    {
        $sql = 'UPDATE productos
                SET nombre = :nombre, categoria = :categoria, precio = :precio
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $nombre,
            ':categoria' => $categoria,
            ':precio'    => $precio,
            ':id'        => $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM productos WHERE id = :id');
        return $stmt->execute([':id' => $id]) && $stmt->rowCount() > 0;
    }
}
