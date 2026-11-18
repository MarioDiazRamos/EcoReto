<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/catalogo.php';
use function EcoReto\{retos,buscar,filtros,encontrar,escapar};
$count = 0;
function verificar(bool $valor, string $nombre): void {
    global $count;
    if (!$valor) { throw new RuntimeException('FALLO: ' . $nombre); }
    ++$count;
    echo 'OK: ' . $nombre . PHP_EOL;
}
function invalido(callable $accion): bool {
    try { $accion(); } catch (InvalidArgumentException $e) { return true; }
    return false;
}
verificar(count(retos()) === 6, 'Catálogo semilla con seis retos');
verificar(count(buscar('agua', '')) === 1, 'Buscar en descripción');
verificar(count(buscar('ENERGIA', '')) === 1, 'Mayúsculas y acentos equivalentes');
verificar(count(buscar('', 'Residuos')) === 2, 'Filtrar categoría');
verificar(count(buscar('agua', 'Residuos')) === 0, 'Combinar criterios sin resultados');
verificar(buscar('zzzinexistente', '') === [], 'Búsqueda vacía de coincidencias');
verificar(encontrar(['id'=>'1'])['puntos'] === 40, 'Detalle conserva puntos del reto');
verificar(encontrar(['id'=>'999']) === null, 'Identificador válido inexistente');
verificar(invalido(fn() => filtros(['q'=>['agua']])), 'Rechazar parámetro de tipo array');
verificar(invalido(fn() => filtros(['q'=>str_repeat('a',161)])), 'Rechazar consulta excesiva');
verificar(invalido(fn() => filtros(['category'=>'Otra'])), 'Rechazar categoría desconocida');
verificar(invalido(fn() => encontrar(['id'=>'-1'])), 'Rechazar identificador negativo');
verificar(invalido(fn() => filtros(['q'=>chr(255)])), 'Rechazar UTF-8 inválido');
verificar(!str_contains(escapar('<script>alert("x")</script>'), '<script>'), 'Escapar HTML de entrada');
echo "TOTAL: {$count} pruebas correctas" . PHP_EOL;
