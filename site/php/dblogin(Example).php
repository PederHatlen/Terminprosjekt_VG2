<?php
	if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
		echo "<pre>Ingenting å se her.</pre>\n<pre>Se på dette istede: <a href=\"https://youtu.be/dQw4w9WgXcQ\">Addexios Minecraft Eventyr</a>";
		exit;
	}
	if (constant("extServer")){
		define("DB_HOST", "");
		define("DB_USERNAME", "");
		define("DB_PASSWORD", "");
	}else{
		define("DB_HOST", "");
		define("DB_USERNAME", "");
		define("DB_PASSWORD", "");
	}
?>