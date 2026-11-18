<?php
declare(strict_types=1);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Usa el botón Cerrar sesión.');
}

$token = $_POST['csrf'] ?? null;
if (!is_string($token) || !isset($_SESSION['csrf'])
    || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    exit('Recarga la página y vuelve a intentarlo.');
}

$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $params['path'],
    $params['domain'], $params['secure'], $params['httponly']);
session_destroy();

header('Location: /login.php', true, 303);
exit;