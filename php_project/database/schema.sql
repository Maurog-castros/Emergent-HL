-- ============================================================
-- LogiSystem — Esquema de Base de Datos MySQL
-- Versión: 1.0
-- Encoding: utf8mb4 / utf8mb4_unicode_ci
-- ============================================================

-- Crear base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS `logistics_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `logistics_db`;

-- ── Tabla: usuarios ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`                  INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `nombre`              VARCHAR(100)      NOT NULL                     COMMENT 'Nombre del usuario',
    `apellido`            VARCHAR(100)      NOT NULL                     COMMENT 'Apellido del usuario',
    `correo`              VARCHAR(191)      NOT NULL                     COMMENT 'Correo electrónico (único)',
    `username`            VARCHAR(80)       NOT NULL                     COMMENT 'Nombre de usuario (único)',
    `password`            VARCHAR(255)      NOT NULL                     COMMENT 'Hash bcrypt de la contraseña',
    `rol`                 ENUM('admin','operador') NOT NULL DEFAULT 'operador' COMMENT 'Rol del usuario',
    `estado`              ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'  COMMENT 'Estado de la cuenta',
    `fecha_creacion`      TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE  KEY `uq_correo`   (`correo`),
    UNIQUE  KEY `uq_username` (`username`),
    KEY `idx_estado` (`estado`),
    KEY `idx_rol`    (`rol`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del sistema';


-- ── Tabla: clientes ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rut`                 VARCHAR(30)  NOT NULL                          COMMENT 'RUT, NIF, EIN u otro ID fiscal (único)',
    `razon_social`        VARCHAR(200) NOT NULL                          COMMENT 'Nombre o razón social',
    `contacto`            VARCHAR(150) DEFAULT NULL                      COMMENT 'Persona de contacto',
    `correo`              VARCHAR(191) DEFAULT NULL                      COMMENT 'Correo electrónico',
    `telefono`            VARCHAR(50)  DEFAULT NULL                      COMMENT 'Teléfono de contacto',
    `direccion`           VARCHAR(300) DEFAULT NULL                      COMMENT 'Dirección física',
    `ciudad`              VARCHAR(100) DEFAULT NULL,
    `pais`                VARCHAR(100) DEFAULT NULL,
    `estado`              ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    `fecha_creacion`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE  KEY `uq_rut`        (`rut`),
    KEY `idx_razon_social` (`razon_social`(50)),
    KEY `idx_estado`       (`estado`),
    KEY `idx_pais`         (`pais`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Clientes de la plataforma logística';


-- ── Tabla: audit_log (preparada para uso futuro) ────────────────────────────
-- Registra acciones críticas: creación, edición, desactivación de registros.
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED DEFAULT NULL                              COMMENT 'Usuario que realizó la acción',
    `accion`      VARCHAR(100) NOT NULL                                  COMMENT 'Tipo de acción (create, update, toggle, etc.)',
    `tabla`       VARCHAR(60)  DEFAULT NULL                              COMMENT 'Tabla afectada',
    `registro_id` INT UNSIGNED DEFAULT NULL                              COMMENT 'ID del registro afectado',
    `datos`       JSON         DEFAULT NULL                              COMMENT 'Snapshot de los datos modificados',
    `ip`          VARCHAR(45)  DEFAULT NULL                              COMMENT 'IP del cliente',
    `creado_en`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_user`         (`user_id`),
    KEY `idx_tabla_reg`    (`tabla`, `registro_id`),
    KEY `idx_creado_en`    (`creado_en`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de auditoría del sistema';


-- ── Tablas futuras (esqueleto comentado) ────────────────────────────────────
-- Las siguientes tablas se habilitarán en fases posteriores:
--
-- embarques        → Gestión de embarques marítimos/aéreos
-- bills_of_lading  → Conocimientos de embarque (BL)
-- contenedores     → Control de contenedores
-- documentos       → Repositorio de documentos por embarque
-- tracking_eventos → Eventos de tracking por embarque
-- clientes_embarques → Relación N:M cliente ↔ embarque
