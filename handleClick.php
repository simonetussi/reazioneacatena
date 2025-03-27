<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (headers_sent()) {
    die('Errore: Header già inviati. Verifica spazi vuoti prima del tag PHP aperto.');
}

require_once 'config.php';
require_once 'configDatabase.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Metodo non consentito']));
}

$jsonInput = file_get_contents('php://input');
$input = json_decode($jsonInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die(json_encode(['error' => 'Dati JSON non validi']));
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['game'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Partita non iniziata o sessione scaduta']));
}

if (empty($input['color']) || !in_array($input['color'], COLORS)) {
    http_response_code(400);
    die(json_encode(['error' => 'Colore non valido']));
}

$game = &$_SESSION['game'];
$currentPosition = count($game['userSequence']);

if (!isset($game['sequence'][$currentPosition]) || $game['sequence'][$currentPosition] !== $input['color']) {
    $bestScore = max($game['score'], $game['bestScore'] ?? 0);
    
    try {
        $stmt = $conn->prepare("INSERT INTO user (nickname, score, date) VALUES (?, ?, NOW())");
        $stmt->bind_param("si", $game['nickname'], $game['score']);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log("Errore database: " . $e->getMessage());
    }
    
    unset($_SESSION['game']);
    
    header('Content-Type: application/json');
    die(json_encode([
        'status' => 'gameOver',
        'score' => $game['score'],
        'bestScore' => $bestScore
    ]));
}

$game['userSequence'][] = $input['color'];

if (count($game['userSequence']) === count($game['sequence'])) {
    $game['score']++;
    $game['sequence'][] = COLORS[array_rand(COLORS)];
    $game['userSequence'] = [];
    
    header('Content-Type: application/json');
    die(json_encode([
        'status' => 'success',
        'score' => $game['score'],
        'sequence' => $game['sequence']
    ]));
}

header('Content-Type: application/json');
die(json_encode([
    'status' => 'success',
    'score' => $game['score']
]));
?>