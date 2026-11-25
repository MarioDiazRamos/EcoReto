<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/base_datos.php';

function comprobar(bool $condicion, string $descripcion): void
{
    if (!$condicion) {
        throw new RuntimeException('FALLO: ' . $descripcion);
    }

    echo 'OK: ' . $descripcion . PHP_EOL;
}

$db = \EcoReto\abrirBaseDatos();

// Comprobar que la tabla exista
$existe = $db->query(
    "SELECT COUNT(*) FROM sqlite_master
     WHERE type = 'table' AND name = 'participaciones'"
)->fetchColumn();

comprobar((int) $existe === 1, 'Tabla participaciones creada');

comprobar(
    (int) $db->query('PRAGMA foreign_keys')->fetchColumn() === 1,
    'Claves foráneas activadas'
);

// Las inserciones de prueba se desharán al terminar
$db->beginTransaction();

try {
    $insertar = $db->prepare(
        'INSERT INTO participantes
         (usuario, nombre_visible, password_hash)
         VALUES (?, ?, ?)'
    );

    $insertar->execute([
        'test_hu02_' . bin2hex(random_bytes(6)),
        'Participante de prueba',
        password_hash('clave-temporal', PASSWORD_DEFAULT),
    ]);

    $participanteId = (int) $db->lastInsertId();

    $sql = 'INSERT INTO participaciones
            (reto_id, participante_id, evidencia_textual)
            VALUES (?, ?, ?)';

    $db->prepare($sql)->execute([
        1,
        $participanteId,
        'Utilicé una botella reutilizable durante siete días.',
    ]);

    $consultar = $db->prepare(
        'SELECT estado FROM participaciones
         WHERE reto_id = ? AND participante_id = ?'
    );

    $consultar->execute([1, $participanteId]);

    comprobar(
        $consultar->fetchColumn() === 'pendiente',
        'Estado pendiente por defecto'
    );

    foreach ([
        'Participación duplicada rechazada' => [
            1, $participanteId,
            'Una segunda evidencia en el mismo reto.'
        ],
        'Reto inexistente rechazado' => [
            999999, $participanteId,
            'Evidencia de un reto inexistente.'
        ],
        'Evidencia demasiado corta rechazada' => [
            1, $participanteId, 'corta'
        ],
    ] as $descripcion => $datos) {
        $rechazado = false;

        try {
            $db->prepare($sql)->execute($datos);
        } catch (PDOException $e) {
            $rechazado = true;
        }

        comprobar($rechazado, $descripcion);
    }
} finally {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
}

echo 'Pruebas HU-02 completadas.' . PHP_EOL;
