<?php
    require 'php/phpRepo.php'; // PHPRepo is where all basic functions are: Connection, token, login etc.  
    unset($_SESSION["chatid"]); // Remove what chat they where inn (is set in chat.php)
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <?php 
        $pageName = "";
        require 'php/head.php';
    ?>
</head>
<body>
    <!-- Main nav-bar, or header -->
    <?php include 'php/header.php';?>
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
        <p>Hvis du trenger hjelp eller har spørsmål kan du gå på <a href="php/help.php">Hjelp</a> siden.</p>
    </main>
    <!-- Footer for extra info -->
    <script src="js/script.js"></script>
    <?php include 'php/footer.php';?>
</body>
</html>
