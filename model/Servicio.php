<?php
class Servicio
{
    public static function listar(): array
    {
        return db()->query('SELECT * FROM servicios ORDER BY nombre')->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM servicios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        $valores = [$d['nombre'], $d['descripcion'], $d['precio'], $d['duracion_min'], $d['estado']];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE servicios SET nombre=?, descripcion=?, precio=?, duracion_min=?, estado=? WHERE id=?');
            $stmt->execute([...$valores, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO servicios (nombre, descripcion, precio, duracion_min, estado) VALUES (?,?,?,?,?)');
            $stmt->execute($valores);
        }
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM servicios WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function activos(): array
    {
        return db()->query('SELECT id, nombre, precio, duracion_min FROM servicios WHERE estado = \'Activo\' ORDER BY nombre')->fetchAll();
    }
}
