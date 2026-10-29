-- =========================================================
--  Clínica Odontológica "Dental Alfadent"
--  Script de base de datos (MySQL / MariaDB - XAMPP)
-- =========================================================
DROP DATABASE IF EXISTS sonrisa_perfecta;
CREATE DATABASE sonrisa_perfecta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sonrisa_perfecta;

-- Usuarios del sistema (login)
CREATE TABLE usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(120) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    rol        ENUM('Administrador','Recepcionista','Odontólogo') NOT NULL DEFAULT 'Recepcionista',
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Pacientes
CREATE TABLE pacientes (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    dni              VARCHAR(12)  NOT NULL UNIQUE,
    nombres          VARCHAR(80)  NOT NULL,
    apellidos        VARCHAR(80)  NOT NULL,
    fecha_nacimiento DATE NULL,
    sexo             ENUM('M','F') NOT NULL,
    telefono         VARCHAR(20)  NULL,
    email            VARCHAR(120) NULL,
    direccion        VARCHAR(200) NULL,
    alergias         VARCHAR(255) NULL,
    creado_en        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Especialidades odontológicas
CREATE TABLE especialidades (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL
) ENGINE=InnoDB;

-- Odontólogos
CREATE TABLE odontologos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    dni             VARCHAR(12) NOT NULL UNIQUE,
    cop             VARCHAR(15) NOT NULL UNIQUE COMMENT 'N° Colegio Odontológico',
    nombres         VARCHAR(80) NOT NULL,
    apellidos       VARCHAR(80) NOT NULL,
    especialidad_id INT NOT NULL,
    telefono        VARCHAR(20) NULL,
    email           VARCHAR(120) NULL,
    estado          ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id)
) ENGINE=InnoDB;

-- Servicios que ofrece la clínica
CREATE TABLE servicios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL UNIQUE,
    descripcion  VARCHAR(255) NULL,
    precio       DECIMAL(10,2) NOT NULL,
    duracion_min INT NOT NULL DEFAULT 30,
    estado       ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB;

-- Citas odontológicas
CREATE TABLE citas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id   INT NOT NULL,
    odontologo_id INT NOT NULL,
    servicio_id   INT NOT NULL,
    fecha         DATE NOT NULL,
    hora          TIME NOT NULL,
    motivo        VARCHAR(255) NULL,
    estado        ENUM('Pendiente','Confirmada','Atendida','Cancelada') NOT NULL DEFAULT 'Pendiente',
    creado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Los cruces de horario se validan en citas.php según la duración del servicio
    KEY idx_agenda (odontologo_id, fecha, hora),
    FOREIGN KEY (paciente_id)   REFERENCES pacientes(id),
    FOREIGN KEY (odontologo_id) REFERENCES odontologos(id),
    FOREIGN KEY (servicio_id)   REFERENCES servicios(id)
) ENGINE=InnoDB;

-- Diagnósticos odontológicos
CREATE TABLE diagnosticos (
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
    FOREIGN KEY (cita_id)       REFERENCES citas(id) ON DELETE SET NULL,
    FOREIGN KEY (paciente_id)   REFERENCES pacientes(id),
    FOREIGN KEY (odontologo_id) REFERENCES odontologos(id)
) ENGINE=InnoDB;

-- Tratamientos realizados (cada uno puede atender un diagnóstico)
CREATE TABLE tratamientos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    cita_id        INT NULL,
    diagnostico_id INT NULL,
    paciente_id    INT NOT NULL,
    odontologo_id  INT NOT NULL,
    servicio_id    INT NOT NULL,
    fecha          DATE NOT NULL,
    pieza_dental   VARCHAR(20) NULL,
    tratamiento    TEXT NOT NULL,
    costo          DECIMAL(10,2) NOT NULL,
    observaciones  VARCHAR(255) NULL,
    FOREIGN KEY (cita_id)        REFERENCES citas(id) ON DELETE SET NULL,
    FOREIGN KEY (diagnostico_id) REFERENCES diagnosticos(id) ON DELETE SET NULL,
    FOREIGN KEY (paciente_id)    REFERENCES pacientes(id),
    FOREIGN KEY (odontologo_id) REFERENCES odontologos(id),
    FOREIGN KEY (servicio_id)   REFERENCES servicios(id)
) ENGINE=InnoDB;

-- Pagos
CREATE TABLE pagos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id    INT NOT NULL,
    tratamiento_id INT NULL,
    fecha          DATE NOT NULL,
    monto          DECIMAL(10,2) NOT NULL,
    metodo         ENUM('Efectivo','Tarjeta de crédito','Tarjeta de débito','Yape') NOT NULL,
    nro_operacion  VARCHAR(40) NULL,
    estado         ENUM('Pagado','Anulado') NOT NULL DEFAULT 'Pagado',
    observacion    VARCHAR(255) NULL,
    FOREIGN KEY (paciente_id)    REFERENCES pacientes(id),
    FOREIGN KEY (tratamiento_id) REFERENCES tratamientos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================================
--  Datos de ejemplo
-- =========================================================
-- Usuario: admin@sonrisa.com  /  Clave: admin123
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@sonrisa.com', '$2y$10$wsV3zfigvgxWi0ZIRtbYBOTQc/AdfiMbqsDC87wy2MHmr1o/mLo4u', 'Administrador');

