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

COMMIT;
