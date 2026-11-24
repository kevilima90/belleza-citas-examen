-- Base de datos del proyecto Belleza Citas.
-- Importar este archivo desde phpMyAdmin.
CREATE DATABASE IF NOT EXISTS peluqueria_pwa
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE peluqueria_pwa;

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT IGNORE INTO roles (nombre) VALUES ('Cliente'), ('Peluquero');

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    duracion_minutos SMALLINT UNSIGNED NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_peluquero INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente','confirmada','rechazada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
    observaciones VARCHAR(500) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_citas_cliente (id_cliente),
    INDEX idx_citas_peluquero_fecha (id_peluquero, fecha),
    CONSTRAINT fk_citas_cliente FOREIGN KEY (id_cliente) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_citas_peluquero FOREIGN KEY (id_peluquero) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_citas_servicio FOREIGN KEY (id_servicio) REFERENCES servicios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Cuentas de demostración. Cambia estas contraseñas después de la instalación.
INSERT IGNORE INTO usuarios (nombre, correo, telefono, contrasena, rol_id)
VALUES
('Peluquero Demo', 'peluquero@demo.com', '0990000001', '$2y$12$kSEZ.uvMySOs.yYvFHbo.O.mqICzh3AOy39XhmFaWlw29CHR5RYgu',
 (SELECT id FROM roles WHERE nombre = 'Peluquero')),
('Cliente Demo', 'cliente@demo.com', '0990000002', '$2y$12$aunACiwXDaKzr/EMhlQEyOTCqASGc0EMX8y/.W3j3tckAgW1Uyg5m',
 (SELECT id FROM roles WHERE nombre = 'Cliente'));

INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio)
SELECT 'Corte de cabello', 'Corte y acabado según el estilo solicitado.', 45, 12.00
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Corte de cabello');

INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio)
SELECT 'Peinado', 'Peinado para uso diario o una ocasión especial.', 60, 18.00
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Peinado');

INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio)
SELECT 'Tinte', 'Aplicación de color y acabado profesional.', 120, 35.00
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Tinte');

INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio)
SELECT 'Tratamiento capilar', 'Tratamiento de hidratación y cuidado del cabello.', 60, 25.00
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Tratamiento capilar');
