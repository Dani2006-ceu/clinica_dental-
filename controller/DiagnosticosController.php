<?php
class DiagnosticosController extends Controller
{
    public function index(): void
    {
        $buscar = trim($_GET['buscar'] ?? '');
        $severidad = $_GET['severidad'] ?? '';
        $this->vista('diagnosticos/listar', [
            'titulo' => 'Diagnósticos', 'activo' => 'diagnosticos',
            'buscar' => $buscar, 'fSeveridad' => $severidad,
            'diagnosticos' => Diagnostico::listar($buscar, $severidad),
        ]);
    }

    public function nuevo(): void
    {
        $r = [];
        // "Atender" desde una cita: precarga paciente y odontólogo
        if (!empty($_GET['cita_id']) && ($cita = Cita::buscar((int)$_GET['cita_id']))) {
            $r = ['cita_id' => $cita['id'], 'paciente_id' => $cita['paciente_id'], 'odontologo_id' => $cita['odontologo_id']];
        }
        $this->formulario($r);
    }

    public function editar(): void
    {
        $this->formulario(Diagnostico::buscar($this->id()) ?? redirigir(url('diagnosticos')));
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [
            'cita_id' => (int)campo('cita_id') ?: null,
            'paciente_id' => (int)campo('paciente_id'), 'odontologo_id' => (int)campo('odontologo_id'),
            'fecha' => campo('fecha'), 'pieza_dental' => campo('pieza_dental'), 'codigo_cie10' => campo('codigo_cie10'),
            'descripcion' => campo('descripcion'), 'observaciones' => campo('observaciones'),
            'severidad' => in_array(campo('severidad'), Diagnostico::SEVERIDADES, true) ? campo('severidad') : 'Leve',
        ];
        $this->ejecutar(fn() => Diagnostico::guardar($datos, $id), 'Diagnóstico guardado. Ahora puede registrar el tratamiento.');
        redirigir(url('diagnosticos'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Diagnostico::eliminar($this->id()), 'Diagnóstico eliminado.');
        redirigir(url('diagnosticos'));
    }

    private function formulario(array $r): void
    {
        $this->vista('diagnosticos/form', [
            'titulo' => 'Diagnósticos', 'activo' => 'diagnosticos', 'r' => $r,
            'pacientes' => Paciente::opciones(), 'odontologos' => Odontologo::opciones(),
        ]);
    }
}
