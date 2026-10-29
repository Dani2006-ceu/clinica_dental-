<?php
class ReportesController extends Controller
{
    const NOMBRES = ['atenciones' => 'Atenciones realizadas', 'ingresos' => 'Ingresos obtenidos', 'pendientes' => 'Citas pendientes'];

    public function index(): void
    {
        $this->vista('reportes/index', ['titulo' => 'Reportes', 'activo' => 'reportes'] + $this->datos());
    }

    // Versión del reporte para imprimir, descargar en PDF o exportar a Excel
    public function documento(): void
    {
        $this->vista('reportes/documento', $this->datos(), false);
    }

    private function datos(): array
    {
        $tipo = $_GET['tipo'] ?? 'atenciones';
        if (!isset(self::NOMBRES[$tipo])) {
            $tipo = 'atenciones';
        }
        // Citas pendientes: por defecto desde hoy hasta 30 días; los demás: el mes actual
        $desde = $_GET['desde'] ?? ($tipo === 'pendientes' ? date('Y-m-d') : date('Y-m-01'));
        $hasta = $_GET['hasta'] ?? ($tipo === 'pendientes' ? date('Y-m-d', strtotime('+30 days')) : date('Y-m-d'));

        if ($tipo === 'atenciones') {
            $filas = Reporte::atenciones($desde, $hasta);
            $resumen = Reporte::atencionesPorOdontologo($desde, $hasta);
        } elseif ($tipo === 'ingresos') {
            $filas = Reporte::ingresos($desde, $hasta);
            $resumen = Reporte::ingresosPorMetodo($desde, $hasta);
        } else {
            $filas = Cita::pendientes($desde, $hasta);
            $resumen = [];
        }
        return ['tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta, 'filas' => $filas, 'resumen' => $resumen, 'nombres' => self::NOMBRES];
    }
}
