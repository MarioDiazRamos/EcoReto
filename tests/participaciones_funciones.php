<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/participaciones.php';

use function EcoReto\{
    registrarParticipacion,
    listarParticipaciones,
    obtenerParticipacion
};

function comprobarHU02(bool $condicion, string $descripcion): void
{
    if (!$condicion) {
        throw new RuntimeException('FALLO: ' . $descripcion);
    }

    echo 'OK: ' . $descripcion . PHP_EOL;
}

function esperarErrorHU02(
    callable $accion,
    string $tipo,
    string $descripcion
): void {
    try {
        $accion();
    } catch (Throwable $e) {
        if (!($e instanceof $tipo)) {
            throw $e;
        }

        comprobarHU02(true, $descripcion);
        return;
    }

    throw new RuntimeException('FALLO: ' . $descripcion);
}

// Crear una base SQLite en memoria
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Aplicar los esquemas del proyecto
$db->exec(file_get_contents(
    dirname(__DIR__) . '/database/001_identidad.sql'
));

$db->exec(file_get_contents(
    dirname(__DIR__) . '/database/002_participaciones.sql'
));

// Crear usuarios de prueba
$db->exec(
    "INSERT INTO participantes
        (id, usuario, nombre_visible, rol, password_hash)
     VALUES
        (1, 'ana', 'Ana', 'participante', 'hash-prueba'),
        (2, 'luis', 'Luis', 'participante', 'hash-prueba')"
);

// Crear retos de prueba
$db->exec(
    "INSERT INTO retos
        (id, titulo, descripcion, categoria,
         fecha_inicio, fecha_fin, puntos, evidencia, activo)
     VALUES
        (1, 'Ahorrar agua', 'Descripción', 'Agua',
         '2026-01-01', '2027-12-31', 25, 'Texto', 1),

        (2, 'Reto cerrado', 'Descripción', 'Agua',
         '2026-01-01', '2027-12-31', 30, 'Texto', 0),

        (3, 'Reciclar', 'Descripción', 'Residuos',
         '2026-01-01', '2027-12-31', 15, 'Texto', 1)"
);

// PRUEBA 1: Registrar una participación
$id = registrarParticipacion(
    $db,
    1,
    1,
    '  Ahorré agua durante toda la semana.  '
);

comprobarHU02(
    $id > 0,
    'Se registra una participación'
);

// PRUEBA 2: Estado inicial
$detalle = obtenerParticipacion($db, $id, 1);

comprobarHU02(
    $detalle !== null && $detalle['estado'] === 'pendiente',
    'Estado inicial pendiente'
);

// PRUEBA 3: Limpiar espacios externos
comprobarHU02(
    $detalle['evidencia_textual'] ===
        'Ahorré agua durante toda la semana.',
    'Se eliminan espacios externos'
);

// PRUEBA 4: Obtener el título
comprobarHU02(
    $detalle['reto_titulo'] === 'Ahorrar agua',
    'Consulta incluye el título del reto'
);

// PRUEBA 5: Privacidad
comprobarHU02(
    obtenerParticipacion($db, $id, 2) === null,
    'Un usuario no consulta evidencia de otro'
);

// PRUEBA 6: Historial
comprobarHU02(
    count(listarParticipaciones($db, 1)) === 1,
    'Se listan participaciones propias'
);

// PRUEBA 7: Otro usuario
comprobarHU02(
    listarParticipaciones($db, 2) === [],
    'Otro usuario no ve evidencias ajenas'
);

// PRUEBA 8: Duplicados
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 1, 1, 'Repetición de evidencia válida.'
    ),
    DomainException::class,
    'Se rechaza un duplicado'
);

// PRUEBA 9: Reto inexistente
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 999, 1, 'Evidencia de una acción correcta.'
    ),
    DomainException::class,
    'Se rechaza un reto inexistente'
);

// PRUEBA 10: Reto inactivo
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 2, 1, 'Evidencia de una acción correcta.'
    ),
    DomainException::class,
    'Se rechaza un reto inactivo'
);

// PRUEBA 11: Usuario inexistente
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 1, 999, 'Evidencia de una acción correcta.'
    ),
    DomainException::class,
    'Se rechaza un usuario inexistente'
);

// PRUEBA 12: Evidencia corta
esperarErrorHU02(
    fn() => registrarParticipacion($db, 3, 1, 'corta'),
    InvalidArgumentException::class,
    'Se rechaza una evidencia corta'
);

// PRUEBA 13: Evidencia larga
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 3, 1, str_repeat('a', 1001)
    ),
    InvalidArgumentException::class,
    'Se rechaza una evidencia larga'
);

// PRUEBA 14: Texto UTF-8 inválido
esperarErrorHU02(
    fn() => registrarParticipacion($db, 3, 1, chr(255)),
    InvalidArgumentException::class,
    'Se rechaza UTF-8 inválido'
);

// PRUEBA 15: Identificador inválido
esperarErrorHU02(
    fn() => registrarParticipacion(
        $db, 0, 1, 'Evidencia de una acción correcta.'
    ),
    InvalidArgumentException::class,
    'Se rechaza un identificador inválido'
);

// PRUEBA 16: Participar en distintos retos
$id2 = registrarParticipacion(
    $db,
    3,
    1,
    'Separé papel y plástico para reciclarlos.'
);

comprobarHU02(
    $id2 > $id && count(listarParticipaciones($db, 1)) === 2,
    'Una persona puede participar en distintos retos'
);

echo 'Pruebas de funciones HU-02 completadas.' . PHP_EOL;