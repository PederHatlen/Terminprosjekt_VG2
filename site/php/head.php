<?php
	if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
		header("location: /BinærChat/index.php");
		exit;
	}
?>

<!-- General html set-up -->
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Customisations to site look (Title, shortcut-icon and stylesheet) -->
<title>BinærChat <?php echo $pageName;?></title>
<link rel="icon" type="image/png" href="/BinærChat/img/favicon.png">
<link rel="stylesheet" href="/BinærChat/css/style.css">
<?php
	$unicorn = false;
	if (isset($_SESSION["unicorn"]) and constant("allowUnicornMode") == true){
		$unicorn = true;
		echo "<link rel=\"stylesheet\" href=\"/BinærChat/css/themes/unicorn.css\">";
	}else{
		echo "<link rel=\"stylesheet\" href=\"/BinærChat/css/themes/normal.css\">";
	}
?>
<?php if(isset($_SESSION["hexclock"])){echo "<script>window.onload = ()=>{toggleHexClock();}</script>";}?>