<?php
	// Main PHP bulk, it is before the document because redirecting does not work otherwise
	require 'phpRepo.php';
	$con = connect();
	$msgText = '';
	$conversation_id;

	if (isset($_GET["chatid"])){
		$_SESSION["chatid"] = $_GET["chatid"];
		header("Location: http://".$_SERVER['HTTP_HOST'].parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
		exit;
	}

	// Stricter security, User has to be logged inn, have the right chat id, and have permits to chat, else they will be ridirected to index
	if (!isLoggedIn($con)) {
		unset($_SESSION["chatid"]);
		$_SESSION["redirectpage"] = "chat.php";
		header('Location: login.php');
		exit;
	}

	if (isset($_SESSION["chatid"])) {
		$conversation_id = $_SESSION["chatid"];
	}else{
		unset($_SESSION["chatid"]);
		header('Location: ../index.php');
		exit;
	}
	$stmt = $con->prepare('SELECT * FROM conversations WHERE conversation_id = ?');
	$stmt->bind_param('i', $conversation_id);
	$stmt->execute();
	$conversationInfo = $stmt->get_result()->fetch_assoc();
	if ($conversationInfo == null){
		unset($_SESSION["chatid"]);
		header('Location: ../index.php');
		exit;
	}

	$stmt = $con->prepare('SELECT * FROM conv_users WHERE conversation_id = ? and user_id = ?');
	$stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
	$stmt->execute();
	$conversation_user = $stmt->get_result()->fetch_assoc();
	if ($conversation_user == null){
		unset($_SESSION["chatid"]);
		header('Location: ../index.php');
		exit;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["form"])){
			if ($_POST["form"] == "changeColor"){
				if (isset($_POST["color"]) and preg_match('/^#([0-9A-F]{3}){1,2}$/i', $_POST["color"])){
					$color = $_POST["color"];
					if (luminance($color) > 50){
						$Fcolor = "";
						if(strlen($color) == 3) $Fcolor = "#".$color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
						$stmt = $con->prepare('UPDATE conv_users SET color = ? WHERE conversation_id = ? and user_id = ?');
						$stmt->bind_param('sii', $color, $conversation_id, $_SESSION["user_id"]);
						$stmt->execute();
	
						$msgText = "Fargen din ble oppdatert!";
					}else{$msgText = "Den fargen er for mørk.";}
				}else{$msgText = "Det er ikke et støttet format.";}
			}else if ($conversation_user["isAdmin"]){
				switch ($_POST["form"]){
					case 'addPerson':
						if (isset($_POST["addPersonName"])){
	
							$stmt = $con->prepare('SELECT user_id FROM users WHERE username = ?');
							$stmt->bind_param('s', $_POST["addPersonName"]);
							$stmt->execute();
							$user_id = $stmt->get_result()->fetch_assoc();
							
							if ($user_id != null){
								$stmt = $con->prepare('SELECT * FROM conv_users WHERE conversation_id = ? and user_id = ?');
								$stmt->bind_param('ii', $conversation_id, $user_id["user_id"]);
								$stmt->execute();
								$res = $stmt->get_result()->fetch_assoc();

								if ($res == null){
									$stmt = $con->prepare('INSERT INTO conv_users (conversation_id, user_id) VALUES (?, ?)');
									$stmt->bind_param('ii', $conversation_id, $user_id["user_id"]);
									$stmt->execute();

									if ($conversationInfo["isGroupChat"] == 0) {
										$stmt = $con->prepare('UPDATE conversations SET isGroupChat = 1 WHERE conversation_id = ?');
										$stmt->bind_param('i', $conversation_id);
										$stmt->execute();
									}
									$msgText = "Brukeren ble lagt til.";
								}else{$msgText = "Brukeren er allerede i chaten.";}
							}else{$msgText = "Brukeren finnes ikke.";}
						}else{$msgText = "Det ble ikke sendt med nokk data";}
						break;
					case 'removePerson':
						if (isset($_POST["removePersonID"])){
							$stmt = $con->prepare('SELECT * FROM conv_users WHERE conversation_id = ? and user_id = ?');
							$stmt->bind_param('ii', $conversation_id, $_POST["removePersonID"]);
							$stmt->execute();
							$res = $stmt->get_result()->fetch_assoc();
							
							if ($res != null){
								$stmt = $con->prepare('DELETE FROM conv_users WHERE conversation_id = ? and user_id = ?');
								$stmt->bind_param('ii', $conversation_id, $_POST["removePersonID"]);
								$stmt->execute();

								$msgText = "Personen ble fjernet.";
							}else{$msgText = "Det er ikke en person i chatten.";}
						}else{$msgText = "Det ble ikke sendt med nokk data";}
						break;
				}
			}
		}
	}

	// After security check, all participants are gathered from conv_users for displaying
	$stmt = $con->prepare('SELECT username, conv_users.user_id FROM conv_users join users on conv_users.user_id = users.user_id where conv_users.conversation_id = ? and conv_users.user_id != ?');
	$stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
	$stmt->execute();
	$participants = $stmt->get_result()->fetch_all();

	$wsToken = bin2hex(random_bytes(16));
	$time = date('Y-m-d H:i:s', strtotime("now +15 seconds"));

	$stmt = $con->prepare('INSERT INTO ws_tokens (conversation_id, user_id, token, expires_at) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('iiss', $_SESSION["chatid"], $_SESSION["user_id"], $wsToken, $time);
	$stmt->execute();
?>

<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "| chat";
		require 'head.php';
	?>
	<link rel="stylesheet" href="/BinærChat/css/chatStyle.css">
	<!-- Slimmer fontstyle for timestamp -->
	<link href="https://fonts.googleapis.com/css2?family=Inconsolata:wdth@60;70;80&display=swap" rel="stylesheet">
</head>
<body>
	<?php include 'header.php';?>
	<main>
		<div id="chatInfo">
			<div id="leftInfo">
				<div id="menu">
					<a href="#" id="menuIconButton" onclick="togglesettings();">
						<svg xmlns="http://www.w3.org/2000/svg" id="menuIcon" width="1.5em" height="1.5em" viewBox="0 0 10 10" style="fill: #ffffff;">
							<rect y="1.2" width="10" height="1.5"/>
							<rect y="7.2" width="10" height="1.5"/>
							<rect y="4.2" width="10" height="1.5"/>
						</svg>
					</a>
					<div id="settingsContent" style="display: none;">
						<h3>Endre egen farge</h3>
						<form action="" method="post" class="horisontalForm">
							<input type="hidden" name="form" value="changeColor">
							<input type="text" name="color" id="changeColor" pattern="^#([0-9A-Fa-f]{3}){1,2}$" placeholder="Hex Color">
							<input type="submit" value="Endre">
						</form>
						
						<?php 
							if ($conversation_user["isAdmin"]){
								echo "<hr><br>
								<h3>Admin instillinger</h3>
								<h3>Legg til person</h3>
								<form action=\"\" method=\"post\" class=\"horisontalForm\">
									<input type=\"hidden\" name=\"form\" value=\"addPerson\">
									<input type=\"text\" id=\"addPersonName\" name=\"addPersonName\" placeholder=\"Legg til person\">
									<input type=\"submit\" value=\"legg til\">
								</form>";

								echo "<br>
								<h3>Fjern person</h3>
								<form action=\"\" method=\"post\" class=\"horisontalForm\" name=\"removePersonForm\" id=\"removePersonForm\">
									<input type=\"hidden\" name=\"form\" value=\"removePerson\">
									<select name=\"removePersonID\" id=\"removePersonID\" form=\"removePersonForm\">";
										// Outputing all conversation participants, for removing people from conversation
										echo "<option value=\"".$_SESSION["user_id"]."\">".($_SESSION["username"] == "1"? "Torshken":$_SESSION["username"])."</option>";
										for ($i=0; $i < count($participants); $i++) {
											echo "<option value=\"".$participants[$i][1]."\">".($participants[$i][0] == "1"? "Torshken":$participants[$i][0])."</option>";
										}
									echo "</select>
									<input type=\"submit\" class=\"remove\" value=\"Fjern\">
								</form>";
							}
						?>
					</div>
				</div>
				<h3>Chatter med: <?php 
				// Outputing all conversation participants, intended for future expansion to group chats, also translates user 1 to Torshken (ME)
				for ($i=0; $i < count($participants); $i++) {
					echo ($participants[$i][0] == "1"? "Torshken":$participants[$i][0]);
					if ($i != count($participants)-1){echo ", ";}
				}
				?></h3>
			</div>
			<?php echo $msgText;?>
			<h4 id="connectionInfo" style="display: none;"></h4>
		</div>
		<div id="chatWindow"><?php
			// Displaying all chat-messages with usernames, on later expansions colors might also be added
			$stmt = $con->prepare('SELECT users.username, conv_users.color, messages.messagetext, messages.sent_at FROM messages join users on users.user_id = messages.sender_id join conv_users on conv_users.user_id = messages.sender_id and conv_users.conversation_id = messages.conversation_id where messages.conversation_id = ? order by messages.sent_at asc');
			$stmt->bind_param('i', $conversation_id);
			$stmt->execute();
			$messages = $stmt->get_result()->fetch_all();

			// output every message
			if (count($messages) != 0){
				for ($i=0; $i < count($messages); $i++) { 
					$date = date_create($messages[$i][3]);
					if (strtotime($messages[$i][3]) < strtotime('-1 day')) {$fdate = date_format($date, 'jS M y');}
					else{$fdate = date_format($date, 'H:i:s');}
					
					$class = "class=\"info";
					// $class .= (luminance($messages[$i][1]) <= 100? " dark":"");
					
					$class .= "\"";
					$msgColor = "";
					$msgColor = (!isset($_SESSION["theme"])? "style=\"color: ". $messages[$i][1] .";\"":"");

					echo("<p><span $class $msgColor><span class='time'>[" . $fdate . "]</span> " . ($messages[$i][0]==1? "Torshken":$messages[$i][0]) . ":</span> " . $messages[$i][2] . "</p>");
				}
			}else{
				// In case no messages
				echo("<p>Her var det ikke noen meldinger, kanskje du kan sende noen?</p>");
			}
		?></div>
		<!-- Form for making messages, goes to chatpost.php if not overriden by JS -->
		<form name="message" action="chatpost.php" id="messageForm" method="post">
			<input type="submit" id="sendBTN" class="input" value="Send">
			<input type="text" class="input" name="usrmsg" id="usrmsg" placeholder="Binær melding" autofocus>
		</form>
	</main>
	<?php include 'footer.php';?>

	<!-- Script is integrated because of data from php that needs to be integrated. -->
	<script>
		const initData = {wsToken:"<?php echo $wsToken;?>"};
		const theme = <?php echo (isset($_SESSION["theme"])? "true":"false");?>;
	</script>
	<script src="../js/chatScript.js"></script>
	<script src="../js/script.js"></script>
</body>
</html>