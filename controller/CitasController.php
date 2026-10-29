<?php
class CitasController extends Controller
{
    public function index(): void
    {
        $filtros = [
            'desde' => $_GET['desde'] ?? '', 'hasta' => $_GET['hasta'] ?? '',
            'estado' => $_GET['estado'] ?? '', 'odontologo' => (int)($_GET['odontologo'] ?? 0),
        ];
        $this->vista('citas/listar', [
            'titulo' => 'Citas', 'activo' => 'citas', 'f' => $filtros,
            'odontologos' => Odontologo::opciones(true), 'citas' => Cita::listar($filtros),
        ]);
    }

    public function nuevo(): void
    {
        $this->formulario([]);
    }

    public function editar(): void
    {
        $this->formulario(Cita::buscar($this->id()) ?? redirigir(url('citas')));
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [
            'paciente_id' => (int)campo('paciente_id'), 'odontologo_id' => (int)campo('odontologo_id'),
            'servicio_id' => (int)campo('servicio_id'), 'fecha' => campo('fecha'), 'hora' => campo('hora'),
            'motivo' => campo('motivo'),
            'estado' => in_array(campo('estado'), Cita::ESTADOS, true) ? campo('estado') : 'Pendiente',
        ];

        if ($datos['estado'] !== 'Cancelada'
            && Cita::hayCruce($datos['odontologo_id'], (string)$datos['fecha'], (string)$datos['hora'], $datos['servicio_id'], $id)) {
            flash('error', 'El odontólogo ya tiene una cita en ese horario. Elija otra hora.');
            redirigir($id ? url('citas', 'editar', ['id' => $id]) : url('citas', 'nuevo'));
        }
        $this->ejecutar(fn() => Cita::guardar($datos, $id), $id ? 'Cita actualizada.' : 'Cita programada.');
        redirigir(url('citas'));
    }

    public function estado(): void
    {
        csrf_verificar();
        $estado = in_array($_POST['estado'] ?? '', Cita::ESTADOS, true) ? $_POST['estado'] : 'Pendiente';
        $this->ejecutar(fn() => Cita::cambiarEstado($this->id(), $estado), 'Estado de la cita actualizado.');
        redirigir(url('citas'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Cita::eliminar($this->id()), 'Cita eliminada.');
        redirigir(url('citas'));
    }

    private function formulario(array $r): void
    {
        $this->vista('citas/form', [
            'titulo' => 'Citas', 'activo' => 'citas', 'r' => $r,
            'pacientes' => Paciente::opciones(), 'odontologos' => Odontologo::opciones(true), 'servicios' => Servicio::activos(),
        ]);
    }
}
