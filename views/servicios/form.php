<div class="titulo-pagina"><h1><?= $r ? 'Editar servicio' : 'Registrar servicio' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('servicios', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div><label>Nombre *</label><input name="nombre" value="<?= e($r['nombre'] ?? '') ?>" required maxlength="100"></div>
            <div><label>Precio (S/) *</label><input type="number" step="0.01" min="0" name="precio" value="<?= e($r['precio'] ?? '') ?>" required></div>
            <div><label>Duración (minutos) *</label><input type="number" min="5" step="5" name="duracion_min" value="<?= e($r['duracion_min'] ?? 30) ?>" required></div>
            <div><label>Estado</label><select name="estado"><?= opciones_lista(['Activo', 'Inactivo'], $r['estado'] ?? 'Activo') ?></select></div>
            <div class="completo"><label>Descripción</label><input name="descripcion" value="<?= e($r['descripcion'] ?? '') ?>" maxlength="255"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('servicios') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
