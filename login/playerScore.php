<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Classifica Punteggi</title>
</head>
<body>
    <h1>Classifica Punteggi</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nickname</th>
            <th>Punteggio</th>
            <th>Data</th>
        </tr>
        <?php
        $SERVER = "localhost";
        $ROOT = "root";
        $PASSWORD = "";
        $DATABASE = "reazioneacatena";

        $con = mysqli_connect($SERVER, $ROOT, $PASSWORD, $DATABASE);
        if (!$con) {
            echo "<tr><td colspan='4'>Errore di connessione al database</td></tr>";
            exit();
        }

        $sql = "SELECT * FROM `scores`";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["fk_nickname"] . "</td><td>" . $row["punteggio"] . "</td><td>" . $row["data"] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nessun risultato trovato</td></tr>";
        }

        mysqli_close($con);
        ?>
    </table>
    <a href="progettoTBF.php">Accedi di nuovo</a>
</body>
</html>