<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<html lang="it">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0">
    <meta name="description" content="">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="" />
    <meta property="og:type" content="website" />
    <title>Accedi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Accedi</h1>
    <form action="controlUserTBF.php" method="post">
        <input type="text" id="nickname" name="nickname" placeholder="Nickname" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Accedi">
    </form>
    <a href="playerRegister.php">Registrati</a>
    <a href="http://localhost/reazioneacatena/">Torna al gioco</a>
</body>
</html>