<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autenticacion.php';

use function EcoReto\autenticar;

$ruta = dirname(__DIR__) . '/storage/ecoreto.sqlite';
$db = new PDO('sqlite:' . $ruta);

echo 'Usuario: ';
$usuario = trim(fgets(STDIN));

echo 'Clave: ';
$clave = rtrim(fgets(STDIN), "\r\n");

$cuenta = autenticar($db, $usuario, $clave);

if ($cuenta === null) {
    echo 'Acceso denegado.', PHP_EOL;
} else {
    echo "Acceso correcto. Usuario: {$cuenta['usuario']} | Rol: {$cuenta['rol']}", PHP_EOL;
}