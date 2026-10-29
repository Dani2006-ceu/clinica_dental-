<?php
// Conexión a MySQL con PDO. Todas las consultas del sistema usan
// sentencias preparadas (prepare + execute) para evitar inyección SQL.
// Las credenciales están en config/credenciales.php (fuera de git) y usan
// un usuario de MySQL con permisos mínimos, nunca root.
$cred = require __DIR__ . '/credenciales.php';
define('DB_HOST', $cred['host']);
define('DB_NAME', $cred['base']);
define('DB_USER', $cred['usuario']);
define('DB_PASS', $cred['clave']);
unset($cred);

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // preparadas reales en el servidor
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false, // impide encadenar consultas con ";"
        ]);
    }
    return $pdo;
}
