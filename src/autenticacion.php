<?php
declare(strict_types=1);

namespace EcoReto;

function autenticar(\PDO $db, string $usuario, string $clave): ?array
{
    $consulta = $db->prepare(
        'SELECT id, usuario, rol, password_hash
         FROM participantes
         WHERE usuario = ?'
    );
    $consulta->execute([trim($usuario)]);
    $cuenta = $consulta->fetch(\PDO::FETCH_ASSOC);

    if ($cuenta === false || !password_verify($clave, $cuenta['password_hash'])) {
        return null;
    }

    return [
        'participante_id' => (int) $cuenta['id'],
        'usuario' => $cuenta['usuario'],
        'rol' => $cuenta['rol'],
    ];
}