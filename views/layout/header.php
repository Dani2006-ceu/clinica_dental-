<?php
// Variables esperadas: $titulo y $activo (clave del menú)
$titulo = $titulo ?? 'Inicio';
$activo = $activo ?? '';
$usuario = $_SESSION['usuario'];
$solicitudesPendientes = es_admin() ? Usuario::totalSolicitudes() : 0;

$menu = [
    'inicio'       => 'Inicio',
    'pacientes'    => 'Pacientes',
    'odontologos'  => 'Odontólogos',
    'citas'        => 'Citas',
    'diagnosticos' => 'Diagnósticos',
    'tratamientos' => 'Tratamientos',
    'servicios'    => 'Servicios',
    'pagos'        => 'Pagos',
    'reportes'     => 'Reportes',
];
$meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
$fechaLarga = ucfirst($dias[date('w')]) . ', ' . date('j') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | <?= CLINICA_NOMBRE ?></title>
    <link rel="icon" href="imagenes/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assetes/css/estilos.css">
</head>
<body>

<header class="encabezado">
    <!-- Nivel 1: barra superior institucional -->
    <div class="encabezado-nivel1">
        <div class="contenedor fila-entre">
            <ul class="contacto">
                <li><a href="mailto:<?= CLINICA_CORREO ?>"><?= icono('correo') ?> <?= CLINICA_CORREO ?></a></li>
                <li class="ocultar-movil"><a href="<?= CLINICA_MAPA ?>" target="_blank" rel="noopener"><?= icono('ubicacion') ?> <?= CLINICA_DIRECCION ?></a></li>
                <li class="ocultar-tablet"><?= icono('reloj') ?> Lun - Sáb · 8:00 a.m. - 8:00 p.m.</li>
            </ul>
            <span class="fecha-hoy ocultar-movil"><?= $fechaLarga ?></span>
        </div>
    </div>

    <!-- Nivel 2: marca y usuario -->
    <div class="encabezado-nivel2">
        <div class="contenedor fila-entre">
            <a href="<?= url() ?>" class="marca">
                <img src="imagenes/logo.svg" alt="Logo <?= CLINICA_NOMBRE ?>">
                <span>
                    <strong><?= CLINICA_NOMBRE ?></strong>
                    <small>Clínica Odontológica</small>
                </span>
            </a>
            <div class="zona-usuario">
                <?php if ($solicitudesPendientes): ?>
                    <a href="<?= url('usuarios') ?>" class="btn btn-chico" title="Solicitudes de cuenta por aprobar"><?= icono('usuarios') ?><?= $solicitudesPendientes ?> <span class="ocultar-movil">solicitud<?= $solicitudesPendientes > 1 ? 'es' : '' ?></span></a>
                <?php endif; ?>
                <div class="chip-usuario">
                    <span class="avatar"><?= e(iniciales($usuario['nombre'])) ?></span>
                    <span class="ocultar-movil">
                        <strong><?= e($usuario['nombre']) ?></strong>
                        <small><?= e($usuario['rol']) ?></small>
                    </span>
                </div>
                <a href="<?= url('auth', 'logout') ?>" class="btn-salir" title="Cerrar sesión"><?= icono('salir') ?><span class="ocultar-movil">Salir</span></a>
                <button class="btn-menu" id="btnMenu" aria-label="Abrir menú"><?= icono('menu') ?></button>
            </div>
        </div>
    </div>

    <!-- Barra de navegación horizontal -->
    <nav class="nav-horizontal" id="navHorizontal">
        <div class="contenedor">
            <ul>
                <?php foreach ($menu as $clave => $texto): ?>
                    <li><a href="<?= url($clave) ?>" class="<?= $activo === $clave ? 'activo' : '' ?>"><?= icono($clave) ?><?= $texto ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<div class="contenedor diseno">
    <main class="contenido">
        <?php if ($activo !== 'inicio'): ?>
            <nav class="migas" aria-label="Ruta">
                <a href="<?= url() ?>">Inicio</a><?= icono('flecha') ?><span><?= e($titulo) ?></span>
            </nav>
        <?php endif; ?>
        <?= mostrar_flash() ?>
