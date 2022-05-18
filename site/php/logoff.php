<?php
	// Small document for logging user off.
	session_start();
	// example; if user is invallid, session variables are cleared
	unset($_SESSION["username"]);
	unset($_SESSION["logintoken"]);
	unset($_SESSION["user_id"]);
	
	session_destroy();
	header('Location: ../index.php');
?>