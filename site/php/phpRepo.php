<?php
    //Global events
    session_start();

    $con = connect();
    $stmt = $con->prepare('DELETE FROM tokens WHERE expires_at < CURRENT_TIMESTAMP');
    $stmt->execute();

    $stmt = $con->prepare('SELECT user_id from users WHERE username = ?');
    $stmt->bind_param('s', $_SESSION["username"]); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $_SESSION["user_id"] = $result;

    //Validate logintoken
    if (isset($_SESSION["logintoken"]) && isset($_SESSION["username"])){
        if (isset($result["id"]) && !validatetoken($con, $_SESSION["logintoken"], $result["id"])){
            unset($_SESSION["username"]);
            unset($_SESSION["logintoken"]);
        }
    }
    $con->close();


    function usernametext(){
        return ('<span id="username_display">' . (isset($_SESSION["username"])? ($_SESSION["username"]):'Ikke pålogget') . '</span>');
    }


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

        $stmt = $con->prepare('SELECT * FROM tokens WHERE token = ? AND user_id = ? AND expires_at > CURRENT_TIMESTAMP');
        $stmt->bind_param('si', $token, $username);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if ($result != null) {
            $token_id = $result["token_id"];
            extendtime($con, $token_id);
            return TRUE;
        }else{
            return FALSE;
        }
    }
?>