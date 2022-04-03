<!-- This page is only for help, php is only used to get basic page things, and help ticket sending -->
<?php 
	require 'phpRepo.php';
	$message = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["ticketQuestion"]) && isset($_POST["email"])){
			if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
				$con = connect();
				if (isset($_SESSION["user_id"])){
					$stmt = $con->prepare('INSERT into help_tickets (messagetext, email, user_id) VALUES (?, ?, ?)');
					$stmt->bind_param('ssi', $_POST["ticketQuestion"], $_POST["email"], $_SESSION["user_id"]); // 's' specifies the variable type => 'string'
					$stmt->execute();
				}else{
					$stmt = $con->prepare('INSERT into help_tickets (messagetext, email) VALUES (?, ?)');
					$stmt->bind_param('ss', $_POST["ticketQuestion"], $_POST["email"]); // 's' specifies the variable type => 'string'
					$stmt->execute();
				}
				$con->close();
				$message = "Spørsmålet ble sendt inn.";
			}else{
				$message = "Det er ikke en gyldig epost.";
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "| Hjelp";
		require 'head.php';
	?>
</head>
<body>
	<?php include 'header.php';?>
	<main id="helpMain">
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
		<hr>
		<div class="question">
			<h3>Jeg har andre spørsmål</h3>
			<p class="answer">Da kan du ta kontakt ved enten å sende mail til <a href="mailto:pehaa002@osloskolen.no" target="_blank">pehaa002@osloskolen.no</a>.</p>
			<p class="answer">Eller send inn et spørsmål her.</p>
			<form action="" id="ticketForm" method="post" class="verticalForm">
				<input type="email" name="email" id="ticketEmail" class="input" placeholder="epost*" required>
				<textarea name="ticketQuestion" id="ticketQuestion" class="input" placeholder="Spørsmål* (255 tegn)" form="ticketForm" maxlength="255" required></textarea>
				<input type="submit" value="Send" id="submitBTN" class="submitwmargin defaultDisabled" disabled>
			</form>
			<?php echo "<p>".$message."</p>"; ?>
			<span>Ved å sende dette spørsmålet vil dataen du sender bli lagret, og vil bli kunne sett av en hjelper.</span><br>
			<span>Hvis du er logget in vil også id-en din ble sendt med.</span>
		</div>
	</main>
	<?php include 'footer.php';?>
	<script>
		let ticketQuestionEl = document.getElementById("ticketQuestion");
		let ticketEmailEl = document.getElementById("ticketEmail");
		let submitBTNEl = document.getElementById("submitBTN");

		Array.from(document.getElementsByClassName("input")).forEach(element => {
			element.oninput = function(e) {
				console.log(ticketQuestionEl.value);
				emailverified = String(ticketEmailEl.value).toLowerCase().match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
				ticketEmailEl.style.backgroundColor = (emailverified? "":"red");
				if ((ticketQuestionEl.value.length > 0) && (emailverified != null)){
					submitBTNEl.disabled = false;
				}else{
					submitBTNEl.disabled = true;
				}
			};
		});
	</script>
</body>
</html>