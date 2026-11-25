<?php
declare(strict_types=1);

use function EcoReto\autenticar;

require_once dirname(__DIR__) . '/src/autenticacion.php';

session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        $error = 'Recarga la página y vuelve a intentarlo.';
    } elseif (is_string($usuario) && is_string($clave)) {
        $db = new PDO('sqlite:' . dirname(__DIR__) . '/storage/ecoreto.sqlite');
        $cuenta = autenticar($db, $usuario, $clave);

        if ($cuenta === null) {
            $error = 'Usuario o clave incorrectos.';
        } else {
            session_regenerate_id(true);
            $_SESSION['cuenta'] = $cuenta;
            header('Location: /login.php', true, 303);
            exit;
        }
    } else {
        $error = 'Usuario o clave inválidos.';
    }
}

$cuenta = $_SESSION['cuenta'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · EcoReto</title>
</head>
<body>
    <main>
        <?php if ($cuenta !== null): ?>
            <h1>Sesión iniciada</h1>
            <p>Usuario: <?= htmlspecialchars($cuenta['usuario'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Rol: <?= htmlspecialchars($cuenta['rol'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($cuenta['rol'] === 'moderador'): ?>
                <p><a href="/moderacion.php">Abrir panel de moderación y crear retos</a></p>
            <?php endif ?>
            <form method="post" action="/logout.php">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <button type="submit">Cerrar sesión</button>
            </form>
        <?php else: ?>
            <h1>Iniciar sesión en EcoReto</h1>

            <?php if ($error !== ''): ?>
                <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <p>
                    <label>Usuario
                        <input name="usuario" autocomplete="username" required>
                    </label>
                </p>
                <p>
                    <label>Clave
                        <input type="password" name="clave" autocomplete="current-password" required>
                    </label>
                </p>
                <button type="submit">Entrar</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>