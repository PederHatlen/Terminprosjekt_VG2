<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">BinærChat</a></h1>
        <div class="rightheader">
            <?php
            include 'php/phpRepo.php';
            unset($_SESSION["chatid"]);
            echo usernametext();
            ?>
        </div>
        

    </header>
    <div class="content">
        <h2>Velkommen til Binærchat!</h2>
        <?php
            if (isset($_SESSION["username"])) {
                echo('<span>Du er pålogget som ' . $_SESSION["username"] . '</span>
                <a href="php/logoff.php">Logg av</a>
                <p>For å finne samtalene dine kan du gå her: <a href="php/conversations.php">Samtaler</a></p>');
            }
            else {
                echo('<span>Du er ikke pålogget.</span>
                <p>Hvis du ikke har laget en bruker før kan du gjøre det her: </p>
                <a href="php/lagBruker.php">Lag bruker</a>
                <span>Eller</span>
                <a href="php/login.php">Logg in</a>');
            }
        ?>
        <p>Hvis du trenger hjelp eller har spørsmål kan du gå på <a href="php/hjelp.php">Hjelp</a> siden.</p>
    </div>
    <footer> 
        <span>Peder 2021</span>
        <a href="php/hjelp.php">Hjelp</a>
    </footer>
</body>
</html>
