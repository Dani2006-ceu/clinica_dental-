<div class="titulo-pagina">
    <div>
        <h1>Servicios de la clínica</h1>
        <p>Catálogo de servicios con su precio y duración.</p>
    </div>
    <a href="<?= url('servicios', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nuevo servicio</a>
</div>

<section class="panel tabla-scroll">
    <table>
        <thead><tr><th>Servicio</th><th>Descripción</th><th class="num">Precio</th><th class="num">Duración</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($servicios as $s): ?>
            <tr>
                <td><strong><?= e($s['nombre']) ?></strong></td>
                <td><?= e($s['descripcion']) ?></td>
                <td class="num"><?= soles($s['precio']) ?></td>
                <td class="num"><?= e($s['duracion_min']) ?> min</td>
                <td><span class="estado estado-<?= e($s['estado']) ?>"><?= e($s['estado']) ?></span></td>
                <td class="acciones">
                    <a class="btn btn-chico" href="<?= url('servicios', 'editar', ['id' => $s['id']]) ?>">Editar</a>
                    <?= boton_post('servicios', 'eliminar', $s['id'], 'Eliminar') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$servicios): ?><tr><td colspan="6" class="vacio">No hay servicios registrados.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
