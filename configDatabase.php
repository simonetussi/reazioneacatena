<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'reazioneacatena';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_errno) {
        throw new Exception("Connessione fallita: " . $conn->connect_error);
    }
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die(json_encode(['success' => false, 'error' => 'Database error']));
}
?>