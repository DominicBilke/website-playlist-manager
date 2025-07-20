<?php
/**
 * Application Initialization Script
 * Sets up all required components and handles errors gracefully
 */

// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Set timezone
date_default_timezone_set("Europe/Berlin");

// Session configuration
ini_set("session.gc_maxlifetime", 31536000);
ini_set("session.cookie_lifetime", 0);
ini_set("session.cookie_httponly", 1);
ini_set("session.cookie_secure", isset($_SERVER['HTTPS']));
ini_set("session.use_strict_mode", 1);
ini_set("session.cookie_samesite", 'Strict');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('SCRIPT_ROOT', __DIR__);
define('ASSETS_ROOT', APP_ROOT . '/assets');
define('COMPONENTS_ROOT', APP_ROOT . '/components');
define('DATABASE_ROOT', APP_ROOT . '/database');

// Include required files
require_once SCRIPT_ROOT . '/database.php';
require_once SCRIPT_ROOT . '/languages.php';
require_once SCRIPT_ROOT . '/language_utils.php';

// Initialize database connection
try {
    $db = Database::getInstance();
    $pdo = $db->getPDO();
    
    // Ensure database and tables exist
    if (!$db->ensureDatabaseExists()) {
        throw new Exception("Failed to ensure database exists");
    }
    
    if (!$db->initializeTables()) {
        throw new Exception("Failed to initialize database tables");
    }
    
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    $pdo = null;
}

// Initialize language manager
try {
    $lang = new LanguageManager();
} catch (Exception $e) {
    error_log("Language manager initialization failed: " . $e->getMessage());
    // Create a fallback language manager
    $lang = new stdClass();
    $lang->get = function($key) { return $key; };
    $lang->getCurrentLanguage = function() { return 'en'; };
}

// Initialize authentication system
try {
    if ($pdo) {
        require_once SCRIPT_ROOT . '/auth.php';
        $auth = new Auth($pdo, $lang);
    } else {
        $auth = null;
    }
} catch (Exception $e) {
    error_log("Authentication system initialization failed: " . $e->getMessage());
    $auth = null;
}

// Initialize platform manager if user is logged in
try {
    if ($pdo && isset($_SESSION['user_id'])) {
        require_once SCRIPT_ROOT . '/PlatformManager.php';
        $platformManager = new PlatformManager($pdo, $lang, $_SESSION['user_id']);
    } else {
        $platformManager = null;
    }
} catch (Exception $e) {
    error_log("Platform manager initialization failed: " . $e->getMessage());
    $platformManager = null;
}

// Set up error handling
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error_message = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($error_message);
    
    // In development, display errors
    if (ini_get('display_errors')) {
        echo "<div style='background: #fee; border: 1px solid #fcc; padding: 10px; margin: 10px; color: #c33;'>";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<strong>File:</strong> $errfile<br>";
        echo "<strong>Line:</strong> $errline";
        echo "</div>";
    }
    
    return true;
}

function handleException($exception) {
    $error_message = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($error_message);
    
    // In development, display exceptions
    if (ini_get('display_errors')) {
        echo "<div style='background: #fee; border: 1px solid #fcc; padding: 10px; margin: 10px; color: #c33;'>";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<strong>Stack Trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    }
}

// Set error handlers
set_error_handler('handleError');
set_exception_handler('handleException');

// Utility functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['session_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin']);
}

function isSuperAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn() || !$pdo) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Clean up old sessions
if ($pdo && rand(1, 100) === 1) { // 1% chance to run cleanup
    try {
        $auth = new Auth($pdo, $lang);
        $auth->cleanExpiredSessions();
    } catch (Exception $e) {
        error_log("Session cleanup failed: " . $e->getMessage());
    }
}

// Set global variables for backward compatibility
$GLOBALS['pdo'] = $pdo;
$GLOBALS['lang'] = $lang;
$GLOBALS['auth'] = $auth;
$GLOBALS['platformManager'] = $platformManager;

// Output any buffered content
ob_end_flush();
?> 