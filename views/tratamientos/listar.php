<div class="titulo-pagina">
    <div>
        <h1>Tratamientos</h1>
        <p>Procedimientos realizados a partir de cada diagnóstico.</p>
    </div>
    <a href="<?= url('tratamientos', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Registrar tratamiento</a>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="tratamientos">
        <div><label>Buscar</label><input name="buscar" value="<?= e($buscar) ?>" placeholder="Paciente, DNI o tratamiento"></div>
        <button class="btn" type="submit"><?= icono('buscar') ?>Buscar</button>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Diagnóstico</th><th>Tratamiento</th><th>Odontólogo</th><th class="num">Costo</th><th class="num">Saldo</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($tratamientos as $t): $saldo = $t['costo'] - $t['pagado']; ?>
                <tr>
                    <td><?= fecha($t['fecha']) ?></td>
                    <td><?= e($t['paciente']) ?></td>
                    <td>
                        <?php if ($t['diagnostico']): ?>
                            <?= e($t['diagnostico']) ?> <span class="estado estado-<?= e($t['severidad']) ?>"><?= e($t['severidad']) ?></span>
                        <?php else: ?><span class="texto-suave">Sin diagnóstico</span><?php endif; ?>
                    </td>
                    <td><strong><?= e($t['servicio']) ?></strong><?= $t['pieza_dental'] ? ' <span class="texto-suave">(pieza ' . e($t['pieza_dental']) . ')</span>' : '' ?>
                        <div class="texto-suave"><?= e($t['tratamiento']) ?></div></td>
                    <td><?= e($t['odontologo']) ?></td>
                    <td class="num"><?= soles($t['costo']) ?></td>
                    <td class="num"><?= $saldo > 0 ? '<span class="estado estado-Pendiente">' . soles($saldo) . '</span>' : '<span class="estado estado-Pagado">Pagado</span>' ?></td>
                    <td class="acciones">
                        <?php if ($saldo > 0): ?>
                            <a class="btn btn-sec btn-chico" href="<?= url('pagos', 'nuevo', ['tratamiento_id' => $t['id']]) ?>">Cobrar</a>
                        <?php endif; ?>
                        <a class="btn btn-chico" href="<?= url('tratamientos', 'editar', ['id' => $t['id']]) ?>">Editar</a>
                        <?= boton_post('tratamientos', 'eliminar', $t['id'], 'Eliminar') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$tratamientos): ?><tr><td colspan="8" class="vacio">No hay tratamientos registrados.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
