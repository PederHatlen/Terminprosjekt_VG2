<?php
    // Document for putting chat messages into the database
    include 'phpRepo.php';
    $con = connect();
    $msgtext = "";

    // Includes the same security as the chat page: Loginrequirement, Chat access and chatid, + post data for this page
    // Post requirement
    if ($_SERVER["REQUEST_METHOD"] != "POST" or ctype_space($_POST["usrmsg"])) {
        header('Location: ../chat.php');
    }else{$msgtext = preg_replace("/[^10 ]+/", "", $_POST["usrmsg"]);}

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