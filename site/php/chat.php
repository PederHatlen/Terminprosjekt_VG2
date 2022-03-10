<?php
    // Main PHP bulk, it is before the document because redirecting does not work otherwise
    require 'phpRepo.php';
    $con = connect();
    $msgText = '';
    $conversation_id;

    if (isset($_GET["chatid"])){
        $_SESSION["chatid"] = $_GET["chatid"];
    }

    // Stricter security, User has to be logged inn, have the right chat id, and have permits to chat, else they will be ridirected to index
    if (!isLoggedIn($con)) {
        unset($_SESSION["chatid"]);
        header('Location: login.php');
        exit;
    }

    if (isset($_SESSION["chatid"])) {
        $conversation_id = $_SESSION["chatid"];
    }else{
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
        exit;
    }

    $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res == null) {
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
        exit;
    }

    // After security check, all participants are gathered from conversation_users for displaying purposes
    $stmt = $con->prepare('SELECT username FROM conversation_users left join users on conversation_users.user_id = users.user_id where conversation_users.conversation_id = ? and conversation_users.user_id != ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $participants = $stmt->get_result()->fetch_all();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BinærChat | Chat</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/chatStyle.css">

    <!-- Slimmer fontstyle for timestamp -->
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wdth@60;70;80&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php';?>
    <main>
        <div id="chatInfo">
            <div id="leftInfo">
                <div id="menu">
                    <a href="#" id="menuIconButton" onclick="togglesettings();">
                        <svg xmlns="http://www.w3.org/2000/svg" id="menuIcon" width="1.5em" height="1.5em" viewBox="0 0 10 10" style="fill: #ffffff;">
                            <rect y="1.2" width="10" height="1.5"/>
                            <rect y="7.2" width="10" height="1.5"/>
                            <rect y="4.2" width="10" height="1.5"/>
                        </svg>
                    </a>
                    <div id="settingsContent" style="display: none;">
                        <form action="" method="post">
                            <input type="text" id="addPersonName" name="addPersonName" placeholder="Legg til person">
                            <input type="submit" value="legg til">
                        </form>
                    </div>
                </div>
                <h3>Chatter med: <?php 
                // Outputing all conversation participants, intended for future expansion to group chats, also translates user 1 to Torshken (ME)
                for ($i=0; $i < count($participants); $i++) {
                    echo ($participants[$i][0] == "1"? "Torshken":$participants[$i][0]);
                    if ($i != count($participants)-1){echo ", ";}
                }
                ?></h3>
            </div>
            <h4 id="connectionInfo"></h4>
        </div>
        <div id="chatWindow"><?php
            // Displaying all chat-messages with usernames, on later expansions colors might also be added
            $stmt = $con->prepare('SELECT * FROM messages left join users on messages.sender_id = users.user_id where conversation_id = ?');
            $stmt->bind_param('i', $conversation_id);
            $stmt->execute();
            $messages = $stmt->get_result();

            // output every message
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
            }else{
                // In case of no messages
                echo("<p>Her var det ikke noen meldinger, kanskje du kan sende noen?</p>");
            }
        ?></div>
        <!-- Form for making messages, goes to chatpost.php -->
        <form name="message" action="chatpost.php" id="messageForm" method="post">
            <input type="text" class="input" name="usrmsg" id="usrmsg" placeholder="Skriv inn en melding (Husk at den må være binær og under 255 tegn)" autofocus>
            <input type="submit" id="sendBTN" class="input" value="Send">
        </form>
        <?php echo $msgText;?>
    </main>
    <?php include 'footer.php';?>

    <!-- Script is integrated because of data from php that needs to be integrated. -->
    <script>
        let chatWindow = document.getElementById("chatWindow");
        let messageFormEL = document.getElementById("messageForm");
        let messageEL = document.getElementById("usrmsg");
        let connectionInfoEL = document.getElementById("connectionInfo");
        let socket = new WebSocket("ws://" + window.location.host + ":5678?");

        let settingsContentEl = document.getElementById("settingsContent");
        let menuIconBTN = document.getElementById("menuIconButton");

        socket.onopen = function () {
            connectionInfoEL.innerHTML += "Connected!";
            connectionInfoEL.style.backgroundColor = "green";
            
            socket.send(<?php echo('"'.$_SESSION["chatid"].', '.$_SESSION["user_id"].', '.$_SESSION["username"].', '.$_SESSION["logintoken"].'"');?>)
            
            socket.onmessage = function (e) {
                chatWindow.innerHTML += e.data;
                chatWindow.scrollTop = chatWindow.scrollHeight;
            };
            
            messageFormEL.onsubmit = function (e) {
                e.preventDefault();
                send();
            }
        };
        socket.onerror = function (e) {connectionInfo.innerHTML = "Someone forgot to start the websocket server.";}
        socket.onclose = function (e) {
            connectionInfo.innerHTML = "Disconnected :(";
            connectionInfoEL.style.backgroundColor = "red";
        }

        function send(message = messageEL.value) {
            socket.send(message);
            messageEL.value = "";
        }
        function togglesettings(){
            if (settingsContentEl.style.display == "none"){
                settingsContentEl.style.display = "inline-block";
                menuIconBTN.style.backgroundColor = "#111";
            }else{
                settingsContentEl.style.display = "none";
                menuIconBTN.style.backgroundColor = "";
            }
        }
        chatWindow.scrollTop = chatWindow.scrollHeight;
    </script>
    <script src="../js/script.js"></script>
</body>
</html>