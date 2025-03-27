<?php
require 'configDatabase.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['nickname'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nickname required']);
    exit;
}

// Aggiungi la definizione dei colori
define('COLORS', ['red', 'green', 'blue', 'yellow']);

// Inizializza la sessione di gioco
$_SESSION['game'] = [
    'nickname' => trim($input['nickname']),
    'sequence' => [COLORS[array_rand(COLORS)]],
    'userSequence' => [],
    'score' => 0,
    'bestScore' => $_SESSION['game']['bestScore'] ?? 0
];

echo json_encode([
    'sequence' => $_SESSION['game']['sequence'],
    'score' => 0
]);
?>