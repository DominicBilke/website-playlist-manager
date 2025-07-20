<?php
/**
 * Logout Script
 * Handles user logout with proper session cleanup
 */

session_start();

// Include required files
require_once '../script/languages.php';
require_once '../script/inc_start.php';
require_once 'auth.php';

// Initialize auth system
$lang = new LanguageManager();
$auth = new Auth($pdo, $lang);

// Logout user
$auth->logout();

// Redirect to login page with success message
header("Location: ../login.php?success=" . urlencode($lang->get('logout_successful')));
exit;
?>