<div class="titulo-pagina"><h1><?= $r ? 'Editar especialidad' : 'Nueva especialidad' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('especialidades', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div><label>Nombre *</label><input name="nombre" value="<?= e($r['nombre'] ?? '') ?>" required maxlength="80"></div>
            <div><label>Descripción</label><input name="descripcion" value="<?= e($r['descripcion'] ?? '') ?>" maxlength="255"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('especialidades') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
