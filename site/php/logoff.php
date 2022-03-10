<?php
    // Small document for logging user off. PHP is before the document because redirecting does not work otherwise

    require 'phpRepo.php';
    // like if user is in vallid, session variables are cleared
    unset($_SESSION["username"]);
    unset($_SESSION["logintoken"]);
    unset($_SESSION["user_id"]);
    header('Location: ../index.php');
?>