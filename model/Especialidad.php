<?php
class Especialidad
{
    public static function listar(): array
    {
        return db()->query('SELECT e.*, COUNT(o.id) AS total
                              FROM especialidades e LEFT JOIN odontologos o ON o.especialidad_id = e.id
                             GROUP BY e.id ORDER BY e.nombre')->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM especialidades WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE especialidades SET nombre=?, descripcion=? WHERE id=?');
            $stmt->execute([$d['nombre'], $d['descripcion'], $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO especialidades (nombre, descripcion) VALUES (?, ?)');
            $stmt->execute([$d['nombre'], $d['descripcion']]);
        }
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM especialidades WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function opciones(): array
    {
        return db()->query('SELECT id, nombre FROM especialidades ORDER BY nombre')->fetchAll();
    }
}
