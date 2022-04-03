<?php
    // Small document for logging user off.
    session_start();
    // like if user is in vallid, session variables are cleared
    unset($_SESSION["username"]);
    unset($_SESSION["logintoken"]);
    unset($_SESSION["user_id"]);
    
    session_destroy();
    header('Location: ../index.php');
?>