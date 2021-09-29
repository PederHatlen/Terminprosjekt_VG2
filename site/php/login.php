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


                $con = connect();

                $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
               
                $stmt->execute();
               
                $rawdata = $stmt->get_result();
                $result = $rawdata->fetch_array(MYSQLI_BOTH);

                if ($rawdata->num_rows > 0 && password_verify($pwd, $result['password'])) {
                    echo "Riktig passord!<br>";
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