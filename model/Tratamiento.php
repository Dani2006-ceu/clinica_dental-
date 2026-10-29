<?php
class Tratamiento
{
    // Subconsulta reutilizable: total pagado (no anulado) de un tratamiento
    const PAGADO = '(SELECT COALESCE(SUM(monto), 0) FROM pagos pg WHERE pg.tratamiento_id = t.id AND pg.estado = \'Pagado\')';

    public static function listar(string $buscar = ''): array
    {
        $like = '%' . $buscar . '%';
        $stmt = db()->prepare(
            'SELECT t.*, CONCAT(p.nombres, \' \', p.apellidos) AS paciente,
                    CONCAT(o.nombres, \' \', o.apellidos) AS odontologo, s.nombre AS servicio,
                    d.descripcion AS diagnostico, d.severidad, ' . self::PAGADO . ' AS pagado
               FROM tratamientos t
               JOIN pacientes p   ON p.id = t.paciente_id
               JOIN odontologos o ON o.id = t.odontologo_id
               JOIN servicios s   ON s.id = t.servicio_id
               LEFT JOIN diagnosticos d ON d.id = t.diagnostico_id
              WHERE p.nombres LIKE ? OR p.apellidos LIKE ? OR p.dni LIKE ? OR t.tratamiento LIKE ?
              ORDER BY t.fecha DESC, t.id DESC'
        );
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM tratamientos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function guardar(array $d, int $id = 0): void
    {
        $pdo = db();
        $valores = [$d['cita_id'], $d['diagnostico_id'], $d['paciente_id'], $d['odontologo_id'], $d['servicio_id'],
                    $d['fecha'], $d['pieza_dental'], $d['tratamiento'], $d['costo'], $d['observaciones']];
        $pdo->beginTransaction();
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE tratamientos SET cita_id=?, diagnostico_id=?, paciente_id=?, odontologo_id=?, servicio_id=?,
                                       fecha=?, pieza_dental=?, tratamiento=?, costo=?, observaciones=? WHERE id=?');
                $stmt->execute([...$valores, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO tratamientos (cita_id, diagnostico_id, paciente_id, odontologo_id, servicio_id,
                                       fecha, pieza_dental, tratamiento, costo, observaciones) VALUES (?,?,?,?,?,?,?,?,?,?)');
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
        $stmt = db()->prepare('DELETE FROM tratamientos WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Tratamientos con su saldo pendiente, para asociarlos a un pago
    public static function conSaldo(): array
    {
        return db()->query(
            'SELECT t.id, t.paciente_id, s.nombre AS servicio,
                    CONCAT(DATE_FORMAT(t.fecha, \'%d/%m/%Y\'), \' - \', p.apellidos, \' - \', s.nombre, \' (S/ \', t.costo, \')\') AS nombre,
                    t.costo - ' . self::PAGADO . ' AS saldo
               FROM tratamientos t
               JOIN pacientes p ON p.id = t.paciente_id
               JOIN servicios s ON s.id = t.servicio_id
              ORDER BY t.fecha DESC'
        )->fetchAll();
    }
}
