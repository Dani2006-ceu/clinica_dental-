-- =========================================================
--  Migración de seguridad (conserva todos los datos)
--  Ejecutar como root desde phpMyAdmin o la consola de MySQL.
-- =========================================================
USE sonrisa_perfecta;

-- 1) Usuario de la aplicación con permisos mínimos (no puede DROP, ALTER, GRANT,
--    ni leer otras bases). Cambiar la clave y copiarla en config/credenciales.php
CREATE USER IF NOT EXISTS 'alfadent_app'@'localhost' IDENTIFIED BY 'CAMBIAR_POR_UNA_CLAVE_LARGA';
GRANT SELECT, INSERT, UPDATE, DELETE ON sonrisa_perfecta.* TO 'alfadent_app'@'localhost';
FLUSH PRIVILEGES;

-- 2) Las cuentas creadas desde "Registrarse" quedan inactivas hasta que el administrador las active
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1 AFTER rol;

-- 3) Intentos fallidos de inicio de sesión (bloqueo contra fuerza bruta)
CREATE TABLE IF NOT EXISTS intentos_login (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    email     VARCHAR(120) NOT NULL,
    ip        VARCHAR(45)  NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email, creado_en),
    INDEX idx_ip (ip, creado_en)
) ENGINE=InnoDB;

-- 4) Bitácora de auditoría: quién hizo qué, cuándo y desde qué IP
CREATE TABLE IF NOT EXISTS bitacora (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    email      VARCHAR(120) NULL,
    accion     VARCHAR(60)  NOT NULL,
    detalle    VARCHAR(255) NULL,
    ip         VARCHAR(45)  NOT NULL,
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (creado_en)
) ENGINE=InnoDB;

-- La bitácora no se puede modificar ni borrar (ni siquiera desde phpMyAdmin)
DROP TRIGGER IF EXISTS trg_bitacora_no_modificar;
DROP TRIGGER IF EXISTS trg_bitacora_no_eliminar;
DELIMITER $$
CREATE TRIGGER trg_bitacora_no_modificar BEFORE UPDATE ON bitacora FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La bitácora no se puede modificar.';
END$$
CREATE TRIGGER trg_bitacora_no_eliminar BEFORE DELETE ON bitacora FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La bitácora no se puede eliminar.';
END$$
DELIMITER ;
