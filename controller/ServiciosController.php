<?php
class ServiciosController extends Controller
{
    public function index(): void
    {
        $this->vista('servicios/listar', ['titulo' => 'Servicios', 'activo' => 'servicios', 'servicios' => Servicio::listar()]);
    }

    public function nuevo(): void
    {
        $this->vista('servicios/form', ['titulo' => 'Servicios', 'activo' => 'servicios', 'r' => []]);
    }

    public function editar(): void
    {
        $r = Servicio::buscar($this->id()) ?? redirigir(url('servicios'));
        $this->vista('servicios/form', ['titulo' => 'Servicios', 'activo' => 'servicios', 'r' => $r]);
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [
            'nombre' => campo('nombre'), 'descripcion' => campo('descripcion'),
            'precio' => (float)campo('precio'), 'duracion_min' => (int)campo('duracion_min'),
            'estado' => campo('estado') === 'Inactivo' ? 'Inactivo' : 'Activo',
        ];
        $this->ejecutar(fn() => Servicio::guardar($datos, $id), $id ? 'Servicio actualizado.' : 'Servicio registrado.');
        redirigir(url('servicios'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Servicio::eliminar($this->id()), 'Servicio eliminado.');
        redirigir(url('servicios'));
    }
}
