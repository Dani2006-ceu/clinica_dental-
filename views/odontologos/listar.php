<div class="titulo-pagina">
    <div>
        <h1>Odontólogos</h1>
        <p>Equipo de especialistas de la clínica.</p>
    </div>
    <div class="acciones">
        <a href="<?= url('especialidades') ?>" class="btn btn-sec"><?= icono('especialidad') ?>Especialidades</a>
        <a href="<?= url('odontologos', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nuevo odontólogo</a>
    </div>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="odontologos">
        <div><label>Especialidad</label>
            <select name="especialidad">
                <option value="0">Todas</option>
                <?= opciones($especialidades, 'id', 'nombre', $filtroEsp) ?>
            </select>
        </div>
        <button class="btn" type="submit">Filtrar</button>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>COP</th><th>Odontólogo</th><th>Especialidad</th><th>Teléfono</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($odontologos as $o): ?>
                <tr>
                    <td><?= e($o['cop']) ?></td>
                    <td><div class="celda-persona"><span class="avatar"><?= e(iniciales($o['nombres'] . ' ' . $o['apellidos'])) ?></span>Dr(a). <?= e($o['nombres'] . ' ' . $o['apellidos']) ?></div></td>
                    <td><?= e($o['especialidad']) ?></td>
                    <td><?= e($o['telefono'] ?? '-') ?></td>
                    <td><?= e($o['email'] ?? '-') ?></td>
                    <td><span class="estado estado-<?= e($o['estado']) ?>"><?= e($o['estado']) ?></span></td>
                    <td class="acciones">
                        <a class="btn btn-chico" href="<?= url('odontologos', 'editar', ['id' => $o['id']]) ?>">Editar</a>
                        <?= boton_post('odontologos', 'eliminar', $o['id'], 'Eliminar') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$odontologos): ?><tr><td colspan="7" class="vacio">No hay odontólogos registrados.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
