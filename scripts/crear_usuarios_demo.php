<?php
declare(strict_types=1);

$ruta = dirname(__DIR__) . '/storage/ecoreto.sqlite';
$db = new PDO('sqlite:' . $ruta);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$insertar = $db->prepare(
    'INSERT INTO participantes (usuario, nombre_visible, rol, password_hash)
     VALUES (?, ?, ?, ?)
     ON CONFLICT(usuario) DO NOTHING'
);

$cuentas = [
    ['rodo_demo', 'Rodó de prueba', 'participante'],
    ['mod_demo', 'Moderador de prueba', 'moderador'],
];

$db->beginTransaction();

foreach ($cuentas as [$usuario, $nombre, $rol]) {
    $clave = bin2hex(random_bytes(8));
    $hash = password_hash($clave, PASSWORD_DEFAULT);

    $insertar->execute([$usuario, $nombre, $rol, $hash]);

    if ($insertar->rowCount() === 1) {
        echo "Creado: $usuario | Rol: $rol | Clave: $clave", PHP_EOL;
    } else {
        echo "Ya existe: $usuario", PHP_EOL;
    }
}

$db->commit();