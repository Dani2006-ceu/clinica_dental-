<?php
// Funciones de apoyo usadas por controladores y vistas
date_default_timezone_set('America/Lima');

// ---- Errores: nunca se muestran al visitante (revelan rutas y datos de la BD); se guardan en logs/ ----
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_errores.log');
set_exception_handler(function (Throwable $ex) {
    error_log($ex);
    http_response_code(500);
    exit('Ocurrió un error interno. Intente nuevamente.');
});

// ---- Cabeceras de seguridad del navegador ----
header('X-Frame-Options: DENY');                       // nadie puede meter el sistema en un iframe (clickjacking)
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; "
     . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; "
     . "img-src 'self' data: blob:; frame-src https://maps.google.com https://www.google.com; "
     . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('Cache-Control: no-store');                     // las páginas con datos de pacientes no quedan en caché
header_remove('X-Powered-By');

// ---- Sesión blindada ----
const SESION_INACTIVIDAD = 30 * 60;   // se cierra tras 30 min sin uso
const SESION_MAXIMA = 8 * 60 * 60;    // y siempre tras 8 horas
$https = ($_SERVER['HTTPS'] ?? '') === 'on';
ini_set('session.use_strict_mode', '1');    // rechaza IDs de sesión inventados
ini_set('session.use_only_cookies', '1');   // el ID nunca viaja en la URL
session_name('ALFADENT_SID');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Strict']);
session_start();
if ($https) {
    header('Strict-Transport-Security: max-age=31536000');
}

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/iconos.php';

// Datos de la clínica (se muestran en el encabezado, pie de página e inicio)
define('CLINICA_NOMBRE', 'Dental Alfadent');
define('CLINICA_DIRECCION', 'Carretera Federico Basadre, Neshuya');
define('CLINICA_CORREO', 'daniizquierdo2006@gmail.com');
define('CLINICA_MAPA', 'https://maps.app.goo.gl/7szxfNci3KPgKH6T7');
define('CLINICA_MAPA_EMBED', 'https://maps.google.com/maps?q=-8.642127,-74.962296&z=16&output=embed');

// Datos del Yape de la clínica (QR estático en imagenes/yape-qr.jpg)
define('YAPE_TITULAR', 'Daniel Adin Izquierdo Segura');
define('YAPE_QR', 'imagenes/yape-qr.jpg');

// Arma la URL de una acción: url('pacientes', 'editar', ['id' => 5])
function url(string $controlador = 'inicio', string $accion = 'index', array $params = []): string
{
    $q = ['c' => $controlador];
    if ($accion !== 'index') {
        $q['a'] = $accion;
    }
    return 'index.php?' . http_build_query($q + $params);
}

// Escapar salida HTML (previene XSS)
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function requiere_login(): void
{
    if (empty($_SESSION['usuario'])) {
        redirigir(url('auth', 'login'));
    }
    $ahora = time();
    $motivo = '';
    if ($ahora - ($_SESSION['ultimo_uso'] ?? 0) > SESION_INACTIVIDAD) {
        $motivo = 'Tu sesión expiró por inactividad.';
    } elseif ($ahora - ($_SESSION['inicio'] ?? 0) > SESION_MAXIMA) {
        $motivo = 'Tu sesión expiró. Vuelve a ingresar.';
    } elseif (!hash_equals($_SESSION['huella'] ?? '', huella_navegador())) {
        $motivo = 'Sesión no válida.'; // cookie robada usada desde otro navegador
    } else {
        // Se relee el usuario en cada petición: si lo eliminaron, desactivaron o cambiaron de rol, aplica al instante
        $u = Usuario::buscar((int)$_SESSION['usuario']['id']);
        if (!$u || !$u['activo']) {
            $motivo = 'Tu cuenta fue desactivada.';
        } else {
            $_SESSION['usuario'] = $u;
        }
    }
    if ($motivo) {
        cerrar_sesion();
        flash('error', $motivo);
        redirigir(url('auth', 'login'));
    }
    $_SESSION['ultimo_uso'] = $ahora;
}

// Abre la sesión del usuario con un ID nuevo (evita fijación de sesión)
function iniciar_sesion(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario;
    $_SESSION['inicio'] = $_SESSION['ultimo_uso'] = time();
    $_SESSION['huella'] = huella_navegador();
    unset($_SESSION['csrf']); // token CSRF nuevo para la sesión nueva
}

function cerrar_sesion(): void
{
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $p['path'], 'secure' => $p['secure'],
                                   'httponly' => true, 'samesite' => 'Strict']);
    session_destroy();
    session_start();
}

function huella_navegador(): string
{
    return hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

// IP real de la conexión (no se usa X-Forwarded-For porque el cliente lo puede falsificar)
function ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function es_admin(): bool
{
    return ($_SESSION['usuario']['rol'] ?? '') === 'Administrador';
}

// Política de contraseñas; devuelve el error o '' si es válida
function error_clave(string $clave): string
{
    if (strlen($clave) < 8) return 'La contraseña debe tener al menos 8 caracteres.';
    if (strlen($clave) > 72) return 'La contraseña no puede superar 72 caracteres.';
    if (!preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave)) return 'La contraseña debe combinar letras y números.';
    if (in_array(strtolower($clave), ['password1', '12345678a', 'admin123', 'qwerty123', 'abc12345'], true)) return 'Esa contraseña es demasiado común.';
    return '';
}

