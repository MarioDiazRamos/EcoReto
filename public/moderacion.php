<?php
declare(strict_types=1);

session_start();

$cuenta = $_SESSION['cuenta'] ?? null;

if ($cuenta === null) {
    header('Location: /login.php');
    exit;
}

if ($cuenta['rol'] !== 'moderador') {
    http_response_code(403);
    exit('Acceso denegado. Solo los moderadores pueden entrar a esta página.');
}

require_once dirname(__DIR__) . '/src/base_datos.php';
require_once dirname(__DIR__) . '/src/vista.php';

use function EcoReto\abrirBaseDatos;
use function EcoReto\escapar;

$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        $error = 'Recarga la página y vuelve a intentarlo.';
    } else {
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $categoria = trim((string) ($_POST['categoria'] ?? ''));
        $puntos = filter_input(INPUT_POST, 'puntos', FILTER_VALIDATE_INT);
        $inicio = trim((string) ($_POST['inicio'] ?? ''));
        $fin = trim((string) ($_POST['fin'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $evidencia = trim((string) ($_POST['evidencia'] ?? ''));

        if (preg_match('/^.{1,120}$/us', $titulo) !== 1
            || preg_match('/^.{1,40}$/us', $categoria) !== 1
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio)
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)
            || $fin < $inicio
            || preg_match('/^.{1,1000}$/us', $descripcion) !== 1
            || preg_match('/^.{1,1000}$/us', $evidencia) !== 1
            || $puntos === false || $puntos === null || $puntos < 1 || $puntos > 1000) {
            http_response_code(422);
            $error = 'Revisa los datos: todos los campos son obligatorios y las fechas deben ser válidas.';
        } else {
            $db = abrirBaseDatos();
            $insertar = $db->prepare(
                'INSERT INTO retos
                    (titulo, descripcion, categoria, fecha_inicio, fecha_fin, puntos, evidencia, activo)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
            );
            $insertar->execute([
                $titulo,
                $descripcion,
                $categoria,
                $inicio,
                $fin,
                $puntos,
                $evidencia,
            ]);
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: /moderacion.php?creado=1', true, 303);
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moderación · EcoReto</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-stone-50 p-5 text-stone-800">
<main class="mx-auto max-w-3xl">
    <a class="text-emerald-800 underline" href="/index.php">← Ir al catálogo</a>
    <h1 class="mt-6 text-3xl font-bold text-emerald-950">Panel de moderación</h1>
    <p class="mt-2 text-stone-600">Sesión de <?= escapar($cuenta['usuario']) ?></p>

    <?php if (isset($_GET['creado'])): ?>
        <p role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">Reto creado y guardado en la base de datos.</p>
    <?php endif ?>
    <?php if ($error !== ''): ?>
        <p role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900"><?= escapar($error) ?></p>
    <?php endif ?>

    <form method="post" class="mt-8 grid gap-5 rounded-2xl border border-stone-200 bg-white p-6">
        <input type="hidden" name="csrf" value="<?= escapar($_SESSION['csrf']) ?>">
        <div>
            <label class="block font-medium" for="titulo">Título</label>
            <input class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="titulo" name="titulo" maxlength="120" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block font-medium" for="categoria">Categoría</label>
                <input class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="categoria" name="categoria" maxlength="40" required>
            </div>
            <div>
                <label class="block font-medium" for="puntos">Puntos</label>
                <input class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="puntos" name="puntos" type="number" min="1" max="1000" required>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block font-medium" for="inicio">Fecha de inicio</label>
                <input class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="inicio" name="inicio" type="date" required>
            </div>
            <div>
                <label class="block font-medium" for="fin">Fecha de cierre</label>
                <input class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="fin" name="fin" type="date" required>
            </div>
        </div>
        <div>
            <label class="block font-medium" for="descripcion">Descripción</label>
            <textarea class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="descripcion" name="descripcion" maxlength="1000" rows="4" required></textarea>
        </div>
        <div>
            <label class="block font-medium" for="evidencia">Qué puede compartir la persona</label>
            <textarea class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3" id="evidencia" name="evidencia" maxlength="1000" rows="3" required></textarea>
        </div>
        <button class="rounded-xl bg-emerald-900 px-5 py-3 font-bold text-white" type="submit">Crear reto</button>
    </form>
</main>
</body>
</html>