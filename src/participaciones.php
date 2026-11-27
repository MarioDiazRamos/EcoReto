<?php
declare(strict_types=1);

namespace EcoReto;

/**
 * Registra una participación en un reto.
 *
 * Validaciones:
 * - Identificadores positivos.
 * - Evidencia de 10 a 1000 caracteres.
 * - Reto existente y activo.
 * - Participante existente.
 * - Una participación por usuario y reto.
 */

function registrarParticipacion(
    \PDO $db,
    int $retoId,
    int $participanteId,
    string $evidencia,
    ?\DateTimeImmutable $fechaActual = null
): int {
    if ($retoId <= 0 || $participanteId <= 0) {
        throw new \InvalidArgumentException(
            'Los identificadores deben ser positivos.'
        );
    }

    // Limpiar y validar la evidencia
    $evidencia = trim($evidencia);
    $longitud = preg_match_all('/./us', $evidencia);

    if ($longitud === false || $longitud < 10 || $longitud > 1000) {
        throw new \InvalidArgumentException(
            'La evidencia debe contener entre 10 y 1000 caracteres válidos.'
        );
    }

    // HU-02: Consultar el reto y sus fechas.
    $consulta = $db->prepare(
        'SELECT fecha_inicio, fecha_fin
        FROM retos
        WHERE id = ? AND activo = 1'
    );

    $consulta->execute([$retoId]);

    $reto = $consulta->fetch(\PDO::FETCH_ASSOC);

    // Validar que exista y esté activo.
    if ($reto === false) {
        throw new \DomainException(
            'El reto no existe o está inactivo.'
        );
    }

    // Zona horaria utilizada por EcoReto.
    $zona = new \DateTimeZone('America/Mexico_City');

    // En funcionamiento normal se utiliza la fecha real.
    // En pruebas se puede proporcionar una fecha específica.
    $fechaActual ??= new \DateTimeImmutable('now', $zona);

    $hoy = $fechaActual
        ->setTimezone($zona)
        ->format('Y-m-d');

    // Validar que el reto ya haya comenzado.
    if ($hoy < $reto['fecha_inicio']) {
        throw new \DomainException(
            'El reto todavía no ha comenzado. Inicia el '
            . $reto['fecha_inicio'] . '.'
        );
    }

    // Validar que el reto no haya terminado.
    if ($hoy > $reto['fecha_fin']) {
        throw new \DomainException(
            'El plazo para participar terminó el '
            . $reto['fecha_fin'] . '.'
        );
    }

    // Comprobar que el usuario exista
    $consulta = $db->prepare(
        'SELECT id FROM participantes WHERE id = ?'
    );
    $consulta->execute([$participanteId]);

    if ($consulta->fetchColumn() === false) {
        throw new \DomainException(
            'El participante no existe.'
        );
    }

    // Evitar participaciones duplicadas
    $consulta = $db->prepare(
        'SELECT id FROM participaciones
         WHERE reto_id = ? AND participante_id = ?'
    );
    $consulta->execute([$retoId, $participanteId]);

    if ($consulta->fetchColumn() !== false) {
        throw new \DomainException(
            'Ya registraste una participación en este reto.'
        );
    }

    // Guardar la participación
    $insertar = $db->prepare(
        'INSERT INTO participaciones
            (reto_id, participante_id, evidencia_textual)
         VALUES (?, ?, ?)'
    );

    $insertar->execute([
        $retoId,
        $participanteId,
        $evidencia
    ]);

    return (int) $db->lastInsertId();
}

/**
 * Devuelve todas las participaciones de un usuario.
 */
function listarParticipaciones(
    \PDO $db,
    int $participanteId
): array {
    if ($participanteId <= 0) {
        throw new \InvalidArgumentException(
            'El identificador del participante debe ser positivo.'
        );
    }

    $consulta = $db->prepare(
        'SELECT
            p.id,
            p.reto_id,
            r.titulo AS reto_titulo,
            p.evidencia_textual,
            p.estado,
            p.creada_en,
            COALESCE(pt.valor, 0) AS puntos_otorgados,
            v.motivo
         FROM participaciones p
         INNER JOIN retos r ON r.id = p.reto_id
         LEFT JOIN puntos pt ON pt.participacion_id = p.id
         LEFT JOIN validaciones v ON v.participacion_id = p.id
         WHERE p.participante_id = ?
         ORDER BY p.creada_en DESC, p.id DESC'
    );

    $consulta->execute([$participanteId]);
    $filas = $consulta->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($filas as &$fila) {
        $fila['id'] = (int) $fila['id'];
        $fila['reto_id'] = (int) $fila['reto_id'];
    }
    unset($fila);

    return $filas;
}

/**
 * Obtiene una participación específica del usuario.
 * Devuelve null si no existe o no le pertenece.
 */
function obtenerParticipacion(
    \PDO $db,
    int $participacionId,
    int $participanteId
): ?array {
    if ($participacionId <= 0 || $participanteId <= 0) {
        throw new \InvalidArgumentException(
            'Los identificadores deben ser positivos.'
        );
    }

    $consulta = $db->prepare(
        'SELECT
            p.id,
            p.reto_id,
            r.titulo AS reto_titulo,
            p.evidencia_textual,
            p.estado,
            p.creada_en,
            COALESCE(pt.valor, 0) AS puntos_otorgados,
            v.motivo
         FROM participaciones p
         INNER JOIN retos r ON r.id = p.reto_id
         LEFT JOIN puntos pt ON pt.participacion_id = p.id
         LEFT JOIN validaciones v ON v.participacion_id = p.id
         WHERE p.id = ? AND p.participante_id = ?'
    );

    $consulta->execute([
        $participacionId,
        $participanteId
    ]);

    $fila = $consulta->fetch(\PDO::FETCH_ASSOC);

    if ($fila === false) {
        return null;
    }

    $fila['id'] = (int) $fila['id'];
    $fila['reto_id'] = (int) $fila['reto_id'];

    return $fila;
}