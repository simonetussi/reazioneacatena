<?php
header('Content-Type: application/json');
require 'configDatabase.php';

session_start();

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Dati JSON non validi");
    }

    $nickname = trim($data['nickname'] ?? '');
    $newScore = filter_var($data['score'] ?? 0, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0]
    ]);

    if (empty($nickname)) {
        throw new Exception("Nickname obbligatorio");
    }

    if ($newScore === false || $newScore < 0) {
        throw new Exception("Punteggio non valido");
    }

    // Controlla esistenza utente
    $stmt = $conn->prepare("SELECT score FROM users WHERE nickname = ?");
    $stmt->bind_param("s", $nickname);
    
    if (!$stmt->execute()) {
        throw new Exception("Errore durante la verifica utente: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Utente non registrato");
    }

    $row = $result->fetch_assoc();
    $currentScore = (int)$row['score'];
    $shouldUpdate = $newScore > $currentScore;

    if ($shouldUpdate) {
        $updateStmt = $conn->prepare("UPDATE users SET score = ? WHERE nickname = ?");
        $updateStmt->bind_param("is", $newScore, $nickname);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Aggiornamento fallito: " . $conn->error);
        }
        
        $currentScore = $newScore;
    }

    echo json_encode([
        'success' => true,
        'newBestScore' => $currentScore,
        'wasUpdated' => $shouldUpdate
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    if ($conn) $conn->close();
}
?>