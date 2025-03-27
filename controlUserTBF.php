<?php
$SERVER = "localhost";
$ROOT = "root";
$PASSWORD = "";
$DATABASE = "reazioneacatena";

// Verifica se i dati sono stati inviati tramite POST
if ($_POST["nickname"] != "" && $_POST["password"] != "") {
    $nick = $_POST["nickname"];
    $pass = $_POST["password"];
}

$con = mysqli_connect($SERVER, $ROOT, $PASSWORD);
if (mysqli_connect_errno()) {
    echo '<script language="javascript">';
    echo 'alert("Connessione al db non corretta!")';
    echo '</script>';
    exit();
}

mysqli_select_db($con, $DATABASE);

// Modifica la query per un controllo più sicuro dei dati
$sql = "SELECT * FROM `users` WHERE `nickname` = ? AND `password` = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $nick, $pass);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: playerScore.php");
    exit();
} else {
    echo '<script language="javascript">';
    echo 'alert("Credenziali errate"); window.location.href="login.php";';
    echo '</script>';
}

mysqli_close($con);
?>
