<?php
require_once 'config.php';

if (empty($input['nickname'])) {
    jsonResponse(['Errore' => 'Il nickname è necessario'], 400);
}

$_SESSION['game'] = [
    'nickname' => trim($input['nickname']),
    'sequence' => [COLORS[array_rand(COLORS)]],
    'userSequence' => [],
    'score' => 0,
    'bestScore' => $_SESSION['game']['bestScore'] ?? 0
];

jsonResponse([
    'sequence' => $_SESSION['game']['sequence'],
    'score' => 0
]);
?>