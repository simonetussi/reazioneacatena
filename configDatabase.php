<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'reazioneacatena';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Errore di connessione al database: " . $conn->connect_error);
}
?>