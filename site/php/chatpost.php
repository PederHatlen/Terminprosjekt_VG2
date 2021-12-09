<?php
    // Document for putting chat messages into the database
    include 'phpRepo.php';
    $con = connect();
    $msgtext = "";

    // Includes the same security as the chat page: Loginrequirement, Chat access and chatid, + post data for this page
    // Post requirement
    if ($_SERVER["REQUEST_METHOD"] != "POST" or $_POST["usrmsg"] == null) {
        header('Location: ../chat.php');
    }else{
        $msgtext = $_POST["usrmsg"];
    }

    // Chat id is sett, and user is logged in
    if (isset($_SESSION["chatid"]) and isLoggedIn($con)) {
        $conversation_id = $_SESSION["chatid"];
    }else{
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

    // User has permits to use the chat
    $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();

    if ($stmt->get_result()->fetch_assoc() == null) {
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

    // inserting the data into Database
    $stmt = $con->prepare('INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $_SESSION["chatid"], $_SESSION["user_id"], $msgtext);
    $stmt->execute();

    //Redirect back to chat
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