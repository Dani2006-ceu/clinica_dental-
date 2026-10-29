<?php
class PagosController extends Controller
{
    public function index(): void
    {
        $filtros = ['desde' => $_GET['desde'] ?? '', 'hasta' => $_GET['hasta'] ?? '', 'metodo' => $_GET['metodo'] ?? ''];
        $pagos = Pago::listar($filtros);
        $total = array_sum(array_map(fn($p) => $p['estado'] === 'Pagado' ? $p['monto'] : 0, $pagos));
        $this->vista('pagos/listar', [
            'titulo' => 'Pagos', 'activo' => 'pagos', 'f' => $filtros, 'pagos' => $pagos, 'total' => $total,
        ]);
    }

    public function nuevo(): void
    {
        $r = [];
        $tratamientos = Tratamiento::conSaldo();
        // "Cobrar" desde un tratamiento: precarga paciente y saldo pendiente
        foreach ($tratamientos as $t) {
            if ((int)$t['id'] === (int)($_GET['tratamiento_id'] ?? 0)) {
                $r = ['tratamiento_id' => $t['id'], 'paciente_id' => $t['paciente_id'], 'monto' => $t['saldo']];
            }
        }
        $this->formulario($r, $tratamientos);
    }

    public function editar(): void
    {
        $r = Pago::buscar($this->id()) ?? redirigir(url('pagos'));
        if ($r['estado'] === 'Anulado') {
            flash('error', 'Un pago anulado no se puede modificar.');
            redirigir(url('pagos'));
        }
        $this->formulario($r, Tratamiento::conSaldo());
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = $this->id();
        $datos = [
            'paciente_id' => (int)campo('paciente_id'), 'tratamiento_id' => (int)campo('tratamiento_id') ?: null,
            'fecha' => campo('fecha'), 'monto' => (float)campo('monto'), 'metodo' => campo('metodo'),
            'nro_operacion' => campo('nro_operacion'), 'observacion' => campo('observacion'),
        ];
        $volver = $id ? url('pagos', 'editar', ['id' => $id]) : url('pagos', 'nuevo');

        if (!in_array($datos['metodo'], Pago::METODOS, true)) {
            flash('error', 'Seleccione un método de pago válido.');
            redirigir($volver);
        }
        if ($datos['metodo'] !== 'Efectivo' && !$datos['nro_operacion']) {
            flash('error', 'Para tarjeta o Yape debe ingresar el número de operación.');
            redirigir($volver);
        }
        if ($datos['monto'] <= 0) {
            flash('error', 'El monto debe ser mayor a cero.');
            redirigir($volver);
        }
        $nuevoId = 0;
        $ok = $this->ejecutar(function () use ($datos, $id, &$nuevoId) {
            $nuevoId = Pago::guardar($datos, $id);
        }, $id ? 'Pago actualizado.' : 'Pago registrado. Ya puede imprimir la boleta.');
        if (!$ok) {
            redirigir($volver);
        }
        // Un pago nuevo abre directamente su boleta para imprimirla
        redirigir($id ? url('pagos') : url('pagos', 'boleta', ['id' => $nuevoId]));
    }

    // Boleta de pago lista para imprimir o descargar en PDF
    public function boleta(): void
    {
        $b = Pago::boleta($this->id()) ?? redirigir(url('pagos'));
        $this->vista('pagos/boleta', ['b' => $b], false);
    }

    // Los pagos no tienen acción "eliminar": solo se anulan y quedan como comprobante
    public function anular(): void
    {
        csrf_verificar();
        $this->ejecutar(fn() => Pago::anular($this->id()), 'Pago anulado. Queda registrado como comprobante.');
        redirigir(url('pagos'));
    }

    private function formulario(array $r, array $tratamientos): void
    {
        $this->vista('pagos/form', [
            'titulo' => 'Pagos', 'activo' => 'pagos', 'r' => $r,
            'pacientes' => Paciente::opciones(), 'tratamientos' => $tratamientos,
        ]);
    }
}
