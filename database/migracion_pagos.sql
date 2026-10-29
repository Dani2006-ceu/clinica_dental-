-- =========================================================
--  Migración: los pagos (boletas) no se eliminan, solo se anulan.
--  Úsala si tu base ya existía antes de este cambio.
-- =========================================================
USE sonrisa_perfecta;

-- Un tratamiento que ya tiene pagos no se puede eliminar
ALTER TABLE pagos DROP FOREIGN KEY pagos_ibfk_2;
ALTER TABLE pagos ADD CONSTRAINT pagos_ibfk_2 FOREIGN KEY (tratamiento_id) REFERENCES tratamientos(id) ON DELETE RESTRICT;

DROP TRIGGER IF EXISTS trg_pagos_no_eliminar;
DROP TRIGGER IF EXISTS trg_pagos_anulado_bloqueado;

DELIMITER $$
-- Ningún pago se borra: queda como comprobante
CREATE TRIGGER trg_pagos_no_eliminar BEFORE DELETE ON pagos FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Los pagos no se eliminan, solo se anulan.';
END$$

-- Un pago anulado ya no se puede modificar
CREATE TRIGGER trg_pagos_anulado_bloqueado BEFORE UPDATE ON pagos FOR EACH ROW
BEGIN
    IF OLD.estado = 'Anulado' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Un pago anulado no se puede modificar.';
    END IF;
END$$
DELIMITER ;
