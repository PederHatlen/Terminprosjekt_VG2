<?php
    include 'phpRepo.php';
    $con = connect();
    $message = "";
    if (!isLoggedIn($con)) {header('Location: ../index.php');}

    if ($_SERVER["REQUEST_METHOD"] == "POST" and $_POST["person"] != null) {
        $conversation_id = null;

        $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $_POST["person"]);
        $stmt->execute();
        $user2_id = $stmt->get_result()->fetch_assoc()["user_id"];

        $stmt = $con->prepare('SELECT * FROM conversations left join conversation_users as conv_users1 on conversations.conversation_id = conv_users1.conversation_id left join conversation_users as conv_users2 on conversations.conversation_id = conv_users2.conversation_id where conv_users1.user_id = ? and conv_users2.user_id = ?');
        $stmt->bind_param('ii', $_SESSION["user_id"], $user2_id);
        $stmt->execute();
        $rawdata = $stmt->get_result();

        if ($rawdata->fetch_array(MYSQLI_BOTH) == null){
            $timestamp = new DateTime();
            $timestamp = $timestamp->format('Y-m-d H:i');
            $stmt = $con->prepare('INSERT INTO conversations (created_by, created_at) VALUES (?, ?)');
            $stmt->bind_param('is', $_SESSION["user_id"], $timestamp);
            $stmt->execute();

            $stmt = $con->prepare('SELECT * FROM conversations WHERE created_by = ? and created_at = ?');
            $stmt->bind_param('is', $_SESSION["user_id"], $timestamp);
            $stmt->execute();
            $conversation_id = $stmt->get_result()->fetch_assoc()["conversation_id"];

            $stmt = $con->prepare('INSERT INTO conversation_users (conversation_id, user_id) VALUES (?, ?)');
            $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
            $stmt->execute();

            $stmt = $con->prepare('INSERT INTO conversation_users (conversation_id, user_id) VALUES (?, ?)');
            $stmt->bind_param('ii', $conversation_id, $user2_id);
            $stmt->execute();
            $message = "Samtalen ble laget!";
        }else{
            $message = "Samtalen finnes allerede";
        }
    }
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | Samtaler</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header>
    <div class="content">
        <h2>Samtaler</h2>
        <div id="tableoptions">
            <input type="text" name="search" id="search" placeholder="Søk etter samtale">
            <input type="button" name="shownewsamtale" id="shownewsamtale" value="Ny samtale" onclick="document.querySelector('#addconv').style.display = 'flex'">
        </div>
        <div id="addconv">
            <div>
                <div style="display: flex; justify-content: space-between; padding: 0;">
                    <h2>Lag ny samtale</h2>
                    <a id="exit" onclick="document.querySelector('#addconv').style.display = ''" href="#">x</a>
                </div>
                <form action="" method="post">
                    <input type="text" name="person" id="person" onkeyup="" placeholder="Brukernavn">
                    <input type="submit" name="submit" value="Lag samtale">
                </form>
            </div>
        </div>
        <?php echo($message);?>

        <table id="conversationtable">
            <tbody>
                <?php
                    $stmt = $con->prepare('SELECT * FROM conversation_users where user_id = ?');
                    $stmt->bind_param('i', $_SESSION["user_id"]);
                    $stmt->execute();
                    $conversations = $stmt->get_result(); //->fetch_array(MYSQLI_BOTH);

                    if ($conversations != null){
                        echo("<tr><td>Person</td><td>Sist aktiv</td></tr>");
                        // echo(var_dump($conversations));
                        while ($row = $conversations->fetch_row()) {
                            $stmt = $con->prepare('SELECT * FROM conversation_users left join users on conversation_users.user_id = users.user_id where conversation_users.conversation_id = ? and conversation_users.user_id != ?');
                            $stmt->bind_param('ii', $row[0], $_SESSION["user_id"]);
                            $stmt->execute();
                            $rawdata = $stmt->get_result()->fetch_array(MYSQLI_BOTH);
                            echo("<tr><td><a class='chatlink' href='chat.php?chatid=". $row[0] ."'>".$rawdata["username"]."</a></td><td></td></tr>");
                        }
                    }else{echo("<tr><td>Du har ingen samtaler.</td><tr>");}
                ?>
            </tbody>
        </table>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>