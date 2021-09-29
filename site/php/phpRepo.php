<?php
    function connect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "binærchatdb";
    
        // Create connection
        $con = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        //Angi UTF-8 som tegnsett
        $con->set_charset("utf8");
    
        return $con;
    }
?>