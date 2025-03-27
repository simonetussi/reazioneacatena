<?php
require 'configDatabase.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception("Richiesta vuota");
    }
    
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON malformato");
    }
    
    $nickname = trim($data['nickname'] ?? '');
    if (empty($nickname)) {
        throw new Exception("Nickname obbligatorio");
    }

    // Query migliorata con controllo errori
    if (!$stmt = $conn->prepare("SELECT score FROM users WHERE nickname = ?")) {
        throw new Exception("Preparazione query fallita: " . $conn->error);
    }
    
    $stmt->bind_param("s", $nickname);
    if (!$stmt->execute()) {
        throw new Exception("Esecuzione query fallita: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $response = ['exists' => false];

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'exists' => true,
            'bestScore' => (int)$row['score']
        ];
    }
    
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Errore checkNickname: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>