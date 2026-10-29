<?php
class OdontologosController extends Controller
{
    public function index(): void
    {
        $especialidad = (int)($_GET['especialidad'] ?? 0);
        $this->vista('odontologos/listar', [
            'titulo' => 'Odontólogos', 'activo' => 'odontologos',
            'filtroEsp' => $especialidad,
            'especialidades' => Especialidad::opciones(),
            'odontologos' => Odontologo::listar($especialidad),
        ]);
    }

    public function nuevo(): void
    {
        $this->formulario([]);
    }

    public function editar(): void
    {
        $this->formulario(Odontologo::buscar($this->id()) ?? redirigir(url('odontologos')));
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [];
        foreach (['dni', 'cop', 'nombres', 'apellidos', 'telefono', 'email'] as $c) {
            $datos[$c] = campo($c);
        }
        $datos['especialidad_id'] = (int)campo('especialidad_id');
        $datos['estado'] = campo('estado') === 'Inactivo' ? 'Inactivo' : 'Activo';
        $this->ejecutar(fn() => Odontologo::guardar($datos, $id), $id ? 'Odontólogo actualizado.' : 'Odontólogo registrado.');
        redirigir(url('odontologos'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Odontologo::eliminar($this->id()), 'Odontólogo eliminado.');
        redirigir(url('odontologos'));
    }

    private function formulario(array $r): void
    {
        $this->vista('odontologos/form', [
            'titulo' => 'Odontólogos', 'activo' => 'odontologos', 'r' => $r, 'especialidades' => Especialidad::opciones(),
        ]);
    }
}
