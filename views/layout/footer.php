    </main>

    <!-- Menú vertical al lado derecho -->
    <aside class="menu-vertical">
        <div class="bloque-menu">
            <h3>Accesos rápidos</h3>
            <ul>
                <li><a href="<?= url('citas', 'nuevo') ?>"><?= icono('citas') ?>Programar cita</a></li>
                <li><a href="<?= url('pacientes', 'nuevo') ?>"><?= icono('pacientes') ?>Nuevo paciente</a></li>
                <li><a href="<?= url('diagnosticos', 'nuevo') ?>"><?= icono('diagnosticos') ?>Registrar diagnóstico</a></li>
                <li><a href="<?= url('tratamientos', 'nuevo') ?>"><?= icono('tratamientos') ?>Registrar tratamiento</a></li>
                <li><a href="<?= url('pagos', 'nuevo') ?>"><?= icono('pagos') ?>Registrar pago</a></li>
            </ul>
        </div>
        <div class="bloque-menu">
            <h3>Gestión</h3>
            <ul>
                <li><a href="<?= url('especialidades') ?>"><?= icono('especialidad') ?>Especialidades</a></li>
                <li><a href="<?= url('servicios') ?>"><?= icono('servicios') ?>Servicios</a></li>
                <li><a href="<?= url('usuarios') ?>"><?= icono('usuarios') ?>Usuarios</a></li>
            </ul>
        </div>
        <div class="bloque-menu">
            <h3>Reportes</h3>
            <ul>
                <li><a href="<?= url('reportes', 'index', ['tipo' => 'atenciones']) ?>"><?= icono('reportes') ?>Atenciones realizadas</a></li>
                <li><a href="<?= url('reportes', 'index', ['tipo' => 'ingresos']) ?>"><?= icono('dinero') ?>Ingresos obtenidos</a></li>
                <li><a href="<?= url('reportes', 'index', ['tipo' => 'pendientes']) ?>"><?= icono('pendiente') ?>Citas pendientes</a></li>
            </ul>
        </div>
        <div class="ayuda">
            <?= icono('escudo') ?>
            <p><strong>¿Necesitas ayuda?</strong>Soporte: <a href="mailto:<?= CLINICA_CORREO ?>"><?= CLINICA_CORREO ?></a></p>
        </div>
    </aside>
</div>

<footer class="pie">
    <div class="contenedor pie-columnas">
        <div class="pie-marca">
            <a href="<?= url() ?>" class="marca marca-clara">
                <img src="imagenes/logo.svg" alt="">
                <span><strong><?= CLINICA_NOMBRE ?></strong><small>Clínica Odontológica</small></span>
            </a>
            <p>Cuidamos tu salud bucal con tecnología moderna y un equipo de especialistas comprometidos con tu sonrisa.</p>
        </div>
        <div>
            <h4>Módulos</h4>
            <ul>
                <li><a href="<?= url('pacientes') ?>">Pacientes</a></li>
                <li><a href="<?= url('citas') ?>">Citas</a></li>
                <li><a href="<?= url('diagnosticos') ?>">Diagnósticos</a></li>
                <li><a href="<?= url('pagos') ?>">Pagos</a></li>
            </ul>
        </div>
        <div>
            <h4>Contacto</h4>
            <ul>
                <li><?= icono('ubicacion') ?><a href="<?= CLINICA_MAPA ?>" target="_blank" rel="noopener"><?= CLINICA_DIRECCION ?></a></li>
                <li><?= icono('correo') ?><a href="mailto:<?= CLINICA_CORREO ?>"><?= CLINICA_CORREO ?></a></li>
                <li><?= icono('flecha') ?><a href="<?= CLINICA_MAPA ?>" target="_blank" rel="noopener">Cómo llegar (Google Maps)</a></li>
            </ul>
        </div>
        <div>
            <h4>Horario</h4>
            <ul>
                <li>Lunes a viernes: 8:00 - 20:00</li>
                <li>Sábados: 8:00 - 14:00</li>
                <li>Emergencias: 24 horas</li>
            </ul>
        </div>
    </div>
    <div class="pie-copy">
        <div class="contenedor fila-entre">
            <span>© <?= date('Y') ?> Clínica Odontológica <?= CLINICA_NOMBRE ?>. Todos los derechos reservados.</span>
            <span>Sistema de Gestión Clínica v2.0</span>
        </div>
    </div>
</footer>

<script src="assetes/js/app.js"></script>
</body>
</html>
