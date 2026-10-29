<?php
class Diagnostico
{
    const SEVERIDADES = ['Leve', 'Moderado', 'Severo'];

    public static function listar(string $buscar = '', string $severidad = ''): array
    {
        $like = '%' . $buscar . '%';
        $sql = 'SELECT d.*, CONCAT(p.nombres, \' \', p.apellidos) AS paciente, p.dni,
                       CONCAT(o.nombres, \' \', o.apellidos) AS odontologo,
                       (SELECT COUNT(*) FROM tratamientos t WHERE t.diagnostico_id = d.id) AS tratamientos
                  FROM diagnosticos d
                  JOIN pacientes p   ON p.id = d.paciente_id
                  JOIN odontologos o ON o.id = d.odontologo_id
                 WHERE (p.nombres LIKE ? OR p.apellidos LIKE ? OR p.dni LIKE ? OR d.descripcion LIKE ? OR d.codigo_cie10 LIKE ?)';
        $params = [$like, $like, $like, $like, $like];
        if (in_array($severidad, self::SEVERIDADES, true)) {
            $sql .= ' AND d.severidad = ?';
            $params[] = $severidad;
        }
        $stmt = db()->prepare($sql . ' ORDER BY d.fecha DESC, d.id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM diagnosticos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Guarda el diagnóstico y, si viene de una cita, la marca como Atendida
    public static function guardar(array $d, int $id = 0): void
    {
        $pdo = db();
        $valores = [$d['cita_id'], $d['paciente_id'], $d['odontologo_id'], $d['fecha'], $d['pieza_dental'],
                    $d['codigo_cie10'], $d['descripcion'], $d['severidad'], $d['observaciones']];
        $pdo->beginTransaction();
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE diagnosticos SET cita_id=?, paciente_id=?, odontologo_id=?, fecha=?, pieza_dental=?,
                                       codigo_cie10=?, descripcion=?, severidad=?, observaciones=? WHERE id=?');
                $stmt->execute([...$valores, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO diagnosticos (cita_id, paciente_id, odontologo_id, fecha, pieza_dental,
                                       codigo_cie10, descripcion, severidad, observaciones) VALUES (?,?,?,?,?,?,?,?,?)');
                $stmt->execute($valores);
            }
            if ($d['cita_id']) {
                Cita::cambiarEstado((int)$d['cita_id'], 'Atendida');
            }
            $pdo->commit();
        } catch (PDOException $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM diagnosticos WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function opciones(): array
    {
        return db()->query(
            'SELECT d.id, d.paciente_id, d.odontologo_id, d.cita_id, d.pieza_dental,
                    CONCAT(DATE_FORMAT(d.fecha, \'%d/%m/%Y\'), \' - \', p.apellidos, \' - \', LEFT(d.descripcion, 60)) AS nombre
               FROM diagnosticos d JOIN pacientes p ON p.id = d.paciente_id
              ORDER BY d.fecha DESC'
        )->fetchAll();
    }
}
