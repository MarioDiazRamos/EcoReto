<?php
declare(strict_types=1);
namespace EcoReto;
require_once __DIR__ . '/catalogo.php';

function cabecera(string $titulo, string $seccion): void
{
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= escapar($titulo) ?> · EcoReto</title>
<meta name="description" content="Retos ecológicos para transformar hábitos en acciones compartidas.">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<script src="/assets/app.js" defer></script>
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 font-['DM_Sans',sans-serif]">
<a href="#contenido" class="sr-only focus:not-sr-only focus:block focus:p-3 focus:bg-lime-200">Saltar al contenido</a>
<header class="border-b border-stone-200 bg-white">
<div class="mx-auto max-w-6xl px-5 py-5 flex items-center justify-between gap-4">
<a href="/index.php" class="flex items-center gap-3 text-xl font-bold text-emerald-950"><span class="grid size-10 place-items-center rounded-2xl bg-emerald-900 text-lime-200" aria-hidden="true">↗</span>EcoReto</a>
<nav aria-label="Principal" class="hidden md:flex gap-6 items-center">
<?php foreach (['retos'=>['/index.php','Retos'], 'ranking'=>['/ranking.php','Ranking'], 'participar'=>['/participar.php','Participar']] as $clave => [$url,$etiqueta]): ?>
<a href="<?= $url ?>" <?= $seccion === $clave ? 'aria-current="page"' : '' ?> class="font-medium hover:underline <?= $seccion === $clave ? 'text-emerald-800 underline underline-offset-8' : 'text-stone-600' ?>"><?= $etiqueta ?></a>
<?php endforeach ?>
</nav>
<button id="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" class="md:hidden rounded-xl border border-stone-300 px-4 py-2">Menú</button>
</div>
<nav id="mobile-menu" aria-label="Principal móvil" hidden class="md:hidden px-5 pb-5 space-y-3">
<a class="block" href="/index.php">Retos</a><a class="block" href="/ranking.php">Ranking</a><a class="block" href="/participar.php">Participar</a>
</nav>
</header>
<main id="contenido" class="mx-auto max-w-6xl px-5 py-8 md:py-12">
<?php
}

function pie(): void
{
?>
</main><footer class="mx-auto max-w-6xl px-5 py-8 border-t border-stone-200 text-sm text-stone-600 flex flex-wrap justify-between gap-3"><span>Pequeñas acciones. Un cambio compartido.</span><span>EcoReto · Demo académica · Noviembre 2026</span></footer>
</body></html>
<?php
}

function errorPagina(int $codigo, string $mensaje): void
{
    http_response_code($codigo);
    cabecera('Solicitud no disponible', 'retos');
    echo '<h1 class="text-3xl font-bold">' . $codigo . ' · Solicitud no disponible</h1><p class="mt-4">' . escapar($mensaje) . '</p><a class="inline-block mt-6 underline" href="/index.php">Volver al catálogo</a>';
    pie();
    exit;
}
