<?php
    include 'phpRepo.php';
    $message;
    // Hente data fra post dataen og legge til stemmen, hvis det var post data.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $pwd = password_hash($_POST['password'], PASSWORD_DEFAULT);


        $con = connect();

        $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if (mysqli_num_rows($result) > 0) {
            $message = "Brukernavnet er allerede tatt :(";
            
        }else{
            $stmt = $con->prepare('INSERT into users (username, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $pwd); // 's' specifies the variable type => 'string'
        
            $stmt->execute();


            $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
        
            $stmt->execute();
            
            $rawdata = $stmt->get_result();
            $userresult = $rawdata->fetch_array(MYSQLI_BOTH);

            maketoken($con, $userresult["id"]);

            $result = gettoken($con, $userresult["id"])[0];

            $_SESSION["logintoken"] = $result["token"];
            $_SESSION["username"] = $userresult["username"];

            $message = '<p>Brukeren er registrert, og inlogget.</p>';

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

    <title>BinærChat | LagBruker</title>
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
        <h2>Lage bruker til binærchat</h2>
        <p>Har du allerede en bruker? <a href="login.php">Logg inn</a></p>

        <h3>Bare lov med binære brukernavn!</h3>
        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Brukernavn"><br>
            <input type="password" name="password" id="password" placeholder="Passord"><br>
            <input type="password" name="passwordControll" id="passwordControll" placeholder="Gjenta passord"><br>
            <input type="submit" value="Lag bruker" id="submit"><br>
        </form>

        <?php

        ?>

    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
    <script src="../js/lagbrukerscript.js"></script>
</body>
</html>