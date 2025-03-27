<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <title>Accedi</title>
</head>
<body>
    <h1>Accedi</h1>
    <form action="controlUserTBF.php" method="post">
        <input type="text" id="nickname" name="nickname" placeholder="Nickname" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Accedi">
    </form>
    <a href="playerRegister.php">Registrati</a>
</body>
<footer>

</footer>
</html>