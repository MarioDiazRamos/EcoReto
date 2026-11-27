<?php
declare(strict_types=1);

// HU-02: Pruebas de fechas de participación.
// Se utiliza SQLite en memoria, sin modificar
// la base de datos real del proyecto.

require_once dirname(__DIR__) . '/src/participaciones.php';

use function EcoReto\registrarParticipacion;

// Comprobar un resultado.
function verificarFechaHU02(
    bool $condicion,
    string $mensaje
): void {
    if (!$condicion) {
        throw new RuntimeException('FALLO: ' . $mensaje);
    }

    echo 'OK: ' . $mensaje . PHP_EOL;
}

// Comprobar que una operación sea rechazada.
function esperarRechazoFechaHU02(
    callable $accion,
    string $texto,
    string $mensaje
): void {
    try {
        $accion();
    } catch (DomainException $error) {
        verificarFechaHU02(
            str_contains($error->getMessage(), $texto),
            $mensaje
        );
        return;
    }

    throw new RuntimeException('FALLO: ' . $mensaje);
}

// ==========================================
// 1. PREPARAR SQLITE EN MEMORIA
// ==========================================

$db = new PDO('sqlite::memory:');

$db->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

$db->exec('PRAGMA foreign_keys = ON');

$raiz = dirname(__DIR__);

$db->exec(file_get_contents(
    $raiz . '/database/001_identidad.sql'
));

$db->exec(file_get_contents(
    $raiz . '/database/002_participaciones.sql'
));

// Crear un usuario temporal.
$db->exec(
    "INSERT INTO participantes
        (id, usuario, nombre_visible, password_hash)
     VALUES
        (1, 'prueba_fechas', 'Usuario de prueba', 'hash')"
);

// ==========================================
// 2. CREAR RETOS DE PRUEBA
// ==========================================

$insertarReto = $db->prepare(
    'INSERT INTO retos
        (id, titulo, descripcion, categoria,
         fecha_inicio, fecha_fin, puntos,
         evidencia, activo)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

// id, inicio, fin, activo
$casos = [
    [1, '2026-11-28', '2026-12-05', 1],
    [2, '2026-11-01', '2026-11-26', 1],
    [3, '2026-11-27', '2026-12-05', 1],
    [4, '2026-11-20', '2026-11-27', 1],
    [5, '2026-11-20', '2026-12-05', 1],
    [6, '2026-11-20', '2026-12-05', 0],
    [7, '2026-11-27', '2026-11-27', 1],
];

foreach ($casos as [$id, $inicio, $fin, $activo]) {
    $insertarReto->execute([
        $id,
        'Reto ' . $id,
        'Descripción',
        'Agua',
        $inicio,
        $fin,
        25,
        'Evidencia escrita',
        $activo
    ]);
}

// Fecha fija para pruebas reproducibles.
$fecha = new DateTimeImmutable(
    '2026-11-27 12:00:00',
    new DateTimeZone('America/Mexico_City')
);

$evidencia = 'Realicé la actividad ecológica durante el reto.';

// ==========================================
// PRUEBA 1: RETO QUE NO HA COMENZADO
// ==========================================

esperarRechazoFechaHU02(
    fn() => registrarParticipacion(
        $db, 1, 1, $evidencia, $fecha
    ),
    'todavía no ha comenzado',
    'Rechazar reto que empieza mañana'
);

// ==========================================
// PRUEBA 2: RETO VENCIDO
// ==========================================

esperarRechazoFechaHU02(
    fn() => registrarParticipacion(
        $db, 2, 1, $evidencia, $fecha
    ),
    'terminó',
    'Rechazar reto vencido'
);

// ==========================================
// PRUEBA 3: NO GUARDAR REGISTROS INVÁLIDOS
// ==========================================

$total = $db->query(
    'SELECT COUNT(*) FROM participaciones'
)->fetchColumn();

verificarFechaHU02(
    (int) $total === 0,
    'Los rechazos no guardan participaciones'
);

// ==========================================
// PRUEBAS 4, 5 Y 6: FECHAS PERMITIDAS
// ==========================================

$pruebasValidas = [
    3 => 'Aceptar el día inicial',
    4 => 'Aceptar el día final',
    5 => 'Aceptar durante el plazo'
];

foreach ($pruebasValidas as $retoId => $mensaje) {

    $id = registrarParticipacion(
        $db,
        $retoId,
        1,
        $evidencia,
        $fecha
    );

    verificarFechaHU02($id > 0, $mensaje);
}

// ==========================================
// PRUEBA 7: RETO INACTIVO
// ==========================================

esperarRechazoFechaHU02(
    fn() => registrarParticipacion(
        $db, 6, 1, $evidencia, $fecha
    ),
    'inactivo',
    'Conservar el rechazo de retos inactivos'
);

// ==========================================
// PRUEBA 8: EVITAR DUPLICADOS
// ==========================================

esperarRechazoFechaHU02(
    fn() => registrarParticipacion(
        $db, 3, 1, $evidencia, $fecha
    ),
    'Ya registraste',
    'Conservar la prevención de duplicados'
);

// ==========================================
// PRUEBA 9: RESPETAR ZONA HORARIA
// ==========================================

// 28 de noviembre a las 02:00 UTC
// todavía es 27 de noviembre en Ciudad de México.
$fechaUtc = new DateTimeImmutable(
    '2026-11-28 02:00:00',
    new DateTimeZone('UTC')
);

$id = registrarParticipacion(
    $db, 7, 1, $evidencia, $fechaUtc
);

verificarFechaHU02(
    $id > 0,
    'Respetar la fecha de Ciudad de México'
);

// ==========================================
// PRUEBA 10: VERIFICAR TOTAL GUARDADO
// ==========================================

$total = $db->query(
    'SELECT COUNT(*) FROM participaciones'
)->fetchColumn();

verificarFechaHU02(
    (int) $total === 4,
    'Solo se guardaron las cuatro participaciones válidas'
);

echo PHP_EOL;
echo 'Pruebas de fechas HU-02 completadas.' . PHP_EOL;
