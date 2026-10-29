<?php
class EspecialidadesController extends Controller
{
    public function index(): void
    {
        $this->vista('especialidades/listar', [
            'titulo' => 'Especialidades', 'activo' => 'odontologos', 'especialidades' => Especialidad::listar(),
        ]);
    }

    public function nuevo(): void
    {
        $this->vista('especialidades/form', ['titulo' => 'Especialidades', 'activo' => 'odontologos', 'r' => []]);
    }

    public function editar(): void
    {
        $r = Especialidad::buscar($this->id()) ?? redirigir(url('especialidades'));
        $this->vista('especialidades/form', ['titulo' => 'Especialidades', 'activo' => 'odontologos', 'r' => $r]);
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = ['nombre' => campo('nombre'), 'descripcion' => campo('descripcion')];
        $this->ejecutar(fn() => Especialidad::guardar($datos, $id), $id ? 'Especialidad actualizada.' : 'Especialidad registrada.');
        redirigir(url('especialidades'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Especialidad::eliminar($this->id()), 'Especialidad eliminada.');
        redirigir(url('especialidades'));
    }
}
