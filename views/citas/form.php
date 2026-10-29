<div class="titulo-pagina"><h1><?= $r ? 'Editar cita' : 'Programar cita' ?></h1></div>

<section class="panel">
    <form method="post" action="<?= url('citas', 'guardar') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
        <div class="form-grid">
            <div class="completo"><label>Paciente *</label>
                <select name="paciente_id" required><option value="">-- Seleccione --</option><?= opciones($pacientes, 'id', 'nombre', $r['paciente_id'] ?? null) ?></select>
            </div>
            <div><label>Odontólogo *</label>
                <select name="odontologo_id" required><option value="">-- Seleccione --</option><?= opciones($odontologos, 'id', 'nombre', $r['odontologo_id'] ?? null) ?></select>
            </div>
            <div><label>Servicio *</label>
                <select name="servicio_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (string)($r['servicio_id'] ?? '') === (string)$s['id'] ? 'selected' : '' ?>><?= e($s['nombre']) ?> - <?= e($s['duracion_min']) ?> min</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Fecha *</label><input type="date" name="fecha" value="<?= e($r['fecha'] ?? date('Y-m-d')) ?>" required></div>
            <div><label>Hora *</label><input type="time" name="hora" value="<?= e(substr($r['hora'] ?? '', 0, 5)) ?>" min="08:00" max="20:00" required></div>
            <div><label>Estado</label><select name="estado"><?= opciones_lista(Cita::ESTADOS, $r['estado'] ?? 'Pendiente') ?></select></div>
            <div><label>Motivo</label><input name="motivo" value="<?= e($r['motivo'] ?? '') ?>" maxlength="255"></div>
        </div>
        <div class="form-botones">
            <button class="btn" type="submit">Guardar</button>
            <a href="<?= url('citas') ?>" class="btn btn-sec">Cancelar</a>
        </div>
    </form>
</section>
