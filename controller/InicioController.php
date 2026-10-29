<?php
class InicioController extends Controller
{
    public function index(): void
    {
        $hoy = date('Y-m-d');
        $this->vista('inicio/index', [
            'titulo'          => 'Inicio',
            'activo'          => 'inicio',
            'totalPacientes'  => Paciente::total(),
            'citasHoy'        => Cita::contarPendientes($hoy, true),
            'citasPendientes' => Cita::contarPendientes($hoy),
            'ingresosMes'     => Pago::ingresosDelMes(date('Y-m')),
            'proximas'        => Cita::pendientes($hoy, null, 5),
        ]);
    }
}
