<?php
declare(strict_types=1);

namespace EcoReto;

function pendientesDeRevision(\PDO $db, int $moderadorId): array
{
    $consulta = $db->prepare(
        "SELECT p.id, p.evidencia_textual, r.titulo, r.puntos, u.nombre_visible
         FROM participaciones p
         JOIN retos r ON r.id = p.reto_id
         JOIN participantes u ON u.id = p.participante_id
         WHERE p.estado = 'pendiente' AND p.participante_id <> ?
         ORDER BY p.creada_en, p.id"
    );
    $consulta->execute([$moderadorId]);
    return $consulta->fetchAll(\PDO::FETCH_ASSOC);
}

function revisarParticipacion(\PDO $db, int $id, int $moderadorId, string $decision, string $motivo): void
{
    $motivo = trim($motivo);
    if (!in_array($decision, ['aprobada', 'rechazada'], true)
        || preg_match('/^.{0,1000}$/usD', $motivo) !== 1) {
        throw new \InvalidArgumentException('Revisa la decisión y el motivo (máximo 1000 caracteres).');
    }

    $db->beginTransaction();
    try {
        // La primera operación toma el bloqueo de escritura antes de decidir o puntuar.
        $cambiar = $db->prepare(
            "UPDATE participaciones SET estado = ?
             WHERE id = ? AND estado = 'pendiente' AND participante_id <> ?
               AND EXISTS (SELECT 1 FROM participantes WHERE id = ? AND rol = 'moderador')"
        );
        $cambiar->execute([$decision, $id, $moderadorId, $moderadorId]);
        if ($cambiar->rowCount() !== 1) {
            throw new \DomainException('La participación ya fue revisada o no está disponible para tu cuenta.');
        }

        $registrar = $db->prepare(
            'INSERT INTO validaciones (participacion_id, moderador_id, decision, motivo)
             VALUES (?, ?, ?, ?)'
        );
        $registrar->execute([$id, $moderadorId, $decision, $motivo]);

        if ($decision === 'aprobada') {
            $otorgar = $db->prepare(
                'INSERT INTO puntos (participacion_id, participante_id, valor)
                 SELECT p.id, p.participante_id, r.puntos
                 FROM participaciones p JOIN retos r ON r.id = p.reto_id WHERE p.id = ?'
            );
            $otorgar->execute([$id]);
        }
        $db->commit();
    } catch (\Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}
