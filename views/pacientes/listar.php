<div class="titulo-pagina">
    <div>
        <h1>Pacientes</h1>
        <p>Datos personales y antecedentes de los pacientes.</p>
    </div>
    <a href="<?= url('pacientes', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nuevo paciente</a>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="pacientes">
        <div><label>Buscar</label><input name="buscar" value="<?= e($buscar) ?>" placeholder="DNI, nombres o apellidos"></div>
        <button class="btn" type="submit"><?= icono('buscar') ?>Buscar</button>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>DNI</th><th>Paciente</th><th>Edad</th><th>Teléfono</th><th>Correo</th><th>Alergias</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= e($p['dni']) ?></td>
                    <td><div class="celda-persona"><span class="avatar"><?= e(iniciales($p['nombres'] . ' ' . $p['apellidos'])) ?></span><?= e($p['apellidos'] . ', ' . $p['nombres']) ?></div></td>
                    <td><?= $p['fecha_nacimiento'] ? (new DateTime($p['fecha_nacimiento']))->diff(new DateTime())->y . ' años' : '-' ?></td>
                    <td><?= e($p['telefono'] ?? '-') ?></td>
                    <td><?= e($p['email'] ?? '-') ?></td>
                    <td><?= e($p['alergias'] ?? '-') ?></td>
                    <td class="acciones">
                        <a class="btn btn-chico" href="<?= url('pacientes', 'editar', ['id' => $p['id']]) ?>">Editar</a>
                        <?= boton_post('pacientes', 'eliminar', $p['id'], 'Eliminar') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pacientes): ?><tr><td colspan="7" class="vacio">No se encontraron pacientes.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
