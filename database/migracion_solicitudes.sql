-- =========================================================
--  Migración: solicitudes de cuenta (conserva todos los datos)
--  Ejecutar como root desde phpMyAdmin o la consola de MySQL.
-- =========================================================
USE sonrisa_perfecta;

-- Quien se registra desde "Crear cuenta" queda como SOLICITUD (aprobado = 0).
-- No puede ingresar hasta que el administrador la apruebe; si la rechaza, se elimina.
-- Las cuentas existentes se consideran ya aprobadas.
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS aprobado TINYINT(1) NOT NULL DEFAULT 1 AFTER activo;
