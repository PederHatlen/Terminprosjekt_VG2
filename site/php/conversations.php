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
            include 'phpRepo.php';
            echo usernametext();
        ?>
    </header>
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
        </table>
    </div>
    <footer>
        <span>Peder 2021</span>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>