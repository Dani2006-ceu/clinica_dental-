<div class="titulo-pagina">
    <div>
        <h1>Bitácora de seguridad</h1>
        <p>Últimos 300 accesos y operaciones. Estos registros no se pueden modificar ni eliminar.</p>
    </div>
    <a href="<?= url('usuarios') ?>" class="btn btn-sec">Volver</a>
</div>

<section class="panel tabla-scroll">
    <table>
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Detalle</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($registros as $b): ?>
            <tr>
                <td><?= e(date('d/m/Y H:i:s', strtotime($b['creado_en']))) ?></td>
                <td><?= e($b['nombre'] ?? $b['email'] ?? '-') ?></td>
                <td><?= e($b['accion']) ?></td>
                <td><?= e($b['detalle']) ?></td>
                <td><?= e($b['ip']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
