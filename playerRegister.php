<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connessione al database
$SERVER = "localhost";
$ROOT = "root";
$PASSWORD = "";
$DATABASE = "reazioneacatena";

$con = mysqli_connect($SERVER, $ROOT, $PASSWORD, $DATABASE);
if (!$con) {
    die('Errore di connessione al database: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Raccogli i dati dal form
    $nickname = mysqli_real_escape_string($con, $_POST['nickname']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    // Controlla se il nickname esiste già nel database
    $sql_check = "SELECT * FROM `users` WHERE `nickname` = ?";
    $stmt_check = mysqli_prepare($con, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $nickname);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Il nickname è già presente nel database
        echo '<script language="javascript">';
        echo 'alert("Nickname già utilizzato!");';
        echo '</script>';
    } else {
        // Il nickname non esiste, inserisci i dati nel database
        $sql_insert = "INSERT INTO `users` (`nickname`, `password`) VALUES (?, ?)";
        $stmt_insert = mysqli_prepare($con, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "ss", $nickname, $password);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            echo '<script language="javascript">';
            echo 'alert("Registrazione avvenuta con successo!");';
            echo 'window.location.href="progettoTBF.php";';  // Redirect alla pagina di login
            echo '</script>';
        } else {
            echo '<script language="javascript">';
            echo 'alert("Errore durante la registrazione!");';
            echo '</script>';
        }
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="" />
    <meta property="og:type" content="website" />
    <link rel="stylesheet" href="style.css">
    <title>Registrazione</title>
</head>
<body>
    <h1>Crea Account</h1>
    <form action="playerRegister.php" method="post">
        <input type="text" id="nickname" name="nickname" placeholder="Nickname" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Registrati">    
    </form>
    <a href="login.php">Accedi</a>
    <a href="http://localhost/reazioneacatena/">Torna al gioco</a>
</body>
<footer>
</footer>
</html>
