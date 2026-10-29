<div class="titulo-pagina"><h1><?= $r ? 'Editar odontólogo' : 'Registrar odontólogo' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('odontologos', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div><label>DNI *</label><input name="dni" value="<?= e($r['dni'] ?? '') ?>" required maxlength="12"></div>
            <div><label>N° COP *</label><input name="cop" value="<?= e($r['cop'] ?? '') ?>" required maxlength="15"></div>
            <div><label>Nombres *</label><input name="nombres" value="<?= e($r['nombres'] ?? '') ?>" required maxlength="80"></div>
            <div><label>Apellidos *</label><input name="apellidos" value="<?= e($r['apellidos'] ?? '') ?>" required maxlength="80"></div>
            <div><label>Especialidad *</label>
                <select name="especialidad_id" required>
                    <option value="">-- Seleccione --</option>
                    <?= opciones($especialidades, 'id', 'nombre', $r['especialidad_id'] ?? null) ?>
                </select>
            </div>
            <div><label>Estado</label><select name="estado"><?= opciones_lista(['Activo', 'Inactivo'], $r['estado'] ?? 'Activo') ?></select></div>
            <div><label>Teléfono</label><input name="telefono" value="<?= e($r['telefono'] ?? '') ?>" maxlength="20"></div>
            <div><label>Correo</label><input type="email" name="email" value="<?= e($r['email'] ?? '') ?>" maxlength="120"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('odontologos') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
