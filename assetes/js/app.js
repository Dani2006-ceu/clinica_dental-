// =========================================================
//  Dental Alfadent - comportamiento de la interfaz
// =========================================================
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };

    // Menú desplegable en celulares
    if ($('btnMenu')) {
        $('btnMenu').addEventListener('click', function () {
            $('navHorizontal').classList.toggle('abierto');
        });
    }

    // Confirmación antes de eliminar o anular (el mensaje viene en data-confirmar)
    document.querySelectorAll('form[data-confirmar]').forEach(function (f) {
        f.addEventListener('submit', function (ev) {
            if (!confirm(f.dataset.confirmar)) ev.preventDefault();
        });
    });

    // Selects que se envían solos al cambiar (estado de la cita)
    document.querySelectorAll('select.auto-enviar').forEach(function (s) {
        s.addEventListener('change', function () { s.form.submit(); });
    });

    // Mostrar / ocultar contraseña en el login
    document.querySelectorAll('.ver-clave').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = $(b.dataset.objetivo);
            i.type = i.type === 'password' ? 'text' : 'password';
        });
    });

    // ---------- Tratamientos ----------
    // El costo se sugiere con el precio del servicio elegido
    if ($('servicio') && $('costo')) {
        $('servicio').addEventListener('change', function () {
            var precio = this.options[this.selectedIndex].dataset.precio;
            if (precio) $('costo').value = precio;
        });
    }
    // Al elegir un diagnóstico se completan paciente, odontólogo y pieza
    if ($('diagnostico')) {
        $('diagnostico').addEventListener('change', function () {
            var op = this.options[this.selectedIndex];
            if (!op.value) return;
            $('paciente').value = op.dataset.paciente;
            $('odontologo').value = op.dataset.odontologo;
            if (op.dataset.pieza) $('pieza').value = op.dataset.pieza;
        });
    }

    // ---------- Pagos y Yape ----------
    if ($('formPago')) {
        var monto = $('monto'), metodo = $('metodo'), tratamiento = $('tratamiento');

        // Escribe el monto actual en todos los lugares del panel de Yape
        var actualizarYape = function () {
            var valor = parseFloat(monto.value);
            var texto = isNaN(valor) || valor <= 0 ? '0.00' : valor.toFixed(2);
            document.querySelectorAll('.js-yape-monto').forEach(function (el) { el.textContent = texto; });

            var op = tratamiento.options[tratamiento.selectedIndex];
            var concepto = op && op.value ? 'Concepto: ' + op.dataset.concepto : '';
            document.querySelectorAll('.js-yape-concepto').forEach(function (el) { el.textContent = concepto; });

            var esYape = metodo.value === 'Yape';
            $('panelYape').hidden = !esYape;
            $('nroOperacion').required = metodo.value !== 'Efectivo';
            $('nroOperacion').placeholder = esYape ? 'N° de operación de la constancia Yape' : '';
        };

        // Al elegir un tratamiento se completan el paciente y el saldo pendiente
        tratamiento.addEventListener('change', function () {
            var op = this.options[this.selectedIndex];
            if (op.dataset.paciente) $('paciente').value = op.dataset.paciente;
            if (parseFloat(op.dataset.saldo) > 0) monto.value = parseFloat(op.dataset.saldo).toFixed(2);
            actualizarYape();
        });
        monto.addEventListener('input', actualizarYape);
        metodo.addEventListener('change', actualizarYape);
        actualizarYape();

        // Copiar el monto para pegarlo o dictarlo
        $('copiarMonto').addEventListener('click', function () {
            var btn = this, valor = document.querySelector('.js-yape-monto').textContent;
            var listo = function () {
                var original = btn.innerHTML;
                btn.textContent = '¡Copiado! S/ ' + valor;
                setTimeout(function () { btn.innerHTML = original; }, 1600);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(valor).then(listo, listo);
            } else {
                listo();
            }
        });

        // Pantalla completa para que el paciente escanee el QR
        $('mostrarYape').addEventListener('click', function () { $('modalYape').hidden = false; });
        $('cerrarYape').addEventListener('click', function () { $('modalYape').hidden = true; });
        $('modalYape').addEventListener('click', function (ev) { if (ev.target === this) this.hidden = true; });
        document.addEventListener('keydown', function (ev) { if (ev.key === 'Escape') $('modalYape').hidden = true; });
    }
})();
