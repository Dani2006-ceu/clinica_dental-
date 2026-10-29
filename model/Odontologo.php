<?php
class Odontologo
{
    public static function listar(int $especialidad = 0): array
    {
        $sql = 'SELECT o.*, e.nombre AS especialidad
                  FROM odontologos o JOIN especialidades e ON e.id = o.especialidad_id';
        $params = [];
        if ($especialidad > 0) {
            $sql .= ' WHERE o.especialidad_id = ?';
            $params[] = $especialidad;
        }
        $stmt = db()->prepare($sql . ' ORDER BY e.nombre, o.apellidos');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM odontologos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        $valores = [$d['dni'], $d['cop'], $d['nombres'], $d['apellidos'], $d['especialidad_id'],
                    $d['telefono'], $d['email'], $d['estado']];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE odontologos SET dni=?, cop=?, nombres=?, apellidos=?, especialidad_id=?,
                                   telefono=?, email=?, estado=? WHERE id=?');
            $stmt->execute([...$valores, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO odontologos (dni, cop, nombres, apellidos, especialidad_id, telefono, email, estado)
                                   VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute($valores);
        }
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM odontologos WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Con $soloActivos se usa al programar citas
    public static function opciones(bool $soloActivos = false): array
    {
        return db()->query(
            'SELECT o.id, CONCAT(o.nombres, \' \', o.apellidos, \' (\', e.nombre, \')\') AS nombre
               FROM odontologos o JOIN especialidades e ON e.id = o.especialidad_id'
            . ($soloActivos ? ' WHERE o.estado = \'Activo\'' : '') . ' ORDER BY o.apellidos'
        )->fetchAll();
    }
}
