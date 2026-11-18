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
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Moderación · EcoReto</title>
</head>
<body>
    <h1>Panel de moderación</h1>
    <p>Sesión de <?= htmlspecialchars($cuenta['usuario'], ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>