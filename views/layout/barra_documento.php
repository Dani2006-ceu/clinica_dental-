<?php // Barra superior de los documentos imprimibles. Espera: $volver, $tituloBarra, $subBarra, $conExcel ?>
<div class="doc-barra no-imprimir">
    <div class="doc-barra-in">
        <div>
            <strong><?= e($tituloBarra) ?></strong>
            <small><?= e($subBarra) ?></small>
        </div>
        <div class="doc-botones">
            <a href="<?= e($volver) ?>" class="doc-btn"><?= icono('inicio') ?>Volver</a>
            <button type="button" class="doc-btn imprimir" id="btnImprimir"><?= icono('imprimir') ?>Imprimir</button>
            <button type="button" class="doc-btn pdf" id="btnPdf"><?= icono('diagnosticos') ?>Descargar PDF</button>
            <?php if (!empty($conExcel)): ?>
                <button type="button" class="doc-btn excel" id="btnExcel"><?= icono('reportes') ?>Exportar Excel</button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="no-imprimir" style="padding:0 16px"><div class="doc-aviso"><?= e($_SESSION['flash']['mensaje']) ?></div></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
