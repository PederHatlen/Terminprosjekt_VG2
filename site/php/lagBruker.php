<?php
	// Main PHP bulk, it is before the document because redirecting does not work otherwise
	require 'phpRepo.php';
	$message = "";

	// if post data, retrieve it and make variables
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = $_POST['username'];
		$pwd = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password, PHP includes salt. Password_default hashing because it knows best

		$con = connect();

		// Finding if user allready exists
		$stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
		$stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		// If user allready exists/output from query is not null
		if (mysqli_num_rows($result) != null) {
			$message = "Brukernavnet er allerede tatt :(";
		}else{
			if (checkBinary($username)){
				// Making a new user, W. username and hashed password, server SQL code does rest
				$stmt = $con->prepare('INSERT into users (username, password) VALUES (?, ?)');
				$stmt->bind_param('ss', $username, $pwd); // 's' specifies the variable type => 'string'
				$stmt->execute();

				// Retrieving user id from newly created user via return value (mysql built in)
				$user_id = $stmt->insert_id;

				// Login function found in phprepo
				login($con, $user_id, $username);

				$message = 'Brukeren er registrert, og inlogget.';
				// If succesfull, redirect to index
				header('Location: ../index.php');
				exit;
			}else{$message = "Navnet er ikke binært.";}
		}

		$con->close();
	}
?>


<!DOCTYPE html>
<html lang="no">
<head>
	<?php 
		$pageName = "| Lag bruker";
		require 'head.php';
	?>
</head>
<body>
	<?php include 'header.php';?>
	<main>
		<!-- Page info and explaination -->
		<h2>Lage bruker til binærchat</h2>
		<p>Har du allerede en bruker? <a href="login.php">Logg inn</a></p>

		<h3>Bare lov med binære brukernavn!</h3>

		<!-- Form for inputting userdate (u.name & pwd), password has too be typed twice, done with JS -->
		<form action="" method="post" class="verticalForm">
			<input type="number" class="input" name="username" id="username" placeholder="Brukernavn" autocomplete="new-username" required autofocus>
			<input type="password" class="input" name="password" id="password" placeholder="Passord" autocomplete="new-password" required>
			<input type="password" class="input" name="passwordControll" id="passwordControll" placeholder="Gjenta passord" autocomplete="repeat-password" required>
			<input type="submit" value="Lag bruker" id="submit" class="submitwmargin defaultDisabled" disabled>
		</form>

		<!-- Output info message -->
		<?php echo("<span>$message</span>");?>

	</main>
	<?php include 'footer.php';?>
	<!-- Extra script, becouse page needs extra functionality -->
	<script src="../js/script.js"></script>
	<script src="../js/lagbrukerscript.js"></script>
</body>
</html>