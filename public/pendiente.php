<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/vista.php';
use function EcoReto\{cabecera,pie};
cabecera('Ejemplo de estado pendiente', 'participar');
?>
<section class="max-w-xl mx-auto rounded-3xl border border-stone-200 bg-white p-8 text-center"><span class="inline-block rounded-full bg-amber-100 px-4 py-2 text-amber-900 font-bold">Pendiente · ejemplo</span><h1 class="mt-6 text-3xl font-bold text-emerald-950">Así se verá una evidencia en revisión</h1><p class="mt-4 text-stone-600">Un moderador deberá revisar la evidencia antes de aprobar o rechazar. Mientras esté pendiente, no otorgará puntos.</p><p class="my-6 text-4xl font-bold text-emerald-900">0 <span class="text-base">puntos</span></p><p class="text-sm text-stone-500">Esta es una vista de ejemplo. No se creó ningún registro ni se guardó el texto del formulario.</p><a href="/index.php" class="inline-block mt-7 rounded-xl bg-emerald-900 px-5 py-3 text-white font-bold">Explorar otros retos</a></section>
<?php pie(); ?>
