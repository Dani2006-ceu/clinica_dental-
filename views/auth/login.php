<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | <?= CLINICA_NOMBRE ?></title>
    <link rel="icon" href="imagenes/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assetes/css/estilos.css">
</head>
<body>
<div class="pagina-acceso">
    <section class="acceso-visual">
        <img src="imagenes/banner.jpg" alt="Consultorio de la clínica <?= CLINICA_NOMBRE ?>">
        <div class="acceso-capa">
            <a href="<?= url('auth', 'login') ?>" class="marca">
                <img src="imagenes/logo.svg" alt="Logo">
                <span><strong><?= CLINICA_NOMBRE ?></strong><small>Clínica Odontológica</small></span>
            </a>
            <div>
                <h2>Tu sonrisa, <span>nuestra</span> prioridad.</h2>
                <p class="lema">Gestiona pacientes, citas, diagnósticos, tratamientos y pagos de la clínica desde un solo lugar.</p>
                <div class="beneficios">
                    <div class="beneficio"><?= icono('citas') ?><strong>Agenda inteligente</strong><span>Sin cruces de horario</span></div>
                    <div class="beneficio"><?= icono('diagnosticos') ?><strong>Historia clínica</strong><span>Diagnósticos y tratamientos</span></div>
                    <div class="beneficio"><?= icono('reportes') ?><strong>Reportes</strong><span>Ingresos y atenciones</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="acceso-form">
        <div class="caja-acceso">
            <div class="marca">
                <img src="imagenes/logo.svg" alt="">
                <span><strong><?= CLINICA_NOMBRE ?></strong><small>Clínica Odontológica</small></span>
            </div>
            <h1>Bienvenido de nuevo</h1>
            <p class="sub">Ingresa tus credenciales para acceder al sistema.</p>

            <?= mostrar_flash() ?>
            <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

            <form method="post" action="<?= url('auth', 'login') ?>" autocomplete="on">
                <?= csrf_campo() ?>
                <div class="grupo">
                    <label for="email">Correo electrónico</label>
                    <div class="campo-icono">
                        <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="tucorreo@sonrisa.com" required autofocus>
                        <?= icono('correo') ?>
                    </div>
                </div>
                <div class="grupo">
                    <label for="password">Contraseña</label>
                    <div class="campo-icono">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <?= icono('candado') ?>
                        <button type="button" class="ver-clave" data-objetivo="password" aria-label="Mostrar contraseña"><?= icono('ojo') ?></button>
                    </div>
                </div>
                <button class="btn" type="submit">Iniciar sesión <?= icono('flecha') ?></button>
            </form>

            <div class="separador">¿Eres nuevo?</div>
            <p class="enlace" style="margin-top:12px">¿No tienes cuenta? <a href="<?= url('auth', 'registro') ?>">Solicitar una cuenta</a></p>
            <p class="seguro"><?= icono('escudo') ?>Conexión protegida · datos cifrados</p>
        </div>
    </section>
</div>
<script src="assetes/js/app.js"></script>
</body>
</html>
