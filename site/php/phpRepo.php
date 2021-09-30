<?php
    //Global events
    session_start();

    $con = connect();
    $stmt = $con->prepare('DELETE FROM tokens WHERE expires_at < CURRENT_TIMESTAMP');
    $stmt->execute();
    $con->close();

    $usernametext = ('<span id="username_display">' . (isset($_SESSION["username"])? 'Logget in som: ' . $_SESSION["username"]:'Ikke pålogget') . '</span>');


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
    function maketoken($con, $user_id){
        $expires = new DateTime();
        $expires->add(new DateInterval('PT20M'));
        $expires_stamp = $expires->format('Y-m-d H:i');
        $datetime = new DateTime();
        $datetime_stamp = $datetime->format('Y-m-d H:i');

        $stmt = $con->prepare('INSERT into tokens (user_id, token, created_at, expires_at) VALUES (?, UUID(), ?, ?)');
        $stmt->bind_param('iss', $user_id, $datetime_stamp, $expires_stamp); // 's' specifies the variable type => 'string'
    
        $stmt->execute();
    }

    function gettoken($con, int $user_id){
        $stmt = $con->prepare('DELETE FROM tokens WHERE expires_at < CURRENT_TIMESTAMP');
        $stmt->execute();

        $query = $con->prepare("SELECT * FROM tokens WHERE user_id = ?");
        $query->bind_param('i', $user_id);
                
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

    function validatetoken($con, $token, $username){
        $stmt = $con->prepare('DELETE FROM tokens WHERE expires_at < CURRENT_TIMESTAMP');
        $stmt->execute();

        $stmt = $con->prepare('SELECT * FROM tokens WHERE token = ? AND username = ? AND expires_at > CURRENT_TIMESTAMP');
        $stmt->bind_param('si', $token, $username);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(MYSQLI_BOTH);

        if (count($result) > 0) {
            $token = $result[$i]["token_id"];
            extendtime($con, $token);
            return TRUE;
        }else{
            return FALSE;
        }
    }
?>