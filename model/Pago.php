<?php
class Pago
{
    const METODOS = ['Efectivo', 'Tarjeta de crédito', 'Tarjeta de débito', 'Yape'];

    public static function listar(array $f): array
    {
        $where = [];
        $params = [];
        if ($f['desde'] !== '') { $where[] = 'pg.fecha >= ?'; $params[] = $f['desde']; }
        if ($f['hasta'] !== '') { $where[] = 'pg.fecha <= ?'; $params[] = $f['hasta']; }
        if (in_array($f['metodo'], self::METODOS, true)) { $where[] = 'pg.metodo = ?'; $params[] = $f['metodo']; }

        $sql = 'SELECT pg.*, CONCAT(p.nombres, \' \', p.apellidos) AS paciente, s.nombre AS servicio
                  FROM pagos pg
                  JOIN pacientes p ON p.id = pg.paciente_id
                  LEFT JOIN tratamientos t ON t.id = pg.tratamiento_id
                  LEFT JOIN servicios s ON s.id = t.servicio_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY pg.fecha DESC, pg.id DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM pagos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Devuelve el id del pago guardado
    public static function guardar(array $d, int $id = 0): int
    {
        $valores = [$d['paciente_id'], $d['tratamiento_id'], $d['fecha'], $d['monto'], $d['metodo'], $d['nro_operacion'], $d['observacion']];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE pagos SET paciente_id=?, tratamiento_id=?, fecha=?, monto=?, metodo=?,
                                   nro_operacion=?, observacion=? WHERE id=?');
            $stmt->execute([...$valores, $id]);
            return $id;
        }
        $stmt = db()->prepare('INSERT INTO pagos (paciente_id, tratamiento_id, fecha, monto, metodo, nro_operacion, observacion)
                               VALUES (?,?,?,?,?,?,?)');
        $stmt->execute($valores);
        return (int)db()->lastInsertId();
    }

    // Todo lo que se imprime en la boleta: paciente, tratamiento, odontólogo y saldo del tratamiento
    public static function boleta(int $id): ?array
    {
        $stmt = db()->prepare(
            'SELECT pg.*, p.dni, CONCAT(p.nombres, \' \', p.apellidos) AS paciente, p.direccion, p.telefono, p.email,
                    t.fecha AS fecha_tratamiento, t.pieza_dental, t.tratamiento, t.costo,
                    s.nombre AS servicio, d.descripcion AS diagnostico,
                    CONCAT(o.nombres, \' \', o.apellidos) AS odontologo,
                    (SELECT COALESCE(SUM(x.monto), 0) FROM pagos x
                      WHERE x.tratamiento_id = pg.tratamiento_id AND x.estado = \'Pagado\') AS total_pagado
               FROM pagos pg
               JOIN pacientes p ON p.id = pg.paciente_id
               LEFT JOIN tratamientos t ON t.id = pg.tratamiento_id
               LEFT JOIN servicios s    ON s.id = t.servicio_id
               LEFT JOIN diagnosticos d ON d.id = t.diagnostico_id
               LEFT JOIN odontologos o  ON o.id = t.odontologo_id
              WHERE pg.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Los pagos no se eliminan (la BD lo impide con un trigger): solo se anulan
    public static function anular(int $id): void
    {
        $stmt = db()->prepare('UPDATE pagos SET estado = \'Anulado\' WHERE id = ? AND estado = \'Pagado\'');
        $stmt->execute([$id]);
    }

    public static function ingresosDelMes(string $mes): float
    {
        $stmt = db()->prepare('SELECT COALESCE(SUM(monto),0) FROM pagos WHERE estado = \'Pagado\' AND DATE_FORMAT(fecha, \'%Y-%m\') = ?');
        $stmt->execute([$mes]);
        return (float)$stmt->fetchColumn();
    }
}
