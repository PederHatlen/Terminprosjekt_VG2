<?php
	if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
		echo "<pre>Ingenting å se her.</pre>\n<pre>Se å dette istede: <a href=\"https://youtu.be/dQw4w9WgXcQ\">PHP hacking for n00bs</a>";
		exit;
	}
	if (constant("extServer")){
		define("DB_HOST", "");
		define("DB_NAME", "");
		define("DB_USERNAME", "");
		define("DB_PASSWORD", "");
	}else{
		define("DB_HOST", "");
		define("DB_NAME", "");
		define("DB_USERNAME", "");
		define("DB_PASSWORD", "");
	}
?>