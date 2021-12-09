<?php
    include 'phpRepo.php';
    $con = connect();
    if ($_SERVER["REQUEST_METHOD"] != "POST" or $_POST["usrmsg"] == null) {
        header('Location: ../chat.php');
    }
    $msgtext = $_POST["usrmsg"];

    if (!isLoggedIn($con)) {
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

    if (isset($_SESSION["chatid"])) {
        $conversation_id = $_SESSION["chatid"];
    }else{
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }
    $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc() == null) {
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

    $stmt = $con->prepare('INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $_SESSION["chatid"], $_SESSION["user_id"], $msgtext);
    $stmt->execute();

    header('Location: chat.php');
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | Chat post</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
    </header>
    <div class="content">
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>