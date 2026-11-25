-- HU-02: Registro de participaciones

CREATE TABLE IF NOT EXISTS participaciones (
    id INTEGER PRIMARY KEY,

    -- Reto al que pertenece la participación
    reto_id INTEGER NOT NULL,

    -- Usuario que registra la participación
    participante_id INTEGER NOT NULL,

    -- Descripción de la evidencia presentada
    evidencia_textual TEXT NOT NULL
        CHECK (
            length(trim(evidencia_textual))
            BETWEEN 10 AND 1000
        ),

    -- Estado inicial de la participación
    estado TEXT NOT NULL DEFAULT 'pendiente'
        CHECK (
            estado IN (
                'pendiente',
                'aprobada',
                'rechazada'
            )
        ),

    -- Fecha y hora del registro
    creada_en TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Relaciones con las tablas existentes
    FOREIGN KEY (reto_id) REFERENCES retos(id),
    FOREIGN KEY (participante_id)
        REFERENCES participantes(id),

    -- Un usuario solo puede participar una vez
    -- en cada reto
    UNIQUE (reto_id, participante_id)
);

-- Índice para consultar participaciones por usuario
CREATE INDEX IF NOT EXISTS
    idx_participaciones_participante_fecha
ON participaciones (participante_id, creada_en);
