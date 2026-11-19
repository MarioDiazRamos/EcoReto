<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/vista.php';
use function EcoReto\{filtros,buscar,categorias,escapar,cabecera,pie,errorPagina};
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!in_array($ruta, ['/', '/index.php'], true)) { errorPagina(404, 'La página solicitada no existe.'); }
try { [$q, $categoria] = filtros($_GET); } catch (InvalidArgumentException $e) { errorPagina(400, $e->getMessage()); }
$resultados = buscar($q, $categoria);
cabecera('Explora los retos', 'retos');
?>
<section class="grid lg:grid-cols-3 gap-8 items-center mb-10">
<div class="lg:col-span-2"><p class="text-sm font-bold tracking-widest uppercase text-emerald-700 mb-4">Tu siguiente paso cuenta</p><h1 class="text-4xl md:text-6xl font-bold tracking-tight leading-tight text-emerald-950">Cambia un hábito.<br>Inspira a tu comunidad.</h1><p class="mt-5 max-w-xl text-lg text-stone-600">Encuentra un reto, comparte tu experiencia y construye un impacto que podamos ver juntos.</p></div>
<aside class="rounded-3xl bg-emerald-950 p-7 text-white"><p class="text-lime-200 text-sm font-bold uppercase tracking-widest">Cómo funciona</p><ol class="mt-5 space-y-4"><li>01 · Elige una acción</li><li>02 · Describe tu participación</li><li>03 · Espera la validación</li></ol><p class="mt-5 text-sm text-emerald-100">Los puntos se otorgan únicamente después de aprobar la evidencia.</p></aside>
</section>
<div class="flex flex-wrap items-end justify-between gap-3 mb-5"><h2 class="text-2xl font-bold text-emerald-950">Explora los retos</h2><span class="text-sm text-stone-500">Catálogo persistente en SQLite</span></div>
<form method="get" action="/index.php" class="rounded-2xl bg-white border border-stone-200 p-4 grid sm:grid-cols-[1fr_auto_auto] gap-4 mb-6">
<div><label for="q" class="block text-sm font-medium mb-2">Buscar por palabra</label><input id="q" name="q" value="<?= escapar($q) ?>" maxlength="160" placeholder="Ej. agua, residuos…" class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
<div><label for="category" class="block text-sm font-medium mb-2">Categoría</label><select id="category" name="category" class="w-full rounded-xl border border-stone-300 px-4 py-3 bg-white"><option value="">Todas las categorías</option><?php foreach (categorias() as $opcion): ?><option value="<?= escapar($opcion) ?>" <?= $categoria === $opcion ? 'selected' : '' ?>><?= escapar($opcion) ?></option><?php endforeach ?></select></div>
<button class="self-end rounded-xl bg-emerald-900 text-white px-6 py-3 font-bold hover:bg-emerald-800" type="submit">Buscar retos</button>
</form>
<div class="mb-5 flex justify-between gap-3"><p role="status"><?= count($resultados) ?> retos encontrados</p><a href="/index.php" class="text-emerald-800 underline">Limpiar filtros</a></div>
<?php if (!$resultados): ?><div class="rounded-2xl border border-dashed border-stone-300 p-12 text-center"><h3 class="text-xl font-bold">No encontramos retos con esos filtros</h3><p class="mt-2">Prueba otra palabra o selecciona todas las categorías.</p></div><?php endif ?>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
<?php foreach ($resultados as $reto): ?>
<article class="rounded-2xl border border-stone-200 bg-white p-6 flex flex-col gap-4">
<div class="flex items-center justify-between gap-2"><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800"><?= escapar($reto['categoria']) ?></span><span class="font-bold text-emerald-900"><?= $reto['puntos'] ?> pts</span></div>
<h3 class="text-xl font-bold text-emerald-950"><?= escapar($reto['titulo']) ?></h3><p class="text-sm text-stone-600 grow"><?= escapar($reto['descripcion']) ?></p>
<p class="text-xs text-stone-500">Del <time datetime="<?= $reto['inicio'] ?>"><?= $reto['inicio'] ?></time> al <time datetime="<?= $reto['fin'] ?>"><?= $reto['fin'] ?></time></p>
<a href="/reto.php?id=<?= $reto['id'] ?>" class="border-t border-stone-100 pt-4 font-bold text-emerald-800 hover:underline">Ver reto <span aria-hidden="true">↗</span><span class="sr-only">: <?= escapar($reto['titulo']) ?></span></a>
</article>
<?php endforeach ?>
</div>
<?php pie(); ?>