// ---- Token CSRF para los formularios ----
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_verificar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Solicitud no válida (token CSRF).');
    }
}

// ---- Mensajes flash ----
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function mostrar_flash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alerta alerta-' . e($f['tipo']) . '">' . e($f['mensaje']) . '</div>';
}

// Lee un campo de POST recortando espacios; cadena vacía => null
function campo(string $nombre): ?string
{
    $v = trim($_POST[$nombre] ?? '');
    return $v === '' ? null : $v;
}

// Iniciales para el avatar (ej. "Ana López" => "AL")
function iniciales(string $nombre): string
{
    $partes = preg_split('/\s+/', trim($nombre));
    $ini = mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[1] ?? '', 0, 1);
    return mb_strtoupper($ini ?: '?');
}

function soles($monto): string
{
    return 'S/ ' . number_format((float)$monto, 2);
}

// Número de boleta a partir del id del pago: 7 => "B001-00000007"
function nro_boleta(int $id): string
{
    return 'B001-' . str_pad((string)$id, 8, '0', STR_PAD_LEFT);
}

// Monto en letras para la boleta: 150.5 => "CIENTO CINCUENTA CON 50/100 SOLES"
function monto_en_letras(float $monto): string
{
    $entero = (int)floor(round($monto, 2));
    $centimos = (int)round(($monto - $entero) * 100);
    return mb_strtoupper(numero_en_letras($entero)) . ' CON ' . str_pad((string)$centimos, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
}

function numero_en_letras(int $n): string
{
    if ($n === 0) return 'cero';
    $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece',
                 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós',
                 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve'];
    $decenas = ['', '', '', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

    // Convierte un número de 1 a 999
    $hasta999 = function (int $x) use ($unidades, $decenas, $centenas): string {
        if ($x === 100) return 'cien';
        $texto = $centenas[intdiv($x, 100)];
        $resto = $x % 100;
        if ($resto > 0) {
            $parte = $resto < 30 ? $unidades[$resto] : $decenas[intdiv($resto, 10)] . ($resto % 10 ? ' y ' . $unidades[$resto % 10] : '');
            $texto = trim($texto . ' ' . $parte);
        }
        return $texto;
    };
    // "uno" pasa a "un" delante de mil / millones (veintiún mil, un millón)
    $apocope = fn(string $t) => preg_replace(['/veintiuno$/', '/uno$/'], ['veintiún', 'un'], $t);

    $millones = intdiv($n, 1000000);
    $miles = intdiv($n % 1000000, 1000);
    $resto = $n % 1000;
    $partes = [];
    if ($millones) $partes[] = $millones === 1 ? 'un millón' : $apocope($hasta999($millones)) . ' millones';
    if ($miles) $partes[] = $miles === 1 ? 'mil' : $apocope($hasta999($miles)) . ' mil';
    if ($resto) $partes[] = $hasta999($resto);
    return implode(' ', $partes);
}

function fecha($valor): string
{
    return $valor ? date('d/m/Y', strtotime($valor)) : '-';
}

// Genera <option> para un <select>
function opciones(array $filas, string $clave, string $texto, $seleccionado = null): string
{
    $html = '';
    foreach ($filas as $f) {
        $sel = ((string)$f[$clave] === (string)$seleccionado) ? ' selected' : '';
        $html .= '<option value="' . e($f[$clave]) . '"' . $sel . '>' . e($f[$texto]) . '</option>';
    }
    return $html;
}

// <option> para listas fijas (estados, roles, métodos...)
function opciones_lista(array $valores, $seleccionado = null): string
{
    $html = '';
    foreach ($valores as $v) {
        $html .= '<option' . ((string)$v === (string)$seleccionado ? ' selected' : '') . '>' . e($v) . '</option>';
    }
    return $html;
}

// Botón que envía un POST a una acción (eliminar, anular...) con su token CSRF.
// $confirmar es el mensaje que se pregunta antes de enviar ('' = sin pregunta)
function boton_post(string $controlador, string $accion, int $id, string $texto, string $clase = 'btn btn-peligro btn-chico',
                    string $confirmar = '¿Seguro que desea eliminar este registro?'): string
{
    return '<form method="post" action="' . e(url($controlador, $accion)) . '"' . ($confirmar ? ' data-confirmar="' . e($confirmar) . '"' : '') . '>'
         . csrf_campo() . '<input type="hidden" name="id" value="' . $id . '">'
         . '<button class="' . $clase . '">' . e($texto) . '</button></form>';
}

// Traduce errores de MySQL a mensajes entendibles
function mensaje_error_bd(PDOException $ex): string
{
    $codigo = $ex->errorInfo[1] ?? 0;
    if ($codigo == 1048) return 'Complete todos los campos obligatorios.';
    if ($codigo == 1062) return 'Ya existe un registro con esos datos (valor duplicado).';
    if ($codigo == 1451) return 'No se puede eliminar: el registro está siendo usado en otro módulo.';
    if ($codigo == 1452) return 'Debe seleccionar valores válidos en las listas.';
    if ($codigo == 1644) return $ex->errorInfo[2]; // reglas de la BD (ej. los pagos no se eliminan)
    return 'Error en la base de datos.';
}
