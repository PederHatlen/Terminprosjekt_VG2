<?php
    $start = microtime(true);    // PerformanceTracking
    session_start();    // Start tracking of session

    if (isset($_GET["devmode"])){
        if (strtolower($_GET["devmode"]) == "false"){unset($_SESSION["devmode"]);}
        else {
            $_SESSION["devmode"] = "true";
        }
        header("Location: http://".$_SERVER['HTTP_HOST'].parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
    }

    // Code that retrives saved username, and makes the user text in the top right corner on all sites
    function usernametext(){return ('<span id="username_display">' . (isset($_SESSION["username"])? ($_SESSION["username"]):'Ikke pålogget') . '</span>');}
    
    // Basic connect functions
    function connect(){
        require 'dblogin.php';
        
        // login Details is retrieved from dblogin.php, which is gitignored
        $con = mysqli_connect(constant("DB_HOST"), constant("DB_USERNAME"), constant("DB_PASSWORD"), "binærchatdb");
        // Check connection
        if (!$con) {die("Connection failed: " . mysqli_connect_error());}
    
        //Angi UTF-8 som tegnsett
        $con->set_charset("utf8");
    
        return $con;
    }
    // Making a "token", actually just a uuid, good enough
    function maketoken($con, $user_id){
        $expires = new DateTime();
        $expires->add(new DateInterval('PT20M')); //https://en.wikipedia.org/wiki/ISO_8601#Durations
        $expires_stamp = $expires->format('Y-m-d H:i');

        $datetime = new DateTime();
        $datetime_stamp = $datetime->format('Y-m-d H:i');

        // Using a SQL-injection proof solution. N/A here, but standardised
        $stmt = $con->prepare('INSERT into tokens (user_id, token, created_at, expires_at) VALUES (?, UUID(), ?, ?)');
        $stmt->bind_param('iss', $user_id, $datetime_stamp, $expires_stamp); // 's' specifies the variable type => 'string'
        $stmt->execute();
    }
    // Getting token from database, used in corelation with validation
    function gettoken($con, $user_id){
        $query = $con->prepare("SELECT * FROM tokens WHERE user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC limit 1");
        $query->bind_param('i', $user_id);
        $query->execute();

        return $query->get_result()->fetch_assoc();
    }
    // Extending token time, to circumnavigate making new ones
    function extendtime($con, $token_id){
        // Works quite similar to make new
        $time = new DateTime();
        $time->add(new DateInterval('PT20M')); //https://en.wikipedia.org/wiki/ISO_8601#Durations
        $stamp = $time->format('Y-m-d H:i');

        $stmt = $con->prepare('UPDATE tokens SET expires_at = ? WHERE token_id = ?');
        $stmt->bind_param('si', $stamp, $token_id);
                            
        $stmt->execute();
    }
    // Validating the token by finding it in the database
    function validatetoken($con, $token, $user_id){
        $stmt = $con->prepare('SELECT * FROM tokens WHERE token = ? AND user_id = ? AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC');
        $stmt->bind_param('si', $token, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // If token can be found (or output is not null), extend time, else nothing
        if ($result != null) {
            $token_id = $result["token_id"];
            extendtime($con, $token_id);
            return TRUE;
        }else{
            return FALSE;
        }
    }
    // Login function used in login and make account, practical to be standardised
    function login($con, $user_id, $user_name){
        // Finding if user has a existing token/making new
        $token = gettoken($con, $user_id);

        if (!is_null($token["token_id"] ?? null)) {
            $token_id = $token["token_id"];
            extendtime($con, $token_id);
        }else{
            maketoken($con, $user_id);
            $token = gettoken($con, $user_id);
        }

        // All logins and proof of logins/extra data is stored in Session variables, because data on user's system, is not trusted
        $_SESSION["logintoken"] = $token["token"];
        $_SESSION["username"] = $user_name;
        $_SESSION["user_id"] = $user_id;
    }
    // Verifying that the user is logged in, using validate token, and in event user is invalid, logging user off
    function isLoggedIn($con){
        // Validate logintoken
        if (($_SESSION["logintoken"] ?? null) == null || ($_SESSION["user_id"] ?? null) == null || !validatetoken($con, $_SESSION["logintoken"], $_SESSION["user_id"])){
            // If unvalid, remove login
            unset($_SESSION["username"]);
            unset($_SESSION["logintoken"]);
            unset($_SESSION["user_id"]);
            return FALSE;
        }else{
            return TRUE;
        }
    }
?>