<?php
// Bloqueo contra fuerza bruta en el login y bitácora de auditoría
class Seguridad
{
    const MAX_FALLOS_CORREO = 5;   // intentos fallidos permitidos por correo...
    const MAX_FALLOS_IP = 15;      // ...y por IP (frena a quien prueba muchos correos)
    const MINUTOS_BLOQUEO = 15;

    public static function loginBloqueado(string $email): bool
    {
        $stmt = db()->prepare('SELECT SUM(email = ?), SUM(ip = ?) FROM intentos_login
                               WHERE creado_en > NOW() - INTERVAL ' . self::MINUTOS_BLOQUEO . ' MINUTE');
        $stmt->execute([$email, ip()]);
        [$porCorreo, $porIp] = $stmt->fetch(PDO::FETCH_NUM);
        return $porCorreo >= self::MAX_FALLOS_CORREO || $porIp >= self::MAX_FALLOS_IP;
    }

    public static function registrarFallo(string $email): void
    {
        db()->prepare('INSERT INTO intentos_login (email, ip) VALUES (?, ?)')->execute([mb_substr($email, 0, 120), ip()]);
        // Limpieza de intentos viejos
        db()->exec('DELETE FROM intentos_login WHERE creado_en < NOW() - INTERVAL 1 DAY');
    }

    public static function limpiarFallos(string $email): void
    {
        db()->prepare('DELETE FROM intentos_login WHERE email = ?')->execute([$email]);
    }

    // Guarda una acción en la bitácora (la tabla no admite UPDATE ni DELETE)
    public static function registrar(string $accion, string $detalle = '', ?array $usuario = null): void
    {
        $usuario ??= $_SESSION['usuario'] ?? null;
        db()->prepare('INSERT INTO bitacora (usuario_id, email, accion, detalle, ip) VALUES (?, ?, ?, ?, ?)')
            ->execute([$usuario['id'] ?? null, $usuario['email'] ?? null, $accion, mb_substr($detalle, 0, 255), ip()]);
    }

    public static function bitacora(int $limite = 300): array
    {
        return db()->query('SELECT b.*, u.nombre FROM bitacora b LEFT JOIN usuarios u ON u.id = b.usuario_id
                            ORDER BY b.id DESC LIMIT ' . $limite)->fetchAll();
    }
}
