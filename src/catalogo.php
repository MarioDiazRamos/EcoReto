<?php
declare(strict_types=1);
namespace EcoReto;

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function retos(): array
{
    require_once __DIR__ . '/base_datos.php';
    $db = abrirBaseDatos();
    $consulta = $db->query(
        'SELECT id, titulo, categoria, puntos, fecha_inicio AS inicio,
                fecha_fin AS fin, descripcion, evidencia
         FROM retos
         WHERE activo = 1
         ORDER BY id'
    );
    $registros = $consulta->fetchAll(\PDO::FETCH_ASSOC);
    return array_map(static function (array $reto): array {
        $reto['id'] = (int) $reto['id'];
        $reto['puntos'] = (int) $reto['puntos'];
        return $reto;
    }, $registros);
}

function categorias(): array
{
    return array_values(array_unique(array_column(retos(), 'categoria')));
}

function normalizar(string $texto): string
{
    return strtolower(strtr($texto, ['Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ü'=>'u','Ñ'=>'n','á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']));
}

function leerTexto(array $entrada, string $clave, int $limite): string
{
    $valor = $entrada[$clave] ?? '';
    if (!is_string($valor) || preg_match('//u', $valor) !== 1 || preg_match_all('/./us', $valor) > $limite) {
        throw new \InvalidArgumentException('Parámetro inválido: ' . $clave);
    }
    return trim($valor);
}

function filtros(array $entrada): array
{
    $q = leerTexto($entrada, 'q', 160);
    $categoria = leerTexto($entrada, 'category', 40);
    if ($categoria !== '' && !in_array($categoria, categorias(), true)) {
        throw new \InvalidArgumentException('Categoría desconocida.');
    }
    return [$q, $categoria];
}

function buscar(string $q, string $categoria): array
{
    $consulta = normalizar($q);
    return array_values(array_filter(retos(), static function (array $reto) use ($consulta, $categoria): bool {
        return ($categoria === '' || $reto['categoria'] === $categoria)
            && ($consulta === '' || str_contains(normalizar($reto['titulo'] . ' ' . $reto['descripcion']), $consulta));
    }));
}

function encontrar(array $entrada): ?array
{
    $id = leerTexto($entrada, 'id', 9);
    if (!preg_match('/^[1-9][0-9]{0,8}$/D', $id)) {
        throw new \InvalidArgumentException('El identificador del reto debe ser un entero positivo.');
    }
    foreach (retos() as $reto) {
        if ($reto['id'] === (int) $id) { return $reto; }
    }
    return null;
}
