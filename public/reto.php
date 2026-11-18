<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/vista.php';
use function EcoReto\{encontrar,escapar,cabecera,pie,errorPagina};
try { $reto = encontrar($_GET); } catch (InvalidArgumentException $e) { errorPagina(400, $e->getMessage()); }
if ($reto === null) { errorPagina(404, 'No existe un reto con ese identificador.'); }
cabecera($reto['titulo'], 'retos');
?>
<a href="/index.php" class="text-emerald-800 underline">← Todos los retos</a>
<section class="mt-8 grid md:grid-cols-3 gap-8"><div class="md:col-span-2"><p class="font-bold text-emerald-700"><?= escapar($reto['categoria']) ?></p><h1 class="mt-3 text-4xl md:text-5xl font-bold text-emerald-950"><?= escapar($reto['titulo']) ?></h1><p class="mt-6 text-lg text-stone-600"><?= escapar($reto['descripcion']) ?></p><h2 class="mt-8 text-xl font-bold">¿Qué puedes compartir?</h2><p class="mt-3"><?= escapar($reto['evidencia']) ?></p><p class="mt-8 text-sm text-stone-500">Reto semilla de demostración. Registrar y validar participaciones se implementará en la siguiente etapa.</p></div>
<aside class="rounded-3xl bg-white border border-stone-200 p-7 h-fit"><p class="text-4xl font-bold text-emerald-900"><?= $reto['puntos'] ?> <span class="text-base">puntos</span></p><p class="mt-2 text-sm text-stone-600">Solo después de aprobación.</p><dl class="mt-6 space-y-3"><div><dt class="font-bold">Inicio</dt><dd><?= $reto['inicio'] ?></dd></div><div><dt class="font-bold">Cierre</dt><dd><?= $reto['fin'] ?></dd></div></dl><a href="/participar.php?id=<?= $reto['id'] ?>" class="block text-center mt-6 rounded-xl bg-emerald-900 text-white px-4 py-3 font-bold">Ver formulario inicial</a></aside></section>
<?php pie(); ?>
