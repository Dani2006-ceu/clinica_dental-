<div class="titulo-pagina"><h1><?= !empty($r['id']) ? 'Editar tratamiento' : 'Registrar tratamiento' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('tratamientos', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <input type="hidden" name="cita_id" value="<?= e($r['cita_id'] ?? '') ?>">
        <div class="form-grid">
            <div class="completo"><label>Diagnóstico que se trata</label>
                <select name="diagnostico_id" id="diagnostico">
                    <option value="">-- Sin diagnóstico previo --</option>
                    <?php foreach ($diagnosticos as $d): ?>
                        <option value="<?= $d['id'] ?>" data-paciente="<?= $d['paciente_id'] ?>" data-odontologo="<?= $d['odontologo_id'] ?>" data-pieza="<?= e($d['pieza_dental']) ?>"
                            <?= (string)($r['diagnostico_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="completo"><label>Paciente *</label>
                <select name="paciente_id" id="paciente" required><option value="">-- Seleccione --</option><?= opciones($pacientes, 'id', 'nombre', $r['paciente_id'] ?? null) ?></select>
            </div>
            <div><label>Odontólogo *</label>
                <select name="odontologo_id" id="odontologo" required><option value="">-- Seleccione --</option><?= opciones($odontologos, 'id', 'nombre', $r['odontologo_id'] ?? null) ?></select>
            </div>
            <div><label>Servicio *</label>
                <select name="servicio_id" id="servicio" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>" data-precio="<?= e($s['precio']) ?>" <?= (string)($r['servicio_id'] ?? '') === (string)$s['id'] ? 'selected' : '' ?>>
                            <?= e($s['nombre']) ?> - <?= soles($s['precio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Fecha *</label><input type="date" name="fecha" value="<?= e($r['fecha'] ?? date('Y-m-d')) ?>" required></div>
            <div><label>Pieza dental</label><input name="pieza_dental" id="pieza" value="<?= e($r['pieza_dental'] ?? '') ?>" maxlength="20" placeholder="Ej. 3.6"></div>
            <div class="completo"><label>Tratamiento realizado *</label><textarea name="tratamiento" required placeholder="Procedimiento, materiales usados, indicaciones..."><?= e($r['tratamiento'] ?? '') ?></textarea></div>
            <div><label>Costo (S/) *</label><input type="number" step="0.01" min="0" name="costo" id="costo" value="<?= e($r['costo'] ?? '') ?>" required></div>
            <div><label>Observaciones</label><input name="observaciones" value="<?= e($r['observaciones'] ?? '') ?>" maxlength="255"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar tratamiento</button>
            <a href="<?= url('tratamientos') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
