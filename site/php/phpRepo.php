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

    function gettoken($con, int $user_id){

        $query = $con->prepare("SELECT * FROM tokens WHERE user_id = ?");
        $query->bind_param('i', $user_id); // 's' specifies the variable type => 'string'
                
        $query->execute();

        return $query->get_result()->fetch_all(MYSQLI_BOTH);
    }

    function extendtime($con, $token_id){
        $time = new DateTime();
        $time->add(new DateInterval('PT20M'));
        $stamp = $time->format('Y-m-d H:i');

        $stmt = $con->prepare('UPDATE tokens SET expires_at = ? WHERE token_id = ?');
        $stmt->bind_param('si', $stamp, $token_id);
                            
        $stmt->execute();
    }
?>