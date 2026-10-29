<div class="titulo-pagina">
    <div>
        <h1>Diagnósticos</h1>
        <p>Registro clínico de los hallazgos de cada paciente.</p>
    </div>
    <a href="<?= url('diagnosticos', 'nuevo') ?>" class="btn"><?= icono('mas') ?>Nuevo diagnóstico</a>
</div>

<section class="panel">
    <form class="filtros" method="get">
        <input type="hidden" name="c" value="diagnosticos">
        <div><label>Buscar</label><input name="buscar" value="<?= e($buscar) ?>" placeholder="Paciente, DNI, código o descripción"></div>
        <div style="flex:0 1 180px"><label>Severidad</label>
            <select name="severidad"><option value="">Todas</option><?= opciones_lista(Diagnostico::SEVERIDADES, $fSeveridad) ?></select>
        </div>
        <button class="btn" type="submit"><?= icono('buscar') ?>Buscar</button>
    </form>
    <div class="tabla-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Odontólogo</th><th>Pieza</th><th>CIE-10</th><th>Diagnóstico</th><th>Severidad</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($diagnosticos as $d): ?>
                <tr>
                    <td><?= fecha($d['fecha']) ?></td>
                    <td>
                        <div class="celda-persona"><span class="avatar"><?= e(iniciales($d['paciente'])) ?></span>
                            <div><?= e($d['paciente']) ?><div class="texto-suave">DNI <?= e($d['dni']) ?></div></div>
                        </div>
                    </td>
                    <td><?= e($d['odontologo']) ?></td>
                    <td><?= e($d['pieza_dental'] ?? '-') ?></td>
                    <td><?= e($d['codigo_cie10'] ?? '-') ?></td>
                    <td><?= e($d['descripcion']) ?></td>
                    <td><span class="estado estado-<?= e($d['severidad']) ?>"><?= e($d['severidad']) ?></span></td>
                    <td class="acciones">
                        <?php if (!$d['tratamientos']): ?>
                            <a class="btn btn-sec btn-chico" href="<?= url('tratamientos', 'nuevo', ['diagnostico_id' => $d['id']]) ?>">Tratar</a>
                        <?php endif; ?>
                        <a class="btn btn-chico" href="<?= url('diagnosticos', 'editar', ['id' => $d['id']]) ?>">Editar</a>
                        <?= boton_post('diagnosticos', 'eliminar', $d['id'], 'Eliminar') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$diagnosticos): ?><tr><td colspan="8" class="vacio">No hay diagnósticos registrados.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
