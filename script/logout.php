<?php
/*
 * Secure Logout Script
 * Properly destroys session and redirects user
 */

session_start();

// Include language system
require_once '../script/languages.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with success message
$meldung = "meldung=" . urlencode($lang->get('logout_successful'));
header("Location: ../index.php?" . $meldung);
exit;
?>