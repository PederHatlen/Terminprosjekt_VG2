<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | LagBruker</title>
    <link rel="icon" type="../image/png" href="img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><a href="../index.html">BinærChat [Temp name]</a></h1>
    </header>
    <div class="content">
        <h2>Lage bruker til binærchat</h2>
        <p>Har du allerede en bruker? <a href="php/login.php">Logg inn</a></p>

        <h3>Bare lov med binære brukernavn!</h3>
        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Brukernavn"><br>
            <input type="password" name="password" id="password" placeholder="Passord"><br>
            <input type="password" name="passwordControll" id="passwordControll" placeholder="Gjenta passord"><br>
            <input type="submit" value="Lag bruker" id="submit"><br>
        </form>

        <?php
            include 'phpRepo.php';

            // Hente data fra post dataen og legge til stemmen, hvis det var post data.
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $parti = $_POST['partivalg'];
                addVote($parti);
            }
        ?>

    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/signupscript.js"></script>
</body>
</html>