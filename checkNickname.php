<?php
header('Content-Type: application/json');
include 'configDatabase.php';

// Controlla errori di connessione
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connessione al database fallita']));
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['error' => 'Dati JSON non validi']));
}

$nickname = $data['nickname'] ?? '';

// Verifica nickname non vuoto
if (empty($nickname)) {
    die(json_encode(['error' => 'Nickname mancante']));
}

$stmt = $conn->prepare("SELECT score FROM users WHERE nickname = ?");
if (!$stmt) {
    die(json_encode(['error' => 'Preparazione query fallita: ' . $conn->error]));
}

$stmt->bind_param("s", $nickname);
if (!$stmt->execute()) {
    die(json_encode(['error' => 'Esecuzione query fallita: ' . $stmt->error]));
}

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'exists' => true,
        'bestScore' => $row['score']
    ]);
} else {
    echo json_encode(['exists' => false]);
}

$stmt->close();
$conn->close();
?>