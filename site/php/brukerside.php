<?php
    require 'phpRepo.php'; // PHPRepo is where all basic functions are: Connection, token, login etc.  
    
    $con = connect();

    if (!isLoggedIn($con)) {
        $_SESSION["redirectpage"] = "brukerside.php";
        header('Location: login.php');
        exit;
    }

    $con->close();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <?php 
        $pageName = "";
        require 'head.php';
    ?>
</head>
<body>
    <!-- Main nav-bar, or header -->
    <?php include 'header.php';?>
    <main>
        <h2>Brukerside for <?php echo $_SESSION["username"];?></h2>
        <div id="settings">
            <h3>Instillinger</h3>
            <form action="" method="post">
                <button onclick="">Slett bruker</button>
            </form>
        </div class="fullPopup">
            <div>
                <h2>Er du sikker på at du vil slette brukeren din?</h2>
            </div>
        <div>
    </main>
    <!-- Footer for extra info -->
    <script src="../js/script.js"></script>
    <?php include 'footer.php';?>
</body>
</html>
