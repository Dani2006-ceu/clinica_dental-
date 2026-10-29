<?php
class Cita
{
    const ESTADOS = ['Pendiente', 'Confirmada', 'Atendida', 'Cancelada'];

    public static function listar(array $f): array
    {
        $where = [];
        $params = [];
        if ($f['desde'] !== '') { $where[] = 'c.fecha >= ?'; $params[] = $f['desde']; }
        if ($f['hasta'] !== '') { $where[] = 'c.fecha <= ?'; $params[] = $f['hasta']; }
        if (in_array($f['estado'], self::ESTADOS, true)) { $where[] = 'c.estado = ?'; $params[] = $f['estado']; }
        if ($f['odontologo'] > 0) { $where[] = 'c.odontologo_id = ?'; $params[] = $f['odontologo']; }

        $sql = 'SELECT c.*, CONCAT(p.nombres, \' \', p.apellidos) AS paciente,
                       CONCAT(o.nombres, \' \', o.apellidos) AS odontologo, s.nombre AS servicio
                  FROM citas c
                  JOIN pacientes p   ON p.id = c.paciente_id
                  JOIN odontologos o ON o.id = c.odontologo_id
                  JOIN servicios s   ON s.id = c.servicio_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY c.fecha DESC, c.hora DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT c.*, s.precio FROM citas c JOIN servicios s ON s.id = c.servicio_id WHERE c.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // true si el odontólogo ya tiene otra cita que se cruza con ese horario (según la duración del servicio)
    public static function hayCruce(int $odontologo, string $fecha, string $hora, int $servicio, int $excluirId): bool
    {
        $stmt = db()->prepare('SELECT duracion_min FROM servicios WHERE id = ?');
        $stmt->execute([$servicio]);
        $duracion = (int)$stmt->fetchColumn();

        $stmt = db()->prepare(
            'SELECT COUNT(*)
               FROM citas c JOIN servicios s ON s.id = c.servicio_id
              WHERE c.odontologo_id = ? AND c.fecha = ? AND c.estado <> \'Cancelada\' AND c.id <> ?
                AND c.hora < ADDTIME(?, SEC_TO_TIME(? * 60))
                AND ADDTIME(c.hora, SEC_TO_TIME(s.duracion_min * 60)) > ?'
        );
        $stmt->execute([$odontologo, $fecha, $excluirId, $hora, $duracion, $hora]);
        return $stmt->fetchColumn() > 0;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        $valores = [$d['paciente_id'], $d['odontologo_id'], $d['servicio_id'], $d['fecha'], $d['hora'], $d['motivo'], $d['estado']];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE citas SET paciente_id=?, odontologo_id=?, servicio_id=?, fecha=?, hora=?,
                                   motivo=?, estado=? WHERE id=?');
            $stmt->execute([...$valores, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha, hora, motivo, estado)
                                   VALUES (?,?,?,?,?,?,?)');
            $stmt->execute($valores);
        }
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        $stmt = db()->prepare('UPDATE citas SET estado = ? WHERE id = ?');
        $stmt->execute([$estado, $id]);
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM citas WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Citas pendientes o confirmadas desde una fecha (para inicio y reportes)
    public static function pendientes(string $desde, ?string $hasta = null, ?int $limite = null): array
    {
        $sql = 'SELECT c.fecha, c.hora, c.estado, CONCAT(p.nombres, \' \', p.apellidos) AS paciente, p.telefono,
                       CONCAT(o.nombres, \' \', o.apellidos) AS odontologo, s.nombre AS servicio
                  FROM citas c
                  JOIN pacientes p   ON p.id = c.paciente_id
                  JOIN odontologos o ON o.id = c.odontologo_id
                  JOIN servicios s   ON s.id = c.servicio_id
                 WHERE c.estado IN (\'Pendiente\', \'Confirmada\') AND c.fecha >= ?';
        $params = [$desde];
        if ($hasta) {
            $sql .= ' AND c.fecha <= ?';
            $params[] = $hasta;
        }
        $sql .= ' ORDER BY c.fecha, c.hora' . ($limite ? ' LIMIT ' . (int)$limite : '');
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function contarPendientes(string $desde, bool $soloEseDia = false): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM citas WHERE estado IN (\'Pendiente\',\'Confirmada\') AND fecha '
                              . ($soloEseDia ? '= ?' : '>= ?'));
        $stmt->execute([$desde]);
        return (int)$stmt->fetchColumn();
    }
}
