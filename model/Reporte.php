<?php
class Reporte
{
    public static function atenciones(string $desde, string $hasta): array
    {
        $stmt = db()->prepare(
            'SELECT t.fecha, CONCAT(p.nombres, \' \', p.apellidos) AS paciente,
                    CONCAT(o.nombres, \' \', o.apellidos) AS odontologo, s.nombre AS servicio,
                    d.descripcion AS diagnostico, t.costo
               FROM tratamientos t
               LEFT JOIN diagnosticos d ON d.id = t.diagnostico_id
               JOIN pacientes p   ON p.id = t.paciente_id
               JOIN odontologos o ON o.id = t.odontologo_id
               JOIN servicios s   ON s.id = t.servicio_id
              WHERE t.fecha BETWEEN ? AND ?
              ORDER BY t.fecha'
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public static function atencionesPorOdontologo(string $desde, string $hasta): array
    {
        $stmt = db()->prepare(
            'SELECT CONCAT(o.nombres, \' \', o.apellidos) AS odontologo, COUNT(*) AS atenciones, SUM(t.costo) AS total
               FROM tratamientos t JOIN odontologos o ON o.id = t.odontologo_id
              WHERE t.fecha BETWEEN ? AND ?
              GROUP BY o.id ORDER BY atenciones DESC'
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public static function ingresos(string $desde, string $hasta): array
    {
        $stmt = db()->prepare(
            'SELECT pg.fecha, CONCAT(p.nombres, \' \', p.apellidos) AS paciente, pg.metodo, pg.nro_operacion, pg.monto
               FROM pagos pg JOIN pacientes p ON p.id = pg.paciente_id
              WHERE pg.estado = \'Pagado\' AND pg.fecha BETWEEN ? AND ?
              ORDER BY pg.fecha'
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public static function ingresosPorMetodo(string $desde, string $hasta): array
    {
        $stmt = db()->prepare(
            'SELECT metodo, COUNT(*) AS cantidad, SUM(monto) AS total
               FROM pagos WHERE estado = \'Pagado\' AND fecha BETWEEN ? AND ?
              GROUP BY metodo ORDER BY total DESC'
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }
}
