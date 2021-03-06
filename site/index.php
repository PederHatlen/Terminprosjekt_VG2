<?php
	require 'php/phpRepo.php'; // PHPRepo is where all basic functions are: Connection, token, login etc.  
	unset($_SESSION["chatid"]); // Remove what chat they where inn (is set in chat.php)
?>

<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "";
		require 'php/head.php';
	?>
</head>
<body>
	<!-- Main nav-bar, or header -->
	<?php include 'php/header.php';?>
	<main>
		<h2>Velkommen til Binærchat!</h2>
		<?php
			// If user is logged in, greet them and give links, else link to sign-in/make account
			if (isset($_SESSION["user_id"])) {
				echo("<p>Du er pålogget som $_SESSION[username] <a href=\"php/logoff.php\">Logg av</a></p>
				<p>For å finne samtalene dine kan du gå her: <a href=\"php/conversations.php\">Samtaler</a></p>");
				if ($_SESSION["username"] == "1") {
					$con = connect();
					$stmt = $con->prepare('SELECT count(ticket_id) FROM help_tickets');
					$stmt->execute();
					$result = $stmt->get_result()->fetch_array();
					echo("<p>Hei sjef, ".($result[0] >= 1?"sjekk support greier, det er ".$result[0]." melding".($result[0] == 1? "":"er")." link:<a href=\"php/tickets.php\">Tickets</a>":"ække no tickets.")."</p>");
					$con->close();
				}
			}
			else {
				echo("<span>Du er ikke pålogget.</span>
				<p>Hvis du ikke har laget en bruker før kan du gjøre det her: </p>
				<a href=\"php/lagBruker.php\">Lag bruker</a>
				<span>Eller</span>
				<a href=\"php/login.php\">Logg in</a>");
			}
		?>
		<!-- Help-page (Also in footer) -->
		<p>Hvis du trenger hjelp eller har spørsmål kan du gå på <a href="php/help.php">Hjelp</a> siden.</p>
	</main>
	<!-- Footer for extra info -->
	<?php include 'php/footer.php';?>
	<script src="js/script.js"></script>
</body>
</html>
