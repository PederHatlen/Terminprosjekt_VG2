<?php
    // Main PHP bulk, it is before the document because redirecting does not work otherwise
    require 'phpRepo.php';
    $con = connect();
    $msgText = '';
    $conversation_id;

    if (isset($_GET["chatid"])){
        $_SESSION["chatid"] = $_GET["chatid"];
        header("Location: http://".$_SERVER['HTTP_HOST'].parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
        exit;
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
    $conversation_user = $stmt->get_result()->fetch_assoc();
    if ($conversation_user == null){
        unset($_SESSION["chatid"]);
        header('Location: ../index.php');
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["form"])){
            if ($_POST["form"] == "changeColor"){
                if (isset($_POST["color"]) and preg_match('/^#([0-9A-F]{3}){1,2}$/i', $_POST["color"])){
                    $stmt = $con->prepare('UPDATE conversation_users SET color = ? WHERE conversation_id = ? and user_id = ? ');
                    $stmt->bind_param('sii', $_POST["color"], $conversation_id, $_SESSION["user_id"]);
                    $stmt->execute();

                    $msgText = "Fargen din ble oppdatert.";
                }else{$msgText = "Den fargen er ikke støttet.";}
            }else if ($conversation_user["isAdmin"]){
                switch ($_POST["form"]){
                    case 'addPerson':
                        if (isset($_POST["addPersonName"])){
    
                            $stmt = $con->prepare('SELECT user_id FROM users WHERE username = ?');
                            $stmt->bind_param('s', $_POST["addPersonName"]);
                            $stmt->execute();
                            $user_id = $stmt->get_result()->fetch_assoc();
                            
                            if ($user_id != null){
                                $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
                                $stmt->bind_param('ii', $conversation_id, $user_id["removePersonID"]);
                                $stmt->execute();
                                $res = $stmt->get_result()->fetch_assoc();

                                if ($res == null){
                                    $stmt = $con->prepare('INSERT INTO conversation_users (conversation_id, user_id) VALUES (?, ?)');
                                    $stmt->bind_param('ii', $conversation_id, $user_id["user_id"]);
                                    $stmt->execute();

                                    $msgText = "Brukeren ble lagt til.";
                                }else{$msgText = "Brukeren er allerede i chaten.";}
                            }else{$msgText = "Brukeren finnes ikke.";}
                        }else{$msgText = "Det ble ikke sendt med nokk data";}
                        break;
                    case 'removePerson':
                        if (isset($_POST["removePersonID"])){
                            $stmt = $con->prepare('SELECT * FROM conversation_users WHERE conversation_id = ? and user_id = ?');
                            $stmt->bind_param('ii', $conversation_id, $_POST["removePersonID"]);
                            $stmt->execute();
                            $res = $stmt->get_result()->fetch_assoc();
                            
                            if ($res != null){
                                $stmt = $con->prepare('DELETE FROM conversation_users WHERE conversation_id = ? and user_id = ?');
                                $stmt->bind_param('ii', $conversation_id, $_POST["removePersonID"]);
                                $stmt->execute();

                                $msgText = "Personen ble fjernet.";
                            }else{$msgText = "Det er ikke en person i chatten.";}
                        }else{$msgText = "Det ble ikke sendt med nokk data";}
                        break;
                }
            }
        }
    }

    // After security check, all participants are gathered from conversation_users for displaying purposes
    $stmt = $con->prepare('SELECT username, conversation_users.user_id FROM conversation_users join users on conversation_users.user_id = users.user_id where conversation_users.conversation_id = ? and conversation_users.user_id != ?');
    $stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
    $stmt->execute();
    $participants = $stmt->get_result()->fetch_all();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <?php 
        $pageName = "| chat";
        require 'head.php';
    ?>
    <link rel="stylesheet" href="/BinærChat/css/chatStyle.css">
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
                        <h3>Endre egen farge</h3>
                        <form action="" method="post" class="horisontalForm">
                            <input type="hidden" name="form" value="changeColor">
                            <input type="text" name="color" id="changeColor" pattern="^#([0-9A-Fa-f]{3}){1,2}$" placeholder="Hex Color">
                            <input type="submit" value="Endre">
                        </form>
                        
                        <?php 
                            if ($conversation_user["isAdmin"]){
                                echo "<hr><br>
                                <h3>Admin instillinger</h3>
                                <h3>Legg til person</h3>
                                <form action=\"\" method=\"post\" class=\"horisontalForm\">
                                    <input type=\"hidden\" name=\"form\" value=\"addPerson\">
                                    <input type=\"text\" id=\"addPersonName\" name=\"addPersonName\" placeholder=\"Legg til person\">
                                    <input type=\"submit\" value=\"legg til\">
                                </form>";

                                echo "<br>
                                <h3>Fjern person</h3>
                                <form action=\"\" method=\"post\" class=\"horisontalForm\" name=\"removePersonForm\" id=\"removePersonForm\">
                                    <input type=\"hidden\" name=\"form\" value=\"removePerson\">
                                    <select name=\"removePersonID\" id=\"removePersonID\" form=\"removePersonForm\">";
                                        // Outputing all conversation participants, for removing people from conversation
                                        echo "<option value=\"".$_SESSION["user_id"]."\">".($_SESSION["username"] == "1"? "Torshken":$_SESSION["username"])."</option>";
                                        for ($i=0; $i < count($participants); $i++) {
                                            echo "<option value=\"".$participants[$i][1]."\">".($participants[$i][0] == "1"? "Torshken":$participants[$i][0])."</option>";
                                        }
                                    echo "</select>
                                    <input type=\"submit\" class=\"remove\" value=\"Fjern\">
                                </form>";
                            }
                        ?>
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
            <?php echo $msgText;?>
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
                menuIconBTN.style.background = "var(--page-main2)";
            }else{
                settingsContentEl.style.display = "none";
                menuIconBTN.style.background = "";
            }
        }
        chatWindow.scrollTop = chatWindow.scrollHeight;
    </script>
    <script src="../js/script.js"></script>
</body>
</html>