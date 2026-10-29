<?php
$tarjetas = [
    ['pacientes',    'pacientes.jpg',    'Pacientes',    'Registra y consulta los datos personales e historial de cada paciente.'],
    ['odontologos',  'odontologos.jpg',  'Odontólogos',  'Administra al equipo de especialistas según su especialidad.'],
    ['citas',        'citas.jpg',        'Citas',        'Programa citas con fecha, hora y estado, sin cruces de horario.'],
    ['diagnosticos', 'diagnosticos.jpg', 'Diagnósticos', 'Registra los hallazgos clínicos con código CIE-10 y severidad.'],
    ['tratamientos', 'tratamientos.jpg', 'Tratamientos', 'Registra los procedimientos realizados a partir de cada diagnóstico.'],
    ['servicios',    'servicios.jpg',    'Servicios',    'Gestiona los servicios que ofrece la clínica, su precio y duración.'],
    ['pagos',        'pagos.jpg',        'Pagos',        'Controla pagos en efectivo, tarjeta de crédito, débito o Yape.'],
    ['reportes',     'banner.jpg',       'Reportes',     'Atenciones realizadas, ingresos obtenidos y citas pendientes.'],
];
?>
<section class="banner">
    <img src="imagenes/banner.jpg" alt="Clínica <?= CLINICA_NOMBRE ?>">
    <div class="banner-texto">
        <span class="etiqueta">Panel de gestión clínica</span>
        <h1>Hola, <?= e(explode(' ', $_SESSION['usuario']['nombre'])[0]) ?>. Bienvenido(a) de nuevo.</h1>
        <p>Gestiona pacientes, citas, diagnósticos, tratamientos y pagos de la clínica desde un solo lugar.</p>
        <div class="banner-botones">
            <a href="<?= url('citas', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Programar cita</a>
            <a href="<?= url('pacientes', 'nuevo') ?>" class="btn btn-sec">Nuevo paciente</a>
        </div>
    </div>
</section>

<section class="indicadores">
    <div class="indicador">
        <span class="icono-caja"><?= icono('pacientes') ?></span>
        <div><span>Pacientes registrados</span><strong><?= e($totalPacientes) ?></strong></div>
    </div>
    <div class="indicador morado">
        <span class="icono-caja"><?= icono('citas') ?></span>
        <div><span>Citas para hoy</span><strong><?= e($citasHoy) ?></strong></div>
    </div>
    <div class="indicador acento">
        <span class="icono-caja"><?= icono('pendiente') ?></span>
        <div><span>Citas pendientes</span><strong><?= e($citasPendientes) ?></strong></div>
    </div>
    <div class="indicador verde">
        <span class="icono-caja"><?= icono('dinero') ?></span>
        <div><span>Ingresos del mes</span><strong><?= soles($ingresosMes) ?></strong></div>
    </div>
</section>

<div class="titulo-pagina">
    <div>
        <h1>Módulos del sistema</h1>
        <p>Accede rápidamente a cada área de la clínica.</p>
    </div>
</div>
<section class="tarjetas">
    <?php foreach ($tarjetas as [$modulo, $img, $nombre, $desc]): ?>
        <a class="tarjeta" href="<?= url($modulo) ?>">
            <div class="tarjeta-img">
                <img src="imagenes/<?= $img ?>" alt="<?= e($nombre) ?>" loading="lazy">
            </div>
            <span class="insignia"><?= icono($modulo) ?></span>
            <div class="tarjeta-cuerpo">
                <h3><?= e($nombre) ?></h3>
                <p><?= e($desc) ?></p>
                <span class="enlace-mod">Ingresar <?= icono('flecha') ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</section>

<section class="panel" style="margin-top:28px">
    <div class="panel-titulo">
        <h2>Próximas citas</h2>
        <a href="<?= url('citas') ?>" class="btn btn-sec btn-chico">Ver todas</a>
    </div>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Estado</th></tr></thead>
            <tbody>
            <?php foreach ($proximas as $c): ?>
                <tr>
                    <td><?= fecha($c['fecha']) ?></td>
                    <td><strong><?= substr($c['hora'], 0, 5) ?></strong></td>
                    <td><div class="celda-persona"><span class="avatar"><?= e(iniciales($c['paciente'])) ?></span><?= e($c['paciente']) ?></div></td>
                    <td><?= e($c['odontologo']) ?></td>
                    <td><?= e($c['servicio']) ?></td>
                    <td><span class="estado estado-<?= e($c['estado']) ?>"><?= e($c['estado']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$proximas): ?><tr><td colspan="6" class="vacio">No hay citas próximas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel ubicacion">
    <div class="ubicacion-info">
        <div>
            <h2>Nuestra ubicación</h2>
            <p class="texto-suave">Visítanos en <?= CLINICA_NOMBRE ?>.</p>
        </div>
        <div class="dato"><span class="icono-caja"><?= icono('ubicacion') ?></span>
            <div><strong>Dirección</strong><?= CLINICA_DIRECCION ?></div></div>
        <div class="dato"><span class="icono-caja"><?= icono('correo') ?></span>
            <div><strong>Correo</strong><a href="mailto:<?= CLINICA_CORREO ?>"><?= CLINICA_CORREO ?></a></div></div>
        <div class="dato"><span class="icono-caja"><?= icono('reloj') ?></span>
            <div><strong>Horario</strong>Lunes a sábado · 8:00 a.m. - 8:00 p.m.</div></div>
        <a href="<?= CLINICA_MAPA ?>" target="_blank" rel="noopener" class="btn" style="align-self:flex-start"><?= icono('ubicacion') ?>Cómo llegar</a>
    </div>
    <iframe src="<?= CLINICA_MAPA_EMBED ?>" title="Mapa de <?= CLINICA_NOMBRE ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>
