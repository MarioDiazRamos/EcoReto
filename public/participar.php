<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/vista.php';
use function EcoReto\{retos,encontrar,escapar,cabecera,pie,errorPagina};
$seleccion = null;
if (isset($_GET['id'])) {
    try { $seleccion = encontrar($_GET); } catch (InvalidArgumentException $e) { errorPagina(400, $e->getMessage()); }
    if ($seleccion === null) { errorPagina(404, 'El reto seleccionado no existe.'); }
}
cabecera('Formulario inicial', 'participar');
?>
<div class="max-w-2xl mx-auto"><p class="font-bold text-emerald-700">Tu experiencia cuenta</p><h1 class="mt-3 text-4xl font-bold text-emerald-950">Comparte una acción</h1><p class="my-5 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-950">Prototipo de revisión 1: no envía ni guarda información, no crea una participación y no asigna puntos. Usa únicamente texto de ejemplo.</p>
<form id="participation-preview" class="rounded-2xl border border-stone-200 bg-white p-6 space-y-5">
<div><label for="reto" class="block font-medium mb-2">Reto</label><select id="reto" required class="w-full border border-stone-300 rounded-xl p-3 bg-white"><option value="">Selecciona un reto</option><?php foreach (retos() as $reto): ?><option value="<?= $reto['id'] ?>" <?= $seleccion && $seleccion['id'] === $reto['id'] ? 'selected' : '' ?>><?= escapar($reto['titulo']) ?></option><?php endforeach ?></select></div>
<div><label for="evidencia" class="block font-medium mb-2">Descripción o evidencia textual</label><textarea id="evidencia" rows="5" required minlength="10" maxlength="1000" placeholder="Describe una acción de ejemplo…" class="w-full border border-stone-300 rounded-xl p-3" aria-describedby="ayuda"></textarea><p id="ayuda" class="text-sm mt-2 text-stone-500">El texto permanece en esta página y no se manda al servidor. La validación definitiva será del servidor en HU-02.</p></div>
<button id="preview-state" type="button" disabled class="rounded-xl bg-emerald-900 text-white px-5 py-3 font-bold disabled:opacity-50">Previsualizar estado pendiente</button><noscript><p>La previsualización del formulario requiere JavaScript; el enlace siguiente permite ver el ejemplo de estado.</p></noscript>
</form><a href="/pendiente.php" class="inline-block mt-5 underline text-emerald-800">Ver ejemplo de estado pendiente</a></div>
<?php pie(); ?>
