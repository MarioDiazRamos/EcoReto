<?php
declare(strict_types=1);
session_start();
// Funciones para mostrar las páginas
require_once dirname(__DIR__) . '/src/vista.php';

// Conexión con la base de datos SQLite
require_once dirname(__DIR__) . '/src/base_datos.php';

// Funciones de registro y consulta de participaciones
require_once dirname(__DIR__) . '/src/participaciones.php';


use function EcoReto\{
    retos,
    encontrar,
    escapar,
    cabecera,
    pie,
    errorPagina,
    abrirBaseDatos,
    registrarParticipacion
};

// Crear token CSRF si no existe.
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

// Recuperar el usuario de la sesión.
$cuenta = $_SESSION['cuenta'] ?? null;

$autenticado = is_array($cuenta)
    && isset($cuenta['participante_id'])
    && is_int($cuenta['participante_id'])
    && $cuenta['participante_id'] > 0;

$error = '';
$evidencia = '';
$retoElegido = '';
$guardada = null;

// Seleccionar el reto desde el catálogo.
if (isset($_GET['id'])) {
    try {
        $seleccion = encontrar($_GET);
    } catch (InvalidArgumentException $e) {
        errorPagina(400, $e->getMessage());
    }

    if ($seleccion === null) {
        errorPagina(404, 'El reto seleccionado no existe.');
    }

    $retoElegido = (string) $seleccion['id'];
}

// Procesar el envío del formulario.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    // Solo los usuarios autenticados pueden registrar.
    if (!$autenticado) {
        header('Location: /login.php', true, 303);
        exit;
    }

    $token = $_POST['csrf'] ?? null;
    $valorReto = $_POST['reto_id'] ?? null;
    $valorEvidencia = $_POST['evidencia'] ?? null;

    // Validar token de seguridad.
    if (!is_string($token)
        || !hash_equals($_SESSION['csrf'], $token)) {

        http_response_code(403);
        $error = 'La sesión del formulario venció. Actualiza la página e inténtalo nuevamente.';

    } elseif (
        !is_string($valorReto)
        || preg_match('/^[1-9][0-9]{0,8}$/D', $valorReto) !== 1
        || !is_string($valorEvidencia)
    ) {

        http_response_code(422);
        $error = 'Selecciona un reto y escribe una evidencia válida.';

    } else {

        $retoElegido = $valorReto;
        $evidencia = $valorEvidencia;

        try {
            // Registrar en SQLite mediante la función
            // que desarrollamos en el Commit 2.
            $id = registrarParticipacion(
                abrirBaseDatos(),
                (int) $valorReto,
                $cuenta['participante_id'],
                $valorEvidencia
            );

            // Guardar confirmación para la siguiente página.
            $_SESSION['participacion_guardada'] = $id;

            // Renovar token CSRF.
            $_SESSION['csrf'] = bin2hex(random_bytes(32));

            // Evitar repetir el POST al actualizar.
            header('Location: /participar.php', true, 303);
            exit;

        } catch (InvalidArgumentException | DomainException $e) {
            http_response_code(422);
            $error = $e->getMessage();
        }
    }

} elseif (isset($_SESSION['participacion_guardada'])) {

    // Recuperar el mensaje de éxito una sola vez.
    $guardada = (int) $_SESSION['participacion_guardada'];
    unset($_SESSION['participacion_guardada']);
}

cabecera('Registrar participación', 'participar');
?>

<div class="max-w-2xl mx-auto">

    <p class="font-bold text-emerald-700">
        Tu experiencia cuenta
    </p>

    <h1 class="mt-3 text-4xl font-bold text-emerald-950">
        Comparte una acción
    </h1>

    <p class="my-5 text-stone-600">
        Registra tu evidencia. Quedará pendiente de revisión
        y no otorgará puntos hasta que un moderador la apruebe.
    </p>

    <?php if ($guardada !== null): ?>
        <p role="status"
           class="my-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-950">
            Participación #<?= $guardada ?> guardada correctamente.
            Estado: pendiente. Puntos otorgados: 0.
        </p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p role="alert"
           class="my-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900">
            <?= escapar($error) ?>
        </p>
    <?php endif; ?>

    <?php if (!$autenticado): ?>
        <p class="my-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-950">
            Para registrar una participación debes
            <a class="underline font-bold" href="/login.php">
                iniciar sesión
            </a>.
        </p>
    <?php endif; ?>

    <form method="post"
          action="/participar.php"
          class="rounded-2xl border border-stone-200 bg-white p-6 space-y-5">

        <input type="hidden"
               name="csrf"
               value="<?= escapar($_SESSION['csrf']) ?>">

        <div>
            <label for="reto" class="block font-medium mb-2">
                Reto
            </label>

            <select id="reto"
                    name="reto_id"
                    required
                    class="w-full border border-stone-300 rounded-xl p-3 bg-white">

                <option value="">Selecciona un reto</option>

                <?php foreach (retos() as $reto): ?>
                    <option
                        value="<?= $reto['id'] ?>"
                        <?= $retoElegido === (string) $reto['id'] ? 'selected' : '' ?>>
                        <?= escapar($reto['titulo']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div>
            <label for="evidencia" class="block font-medium mb-2">
                Descripción o evidencia textual
            </label>

            <textarea id="evidencia"
                      name="evidencia"
                      rows="5"
                      required
                      minlength="10"
                      maxlength="1000"
                      placeholder="Describe la acción que realizaste…"
                      class="w-full border border-stone-300 rounded-xl p-3"
                      aria-describedby="ayuda"><?= escapar($evidencia) ?></textarea>

            <p id="ayuda" class="text-sm mt-2 text-stone-500">
                De 10 a 1000 caracteres. El servidor revisará
                los datos antes de guardarlos.
            </p>
        </div>

        <button type="submit"
                <?= $autenticado ? '' : 'disabled' ?>
                class="rounded-xl bg-emerald-900 text-white px-5 py-3 font-bold disabled:opacity-50">
            Registrar participación
        </button>

    </form>
</div>

<?php pie(); ?>