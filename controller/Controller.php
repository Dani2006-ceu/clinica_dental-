<?php
// Clase base de todos los controladores
abstract class Controller
{
    // Los controladores públicos (login/registro) ponen esto en true
    protected bool $publico = false;

    public function __construct()
    {
        if (!$this->publico) {
            requiere_login();
        }
    }

    // Muestra una vista dentro del diseño general (encabezado, menús y pie)
    protected function vista(string $vista, array $datos = [], bool $conDiseno = true): void
    {
        extract($datos);
        if ($conDiseno) require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/' . $vista . '.php';
        if ($conDiseno) require __DIR__ . '/../views/layout/footer.php';
    }

    protected function id(): int
    {
        return (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    }

    // Ejecuta una operación de BD mostrando el mensaje de éxito o el error traducido
    protected function ejecutar(callable $operacion, string $mensajeExito): bool
    {
        try {
            $operacion();
            flash('exito', $mensajeExito);
            return true;
        } catch (PDOException $ex) {
            flash('error', mensaje_error_bd($ex));
            return false;
        }
    }
}
