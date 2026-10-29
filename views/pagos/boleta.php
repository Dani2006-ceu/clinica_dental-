<?php
$numero = nro_boleta((int)$b['id']);
$anulado = $b['estado'] === 'Anulado';
$saldo = $b['tratamiento_id'] ? max(0, $b['costo'] - $b['total_pagado']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta <?= $numero ?> | <?= CLINICA_NOMBRE ?></title>
    <link rel="icon" href="imagenes/logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assetes/css/documento.css">
</head>
<body class="doc-body" data-archivo="Boleta-<?= $numero ?>">

<?php
$volver = url('pagos');
$tituloBarra = 'Boleta de pago ' . $numero;
$subBarra = $b['paciente'] . ' · ' . soles($b['monto']);
require __DIR__ . '/../layout/barra_documento.php';
?>

<article class="hoja">
    <?php if ($anulado): ?><div class="sello-anulado">ANULADO</div><?php endif; ?>

    <div class="hoja-cuerpo">
        <header class="membrete">
            <div class="membrete-marca">
                <img src="imagenes/logo.svg" alt="Logo">
                <div>
                    <div class="lema">Clínica Odontológica</div>
                    <h1><?= CLINICA_NOMBRE ?></h1>
                    <p><?= CLINICA_DIRECCION ?><br><?= CLINICA_CORREO ?></p>
                </div>
            </div>
            <div class="caja-doc">
                <div class="tipo">BOLETA DE PAGO</div>
                <div class="numero"><?= $numero ?></div>
                <div class="fecha">Fecha de emisión: <?= fecha($b['fecha']) ?></div>
            </div>
        </header>

        <div class="titulo-seccion">Datos del paciente</div>
        <div class="datos">
            <div class="dato-doc completo"><span>Paciente</span><strong><?= e($b['paciente']) ?></strong></div>
            <div class="dato-doc"><span>DNI</span><strong><?= e($b['dni']) ?></strong></div>
            <div class="dato-doc"><span>Teléfono</span><strong><?= e($b['telefono'] ?? '—') ?></strong></div>
            <div class="dato-doc"><span>Correo</span><strong><?= e($b['email'] ?? '—') ?></strong></div>
            <?php if ($b['direccion']): ?>
                <div class="dato-doc completo"><span>Dirección</span><strong><?= e($b['direccion']) ?></strong></div>
            <?php endif; ?>
        </div>

        <div class="titulo-seccion">Detalle</div>
        <table class="tabla-doc">
            <thead><tr><th style="width:8%">Cant.</th><th>Descripción</th><th style="width:22%">Odontólogo</th><th class="num" style="width:18%">Importe</th></tr></thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <?php if ($b['tratamiento_id']): ?>
                            <strong><?= e($b['servicio']) ?></strong><?= $b['pieza_dental'] ? ' — pieza ' . e($b['pieza_dental']) : '' ?>
                            <span class="sub"><?= e($b['tratamiento']) ?></span>
                            <?php if ($b['diagnostico']): ?><span class="sub">Diagnóstico: <?= e($b['diagnostico']) ?></span><?php endif; ?>
                        <?php else: ?>
                            <strong>Pago a cuenta</strong>
                            <span class="sub">Abono a la cuenta del paciente</span>
                        <?php endif; ?>
                        <?php if ($b['observacion']): ?><span class="sub">Obs.: <?= e($b['observacion']) ?></span><?php endif; ?>
                    </td>
                    <td><?= e($b['odontologo'] ?? '—') ?></td>
                    <td class="num"><?= soles($b['monto']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="totales-fila">
            <div class="son">
                SON:
                <strong><?= monto_en_letras((float)$b['monto']) ?></strong>
            </div>
            <div class="totales">
                <div><span>Subtotal</span><span><?= soles($b['monto']) ?></span></div>
                <div><span>Descuento</span><span><?= soles(0) ?></span></div>
                <div class="total"><span>TOTAL</span><span><?= soles($b['monto']) ?></span></div>
            </div>
        </div>

        <div class="titulo-seccion">Forma de pago</div>
        <div class="datos">
            <div class="dato-doc"><span>Método</span>
                <strong><span class="metodo-chip <?= $b['metodo'] === 'Yape' ? 'yape' : '' ?>"><?= e($b['metodo']) ?></span></strong></div>
            <div class="dato-doc"><span>N° de operación</span><strong><?= e($b['nro_operacion'] ?? '—') ?></strong></div>
            <div class="dato-doc"><span>Estado</span>
                <strong><span class="estado-chip <?= $anulado ? 'anulado' : '' ?>"><?= e(mb_strtoupper($b['estado'])) ?></span></strong></div>
        </div>

        <?php if ($saldo !== null): ?>
            <div class="titulo-seccion">Estado de cuenta del tratamiento</div>
            <div class="saldo">
                <div><span>Costo del tratamiento</span><strong><?= soles($b['costo']) ?></strong></div>
                <div><span>Total pagado</span><strong><?= soles($b['total_pagado']) ?></strong></div>
                <div class="<?= $saldo > 0 ? 'pendiente' : 'cancelado' ?>"><span>Saldo pendiente</span>
                    <strong><?= $saldo > 0 ? soles($saldo) : 'Cancelado' ?></strong></div>
            </div>
        <?php endif; ?>

        <div class="firmas">
            <div><strong><?= e($b['paciente']) ?></strong>Paciente · DNI <?= e($b['dni']) ?></div>
            <div><strong><?= CLINICA_NOMBRE ?></strong>Caja / Recepción</div>
        </div>

        <p class="gracias">¡Gracias por confiar tu sonrisa en <?= CLINICA_NOMBRE ?>!</p>
        <p class="nota">Conserve este documento como constancia de pago. Comprobante interno de la clínica.</p>
    </div>

    <footer class="pie-doc">
        <span><?= CLINICA_NOMBRE ?> · <?= CLINICA_DIRECCION ?></span>
        <span>Impreso el <?= date('d/m/Y H:i') ?> por <?= e($_SESSION['usuario']['nombre']) ?></span>
    </footer>
</article>

<script src="assetes/js/lib/html2pdf.bundle.min.js"></script>
<script src="assetes/js/documento.js"></script>
</body>
</html>
