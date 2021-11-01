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
        <h1><a href="index.html">BinærChat [Temp name]</a></h1>
        <div class="rightheader">
            <?php
            include 'php/phpRepo.php';
            echo $usernametext;
            ?>
            <a class="" href="">
                <svg class="menuicon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                    <rect x="25" y="50" width="250" height="50"/>
                    <rect x="25" y="125" width="250" height="50"/>
                    <rect x="25" y="200" width="250" height="50"/>
                </svg>
            </a>
        </div>
        

    </header>
    <div class="content">
        <h2>Velkommen til Binærchat!</h2>
        <p>Hvis du ikke har laget en bruker før kan du gjøre det her: </p>
        <a href="php/lagBruker.php">Lag bruker</a>
        <span>Eller</span>
        <a href="php/login.php">Logg in</a>
    </div>
    <footer> 
        <span>Peder 2021</span>
    </footer>
</body>
</html>
