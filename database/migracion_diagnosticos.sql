-- =========================================================
--  Migración: separa los diagnósticos en su propia tabla.
--  Úsala solo si ya tenías la base creada con la versión anterior
--  (conserva usuarios, pacientes, citas, tratamientos y pagos).
-- =========================================================
USE sonrisa_perfecta;

CREATE TABLE IF NOT EXISTS diagnosticos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    cita_id       INT NULL,
    paciente_id   INT NOT NULL,
    odontologo_id INT NOT NULL,
    fecha         DATE NOT NULL,
    pieza_dental  VARCHAR(20) NULL,
    codigo_cie10  VARCHAR(10) NULL COMMENT 'Código CIE-10 (ej. K02.1 caries de la dentina)',
    descripcion   TEXT NOT NULL,
    severidad     ENUM('Leve','Moderado','Severo') NOT NULL DEFAULT 'Leve',
    observaciones VARCHAR(255) NULL,
    creado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tmp_trat_id   INT NULL,
    FOREIGN KEY (cita_id)       REFERENCES citas(id) ON DELETE SET NULL,
    FOREIGN KEY (paciente_id)   REFERENCES pacientes(id),
    FOREIGN KEY (odontologo_id) REFERENCES odontologos(id)
) ENGINE=InnoDB;

ALTER TABLE tratamientos ADD COLUMN diagnostico_id INT NULL AFTER cita_id;

-- Copia los diagnósticos que estaban dentro de tratamientos
INSERT INTO diagnosticos (cita_id, paciente_id, odontologo_id, fecha, pieza_dental, descripcion, tmp_trat_id)
SELECT cita_id, paciente_id, odontologo_id, fecha, pieza_dental, diagnostico, id FROM tratamientos;

UPDATE tratamientos t JOIN diagnosticos d ON d.tmp_trat_id = t.id SET t.diagnostico_id = d.id;

ALTER TABLE diagnosticos DROP COLUMN tmp_trat_id;
ALTER TABLE tratamientos
    DROP COLUMN diagnostico,
    ADD CONSTRAINT fk_trat_diag FOREIGN KEY (diagnostico_id) REFERENCES diagnosticos(id) ON DELETE SET NULL;
