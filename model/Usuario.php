<?php
class Usuario
{
    const ROLES = ['Administrador', 'Recepcionista', 'Odontólogo'];

    // Devuelve el usuario (sin contraseña) si las credenciales son correctas
    public static function autenticar(string $email, string $clave): ?array
    {
        $stmt = db()->prepare('SELECT id, nombre, email, password, rol, activo, aprobado FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        // Si el correo no existe se verifica igual contra un hash falso: así la respuesta tarda lo mismo
        // y un atacante no puede averiguar qué correos están registrados midiendo el tiempo
        $hash = $u['password'] ?? '$2y$10$wsV3zfigvgxWi0ZIRtbYBOTQc/AdfiMbqsDC87wy2MHmr1o/mLo4u';
        if (!password_verify($clave, $hash) || !$u) {
            return null;
        }
        // Si el algoritmo de cifrado se actualizó, se vuelve a cifrar la contraseña
        if (password_needs_rehash($u['password'], PASSWORD_DEFAULT)) {
            $stmt = db()->prepare('UPDATE usuarios SET password=? WHERE id=?');
            $stmt->execute([password_hash($clave, PASSWORD_DEFAULT), $u['id']]);
        }
        unset($u['password']);
        return $u;
    }

    public static function existeEmail(string $email): bool
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function crear(string $nombre, string $email, string $clave, string $rol, int $activo = 1, int $aprobado = 1): void
    {
        $stmt = db()->prepare('INSERT INTO usuarios (nombre, email, password, rol, activo, aprobado) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $email, password_hash($clave, PASSWORD_DEFAULT), $rol, $activo, $aprobado]);
    }

    public static function actualizar(int $id, string $nombre, string $email, string $rol, int $activo, string $clave = ''): void
    {
        $stmt = db()->prepare('UPDATE usuarios SET nombre=?, email=?, rol=?, activo=? WHERE id=?');
        $stmt->execute([$nombre, $email, $rol, $activo, $id]);
        if ($clave !== '') {
            $stmt = db()->prepare('UPDATE usuarios SET password=? WHERE id=?');
            $stmt->execute([password_hash($clave, PASSWORD_DEFAULT), $id]);
        }
    }

    public static function listar(): array
    {
        return db()->query('SELECT id, nombre, email, rol, activo, creado_en FROM usuarios WHERE aprobado = 1 ORDER BY activo, nombre')->fetchAll();
    }

    // Solicitudes de cuenta hechas desde "Crear cuenta" que el administrador aún no revisa
    public static function solicitudes(): array
    {
        return db()->query('SELECT id, nombre, email, creado_en FROM usuarios WHERE aprobado = 0 ORDER BY creado_en')->fetchAll();
    }

    public static function totalSolicitudes(): int
    {
        return (int)db()->query('SELECT COUNT(*) FROM usuarios WHERE aprobado = 0')->fetchColumn();
    }

    // Aprueba la solicitud con el rol elegido por el administrador; desde ese momento puede ingresar
    public static function aprobar(int $id, string $rol): bool
    {
        $stmt = db()->prepare('UPDATE usuarios SET aprobado = 1, activo = 1, rol = ? WHERE id = ? AND aprobado = 0');
        $stmt->execute([$rol, $id]);
        return $stmt->rowCount() > 0;
    }

    // Rechazar una solicitud borra la cuenta: nunca llegó a existir para el sistema
    public static function rechazar(int $id): bool
    {
        $stmt = db()->prepare('DELETE FROM usuarios WHERE id = ? AND aprobado = 0');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, nombre, email, rol, activo FROM usuarios WHERE id = ? AND aprobado = 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function eliminar(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
    }
}
