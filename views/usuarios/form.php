<div class="titulo-pagina"><h1><?= $r ? 'Editar usuario' : 'Nuevo usuario' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('usuarios', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div><label>Nombre *</label><input name="nombre" value="<?= e($r['nombre'] ?? '') ?>" required maxlength="100"></div>
            <div><label>Correo *</label><input type="email" name="email" value="<?= e($r['email'] ?? '') ?>" required maxlength="120"></div>
            <div><label>Rol</label><select name="rol"><?= opciones_lista(Usuario::ROLES, $r['rol'] ?? 'Recepcionista') ?></select></div>
            <div><label>Estado</label><select name="activo">
                <option value="1">Activo</option>
                <option value="0"<?= isset($r['activo']) && !$r['activo'] ? ' selected' : '' ?>>Inactivo (sin acceso)</option>
            </select></div>
            <div><label>Contraseña <?= $r ? '(dejar vacío para no cambiar)' : '*' ?></label>
                <input type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" <?= $r ? '' : 'required' ?>>
                <small>Mín. 8 caracteres, con letras y números.</small></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('usuarios') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
