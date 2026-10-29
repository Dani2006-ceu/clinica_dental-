<div class="titulo-pagina"><h1><?= $r ? 'Editar paciente' : 'Registrar paciente' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('pacientes', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div><label>DNI *</label><input name="dni" value="<?= e($r['dni'] ?? '') ?>" required maxlength="12" pattern="[0-9A-Za-z]{8,12}"></div>
            <div><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" value="<?= e($r['fecha_nacimiento'] ?? '') ?>" max="<?= date('Y-m-d') ?>"></div>
            <div><label>Nombres *</label><input name="nombres" value="<?= e($r['nombres'] ?? '') ?>" required maxlength="80"></div>
            <div><label>Apellidos *</label><input name="apellidos" value="<?= e($r['apellidos'] ?? '') ?>" required maxlength="80"></div>
            <div><label>Sexo *</label>
                <select name="sexo" required>
                    <option value="F" <?= ($r['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                    <option value="M" <?= ($r['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                </select>
            </div>
            <div><label>Teléfono</label><input name="telefono" value="<?= e($r['telefono'] ?? '') ?>" maxlength="20"></div>
            <div><label>Correo</label><input type="email" name="email" value="<?= e($r['email'] ?? '') ?>" maxlength="120"></div>
            <div><label>Dirección</label><input name="direccion" value="<?= e($r['direccion'] ?? '') ?>" maxlength="200"></div>
            <div class="completo"><label>Alergias / antecedentes</label><input name="alergias" value="<?= e($r['alergias'] ?? '') ?>" maxlength="255"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('pacientes') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
