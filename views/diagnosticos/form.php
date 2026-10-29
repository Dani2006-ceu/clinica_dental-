<div class="titulo-pagina"><h1><?= !empty($r['id']) ? 'Editar diagnóstico' : 'Registrar diagnóstico' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('diagnosticos', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <input type="hidden" name="cita_id" value="<?= e($r['cita_id'] ?? '') ?>">
        <div class="form-grid">
            <div class="completo"><label>Paciente *</label>
                <select name="paciente_id" required><option value="">-- Seleccione --</option><?= opciones($pacientes, 'id', 'nombre', $r['paciente_id'] ?? null) ?></select>
            </div>
            <div><label>Odontólogo *</label>
                <select name="odontologo_id" required><option value="">-- Seleccione --</option><?= opciones($odontologos, 'id', 'nombre', $r['odontologo_id'] ?? null) ?></select>
            </div>
            <div><label>Fecha *</label><input type="date" name="fecha" value="<?= e($r['fecha'] ?? date('Y-m-d')) ?>" required></div>
            <div><label>Pieza dental</label><input name="pieza_dental" value="<?= e($r['pieza_dental'] ?? '') ?>" maxlength="20" placeholder="Ej. 3.6 o General"></div>
            <div><label>Código CIE-10</label><input name="codigo_cie10" value="<?= e($r['codigo_cie10'] ?? '') ?>" maxlength="10" placeholder="Ej. K02.1" list="cie10"></div>
            <div><label>Severidad *</label><select name="severidad" required><?= opciones_lista(Diagnostico::SEVERIDADES, $r['severidad'] ?? 'Leve') ?></select></div>
            <div><label>Observaciones</label><input name="observaciones" value="<?= e($r['observaciones'] ?? '') ?>" maxlength="255"></div>
            <div class="completo"><label>Descripción del diagnóstico *</label><textarea name="descripcion" required placeholder="Hallazgos clínicos, síntomas, resultado de la radiografía..."><?= e($r['descripcion'] ?? '') ?></textarea></div>
        </div>
        <datalist id="cie10">
            <option value="K02.1">Caries de la dentina</option>
            <option value="K03.6">Depósitos (sarro) en los dientes</option>
            <option value="K04.0">Pulpitis</option>
            <option value="K05.1">Gingivitis crónica</option>
            <option value="K05.3">Periodontitis crónica</option>
            <option value="K07.3">Anomalías de la posición del diente</option>
            <option value="K08.1">Pérdida de dientes</option>
            <option value="S02.5">Fractura de diente</option>
        </datalist>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar diagnóstico</button>
            <a href="<?= url('diagnosticos') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
