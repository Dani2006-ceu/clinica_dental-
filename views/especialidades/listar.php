<div class="titulo-pagina">
    <div>
        <h1>Especialidades</h1>
        <p>Áreas de atención de los odontólogos.</p>
    </div>
    <a href="<?= url('especialidades', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nueva especialidad</a>
</div>

<section class="panel tabla-scroll">
    <table>
        <thead><tr><th>Especialidad</th><th>Descripción</th><th class="num">Odontólogos</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($especialidades as $esp): ?>
            <tr>
                <td><strong><?= e($esp['nombre']) ?></strong></td>
                <td><?= e($esp['descripcion']) ?></td>
                <td class="num"><?= e($esp['total']) ?></td>
                <td class="acciones">
                    <a class="btn btn-chico" href="<?= url('especialidades', 'editar', ['id' => $esp['id']]) ?>">Editar</a>
                    <?= boton_post('especialidades', 'eliminar', $esp['id'], 'Eliminar') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$especialidades): ?><tr><td colspan="4" class="vacio">No hay especialidades registradas.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
