<?php
    // Main PHP bulk, it is before the document because redirecting does not work otherwise
    include 'phpRepo.php';
    $con = connect();
    $msgText = '';

    // If post data (data from the form on the site)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // setting temporary variables
        $username = $_POST['username'];
        $pwd = $_POST['password'];

        // finding if the user exists in DB
        $stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
        $stmt->execute();

        $rawdata = $stmt->get_result();
        $userquery_res = $rawdata->fetch_array(MYSQLI_BOTH);

        // if user exists run login function (from phpRepo), else output error message
        if (!is_null($userquery_res) && password_verify($pwd, $userquery_res['password'])) {
            login($con, $userquery_res["user_id"], $userquery_res["username"]);
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
        <!-- Same for all pages, link to index, and username/login indication -->
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header>
    <main>
        <!-- Page text / explaination -->
        <h2>Logg inn på binærchat</h2>
        <p>Har du ikke laget en bruker? <a href="lagBruker.php">Lag bruker</a></p>

        <!-- Form for inputting login details, text removing/formating done in JS (script.js) -->
        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Brukernavn"><br>
            <input type="password" name="password" id="password" placeholder="Passord"><br>
            <input type="submit" value="log in" id="submit" class="submitwmargin"><br>
        </form>
        <?php
            // Messages for debugging/event info, made inn main PHP code
            echo $msgText;
        ?>
    </main>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>