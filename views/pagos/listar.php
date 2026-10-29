<div class="titulo-pagina">
    <div>
        <h1>Pagos</h1>
        <p>Cobros en efectivo, tarjeta de crédito, tarjeta de débito o Yape.</p>
    </div>
    <a href="<?= url('pagos', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Registrar pago</a>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="pagos">
        <div><label>Desde</label><input type="date" name="desde" value="<?= e($f['desde']) ?>"></div>
        <div><label>Hasta</label><input type="date" name="hasta" value="<?= e($f['hasta']) ?>"></div>
        <div><label>Método</label><select name="metodo"><option value="">Todos</option><?= opciones_lista(Pago::METODOS, $f['metodo']) ?></select></div>
        <button class="btn" type="submit">Filtrar</button>
        <a href="<?= url('pagos') ?>" class="btn btn-sec">Limpiar</a>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>N° boleta</th><th>Fecha</th><th>Paciente</th><th>Concepto</th><th>Método</th><th>N° operación</th><th class="num">Monto</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($pagos as $p): ?>
                <tr>
                    <td><strong><?= nro_boleta((int)$p['id']) ?></strong></td>
                    <td><?= fecha($p['fecha']) ?></td>
                    <td><?= e($p['paciente']) ?></td>
                    <td><?= e($p['servicio'] ?? 'Pago a cuenta') ?></td>
                    <td><?= $p['metodo'] === 'Yape' ? '<span class="metodo-yape">Yape</span>' : e($p['metodo']) ?></td>
                    <td><?= e($p['nro_operacion'] ?? '-') ?></td>
                    <td class="num"><strong><?= soles($p['monto']) ?></strong></td>
                    <td><span class="estado estado-<?= e($p['estado']) ?>"><?= e($p['estado']) ?></span></td>
                    <td class="acciones">
                        <a class="btn btn-sec btn-chico" href="<?= url('pagos', 'boleta', ['id' => $p['id']]) ?>" target="_blank"><?= icono('imprimir') ?>Boleta</a>
                        <?php if ($p['estado'] === 'Pagado'): ?>
                            <a class="btn btn-chico" href="<?= url('pagos', 'editar', ['id' => $p['id']]) ?>">Editar</a>
                            <?= boton_post('pagos', 'anular', $p['id'], 'Anular', 'btn btn-peligro btn-chico', '¿Anular este pago? Quedará registrado como anulado y no se podrá modificar.') ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pagos): ?><tr><td colspan="9" class="vacio">No hay pagos registrados.</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><th colspan="6" class="num">Total cobrado</th><th class="num"><?= soles($total) ?></th><th colspan="2"></th></tr></tfoot>
        </table>
    </div>
</section>
