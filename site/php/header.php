<header>
	<h1><a href="/BinærChat/index.php">BinærChat</a></h1>
	<span id="clock"></span>
	<div class="rightheader">
		<!-- Code to insert username in top right, data is stored in session variables declared in phprepo -->
		<span id="username_display"><?php echo (isset($_SESSION["username"])? ("Pålogget som: <a href=\"/Binærchat/php/brukerside.php\" class=\"linkButton\">".$_SESSION["username"]."</a>"):'Ikke pålogget');?></span>
	</div>
</header>