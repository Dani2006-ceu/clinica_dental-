<?php
class AuthController extends Controller
{
    protected bool $publico = true;

    public function login(): void
    {
        if (!empty($_SESSION['usuario'])) {
            redirigir(url());
        }
        $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verificar();
            $email = mb_strtolower(trim($_POST['email'] ?? ''));
            if (Seguridad::loginBloqueado($email)) {
                Seguridad::registrar('login_bloqueado', $email);
                $error = 'Demasiados intentos fallidos. Espere ' . Seguridad::MINUTOS_BLOQUEO . ' minutos e intente de nuevo.';
            } elseif (!($u = Usuario::autenticar($email, $_POST['password'] ?? ''))) {
                Seguridad::registrarFallo($email);
                Seguridad::registrar('login_fallido', $email);
                $error = 'Correo o contraseña incorrectos.';
            } elseif (!$u['aprobado']) {
                $error = 'Tu solicitud de cuenta aún no ha sido aprobada por el administrador.';
            } elseif (!$u['activo']) {
                $error = 'Tu cuenta está desactivada. Comunícate con el administrador.';
            } else {
                Seguridad::limpiarFallos($email);
                iniciar_sesion($u);
                Seguridad::registrar('login', '', $u);
                redirigir(url());
            }
        }
        $this->vista('auth/login', compact('error', 'email'), false);
    }

    public function registro(): void
    {
        if (!empty($_SESSION['usuario'])) {
            redirigir(url());
        }
        $errores = [];
        $nombre = $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verificar();
            $nombre = trim($_POST['nombre'] ?? '');
            $email = mb_strtolower(trim($_POST['email'] ?? ''));
            $clave = $_POST['password'] ?? '';

            if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) $errores[] = 'El nombre debe tener entre 3 y 100 caracteres.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) $errores[] = 'Ingrese un correo válido.';
            if ($errClave = error_clave($clave)) $errores[] = $errClave;
            if ($clave !== ($_POST['password2'] ?? '')) $errores[] = 'Las contraseñas no coinciden.';
            if (!$errores && Usuario::existeEmail($email)) $errores[] = 'Ese correo ya está registrado.';

            if (!$errores) {
                // Se guarda solo como SOLICITUD (sin aprobar ni activar): no puede ingresar ni ver datos
                // hasta que el administrador la apruebe y le asigne un rol
                Usuario::crear($nombre, $email, $clave, 'Recepcionista', 0, 0);
                Seguridad::registrar('solicitud_cuenta', $email);
                flash('exito', 'Solicitud enviada. El administrador debe aprobarla antes de que puedas ingresar.');
                redirigir(url('auth', 'login'));
            }
        }
        $this->vista('auth/registro', compact('errores', 'nombre', 'email'), false);
    }

    public function logout(): void
    {
        if (!empty($_SESSION['usuario'])) {
            Seguridad::registrar('logout');
        }
        cerrar_sesion();
        flash('exito', 'Sesión cerrada correctamente.');
        redirigir(url('auth', 'login'));
    }
}
