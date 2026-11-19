PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS grupos (
    id INTEGER PRIMARY KEY,
    nombre TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS participantes (
    id INTEGER PRIMARY KEY,
    usuario TEXT NOT NULL COLLATE NOCASE UNIQUE,
    nombre_visible TEXT NOT NULL,
    grupo_id INTEGER REFERENCES grupos(id),
    rol TEXT NOT NULL DEFAULT 'participante'
        CHECK (rol IN ('participante', 'moderador')),
    password_hash TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS retos (
    id INTEGER PRIMARY KEY,
    titulo TEXT NOT NULL,
    descripcion TEXT NOT NULL,
    categoria TEXT NOT NULL,
    fecha_inicio TEXT NOT NULL,
    fecha_fin TEXT NOT NULL,
    puntos INTEGER NOT NULL CHECK (puntos > 0),
    evidencia TEXT NOT NULL,
    activo INTEGER NOT NULL DEFAULT 1 CHECK (activo IN (0, 1))
);

COMMIT;
