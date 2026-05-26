-- ============================================================
-- Bluease — Base de datos interna
-- ============================================================

CREATE DATABASE IF NOT EXISTS bluease
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bluease;

-- ------------------------------------------------------------
-- Tabla: usuarios
-- rol 0 → externo   (redirige a bluease.io)
-- rol 1 → empleado  (acceso a la app)
-- rol 2 → admin     (acceso a la app + gestión de usuarios)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol        TINYINT      NOT NULL DEFAULT 0
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
