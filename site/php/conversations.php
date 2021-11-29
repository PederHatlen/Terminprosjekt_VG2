<?php
    include 'phpRepo.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
    }

    $stmt = $con->prepare('SELECT * FROM conversations left join conversations_users on conversations.conversation_id = conversations_users.conversation_id where users.username = ?');
    $stmt->bind_param('s', $_SESSION["username"]); // 's' specifies the variable type => 'string'
    
    $stmt->execute();
        
    $rawdata = $stmt->get_result();
    $conversations = $rawdata->fetch_array(MYSQLI_BOTH);
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
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header><
    <div class="content">
        <h2>Samtaler</h2>
        <div id="tableoptions">
            <input type="text" name="searchterm" id="searchterm" onkeyup="" placeholder="Søk etter person">
            <form>
                <input type="text" name="person" id="person" onkeyup="" placeholder="Brukernavn">
                <input type="submit" name="submit" value="Lag samtale">
            </form>
        </div>

        <table id="conversationtable">
            <tr>
                <td>Hello</td>
                <td>hi</td>
            </tr>
            <?php
                for ($i=0; $i < count($conversations); $i++) { 
                    echo($conversations[1]);
                }
            ?>
        </table>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>