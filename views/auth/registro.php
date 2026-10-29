<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar cuenta | <?= CLINICA_NOMBRE ?></title>
    <link rel="icon" href="imagenes/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assetes/css/estilos.css">
</head>
<body>
<div class="pagina-acceso">
    <section class="acceso-visual">
        <img src="imagenes/odontologos.jpg" alt="Odontóloga de la clínica">
        <div class="acceso-capa">
            <a href="<?= url('auth', 'login') ?>" class="marca">
                <img src="imagenes/logo.svg" alt="Logo">
                <span><strong><?= CLINICA_NOMBRE ?></strong><small>Clínica Odontológica</small></span>
            </a>
            <div>
                <h2>Únete al equipo de <span><?= CLINICA_NOMBRE ?></span>.</h2>
                <p class="lema">Crea tu cuenta para registrar pacientes, programar citas y controlar los pagos de la clínica.</p>
                <div class="beneficios">
                    <div class="beneficio"><?= icono('pacientes') ?><strong>Pacientes</strong><span>Datos e historial</span></div>
                    <div class="beneficio"><?= icono('pagos') ?><strong>Pagos</strong><span>Efectivo, tarjeta y Yape</span></div>
                    <div class="beneficio"><?= icono('escudo') ?><strong>Seguro</strong><span>Acceso con contraseña</span></div>
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
            <h1>Solicitar cuenta</h1>
            <p class="sub">Completa tus datos. El administrador debe aprobar tu solicitud antes de que puedas ingresar.</p>

            <?php if ($errores): ?>
                <div class="alerta alerta-error"><?= implode('<br>', array_map('e', $errores)) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('auth', 'registro') ?>">
                <?= csrf_campo() ?>
                <div class="grupo">
                    <label for="nombre">Nombre completo</label>
                    <div class="campo-icono">
                        <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" placeholder="Ej. Ana García" required maxlength="100">
                        <?= icono('usuario') ?>
                    </div>
                </div>
                <div class="grupo">
                    <label for="email">Correo electrónico</label>
                    <div class="campo-icono">
                        <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="tucorreo@sonrisa.com" required maxlength="120">
                        <?= icono('correo') ?>
                    </div>
                </div>
                <div class="doble">
                    <div class="grupo">
                        <label for="password">Contraseña</label>
                        <div class="campo-icono">
                            <input type="password" id="password" name="password" placeholder="Mín. 8, letras y números" required minlength="8" maxlength="72" autocomplete="new-password">
                            <?= icono('candado') ?>
                        </div>
                    </div>
                    <div class="grupo">
                        <label for="password2">Confirmar</label>
                        <div class="campo-icono">
                            <input type="password" id="password2" name="password2" placeholder="Repetir" required minlength="8" maxlength="72" autocomplete="new-password">
                            <?= icono('candado') ?>
                        </div>
                    </div>
                </div>
                <button class="btn" type="submit">Enviar solicitud <?= icono('flecha') ?></button>
            </form>
            <p class="enlace">¿Ya tienes cuenta? <a href="<?= url('auth', 'login') ?>">Inicia sesión</a></p>
            <p class="seguro"><?= icono('escudo') ?>Tu contraseña se guarda cifrada</p>
        </div>
    </section>
</div>
</body>
</html>
