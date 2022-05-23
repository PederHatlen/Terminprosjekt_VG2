<!-- This page is only for help, php is only used to get basic page things, and help ticket sending -->
<?php 
	require 'phpRepo.php';
	$message = "";

	if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != "1") {
		echo "<pre>Ingenting å se her.</pre>\n<pre>Se på dette istede: <a href=\"https://youtu.be/dQw4w9WgXcQ\">PHP hacking for n00bs</a>";
		exit;
	}

	$con = connect();

	if (isset($_GET["remove"])) {
		$query = $con->prepare("DELETE FROM help_tickets WHERE ticket_id = ?");
		$query->bind_param("i", $_GET["remove"]);
		$query->execute();

		$con->close();

		refreshNoGET();
	}

	$query = $con->prepare("SELECT * FROM help_tickets JOIN users on help_tickets.user_id = users.user_id");
	$query->execute();
	$tickets = $query->get_result()->fetch_all(MYSQLI_ASSOC);

	$con->close();
?>
<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "| Tickets";
		require 'head.php';
	?>
	<!-- Not linked, because this page is "secret" and i don't want to have a css page available -->
	<style>
		.ticketContainer{
			background: var(--page-main1, #222);
			padding: 0.5rem 0.25rem ;
			margin: 0.5rem 0
		}
		.ticketTitle{
			font-size: 1.5rem;
			margin: 0;
		}
		.ticketEmail, .exit{
			background: none;
			font-size: 1.5rem;
		}
	</style>
</head>
<body>
	<?php include 'header.php';?>
	<main>
		<h2>Tickets</h2>
		<?php
			if (count($tickets) == 0) {echo("<span>No tickets boss, u did guud</span>");}
			else{
				// var_dump($results);
				for ($i=0; $i < count($tickets); $i++) {
					echo("<div class=\"ticketContainer\">
						<span class=\"ticketTitle\">".(isset($tickets[$i]["username"])?$tickets[$i]["username"]:"Ingen navn")."<a class=\"ticketEmail\" href=\"mailto:".$tickets[$i]["email"]."\">".$tickets[$i]["email"]."</a></span><a class=\"exit\" href=\"?remove=".$tickets[$i]["ticket_id"]."\">x</a>
						<p class=\"ticketMessage\">".$tickets[$i]["messagetext"]."</p>
					</div>");
				}
			}
		?>
	</main>
	<?php include 'footer.php';?>
</body>
</html>