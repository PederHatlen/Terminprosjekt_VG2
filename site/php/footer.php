<footer>
    <?php
        if(isset($_SESSION["devmode"])){
            echo "<span onclick=\"window.location.replace(window.location.href + '?devmode=false')\">Peder 2022</span>";
            echo "Excecution time: ".(microtime(true) - $start);
        }else{
            echo "<span onclick=\"window.location.replace(window.location.href + '?devmode')\">Peder 2022</span>";
        }
    ?>
    <a href="/BinærChat/php/help.php">Hjelp</a>
</footer>