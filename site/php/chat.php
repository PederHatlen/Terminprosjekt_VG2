<?php
    include 'phpRepo.php';
    $con = connect();
    $msgText = '';
    $conversation_id;

    if (!isLoggedIn($con)) {header('Location: ../index.php');}

    if (isset($_SESSION["chatid"])) {
        $conversation_id = $_SESSION["chatid"];
    }else if (isset($_GET["chatid"])) {
        $_SESSION["chatid"] = $_GET["chatid"];
        $conversation_id = $_SESSION["chatid"];
    }else{
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

    $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res == null) {
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
    }

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

    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wdth@60;70;80&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header>
    <div class="content">
        <div id="chatInfo"><h3>Chatter med: <?php 
            while ($row = $participants->fetch_row()) {
                echo ($row[3] == "1"? "Torshken":$row[3]);
            }
        ?></h3></div>
        <div id="chatWindow"><?php
            $stmt = $con->prepare('SELECT * FROM messages left join users on messages.sender_id = users.user_id where conversation_id = ?');
            $stmt->bind_param('i', $conversation_id);
            $stmt->execute();
            $messages = $stmt->get_result();

            if (mysqli_num_rows($messages) != 0){
                while ($row = $messages->fetch_row()) {
                    $date = date_create($row[4]);
                    if ( strtotime($row[4]) < strtotime('-1 day')) {
                        $fdate = date_format($date, 'jS M y');
                    }else{
                        $fdate = date_format($date, 'H:i:s');
                    }
                    
                    echo("<p><span class='time'>[" . $fdate . "]</span> " . ($row[6]==1? "Torshken":$row[6]) . ": " . $row[3] . "");
                }
            }else{echo("<p>Her var det ikke noen meldinger, kanskje du kan fikse det?</p>");}
        ?></div>
        <form name="message" action="chatpost.php" id="messageForm" method="post">
            <input type="text" name="usrmsg" id="usrmsg" placeholder="Skriv inn en melding (Husk at den må være binær)">
            <input type="submit" value="Send">
        </form>
        <?php echo $msgText;?>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
    <script src="../js/chatscript.js"></script>
</body>
</html>