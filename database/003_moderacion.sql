CREATE TABLE IF NOT EXISTS validaciones (
    id INTEGER PRIMARY KEY,
    participacion_id INTEGER NOT NULL UNIQUE REFERENCES participaciones(id),
    moderador_id INTEGER NOT NULL REFERENCES participantes(id),
    decision TEXT NOT NULL CHECK (decision IN ('aprobada', 'rechazada')),
    motivo TEXT NOT NULL DEFAULT '',
    validada_en TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS puntos (
    id INTEGER PRIMARY KEY,
    participacion_id INTEGER NOT NULL UNIQUE REFERENCES participaciones(id),
    participante_id INTEGER NOT NULL REFERENCES participantes(id),
    valor INTEGER NOT NULL CHECK (valor > 0),
    otorgados_en TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
