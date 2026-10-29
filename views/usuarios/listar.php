<div class="titulo-pagina">
    <div>
        <h1>Usuarios del sistema</h1>
        <p>Cuentas de acceso y sus roles.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="<?= url('usuarios', 'bitacora') ?>" class="btn btn-sec"><?= icono('escudo') ?>Bitácora</a>
        <a href="<?= url('usuarios', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nuevo usuario</a>
    </div>
</div>

<?php if ($solicitudes): ?>
<section class="panel tabla-scroll">
    <div class="panel-titulo">
        <h2>Solicitudes de cuenta pendientes (<?= count($solicitudes) ?>)</h2>
    </div>
    <p style="margin-bottom:14px;color:var(--texto-3)">Estas personas pidieron acceso desde "Crear cuenta". No pueden ingresar hasta que las apruebes.</p>
    <table>
        <thead><tr><th>Nombre</th><th>Correo</th><th>Solicitado</th><th>Rol a asignar</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><div class="celda-persona"><span class="avatar"><?= e(iniciales($s['nombre'])) ?></span><?= e($s['nombre']) ?></div></td>
                <td><?= e($s['email']) ?></td>
                <td><?= fecha($s['creado_en']) ?></td>
                <td>
                    <select name="rol" form="aprobar<?= $s['id'] ?>">
                        <?php foreach (Usuario::ROLES as $rol): ?>
                            <option <?= $rol === 'Recepcionista' ? 'selected' : '' ?>><?= e($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="acciones">
                    <form method="post" action="<?= url('usuarios', 'aprobar') ?>" id="aprobar<?= $s['id'] ?>">
                        <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $s['id'] ?>">
                        <button class="btn btn-chico">Aprobar</button>
                    </form>
                    <?= boton_post('usuarios', 'rechazar', $s['id'], 'Rechazar', 'btn btn-peligro btn-chico', '¿Rechazar esta solicitud? La cuenta se eliminará.') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php endif; ?>

<section class="panel tabla-scroll">
    <table>
        <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Registrado</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><div class="celda-persona"><span class="avatar"><?= e(iniciales($u['nombre'])) ?></span><?= e($u['nombre']) ?></div></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['rol']) ?></td>
                <td><?= $u['activo'] ? 'Activo' : '<strong>Inactivo</strong>' ?></td>
                <td><?= fecha($u['creado_en']) ?></td>
                <td class="acciones">
                    <a class="btn btn-chico" href="<?= url('usuarios', 'editar', ['id' => $u['id']]) ?>">Editar</a>
                    <?php if ((int)$u['id'] !== $miId): ?>
                        <?= boton_post('usuarios', 'eliminar', $u['id'], 'Eliminar') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
