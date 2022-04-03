<?php
	// Document for putting chat messages into the database
	require 'phpRepo.php';
	$con = connect();
	$msgtext = "";

	// Includes the same security as the chat page: Loginrequirement, Chat access and chatid, + post data for this page
	// Chat id is sett, and user is logged in
	if (isset($_SESSION["chatid"]) and isLoggedIn($con)) {
		$conversation_id = $_SESSION["chatid"];
		// Post requirement
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$msgtext = preg_replace("/[^10]+/", "", $_POST["usrmsg"]);

			if (strlen($msgtext) != 0) {
				// User has permits to use the chat
				$stmt = $con->prepare('SELECT * FROM conv_users WHERE conversation_id = ? and user_id = ?');
				$stmt->bind_param('ii', $conversation_id, $_SESSION["user_id"]);
				$stmt->execute();
				if ($stmt->get_result()->fetch_assoc() != null) {

					// inserting the data into Database, if the message was long enough
					$stmt = $con->prepare('INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (?, ?, ?)');
					$stmt->bind_param('iis', $_SESSION["chatid"], $_SESSION["user_id"], $msgtext);
					$stmt->execute();

					header('Location: chat.php');
				}else{header('Location: ../index.php');}
			}else{header('Location: chat.php');}
		}else{header('Location: chat.php');}
	}else{header('Location: ../index.php');}
	$con->close();
?>