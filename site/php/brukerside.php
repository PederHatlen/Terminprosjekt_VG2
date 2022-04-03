<?php
	require 'phpRepo.php'; // PHPRepo is where all basic functions are: Connection, token, login etc.  
	$msgText = '';
	
	$con = connect();

	if (!isLoggedIn($con)) {
		$_SESSION["redirectpage"] = "brukerside.php";
		header('Location: login.php');
		exit;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["request"])) {
		switch ($_POST["request"]) {
			case 'remove':
				// Preparing a statement for binding users to conversation, se Setup.sql for database structure
				$stmt = $con->prepare('UPDATE conv_users SET isAdmin = 1 WHERE conversation_id = ? and user_id = ?');
				$stmt->bind_param('ii', $conversation_id, $insert_UID);

				$keys = array_keys($_POST);
				for ($i=0; $i < count($_POST)-1; $i++) { 
					$tempVars = explode(",", $_POST[$keys[$i]]);

					$conversation_id = $tempVars[0];
					$insert_UID = $tempVars[1];
					$stmt->execute();
				}

				$stmt = $con->prepare('DELETE FROM users WHERE users.user_id = ?;');
				$stmt->bind_param('i', $_SESSION["user_id"]);
				$stmt->execute();

				header('Location: logoff.php');

				break;
			case '':
				break;
		}
	}

	// $stmt = $con->prepare('SELECT * FROM conv_users where user_id = ? and isAdmin = 1');
	$stmt = $con->prepare('SELECT conv_users.conversation_id, users.user_id,users.username FROM conv_users 
	join conv_users otherUsers on conv_users.conversation_id = otherUsers.conversation_id and not conv_users.user_id = otherUsers.user_id
	join users on otherUsers.user_id = users.user_id
	WHERE conv_users.user_id = ? and conv_users.isAdmin = 1;');
	$stmt->bind_param('i', $_SESSION["user_id"]);
	$stmt->execute();
	$result = $stmt->get_result()->fetch_all();

	$index = [];
	$conversations = [];
	$j = 0;
	for ($i=0; $i < count($result); $i++) {
		if (!isset($index[$result[$i][0]])){
			$index[$result[$i][0]] = $j;
			$conversations[$j][0] = $result[$i][0];
			$conversations[$j][1][] = $result[$i][1];
			$conversations[$j][2][] = ($result[$i][2] == "1"? "Torshken":$result[$i][2]);
			$j++;
		}else{
			$conversations[$index[$result[$i][0]]][2][] = ($result[$i][2] == "1"? "Torshken":$result[$i][2]);
		}
	}
	$con->close();
?>

<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "";
		require 'head.php';
	?>
</head>
<body>
	<!-- Main nav-bar, or header -->
	<?php include 'header.php';?>
	<main>
		<h2>Instillinger for <?php echo $_SESSION["username"];?></h2>
		<div id="settings">
			<button onclick="removeAccountEl.style.display = 'flex'" class="deleteBtn">Slett bruker</button>
		</div>
		<div class="fullPopup" id="removeAccount">
			<div>
				<h2>Er du sikker på at du vil slette brukeren din?</h2>
				<p>Dette er ikke reversibelt, og vil fjerne <u><b>ALT</b></u> du har skrevet og gjort i binærchat.</p>
				<form action="" method="post" id="delUsrForm">
					<?php
						if (count($conversations)!= 0) {
							echo "<hr><p>Før du sletter brukeren din må du sette en administrator i chattene dine.</p>
							<table id=\"useradmintable\"><tr id='tableHeader'><th>Personer&nbsp;</th><th>Velg admin</th></tr>";
							
							for ($i=0; $i < count($conversations); $i++) {
								var_dump($conversations[$i]);
								echo "<tr><td><label for='convAdminSelect'>[".implode(', ', $conversations[$i][2])."]</label></td>
								<td><select name='convAdminSelect".$conversations[$i][0]."' form='delUsrForm'>";
								for ($j=0; $j < count($conversations[$i][1]); $j++) {echo "<option value='".$conversations[$i][0].",".$conversations[$i][1][$j]."'>".$conversations[$i][2][$j]."</option>";}
								echo "</select></td></tr>";
							}
							echo "</table><br>";
						}
					?>
					<input type="hidden" name="request" value="remove">
					<input type="submit" class="deleteBtn" value="Jeg vil slette brukeren min">
				</form>
				<button onclick="removeAccountEl.style.display = '';">Nei, ta meg tilbake</button>
			</div>
		</div>
	</main>
	<!-- Footer for extra info -->
	<script src="../js/script.js"></script>
	<?php include 'footer.php';?>
	<script>let removeAccountEl = document.getElementById("removeAccount");</script>
</body>
</html>
