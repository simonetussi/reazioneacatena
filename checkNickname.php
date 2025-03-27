<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'reazioneacatena';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connessione al database fallita']));
}

$data = json_decode(file_get_contents('php://input'), true);
$nickname = $conn->real_escape_string($data['nickname'] ?? '');

if (empty($nickname)) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE nickname = ?");
$stmt->bind_param("s", $nickname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['count'] > 0]);

$stmt->close();
$conn->close();
?>