<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/vista.php';
use function EcoReto\{cabecera,pie,escapar};
cabecera('Ranking inicial', 'ranking');
?>
<p class="text-sm font-bold uppercase tracking-widest text-emerald-700">Crecer en comunidad</p><h1 class="mt-3 text-4xl font-bold text-emerald-950">Cada acción deja huella</h1>
<p class="mt-5 max-w-2xl text-stone-600">Vista inicial con ejemplos sintéticos. Estos puntos no proceden de participaciones guardadas; el ranking real y las estadísticas se desarrollarán con el equipo.</p>
<div class="mt-8 grid md:grid-cols-2 gap-6">
<?php foreach (['Personas de ejemplo'=>[['Persona A',40],['Persona B',25],['Persona C',0]], 'Grupos de ejemplo'=>[['Grupo Bosque',40],['Grupo Río',25],['Grupo Tierra',0]]] as $titulo => $filas): ?>
<section class="bg-white border border-stone-200 rounded-2xl p-6"><h2 class="text-xl font-bold mb-4"><?= escapar($titulo) ?></h2><table class="w-full text-left"><caption class="sr-only"><?= escapar($titulo) ?>: datos ilustrativos</caption><thead><tr class="text-sm text-stone-500"><th scope="col" class="py-3">Posición</th><th scope="col">Nombre</th><th scope="col" class="text-right">Puntos</th></tr></thead><tbody><?php foreach ($filas as $i => [$nombre,$puntos]): ?><tr class="border-t border-stone-100"><td class="py-4"><?= $i+1 ?></td><th scope="row" class="font-medium"><?= escapar($nombre) ?></th><td class="text-right font-bold text-emerald-800"><?= $puntos ?></td></tr><?php endforeach ?></tbody></table></section>
<?php endforeach ?>
</div><p class="mt-6 rounded-xl bg-emerald-50 p-4 text-emerald-900">Regla del proyecto: pendiente = 0 puntos. Aprobar dos veces nunca debe duplicar el puntaje.</p>
<?php pie(); ?>
