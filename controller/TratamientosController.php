<?php
class TratamientosController extends Controller
{
    public function index(): void
    {
        $buscar = trim($_GET['buscar'] ?? '');
        $this->vista('tratamientos/listar', [
            'titulo' => 'Tratamientos', 'activo' => 'tratamientos',
            'buscar' => $buscar, 'tratamientos' => Tratamiento::listar($buscar),
        ]);
    }

    public function nuevo(): void
    {
        $r = [];
        // "Tratar" desde un diagnóstico: precarga paciente, odontólogo, pieza y el servicio de la cita
        if (!empty($_GET['diagnostico_id']) && ($d = Diagnostico::buscar((int)$_GET['diagnostico_id']))) {
            $r = ['diagnostico_id' => $d['id'], 'paciente_id' => $d['paciente_id'], 'odontologo_id' => $d['odontologo_id'],
                  'cita_id' => $d['cita_id'], 'pieza_dental' => $d['pieza_dental']];
            if ($d['cita_id'] && ($cita = Cita::buscar((int)$d['cita_id']))) {
                $r += ['servicio_id' => $cita['servicio_id'], 'costo' => $cita['precio']];
            }
        }
        $this->formulario($r);
    }

    public function editar(): void
    {
        $this->formulario(Tratamiento::buscar($this->id()) ?? redirigir(url('tratamientos')));
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [
            'cita_id' => (int)campo('cita_id') ?: null, 'diagnostico_id' => (int)campo('diagnostico_id') ?: null,
            'paciente_id' => (int)campo('paciente_id'), 'odontologo_id' => (int)campo('odontologo_id'),
            'servicio_id' => (int)campo('servicio_id'), 'fecha' => campo('fecha'), 'pieza_dental' => campo('pieza_dental'),
            'tratamiento' => campo('tratamiento'), 'costo' => (float)campo('costo'), 'observaciones' => campo('observaciones'),
        ];
        $this->ejecutar(fn() => Tratamiento::guardar($datos, $id), $id ? 'Tratamiento actualizado.' : 'Tratamiento registrado.');
        redirigir(url('tratamientos'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Tratamiento::eliminar($this->id()), 'Tratamiento eliminado.');
        redirigir(url('tratamientos'));
    }

    private function formulario(array $r): void
    {
        $this->vista('tratamientos/form', [
            'titulo' => 'Tratamientos', 'activo' => 'tratamientos', 'r' => $r,
            'pacientes' => Paciente::opciones(), 'odontologos' => Odontologo::opciones(),
            'servicios' => Servicio::listar(), 'diagnosticos' => Diagnostico::opciones(),
        ]);
    }
}
