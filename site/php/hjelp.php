<!-- This page is only for help, not much php exept login things, and username display -->
<?php include 'phpRepo.php';?>
<!DOCTYPE html>
<html lang="no">
<head>
    <!-- Technical details for browser -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- As sed previously styling and genneral design traits -->
    <title>BinærChat | Hjelp</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">BinærChat</a></h1>
        <?php echo usernametext();?>
    </header>
    <div class="content">
        <!-- Page is split up into parts where one question is answered/topic is discussed -->
        <h2>Hjelp</h2>
        <div class="question">
            <h3>Hva er dette?</h3>
            <p class="answer">Dette er en chattetjeneste som jeg har laget. Den går ut på at du bare kan skrive binært til verandre.</p>
            <p class="answer">Det er ikke ment som en veldig seriøs tjeneste.</p>
        </div>
        <!-- Not much to say here really, links are standard from style.css -->
        <div class="question">
            <h3>Hvordan kan jeg bruke det?</h3>
            <p class="answer">Du må først <a href="lagBruker.php">lage bruker</a> (Husk binært navn/passord, det vil ikke fungere ellers).</p>
            <p class="answer">Derreter kan du gå inn på hjemmesiden og navigere deg til <a href="conversations.php">samtaler</a>. Hvis du ikke har laget noen tidligere kan da lage ny ved å trykke på Ny samtale.</p>
        </div>
        <div class="question">
            <h3>Hvorfor kan jeg ikke skrive meldinger?</h3>
            <p class="answer">Alle meldinger som skrives inn i denne siden vil automatisk fjerne alle tegn som ikke er 1 eller 0.</p>
            <p class="answer">Det er derfor meldingene ikke kommer opp.</p>
            <p class="answer">Hvis du har andre problemer kan du ta kontakt på <a href="mailto:pehaa002@osloskolen.no" target="_blank">pehaa002@osloskolen.no</a>.</p>
        </div>
        <div class="question">
            <h3>Hvordan skriver jeg binært?</h3>
            <!-- YouTube is much better at explaining than i am. -->
            <p class="answer"><a href="https://youtu.be/wCQSIub_g7M">How to read text in binary</a></wbr><b> (Tom Scott, YouTube)</b></p>
            <p class="answer"><a href="https://youtu.be/LpuPe81bc2w">Binary Numbers and Base Systems as Fast as Possible</a></wbr><b> (Techquickie, YouTube)</b></p>
        </div>
    </div>
    <footer>
        <span>Peder 2021</span>
        <!-- Wow, that's a cool guy, wonder what he has done -->
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>