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
            $temp_UID = $_SESSION["user_id"];
            $stmt->bind_param('ii', $conversation_id, $temp_UID);
            $stmt->execute();

            $temp_UID = $user2_id;
            $stmt->execute();

            $message = "Samtalen ble laget!";
            $_SESSION["chatid"] = $conversation_id;
            header('Location: chat.php');
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
    <link rel="stylesheet" href="../css/conversationsStyle.css">
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
            <div id="addconv_box">
                <div id="addconvText">
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
                    if (mysqli_num_rows($conversations) != 0){
                        echo("<tr><td>Person</td><td>Sist aktiv</td></tr>");
                        // echo(var_dump($conversations));
                        while ($row = $conversations->fetch_row()) {
                            $conversation_id = $row[0];

                            $stmt = $con->prepare('SELECT * FROM conversation_users left join users on conversation_users.user_id = users.user_id where conversation_users.conversation_id = ? and conversation_users.user_id != ?');
                            $stmt->bind_param('ii', $row[0], $_SESSION["user_id"]);
                            $stmt->execute();
                            $username = $stmt->get_result()->fetch_array(MYSQLI_BOTH)["username"];

                            // echo $row[0];
                            $stmt = $con->prepare('SELECT sent_at FROM messages where conversation_id = ? order by sent_at desc limit 1');
                            $stmt->bind_param('i', $conversation_id);
                            $stmt->execute();
                            $sent_at = $stmt->get_result()->fetch_assoc()["sent_at"];
                            $date = date_create($sent_at);
                            $fdate = null;
                            if (strtotime($sent_at) < strtotime('-1 day')) {
                                $fdate = date_format($date, 'jS M y');
                            }else{
                                $fdate = date_format($date, 'H:i:s');
                            }

                            echo("<tr><td><a class='chatlink' href='chat.php?chatid=". $conversation_id ."'>".($username == "1"? "Torshken":$username)."</a></td><td>". $fdate ."</td></tr>");
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