<?php
    include 'php/phpRepo.php'; // PHPRepo is where all basic functions are: Connection, token, login etc.  
    unset($_SESSION["chatid"]); // Remove what chat they where inn (is set in chat.php)
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <!-- General html set-up -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Customisations to site look (Title, shortcut-icon and stylesheet) -->
    <title>BinærChat</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Main nav-bar, or header -->
    <header>
        <h1><a href="index.php">BinærChat</a></h1>
        <div class="rightheader">
            <!-- Code to insert username in top right, data is stored in session variables declared in phprepo -->
            <?php echo usernametext();?>
        </div>
    </header>
    <main>
        <h2>Velkommen til Binærchat!</h2>
        <?php
            // If user is logged in, greet them and give links, else link to sign-in/make account
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
        <!-- Help-page (always here) -->
        <p>Hvis du trenger hjelp eller har spørsmål kan du gå på <a href="php/hjelp.php">Hjelp</a> siden.</p>
        </main>
    <!-- Footer for extra info -->
    <footer> 
        <span>Peder 2021</span>
        <a href="php/hjelp.php">Hjelp</a>
    </footer>
</body>
</html>
