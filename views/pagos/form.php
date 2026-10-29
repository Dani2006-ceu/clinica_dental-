<div class="titulo-pagina"><h1><?= !empty($r['id']) ? 'Editar pago' : 'Registrar pago' ?></h1></div>

<div class="pago-layout">
    <section class="panel">
        <form method="post" action="<?= url('pagos', 'guardar') ?>" id="formPago">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
            <div class="form-grid">
                <div class="completo"><label>Paciente *</label>
                    <select name="paciente_id" id="paciente" required><option value="">-- Seleccione --</option><?= opciones($pacientes, 'id', 'nombre', $r['paciente_id'] ?? null) ?></select>
                </div>
                <div class="completo"><label>Tratamiento (opcional)</label>
                    <select name="tratamiento_id" id="tratamiento">
                        <option value="">-- Pago a cuenta / sin tratamiento --</option>
                        <?php foreach ($tratamientos as $t): ?>
                            <option value="<?= $t['id'] ?>" data-paciente="<?= $t['paciente_id'] ?>" data-saldo="<?= e($t['saldo']) ?>" data-concepto="<?= e($t['servicio']) ?>"
                                <?= (string)($r['tratamiento_id'] ?? '') === (string)$t['id'] ? 'selected' : '' ?>>
                                <?= e($t['nombre']) ?> — saldo <?= soles($t['saldo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label>Fecha *</label><input type="date" name="fecha" value="<?= e($r['fecha'] ?? date('Y-m-d')) ?>" required></div>
                <div><label>Monto (S/) *</label><input type="number" step="0.01" min="0.01" name="monto" id="monto" value="<?= e($r['monto'] ?? '') ?>" placeholder="0.00" required></div>
                <div><label>Método de pago *</label>
                    <select name="metodo" id="metodo" required><?= opciones_lista(Pago::METODOS, $r['metodo'] ?? 'Efectivo') ?></select>
                </div>
                <div><label id="lblOperacion">N° de operación (tarjeta / Yape)</label><input name="nro_operacion" id="nroOperacion" value="<?= e($r['nro_operacion'] ?? '') ?>" maxlength="40"></div>
                <div class="completo"><label>Observación</label><input name="observacion" value="<?= e($r['observacion'] ?? '') ?>" maxlength="255"></div>
            </div>
            <div class="form-botones">
                <button class="btn" type="submit">Guardar pago</button>
                <a href="<?= url('pagos') ?>" class="btn btn-sec">Cancelar</a>
            </div>
        </form>
    </section>

    <!-- Panel de cobro con Yape: se muestra al elegir el método Yape -->
    <aside class="yape-card" id="panelYape" hidden>
        <div class="yape-cabecera">
            <span class="yape-logo">yape</span>
            <span>Cobro con QR</span>
        </div>
        <div class="yape-monto">
            <small>Monto a pagar</small>
            <strong>S/ <span class="js-yape-monto">0.00</span></strong>
            <span class="yape-concepto js-yape-concepto"></span>
        </div>
        <img src="<?= e(YAPE_QR) ?>" alt="QR de Yape de <?= e(YAPE_TITULAR) ?>" class="yape-qr">
        <div class="yape-botones">
            <button type="button" class="btn yape-btn" id="copiarMonto"><?= icono('pagos') ?>Copiar monto</button>
            <button type="button" class="btn btn-sec" id="mostrarYape">Mostrar al paciente</button>
        </div>
        <ol class="yape-pasos">
            <li>El paciente escanea el QR con su app Yape.</li>
            <li>Escribe el monto <strong>S/ <span class="js-yape-monto">0.00</span></strong> y confirma.</li>
            <li>Copia aquí el <strong>N° de operación</strong> de la constancia.</li>
        </ol>
        <p class="yape-nota">El QR personal de Yape no guarda montos: el paciente debe escribir el monto mostrado.</p>
    </aside>
</div>

<!-- Vista a pantalla completa para mostrar al paciente -->
<div class="yape-modal" id="modalYape" hidden>
    <div class="yape-modal-caja">
        <button type="button" class="yape-cerrar" id="cerrarYape" aria-label="Cerrar">✕</button>
        <p class="yape-modal-titulo">Paga con Yape</p>
        <p class="yape-modal-monto">S/ <span class="js-yape-monto">0.00</span></p>
        <p class="yape-concepto js-yape-concepto"></p>
        <img src="<?= e(YAPE_QR) ?>" alt="QR de Yape">
        <p class="yape-titular">A nombre de <strong><?= e(YAPE_TITULAR) ?></strong></p>
    </div>
</div>
