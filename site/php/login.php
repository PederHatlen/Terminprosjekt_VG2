<?php
    include 'phpRepo.php';
    $con = connect();
    $msgText = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $pwd = $_POST['password'];

        $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
       
        $stmt->execute();
         
        $rawdata = $stmt->get_result();
        $userresult = $rawdata->fetch_array(MYSQLI_BOTH);

        if (!is_null($userresult) && password_verify($pwd, $userresult['password'])) {
            $user_id = $userresult["user_id"];

            $result = gettoken($con, $user_id);

            if (!is_null($result["token_id"] ?? null)) {
                $token_id = $result["token_id"];
                extendtime($con, $token_id);
                echo("extend(login)");
            }else{
                maketoken($con, $user_id);
                echo("new(login)");
            }

            $result = gettoken($con, $user_id);

            $_SESSION["logintoken"] = $result["token"];
            $_SESSION["username"] = $userresult["username"];
            $_SESSION["user_id"] = $user_id;

            $msgText = '<p>Innloggingen fungerte!</p>';
            header('Location: ../index.php');
        }else{
            $msgText = '<p>Feil brukernavn eller passord.</p>';
        }
        $con->close();
    }
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
        <?php
            echo usernametext();
        ?>
    </header>
    <div class="content">
        <h2>Logg inn på binærchat</h2>
        <p>Har du ikke laget en bruker? <a href="lagBruker.php">Lag bruker</a></p>

        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Brukernavn"><br>
            <input type="password" name="password" id="password" placeholder="Passord"><br>
            <input type="submit" value="log in" id="submit" class="submitwmargin"><br>
        </form>
        <?php
            echo $msgText;
        ?>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>