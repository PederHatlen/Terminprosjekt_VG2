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
        <h1><a href="../index.html">BinærChat [Temp name]</a></h1>
    </header>
    <div class="content">
        <h2>Lage bruker til binærchat</h2>
        <p>Har du ikke laget en bruker? <a href="lagBruker.php">Lag bruker</a></p>

        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Brukernavn"><br>
            <input type="password" name="password" id="password" placeholder="Passord"><br>
            <input type="submit" value="log in" id="submit"><br>
        </form>
        <?php
            include 'phpRepo.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $pwd = $_POST['password'];

                $token;

                $con = connect();

                $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
               
                $stmt->execute();
               
                $rawdata = $stmt->get_result();
                $result = $rawdata->fetch_array(MYSQLI_BOTH);

                $user_id = $result["id"];

                if (count($result) > 0 && password_verify($pwd, $result['password'])) {
                    $result = gettoken($con, $user_id);

                    $validtoken = FALSE;

                    if (count($result) > 0) {
                        //Bør skrive sql kode som automatisk sletter alle som er expired
                        for ($i=0; $i < count($result); $i++) { 
                            if (new DateTime($result[$i]["expires_at"]) < new DateTime()) {
                                $stmt = $con->prepare('DELETE FROM tokens WHERE token_id = ?');
                                $stmt->bind_param('s', $result[$i]["token_id"]); // 's' specifies the variable type => 'string'
                            
                                $stmt->execute();
                            }else{
                                $token = $result[$i]["token_id"];
                                extendtime($con, $token);
                                $validtoken = TRUE;
                            }
                        }
                    }
                    if (!$validtoken){
                        $time = new DateTime();
                        $time->add(new DateInterval('PT20M'));
                        $stamp = $time->format('Y-m-d H:i');
                        $datetime = new DateTime();
                        $datetime = $datetime->format('Y-m-d H:i');

                        $stmt = $con->prepare('INSERT into tokens (user_id, token, created_at, expires_at) VALUES (?, UUID(), ?, ?)');
                        $stmt->bind_param('iss', $user_id, $datetime, $stamp); // 's' specifies the variable type => 'string'
                    
                        $stmt->execute();
                    }
                    $result = gettoken($con, $user_id)[0];
                    echo "Innloggingen fungerte!<br>
                    <script>
                        localStorage.setItem('LoginToken', '". $result["token"] . "');
                        localStorage.setItem('Username', '" . $user_id . "');
                        window.location.replace('../index.html');
                    </script>";
                }else{
                    echo '<p>Feil brukernavn eller passord.</p>';
                }

                $con->close();
            }
        ?>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>