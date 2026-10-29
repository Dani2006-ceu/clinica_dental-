<?php
class UsuariosController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Solo el administrador gestiona usuarios
        if (!es_admin()) {
            flash('error', 'Solo el administrador puede gestionar usuarios.');
            redirigir(url());
        }
    }

    public function index(): void
    {
        $this->vista('usuarios/listar', [
            'titulo' => 'Usuarios', 'activo' => '', 'usuarios' => Usuario::listar(), 'miId' => (int)$_SESSION['usuario']['id'],
            'solicitudes' => Usuario::solicitudes(),
        ]);
    }

    // Da el permiso de acceso a quien pidió una cuenta desde "Crear cuenta"
    public function aprobar(): void
    {
        csrf_verificar();
        $rol = in_array(campo('rol'), Usuario::ROLES, true) ? campo('rol') : 'Recepcionista';
        if (Usuario::aprobar($this->id(), $rol)) {
            Seguridad::registrar('solicitud_aprobada', 'id ' . $this->id() . ' como ' . $rol);
            flash('exito', 'Solicitud aprobada. El usuario ya puede ingresar como ' . $rol . '.');
        } else {
            flash('error', 'La solicitud ya no existe o ya fue revisada.');
        }
        redirigir(url('usuarios'));
    }

    public function rechazar(): void
    {
        csrf_verificar();
        if (Usuario::rechazar($this->id())) {
            Seguridad::registrar('solicitud_rechazada', 'id ' . $this->id());
            flash('exito', 'Solicitud rechazada.');
        } else {
            flash('error', 'La solicitud ya no existe o ya fue revisada.');
        }
        redirigir(url('usuarios'));
    }

    public function nuevo(): void
    {
        $this->vista('usuarios/form', ['titulo' => 'Usuarios', 'activo' => '', 'r' => []]);
    }

    public function editar(): void
    {
        $r = Usuario::buscar($this->id()) ?? redirigir(url('usuarios'));
        $this->vista('usuarios/form', ['titulo' => 'Usuarios', 'activo' => '', 'r' => $r]);
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $nombre = (string)campo('nombre');
        $email = mb_strtolower((string)campo('email'));
        $rol = in_array(campo('rol'), Usuario::ROLES, true) ? campo('rol') : 'Recepcionista';
        $activo = ($_POST['activo'] ?? '1') === '1' ? 1 : 0;
        $clave = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Ingrese un correo válido.');
        } elseif (($clave !== '' || $id === 0) && ($errClave = error_clave($clave))) {
            flash('error', $errClave);
        } elseif ($id === (int)$_SESSION['usuario']['id'] && (!$activo || $rol !== 'Administrador')) {
            flash('error', 'No puede desactivarse ni quitarse el rol de administrador a sí mismo.');
        } elseif ($id > 0) {
            $this->ejecutar(fn() => Usuario::actualizar($id, $nombre, $email, $rol, $activo, $clave), 'Usuario actualizado.');
        } else {
            $this->ejecutar(fn() => Usuario::crear($nombre, $email, $clave, $rol, $activo), 'Usuario creado.');
        }
        redirigir(url('usuarios'));
    }

    // Bitácora de auditoría: últimos accesos y operaciones del sistema
    public function bitacora(): void
    {
        $this->vista('usuarios/bitacora', ['titulo' => 'Bitácora', 'activo' => '', 'registros' => Seguridad::bitacora()]);
    }

    public function eliminar(): void
    {
        csrf_verificar();
        if ($this->id() === (int)$_SESSION['usuario']['id']) {
            flash('error', 'No puede eliminar su propio usuario.');
        } else {
            $this->ejecutar(fn() => Usuario::eliminar($this->id()), 'Usuario eliminado.');
        }
        redirigir(url('usuarios'));
    }
}
