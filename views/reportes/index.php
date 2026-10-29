<div class="titulo-pagina">
    <div>
        <h1><?= $nombres[$tipo] ?></h1>
        <p>Periodo: <?= fecha($desde) ?> al <?= fecha($hasta) ?></p>
    </div>
    <a class="btn no-imprimir" target="_blank"
       href="<?= url('reportes', 'documento', ['tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta]) ?>"><?= icono('imprimir') ?>Imprimir / PDF / Excel</a>
</div>

<nav class="pestanas no-imprimir">
    <?php foreach ($nombres as $clave => $texto): ?>
        <a href="<?= url('reportes', 'index', ['tipo' => $clave]) ?>" class="<?= $tipo === $clave ? 'activo' : '' ?>"><?= $texto ?></a>
    <?php endforeach; ?>
</nav>

<section class="panel">
    <form class="filtros no-imprimir" method="get">
        <input type="hidden" name="c" value="reportes">
        <input type="hidden" name="tipo" value="<?= e($tipo) ?>">
        <div><label>Desde</label><input type="date" name="desde" value="<?= e($desde) ?>" required></div>
        <div><label>Hasta</label><input type="date" name="hasta" value="<?= e($hasta) ?>" required></div>
        <button class="btn" type="submit">Generar</button>
    </form>

    <?php if ($tipo === 'atenciones'): ?>
        <div class="resumen">
            <div class="indicador"><span>Total de atenciones</span><strong><?= count($filas) ?></strong></div>
            <div class="indicador acento"><span>Monto facturado</span><strong><?= soles(array_sum(array_column($filas, 'costo'))) ?></strong></div>
        </div>
        <?php if ($resumen): ?>
            <h3 class="seccion-titulo">Atenciones por odontólogo</h3>
            <div class="tabla-scroll" style="margin-bottom:20px">
                <table>
                    <thead><tr><th>Odontólogo</th><th class="num">Atenciones</th><th class="num">Monto</th></tr></thead>
                    <tbody>
                    <?php foreach ($resumen as $r): ?>
                        <tr><td><?= e($r['odontologo']) ?></td><td class="num"><?= e($r['atenciones']) ?></td><td class="num"><?= soles($r['total']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <h3 class="seccion-titulo">Detalle</h3>
        <div class="tabla-scroll">
            <table>
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Diagnóstico</th><th class="num">Costo</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td><?= fecha($f['fecha']) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td><?= e($f['odontologo']) ?></td>
                        <td><?= e($f['servicio']) ?></td>
                        <td><?= e($f['diagnostico'] ?? '-') ?></td>
                        <td class="num"><?= soles($f['costo']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="6" class="vacio">Sin atenciones en el periodo.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tipo === 'ingresos'): ?>
        <div class="resumen">
            <div class="indicador acento"><span>Total de ingresos</span><strong><?= soles(array_sum(array_column($filas, 'monto'))) ?></strong></div>
            <?php foreach ($resumen as $r): ?>
                <div class="indicador"><span><?= e($r['metodo']) ?> (<?= e($r['cantidad']) ?>)</span><strong><?= soles($r['total']) ?></strong></div>
            <?php endforeach; ?>
        </div>
        <div class="tabla-scroll">
            <table>
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Método</th><th>N° operación</th><th class="num">Monto</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td><?= fecha($f['fecha']) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td><?= e($f['metodo']) ?></td>
                        <td><?= e($f['nro_operacion'] ?? '-') ?></td>
                        <td class="num"><?= soles($f['monto']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="5" class="vacio">Sin ingresos en el periodo.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="resumen">
            <div class="indicador acento"><span>Citas pendientes</span><strong><?= count($filas) ?></strong></div>
        </div>
        <div class="tabla-scroll">
            <table>
                <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Teléfono</th><th>Odontólogo</th><th>Servicio</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td><?= fecha($f['fecha']) ?></td>
                        <td><?= substr($f['hora'], 0, 5) ?></td>
                        <td><?= e($f['paciente']) ?></td>
                        <td><?= e($f['telefono'] ?? '-') ?></td>
                        <td><?= e($f['odontologo']) ?></td>
                        <td><?= e($f['servicio']) ?></td>
                        <td><span class="estado estado-<?= e($f['estado']) ?>"><?= e($f['estado']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$filas): ?><tr><td colspan="7" class="vacio">No hay citas pendientes en el periodo.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
