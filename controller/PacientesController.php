<?php
class PacientesController extends Controller
{
    public function index(): void
    {
        $buscar = trim($_GET['buscar'] ?? '');
        $this->vista('pacientes/listar', [
            'titulo' => 'Pacientes', 'activo' => 'pacientes',
            'buscar' => $buscar, 'pacientes' => Paciente::listar($buscar),
        ]);
    }

    public function nuevo(): void
    {
        $this->vista('pacientes/form', ['titulo' => 'Pacientes', 'activo' => 'pacientes', 'r' => []]);
    }

    public function editar(): void
    {
        $r = Paciente::buscar($this->id()) ?? redirigir(url('pacientes'));
        $this->vista('pacientes/form', ['titulo' => 'Pacientes', 'activo' => 'pacientes', 'r' => $r]);
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [];
        foreach (['dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo', 'telefono', 'email', 'direccion', 'alergias'] as $c) {
            $datos[$c] = campo($c);
        }
        $this->ejecutar(fn() => Paciente::guardar($datos, $id), $id ? 'Paciente actualizado.' : 'Paciente registrado.');
        redirigir(url('pacientes'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Paciente::eliminar($this->id()), 'Paciente eliminado.');
        redirigir(url('pacientes'));
    }
}
