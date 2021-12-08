<?php
    //Global events
    session_start();

    function usernametext(){return ('<span id="username_display">' . (isset($_SESSION["username"])? ($_SESSION["username"]):'Ikke pålogget') . '</span>');}
    function connect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "binærchatdb";
    
        // Create connection
        $con = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$con) {die("Connection failed: " . mysqli_connect_error());}
    
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
    function gettoken($con, $user_id){
        $query = $con->prepare("SELECT * FROM tokens WHERE user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC limit 1");
        $query->bind_param('i', $user_id);
        $query->execute();

        return $query->get_result()->fetch_assoc();
    }
    function extendtime($con, $token_id){
        $time = new DateTime();
        $time->add(new DateInterval('PT20M'));
        $stamp = $time->format('Y-m-d H:i');

        $stmt = $con->prepare('UPDATE tokens SET expires_at = ? WHERE token_id = ?');
        $stmt->bind_param('si', $stamp, $token_id);
                            
        $stmt->execute();
    }
    function validatetoken($con, $token, $user_id){
        $stmt = $con->prepare('SELECT * FROM tokens WHERE token = ? AND user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC');
        $stmt->bind_param('si', $token, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!is_null($result)) {
            $token_id = $result["token_id"];
            extendtime($con, $token_id);
            return TRUE;
        }else{
            return FALSE;
        }
    }
    function login($con, $user_id, $user_name){
        $token = gettoken($con, $user_id);

        if (!is_null($token["token_id"] ?? null)) {
            $token_id = $token["token_id"];
            extendtime($con, $token_id);
        }else{maketoken($con, $user_id);}

        $token = gettoken($con, $user_id);

        $_SESSION["logintoken"] = $token["token"];
        $_SESSION["username"] = $user_name;
        $_SESSION["user_id"] = $user_id;
    }
    function isLoggedIn($con){
        //Validate logintoken
        if (($_SESSION["logintoken"] ?? null) == null || ($_SESSION["user_id"] ?? null) == null || !validatetoken($con, $_SESSION["logintoken"], $_SESSION["user_id"])){
            unset($_SESSION["username"]);
            unset($_SESSION["logintoken"]);
            unset($_SESSION["user_id"]);
            return FALSE;
        }else{
            return TRUE;
        }
    }
?>