INSERT INTO especialidades (nombre, descripcion) VALUES
('Odontología General', 'Diagnóstico y tratamiento básico'),
('Ortodoncia', 'Corrección de la posición de los dientes'),
('Endodoncia', 'Tratamiento de conductos'),
('Periodoncia', 'Tratamiento de encías'),
('Odontopediatría', 'Atención dental para niños'),
('Implantología', 'Colocación de implantes dentales');

INSERT INTO odontologos (dni, cop, nombres, apellidos, especialidad_id, telefono, email) VALUES
('40112233', 'COP-12345', 'Carla',  'Mendoza Ríos',   1, '987654321', 'cmendoza@sonrisa.com'),
('41223344', 'COP-23456', 'Jorge',  'Paredes Luna',   2, '986543210', 'jparedes@sonrisa.com'),
('42334455', 'COP-34567', 'Lucía',  'Torres Vega',    3, '985432109', 'ltorres@sonrisa.com'),
('43445566', 'COP-45678', 'Miguel', 'Salas Quispe',   5, '984321098', 'msalas@sonrisa.com');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min) VALUES
('Consulta y evaluación', 'Evaluación general y odontograma', 50.00, 30),
('Profilaxis (limpieza)', 'Limpieza dental con ultrasonido', 120.00, 45),
('Curación con resina', 'Restauración estética por pieza', 150.00, 45),
('Endodoncia', 'Tratamiento de conducto por pieza', 450.00, 90),
('Extracción simple', 'Exodoncia de pieza dental', 100.00, 30),
('Blanqueamiento dental', 'Blanqueamiento en consultorio', 600.00, 60),
('Ortodoncia (control)', 'Control mensual de brackets', 180.00, 30);

INSERT INTO pacientes (dni, nombres, apellidos, fecha_nacimiento, sexo, telefono, email, direccion, alergias) VALUES
('70123456', 'Ana',    'García López',    '1995-04-12', 'F', '999111222', 'ana.garcia@mail.com', 'Av. Los Olivos 123', 'Penicilina'),
('71234567', 'Luis',   'Ramírez Soto',    '1988-09-30', 'M', '999222333', 'luis.ramirez@mail.com', 'Jr. Lima 456', NULL),
('72345678', 'Sofía',  'Huamán Castillo', '2012-01-20', 'F', '999333444', NULL, 'Calle Las Flores 789', NULL),
('73456789', 'Pedro',  'Flores Díaz',     '1975-11-05', 'M', '999444555', 'pedro.flores@mail.com', 'Av. Grau 321', 'Látex');

INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha, hora, motivo, estado) VALUES
(1, 1, 2, CURDATE() - INTERVAL 5 DAY, '09:00', 'Limpieza semestral', 'Atendida'),
(2, 3, 4, CURDATE() - INTERVAL 2 DAY, '10:30', 'Dolor en molar', 'Atendida'),
(3, 4, 1, CURDATE(),                  '15:00', 'Primera consulta', 'Confirmada'),
(4, 1, 3, CURDATE() + INTERVAL 1 DAY, '11:00', 'Caries', 'Pendiente'),
(1, 2, 7, CURDATE() + INTERVAL 3 DAY, '16:00', 'Evaluación de ortodoncia', 'Pendiente');

INSERT INTO diagnosticos (cita_id, paciente_id, odontologo_id, fecha, pieza_dental, codigo_cie10, descripcion, severidad) VALUES
(1, 1, 1, CURDATE() - INTERVAL 5 DAY, 'General', 'K03.6', 'Placa bacteriana y sarro moderado', 'Leve'),
(2, 2, 3, CURDATE() - INTERVAL 2 DAY, '3.6', 'K04.0', 'Pulpitis irreversible', 'Severo');

INSERT INTO tratamientos (cita_id, diagnostico_id, paciente_id, odontologo_id, servicio_id, fecha, pieza_dental, tratamiento, costo) VALUES
(1, 1, 1, 1, 2, CURDATE() - INTERVAL 5 DAY, 'General', 'Profilaxis con ultrasonido y pulido', 120.00),
(2, 2, 2, 3, 4, CURDATE() - INTERVAL 2 DAY, '3.6', 'Endodoncia en pieza 3.6', 450.00);

INSERT INTO pagos (paciente_id, tratamiento_id, fecha, monto, metodo, nro_operacion) VALUES
(1, 1, CURDATE() - INTERVAL 5 DAY, 120.00, 'Yape', 'YP-88451'),
(2, 2, CURDATE() - INTERVAL 2 DAY, 250.00, 'Tarjeta de débito', 'VD-55120'),
(2, 2, CURDATE(),                  200.00, 'Efectivo', NULL);

-- =========================================================
--  Reglas de pagos: no se eliminan, solo se anulan
-- =========================================================
DELIMITER $$
CREATE TRIGGER trg_pagos_no_eliminar BEFORE DELETE ON pagos FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Los pagos no se eliminan, solo se anulan.';
END$$

CREATE TRIGGER trg_pagos_anulado_bloqueado BEFORE UPDATE ON pagos FOR EACH ROW
BEGIN
    IF OLD.estado = 'Anulado' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Un pago anulado no se puede modificar.';
    END IF;
END$$
DELIMITER ;
