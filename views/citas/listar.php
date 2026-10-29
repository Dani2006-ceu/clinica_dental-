<div class="titulo-pagina">
    <div>
        <h1>Citas odontológicas</h1>
        <p>Agenda de la clínica con fecha, hora y estado.</p>
    </div>
    <a href="<?= url('citas', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Programar cita</a>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="citas">
        <div><label>Desde</label><input type="date" name="desde" value="<?= e($f['desde']) ?>"></div>
        <div><label>Hasta</label><input type="date" name="hasta" value="<?= e($f['hasta']) ?>"></div>
        <div><label>Estado</label><select name="estado"><option value="">Todos</option><?= opciones_lista(Cita::ESTADOS, $f['estado']) ?></select></div>
        <div><label>Odontólogo</label>
            <select name="odontologo"><option value="0">Todos</option><?= opciones($odontologos, 'id', 'nombre', $f['odontologo']) ?></select>
        </div>
        <button class="btn" type="submit">Filtrar</button>
        <a href="<?= url('citas') ?>" class="btn btn-sec">Limpiar</a>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($citas as $c): ?>
                <tr>
                    <td><?= fecha($c['fecha']) ?></td>
                    <td><strong><?= substr($c['hora'], 0, 5) ?></strong></td>
                    <td><?= e($c['paciente']) ?></td>
                    <td><?= e($c['odontologo']) ?></td>
                    <td><?= e($c['servicio']) ?></td>
                    <td>
                        <form method="post" action="<?= url('citas', 'estado') ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <select name="estado" class="auto-enviar" title="Cambiar estado"><?= opciones_lista(Cita::ESTADOS, $c['estado']) ?></select>
                        </form>
                    </td>
                    <td class="acciones">
                        <?php if ($c['estado'] !== 'Cancelada'): ?>
                            <a class="btn btn-sec btn-chico" href="<?= url('diagnosticos', 'nuevo', ['cita_id' => $c['id']]) ?>">Atender</a>
                        <?php endif; ?>
                        <a class="btn btn-chico" href="<?= url('citas', 'editar', ['id' => $c['id']]) ?>">Editar</a>
                        <?= boton_post('citas', 'eliminar', $c['id'], 'Eliminar') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$citas): ?><tr><td colspan="7" class="vacio">No hay citas para los filtros indicados.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
