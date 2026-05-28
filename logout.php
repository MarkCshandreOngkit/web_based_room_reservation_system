<?php
session_start();

// Unset all session global variables
$_SESSION = array();

// Destroy the tracking cookie session instance
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Kill the session state storage on server
session_destroy();

// Safely bounce them back out to the sign-in prompt panel
header("Location: index.php");
exit;
?>