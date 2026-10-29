<?php
class Paciente
{
    public static function listar(string $buscar = ''): array
    {
        $like = '%' . $buscar . '%';
        $stmt = db()->prepare('SELECT * FROM pacientes
                                WHERE dni LIKE ? OR nombres LIKE ? OR apellidos LIKE ?
                                ORDER BY apellidos, nombres');
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM pacientes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        $valores = [$d['dni'], $d['nombres'], $d['apellidos'], $d['fecha_nacimiento'], $d['sexo'],
                    $d['telefono'], $d['email'], $d['direccion'], $d['alergias']];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE pacientes SET dni=?, nombres=?, apellidos=?, fecha_nacimiento=?, sexo=?,
                                   telefono=?, email=?, direccion=?, alergias=? WHERE id=?');
            $stmt->execute([...$valores, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO pacientes (dni, nombres, apellidos, fecha_nacimiento, sexo,
                                   telefono, email, direccion, alergias) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute($valores);
        }
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM pacientes WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Lista para los <select> de otros módulos
    public static function opciones(): array
    {
        return db()->query('SELECT id, CONCAT(apellidos, \', \', nombres, \' - \', dni) AS nombre
                              FROM pacientes ORDER BY apellidos')->fetchAll();
    }

    public static function total(): int
    {
        return (int)db()->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    }
}
