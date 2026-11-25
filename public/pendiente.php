<?php
declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/src/vista.php';
require_once dirname(__DIR__) . '/src/base_datos.php';
require_once dirname(__DIR__) . '/src/participaciones.php';

use function EcoReto\{
    cabecera,
    pie,
    escapar,
    errorPagina,
    abrirBaseDatos,
    listarParticipaciones,
    obtenerParticipacion
};

// Obtener la sesión del usuario
$cuenta = $_SESSION['cuenta'] ?? null;

$autenticado = is_array($cuenta)
    && isset($cuenta['participante_id'])
    && is_int($cuenta['participante_id'])
    && $cuenta['participante_id'] > 0;

// Si viene un ID, consultaremos solo esa participación
$participacionId = null;

if (array_key_exists('id', $_GET)) {
    $valor = $_GET['id'];

    if (
        !is_string($valor)
        || preg_match('/^[1-9][0-9]{0,8}$/D', $valor) !== 1
    ) {
        errorPagina(
            400,
            'Identificador de participación inválido.'
        );
    }

    $participacionId = (int) $valor;
}

// Lista que se mostrará en la página
$participaciones = [];

if ($autenticado) {

    $db = abrirBaseDatos();

    if ($participacionId !== null) {

        // Consultar únicamente participaciones propias
        $detalle = obtenerParticipacion(
            $db,
            $participacionId,
            $cuenta['participante_id']
        );

        if ($detalle === null) {
            errorPagina(
                404,
                'No encontramos esa participación.'
            );
        }

        $participaciones = [$detalle];

    } else {

        // Consultar el historial del usuario
        $participaciones = listarParticipaciones(
            $db,
            $cuenta['participante_id']
        );
    }
}

cabecera('Mis participaciones', 'participar');
?>

<section class="max-w-3xl mx-auto">

    <p class="font-bold text-emerald-700">
        Tu avance en EcoReto
    </p>

    <h1 class="mt-3 text-4xl font-bold text-emerald-950">
        <?= $participacionId !== null
            ? 'Detalle de participación'
            : 'Mis participaciones' ?>
    </h1>

    <p class="mt-4 text-stone-600">
        Consulta tus evidencias y el estado de su revisión.
    </p>

    <?php if (!$autenticado): ?>

        <!-- Usuario sin sesión -->
        <div class="mt-7 rounded-2xl border border-amber-200 bg-amber-50 p-6">

            <p class="font-semibold text-amber-950">
                Inicia sesión para consultar tus participaciones.
            </p>

            <p class="mt-2 text-amber-900">
                Tus evidencias son privadas y solo se muestran
                en tu cuenta.
            </p>

            <a href="/login.php"
               class="inline-block mt-4 rounded-xl bg-emerald-900 px-5 py-3 font-bold text-white">
                Iniciar sesión
            </a>

        </div>

    <?php elseif ($participaciones === []): ?>

        <!-- Usuario sin participaciones -->
        <div class="mt-7 rounded-2xl border border-stone-200 bg-white p-7">

            <h2 class="text-xl font-bold text-emerald-950">
                Todavía no tienes participaciones
            </h2>

            <p class="mt-2 text-stone-600">
                Elige un reto y registra tu primera evidencia.
            </p>

            <a href="/participar.php"
               class="inline-block mt-5 rounded-xl bg-emerald-900 px-5 py-3 font-bold text-white">
                Registrar participación
            </a>

        </div>

    <?php else: ?>

        <!-- Mostrar las participaciones guardadas -->
        <p class="mt-6 text-sm text-stone-600">
            <?= count($participaciones) ?>
            participación(es) encontrada(s).
        </p>

        <div class="mt-4 space-y-5">

            <?php foreach ($participaciones as $participacion): ?>

                <?php
                $estado = $participacion['estado'];

                // Etiqueta del estado
                $etiqueta = match ($estado) {
                    'pendiente' => 'Pendiente',
                    'aprobada' => 'Aprobada',
                    'rechazada' => 'Rechazada',
                    default => 'Desconocido',
                };

                // Color según el estado
                $claseEstado = match ($estado) {
                    'pendiente' => 'bg-amber-100 text-amber-900',
                    'aprobada' => 'bg-emerald-100 text-emerald-900',
                    'rechazada' => 'bg-red-100 text-red-900',
                    default => 'bg-stone-100 text-stone-700',
                };
                ?>

                <article class="rounded-2xl border border-stone-200 bg-white p-6">

                    <div class="flex flex-wrap justify-between items-start gap-3">

                        <h2 class="text-xl font-bold text-emerald-950">
                            <?= escapar($participacion['reto_titulo']) ?>
                        </h2>

                        <span class="rounded-full px-3 py-1 text-sm font-bold <?= $claseEstado ?>">
                            <?= $etiqueta ?>
                        </span>

                    </div>

                    <p class="mt-3 text-sm text-stone-500">
                        Participación #<?= $participacion['id'] ?>
                        · Registrada:
                        <?= escapar($participacion['creada_en']) ?> UTC
                    </p>

                    <h3 class="mt-5 font-semibold">
                        Evidencia enviada
                    </h3>

                    <p class="mt-2 whitespace-pre-wrap break-words text-stone-700"><?= escapar($participacion['evidencia_textual']) ?></p>

                    <?php if (
                        $estado === 'pendiente'
                        || $estado === 'rechazada'
                    ): ?>

                        <p class="mt-4 text-sm font-semibold text-stone-600">
                            Puntos otorgados: 0
                        </p>

                    <?php else: ?>

                        <p class="mt-4 text-sm text-stone-600">
                            La puntuación se consultará cuando esté
                            integrado el módulo de puntos.
                        </p>

                    <?php endif; ?>

                    <?php if ($participacionId === null): ?>

                        <a href="/pendiente.php?id=<?= $participacion['id'] ?>"
                           class="inline-block mt-5 font-bold text-emerald-800 underline">
                            Ver detalle
                        </a>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <!-- Enlaces de navegación -->
    <div class="mt-8 flex flex-wrap gap-5">

        <?php if ($participacionId !== null): ?>

            <a href="/pendiente.php"
               class="font-bold text-emerald-800 underline">
                Volver a mis participaciones
            </a>

        <?php endif; ?>

        <a href="/index.php"
           class="font-bold text-emerald-800 underline">
            Explorar otros retos
        </a>

    </div>

</section>

<?php pie(); ?>
