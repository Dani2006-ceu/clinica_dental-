<?php
/*
 * Controlador frontal: todas las páginas pasan por aquí.
 * URL: index.php?c=<controlador>&a=<accion>   (ej. index.php?c=pacientes&a=nuevo)
 */
require_once __DIR__ . '/config/funciones.php';

// Carga automática de clases desde controller/ y model/
spl_autoload_register(function (string $clase) {
    foreach (['controller', 'model'] as $carpeta) {
        $archivo = __DIR__ . "/$carpeta/$clase.php";
        if (is_file($archivo)) {
            require_once $archivo;
            return;
        }
    }
});

$rutas = [
    'auth'           => 'AuthController',
    'inicio'         => 'InicioController',
    'pacientes'      => 'PacientesController',
    'especialidades' => 'EspecialidadesController',
    'odontologos'    => 'OdontologosController',
    'servicios'      => 'ServiciosController',
    'citas'          => 'CitasController',
    'diagnosticos'   => 'DiagnosticosController',
    'tratamientos'   => 'TratamientosController',
    'pagos'          => 'PagosController',
    'reportes'       => 'ReportesController',
    'usuarios'       => 'UsuariosController',
];

$c = $_GET['c'] ?? (empty($_SESSION['usuario']) ? 'auth' : 'inicio');
$a = $_GET['a'] ?? ($c === 'auth' ? 'login' : 'index');

if (!is_string($c) || !is_string($a) || !isset($rutas[$c]) || !preg_match('/^[a-zA-Z]{1,30}$/', $a)) {
    http_response_code(404);
    exit('Página no encontrada.');
}

$controlador = new $rutas[$c]();

// Solo se pueden llamar los métodos públicos del controlador
if (str_starts_with($a, '_') || !is_callable([$controlador, $a])) {
    http_response_code(404);
    exit('Acción no encontrada.');
}

// Bitácora: toda operación que modifica datos (crear, editar, eliminar, anular...) queda registrada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $c !== 'auth') {
    Seguridad::registrar("$c.$a", 'id=' . (int)($_POST['id'] ?? 0));
}
$controlador->$a();
