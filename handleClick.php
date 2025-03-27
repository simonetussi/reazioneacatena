<?php
require 'configDatabase.php';
session_start();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $color = $input['color'] ?? null;

    if (!isset($_SESSION['game']) || !$color) {
        throw new Exception('Gioco non inizializzato');
    }

    $_SESSION['game']['userSequence'][] = $color;

    // Controlla la sequenza utente
    $currentStep = count($_SESSION['game']['userSequence']) - 1;
    
    if ($_SESSION['game']['sequence'][$currentStep] !== $color) {
        // Game Over
        echo json_encode([
            'status' => 'gameOver',
            'score' => $_SESSION['game']['score']
        ]);
        unset($_SESSION['game']);
        exit;
    }

    // Se ha completato la sequenza
    if (count($_SESSION['game']['userSequence']) === count($_SESSION['game']['sequence'])) {
        $_SESSION['game']['score']++;
        $_SESSION['game']['sequence'][] = COLORS[array_rand(COLORS)]; // Aggiungi nuovo colore
        $_SESSION['game']['userSequence'] = [];
    }

    echo json_encode([
        'status' => 'continue',
        'score' => $_SESSION['game']['score'],
        'sequence' => $_SESSION['game']['sequence']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>