<?php
    // Small document for logging user off. PHP is before the document because redirecting does not work otherwise

    include 'phpRepo.php';
    // like if user is in vallid, session variables are cleared
    unset($_SESSION["username"]);
    unset($_SESSION["logintoken"]);
    unset($_SESSION["user_id"]);
    header('Location: ../index.php');
?>

<!-- Basic doccument, here because of previouse experiments/testing -->
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | Log-off</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
    </header>
    <main></main>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>