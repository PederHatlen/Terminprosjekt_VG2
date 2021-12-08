<?php
    include 'phpRepo.php';
    $con = connect();
    $msgText = '';
    $conversation_id;

    if (!isLoggedIn($con)) {header('Location: ../index.php');}

    if (isset($_GET["chatid"])) {
        $conversation_id =  intval($_GET["chatid"]);
    }else{header('Location: ../index.php');}

    $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res == null) {header('Location: ../index.php');}

    $stmt = $con->prepare('SELECT * FROM conversation_users left join users on conversation_users.user_id = users.user_id where conversation_users.conversation_id = ? and conversation_users.user_id != ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $participants = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | Login</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/chatStyle.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header>
    <div class="content">
        <div id="chatInfo"><h3>Chatter med: <?php 
            while ($row = $participants->fetch_row()) {
                echo $row[3];
            }
        ?></h3></div>
        <div id="chatWindow"></br></div>
        <form name="message" action="" id="messageForm">
            <input type="text" name="usrmsg" id="usrmsg" placeholder="Skriv inn en melding (Husk at den må være binær)">
            <input type="submit" value="Send">
        </form>
        <?php echo $msgText;?>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/chatscript.js"></script>
</body>
</html>