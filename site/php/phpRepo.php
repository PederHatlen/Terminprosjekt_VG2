<?php
	$start = microtime(true);    // PerformanceTracking
	session_start();             // Start tracking of session

	if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
		echo "<pre>Ingenting å se her.</pre>\n<pre>Se på dette istede: <a href=\"https://youtu.be/dQw4w9WgXcQ\">PHP hacking for n00bs</a>";
		exit;
	}

	$themes = ["normal", "unicorn", "norge", "ukraina"];

	if (!isset($isLoginPage)){unset($_SESSION["redirectpage"]);}

	// Global Settings
	define("extServer", false); // Using external server (Parameters can be set in dblogin.php (gitignored))

	define("allowDevMode", false);
	define("allowHexClock", true);

	define("allowTheme", true);

	function refreshNoGET(){
		header("Location: http://".$_SERVER['HTTP_HOST'].parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
		exit;
	}

	if (isset($_GET["devmode"]) and constant("allowDevMode") == true){
		if (isset($_SESSION["devmode"])){unset($_SESSION["devmode"]);}
		else {$_SESSION["devmode"] = "true";}
		refreshNoGET();
	}
	if (isset($_GET["hexclock"]) and constant("allowHexClock") == true){
		if (isset($_SESSION["hexclock"])){unset($_SESSION["hexclock"]);}
		else {$_SESSION["hexclock"] = "true";}
		refreshNoGET();
	}

	if (isset($_GET["theme"]) and constant("allowTheme") == true and in_array($_GET["theme"], $themes)){
		if((isset($_SESSION["theme"]) and $_SESSION["theme"] == $_GET["theme"]) or $_GET["theme"] == "normal"){
			unset($_SESSION["theme"]);
		}else{$_SESSION["theme"] = $_GET["theme"];}
		refreshNoGET();
	}
	
	// Basic connect functions
	function connect(){
		require 'dblogin.php';
		
		// login Details is retrieved from dblogin.php, which is gitignored
		$con = mysqli_connect(constant("DB_HOST"), constant("DB_USERNAME"), constant("DB_PASSWORD"), "binærchatdb");
		// Check connection
		if (!$con) {die("Connection failed: " . mysqli_connect_error());}
	
		//Angi UTF-8 som tegnsett
		$con->set_charset("utf8");
	
		return $con;
	}
	// Getting token from database, used in corelation with validation
	function gettoken($con, $user_id){
		$query = $con->prepare("SELECT * FROM tokens WHERE user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC limit 1");
		$query->bind_param('i', $user_id);
		$query->execute();

		return $query->get_result()->fetch_assoc();
	}
	// Making a "token", actually just a uuid, good enough
	function maketoken($con, $user_id){
		$expires = new DateTime();
		$expires->add(new DateInterval('PT20M')); //https://en.wikipedia.org/wiki/ISO_8601#Durations
		$expires_stamp = $expires->format('Y-m-d H:i');

		$datetime = new DateTime();
		$datetime_stamp = $datetime->format('Y-m-d H:i');

		// Using a SQL-injection proof solution. N/A here, but standardised
		$stmt = $con->prepare('INSERT into tokens (user_id, token, created_at, expires_at) VALUES (?, UUID(), ?, ?)');
		$stmt->bind_param('iss', $user_id, $datetime_stamp, $expires_stamp); // 's' specifies the variable type => 'string'
		$stmt->execute();

		return gettoken($con, $user_id);
	}
	// Validating the token by finding it in the database
	function validatetoken($con, $token, $user_id){
		$stmt = $con->prepare('SELECT * FROM tokens WHERE token = ? AND user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC limit 1');
		$stmt->bind_param('si', $token, $user_id);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();

		// If token is not foun (is null) then return false, else update
		if ($result == null) return FALSE;

		if (strtotime($result["expires_at"]) < strtotime("now +5 minutes")){
			$_SESSION["token"] = maketoken($con, $user_id)["token"];
		}

		return TRUE;
	}
	// Login function used in login and make account, practical to be standardised
	function login($con, $user_id, $user_name){
		// making new token, do not want user to use old token 
		$token = maketoken($con, $user_id);

		// All logins and proof of logins/extra data is stored in Session variables, because data on user's system, is not trusted
		$_SESSION["token"] = $token["token"];
		$_SESSION["username"] = $user_name;
		$_SESSION["user_id"] = $user_id;
	}
	// Verifying that the user is logged in, using validate token, and in event user is invalid, logging user off
	function isLoggedIn($con){
		// Validate logintoken
		if (($_SESSION["token"] ?? null) == null || ($_SESSION["user_id"] ?? null) == null || !validatetoken($con, $_SESSION["token"], $_SESSION["user_id"])){
			// If unvalid, remove login
			unset($_SESSION["username"]);
			unset($_SESSION["token"]);
			unset($_SESSION["user_id"]);
			return FALSE;
		}else return TRUE;
	}

	function checkBinary($str){return (preg_match("/[^10]+/", $str)? false:true);}

	// Get random color (Used for individual colors in chat)
	// Retrieved from the Booking system i made, most likely from here: https://stackoverflow.com/q/61709592#comment109154735_61709592
	function randomColor(){return sprintf('#%06X', mt_rand(0, 0xFFFFFF));}

	// Code for splitting hex into rgb and calculating luminance
	// Retrieved from https://stackoverflow.com/a/67325435 and https://en.wikipedia.org/wiki/Relative_luminance
	function luminance($color) {
		if (strlen($color) < 3) return;
		if ($color[0] == '#') $color = substr($color, 1);
		if(strlen($color) == 3) $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
		list($r, $g, $b) = array_map("hexdec", str_split($color, (strlen($color) / 3)));
		// echo("[$r | $g | $b] ".(0.2126*$r + 0.7152*$g + 0.0722*$b));
		return (0.2126*$r + 0.7152*$g + 0.0722*$b);
	}
?>