<?php
    // Main PHP bulk, it is before the document because redirecting does not work otherwise
    require 'phpRepo.php';
    $con = connect();
    $message = "";
    if (!isLoggedIn($con)) {
        header('Location: ../index.php');
        exit;
    }

    // If post data (New conversation)
    if ($_SERVER["REQUEST_METHOD"] == "POST" and $_POST["person"] != null) {
        $conversation_id = null;

        // trying to find input user in DB
        $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $_POST["person"]);
        $stmt->execute();
        $user2_id = $stmt->get_result();

        // If user exists, check if conversation already exists, else update output message with error info
        if (mysqli_num_rows($user2_id) != null){
            $user2_id = $user2_id->fetch_assoc()["user_id"];

            $stmt = $con->prepare('SELECT * FROM conversations left join conversation_users as conv_users1 on conversations.conversation_id = conv_users1.conversation_id left join conversation_users as conv_users2 on conversations.conversation_id = conv_users2.conversation_id where conv_users1.user_id = ? and conv_users2.user_id = ?');
            $stmt->bind_param('ii', $_SESSION["user_id"], $user2_id);
            $stmt->execute();
            $rawdata = $stmt->get_result();

            // If conversation doesn't exist, make new convo
            if ($rawdata->fetch_array(MYSQLI_BOTH) == null){
                // Datetime, made in php because then the timestamp can be saved, and used to find the conversation after creation (temporarily)
                $timestamp = new DateTime();
                $timestamp = $timestamp->format('Y-m-d H:i');
                $stmt = $con->prepare('INSERT INTO conversations (created_by, created_at) VALUES (?, ?)');
                $stmt->bind_param('is', $_SESSION["user_id"], $timestamp);
                $stmt->execute();

                // Retrieving created conversation id
                $conversation_id = $stmt->insert_id;

                // Preparing a statement for binding users to conversation, se Setup.sql for database structure
                $stmt = $con->prepare('INSERT INTO conversation_users (conversation_id, user_id, color, isAdmin) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('iisi', $conversation_id, $insert_UID, $color, $isAdmin);

                // First user input is current user
                $insert_UID = $_SESSION["user_id"];
                $color = randomColor();
                $isAdmin = 1;
                $stmt->execute();
                
                //Swapping user ids to second user
                $insert_UID = $user2_id;
                $color = randomColor();
                $isAdmin = 0;
                $stmt->execute();

                // updating message and setting what chat the user is being sent too, then sending the user to the chat
                $message = "Samtalen ble laget!";
                $_SESSION["chatid"] = $conversation_id;
                header('Location: chat.php');
                exit;
            }else{
                $message = "Samtalen finnes allerede.";
            }
        }else{
            $message = "Finner ikke brukeren.";
        }
    }
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <?php 
        $pageName = "| Conversations";
        require 'head.php';
    ?>
    <link rel="stylesheet" href="/BinærChat/css/conversationsStyle.css">
</head>
<body>
    <?php include 'header.php';?>
    <main>
        <h2>Samtaler</h2>

        <!-- Options for sorting/making new conversations (Not fully implemented yet) -->
        <div id="tableoptions">
            <!-- Search option was never implemented, but is intended to be -->
            <!-- <input type="text" name="search" id="search" placeholder="Søk etter samtale"> -->
            <input type="button" name="shownewsamtale" id="shownewsamtale" value="Ny samtale" onclick="document.querySelector('#addconv').style.display = 'flex'">
        </div>
        <!-- Add new conversation box, hidden by default, but is shown when Show button is pressed (Done in inline JS (I know, not great)) -->
        <div id="addconv">
            <div id="addconv_box">
                <div id="addconvText">
                    <h2>Lag ny samtale</h2>
                    <a class="exit" onclick="document.querySelector('#addconv').style.display = ''" href="#">x</a>
                </div>
                <!-- Form for making new conversation, goes to this document -->
                <form action="" method="post">
                    <input type="text" class="input" name="person" id="person" onkeyup="" placeholder="Brukernavn">
                    <input type="submit" name="submit" value="Lag samtale">
                </form>
            </div>
        </div>
        <?php echo($message);?>

        <!-- Main table for showing all the conversations the user is in -->
        <table id="conversationtable">
            <tbody>
                <?php
                    // The second bulk of PHP on this page, this is for retrieving the right information for the table

                    // Finding all the conversation the user is inn
                    $stmt = $con->prepare('SELECT conversation_users.conversation_id, lastSent, users.username FROM conversation_users 
                    left join (
                        SELECT conversation_id, MAX(sent_at) AS lastSent 
                        FROM messages 
                        GROUP BY conversation_id
                    ) lastMSG on conversation_users.conversation_id = lastMSG.conversation_id 
                    join conversation_users otherUsers on conversation_users.conversation_id = otherUsers.conversation_id and not conversation_users.user_id = otherUsers.user_id
                    join users on otherUsers.user_id = users.user_id
                    WHERE conversation_users.user_id = ?
                    ORDER BY lastSent desc;');
                    
                    $stmt->bind_param('i', $_SESSION["user_id"]);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_all(MYSQLI_NUM);
                    // If there is anny, go throught them, else make table row with info
                    if (count($result) != 0){
                        echo("<tr id='tableHeader'><th>Personer</th><th>Sist aktiv</th></tr>");

                        $conversation_users = [];
                        $j = 0;
                        for ($i=0; $i < count($result); $i++) {
                            if (!isset($conversation_users[$result[$i][0]])){
                                $conversation_users[$result[$i][0]] = $j;
                                $conversations[$j][0] = $result[$i][0];
                                $conversations[$j][1] = $result[$i][1];
                                $conversations[$j][2] = ($result[$i][2] == "1"? "Torshken":$result[$i][2]);
                                $j++;
                            }else{
                                $conversations[$conversation_users[$result[$i][0]]][2] .= ", ".($result[$i][2] == "1"? "Torshken":$result[$i][2]);
                            }
                        }

                        for ($i=0; $i < count($conversations); $i++) { 
                            $conversation_id = $conversations[$i][0];
                            $sent_at = $conversations[$i][1];
                            $usernames = $conversations[$i][2];

                            // If there is anny messages format the time properly
                            if ($sent_at != null){
                                $date = date_create($sent_at);
                                $fdate = null;
                                if (strtotime($sent_at) < strtotime('-1 day')) {
                                    $fdate = date_format($date, 'jS M y');
                                }else{
                                    $fdate = date_format($date, 'H:i:s');
                                }
                            }else{
                                $fdate = "Ingen aktivitet";
                            }

                            echo("<tr><td><a class='chatlink' href='chat.php?chatid=". $conversation_id ."'>".$usernames."</a></td><td>". $fdate ."</td></tr>");
                        }
                    }else{echo("<tr id='tableHeader'><th>Du har ingen samtaler.</th><tr>");}
                ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php';?>
    <script src="../js/script.js"></script>
</body>
</html>