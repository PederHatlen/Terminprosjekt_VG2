<footer>
	<?php
		if(constant("allowTheme") == true){
			echo "<span onclick=\"this.nextSibling.style.display = (this.nextSibling.style.display == ('')? 'flex':'');\">Peder 2022</span>";
			echo "<div id=\"themeDropdown\">
				<h2>Temaer</h2>
				<a href=\"?theme=unicorn\">Unicorn</a>
				<a href=\"?theme=ukraina\">Ukraina</a>
				<a href=\"?theme=norge\">Norge</a>
				<a href=\"?theme=normal\">Normal</a>
			</div>";
		}else{
			echo "<span>Peder 2022</span>";
		}
	?>
	<a href="/BinærChat/php/help.php">Hjelp</a>
</footer>