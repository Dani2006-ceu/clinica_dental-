<?php
$titulo = mb_strtoupper('Reporte de ' . $nombres[$tipo]);
$periodo = 'Del ' . fecha($desde) . ' al ' . fecha($hasta);
// Atributos para que Excel reciba montos como números y textos (fechas, DNI) sin convertir
$monto = fn($v) => 'data-v="' . (float)$v . '" data-z="&quot;S/ &quot;#,##0.00"';
$texto = 'data-t="s"';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($nombres[$tipo]) ?> | <?= CLINICA_NOMBRE ?></title>
    <link rel="icon" href="imagenes/logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assetes/css/documento.css">
</head>
<body class="doc-body"
      data-archivo="Reporte-<?= e($tipo) ?>-<?= e($desde) ?>-al-<?= e($hasta) ?>"
      data-titulo="<?= e($titulo) ?>" data-periodo="<?= e($periodo) ?>" data-clinica="<?= e(CLINICA_NOMBRE . ' - ' . CLINICA_DIRECCION) ?>">

<?php
$volver = url('reportes', 'index', ['tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta]);
$tituloBarra = $nombres[$tipo];
$subBarra = $periodo;
$conExcel = true;
require __DIR__ . '/../layout/barra_documento.php';
?>

<article class="hoja">
    <div class="hoja-cuerpo">
        <header class="membrete">
            <div class="membrete-marca">
                <img src="imagenes/logo.svg" alt="Logo">
                <div>
                    <div class="lema">Clínica Odontológica</div>
                    <h1><?= CLINICA_NOMBRE ?></h1>
                    <p><?= CLINICA_DIRECCION ?><br><?= CLINICA_CORREO ?></p>
                </div>
            </div>
            <div class="caja-doc">
                <div class="tipo">REPORTE</div>
                <div class="numero" style="font-size:14px"><?= e(mb_strtoupper($nombres[$tipo])) ?></div>
                <div class="fecha"><?= $periodo ?></div>
            </div>
        </header>

        <div class="titulo-seccion">Información del reporte</div>
        <div class="datos">
            <div class="dato-doc"><span>Reporte</span><strong><?= e($nombres[$tipo]) ?></strong></div>
            <div class="dato-doc"><span>Periodo</span><strong><?= $periodo ?></strong></div>
            <div class="dato-doc"><span>Generado</span><strong><?= date('d/m/Y H:i') ?></strong></div>
            <div class="dato-doc completo"><span>Elaborado por</span><strong><?= e($_SESSION['usuario']['nombre']) ?> (<?= e($_SESSION['usuario']['rol']) ?>)</strong></div>
        </div>

        <?php if ($tipo === 'atenciones'): $totalAt = array_sum(array_column($filas, 'costo')); ?>
            <div class="titulo-seccion">Resumen</div>
            <div class="resumen-doc">
                <div><span>Atenciones</span><strong><?= count($filas) ?></strong></div>
                <div><span>Odontólogos</span><strong><?= count($resumen) ?></strong></div>
                <div class="destacado"><span>Monto facturado</span><strong><?= soles($totalAt) ?></strong></div>
                <div><span>Promedio por atención</span><strong><?= soles($filas ? $totalAt / count($filas) : 0) ?></strong></div>
            </div>

            <?php if ($resumen): ?>
                <div class="titulo-seccion">Atenciones por odontólogo</div>
                <table class="tabla-doc tabla-excel" data-hoja="Por odontólogo">
                    <thead><tr><th>Odontólogo</th><th class="num">Atenciones</th><th class="num">Monto</th><th class="num">% del total</th></tr></thead>
                    <tbody>
                    <?php foreach ($resumen as $r): ?>
                        <tr>
                            <td><?= e($r['odontologo']) ?></td>
                            <td class="num"><?= e($r['atenciones']) ?></td>
                            <td class="num" <?= $monto($r['total']) ?>><?= soles($r['total']) ?></td>
                            <td class="num" <?= $texto ?>><?= $totalAt > 0 ? number_format($r['total'] / $totalAt * 100, 1) : '0.0' ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="titulo-seccion">Detalle de atenciones</div>
            <table class="tabla-doc tabla-excel" data-hoja="Detalle">
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Diagnóstico</th><th class="num">Costo</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td <?= $texto ?>><?= fecha($f['fecha']) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td><?= e($f['odontologo']) ?></td>
                        <td><?= e($f['servicio']) ?></td>
                        <td><?= e($f['diagnostico'] ?? '—') ?></td>
                        <td class="num" <?= $monto($f['costo']) ?>><?= soles($f['costo']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="6" class="vacio">Sin atenciones en el periodo.</td></tr><?php endif; ?>
                </tbody>
                <tfoot><tr><td colspan="5">TOTAL</td><td class="num" <?= $monto($totalAt) ?>><?= soles($totalAt) ?></td></tr></tfoot>
            </table>

        <?php elseif ($tipo === 'ingresos'): $totalIn = array_sum(array_column($filas, 'monto')); ?>
            <div class="titulo-seccion">Resumen por método de pago</div>
            <div class="resumen-doc">
                <div class="destacado"><span>Total de ingresos</span><strong><?= soles($totalIn) ?></strong></div>
                <?php foreach ($resumen as $r): ?>
                    <div><span><?= e($r['metodo']) ?> (<?= e($r['cantidad']) ?>)</span><strong><?= soles($r['total']) ?></strong></div>
                <?php endforeach; ?>
            </div>

            <?php if ($resumen): ?>
                <table class="tabla-doc tabla-excel" data-hoja="Por método" style="margin-top:12px">
                    <thead><tr><th>Método de pago</th><th class="num">N° de pagos</th><th class="num">Monto</th><th class="num">% del total</th></tr></thead>
                    <tbody>
                    <?php foreach ($resumen as $r): ?>
                        <tr>
                            <td><?= e($r['metodo']) ?></td>
                            <td class="num"><?= e($r['cantidad']) ?></td>
                            <td class="num" <?= $monto($r['total']) ?>><?= soles($r['total']) ?></td>
                            <td class="num" <?= $texto ?>><?= $totalIn > 0 ? number_format($r['total'] / $totalIn * 100, 1) : '0.0' ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="titulo-seccion">Detalle de pagos</div>
            <table class="tabla-doc tabla-excel" data-hoja="Detalle">
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Método</th><th>N° operación</th><th class="num">Monto</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td <?= $texto ?>><?= fecha($f['fecha']) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td><?= e($f['metodo']) ?></td>
                        <td <?= $texto ?>><?= e($f['nro_operacion'] ?? '—') ?></td>
                        <td class="num" <?= $monto($f['monto']) ?>><?= soles($f['monto']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="5" class="vacio">Sin ingresos en el periodo.</td></tr><?php endif; ?>
                </tbody>
                <tfoot><tr><td colspan="4">TOTAL</td><td class="num" <?= $monto($totalIn) ?>><?= soles($totalIn) ?></td></tr></tfoot>
            </table>

        <?php else: ?>
            <div class="titulo-seccion">Resumen</div>
            <div class="resumen-doc">
                <div class="destacado"><span>Citas pendientes</span><strong><?= count($filas) ?></strong></div>
                <div><span>Pendientes</span><strong><?= count(array_filter($filas, fn($f) => $f['estado'] === 'Pendiente')) ?></strong></div>
                <div><span>Confirmadas</span><strong><?= count(array_filter($filas, fn($f) => $f['estado'] === 'Confirmada')) ?></strong></div>
            </div>

            <div class="titulo-seccion">Agenda</div>
            <table class="tabla-doc tabla-excel" data-hoja="Citas pendientes">
                <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Teléfono</th><th>Odontólogo</th><th>Servicio</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td <?= $texto ?>><?= fecha($f['fecha']) ?></td>
                        <td <?= $texto ?>><?= substr($f['hora'], 0, 5) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td <?= $texto ?>><?= e($f['telefono'] ?? '—') ?></td>
                        <td><?= e($f['odontologo']) ?></td>
                        <td><?= e($f['servicio']) ?></td>
                        <td><?= e($f['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="7" class="vacio">No hay citas pendientes en el periodo.</td></tr><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="firmas">
            <div><strong><?= e($_SESSION['usuario']['nombre']) ?></strong>Elaborado por</div>
            <div><strong>Administración</strong><?= CLINICA_NOMBRE ?></div>
        </div>
    </div>

    <footer class="pie-doc">
        <span><?= CLINICA_NOMBRE ?> · <?= CLINICA_DIRECCION ?> · <?= CLINICA_CORREO ?></span>
        <span>Documento generado el <?= date('d/m/Y H:i') ?></span>
    </footer>
</article>

<script src="assetes/js/lib/html2pdf.bundle.min.js"></script>
<script src="assetes/js/lib/xlsx.full.min.js"></script>
<script src="assetes/js/documento.js"></script>
</body>
</html>
