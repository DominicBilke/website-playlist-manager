<?php
/**
 * Centralized Include System
 * Manages all application dependencies and prevents conflicts
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Track what's already been included to prevent duplicates
if (!isset($GLOBALS['_INCLUDED_FILES'])) {
    $GLOBALS['_INCLUDED_FILES'] = [];
}

/**
 * Safe include function that prevents duplicates and handles errors
 */
function safe_include($file, $type = 'require_once') {
    $fullPath = APP_ROOT . '/' . $file;
    
    // Check if already included
    if (in_array($fullPath, $GLOBALS['_INCLUDED_FILES'])) {
        return true;
    }
    
    // Check if file exists
    if (!file_exists($fullPath)) {
        error_log("Include file not found: $fullPath");
        return false;
    }
    
    try {
        switch ($type) {
            case 'require':
                require $fullPath;
                break;
            case 'require_once':
                require_once $fullPath;
                break;
            case 'include':
                include $fullPath;
                break;
            case 'include_once':
                include_once $fullPath;
                break;
            default:
                require_once $fullPath;
        }
        
        $GLOBALS['_INCLUDED_FILES'][] = $fullPath;
        return true;
    } catch (Exception $e) {
        error_log("Error including file $fullPath: " . $e->getMessage());
        return false;
    }
}

/**
 * Initialize core application
 */
function init_app() {
    // Set error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
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
    
    // Include core files
    safe_include('script/inc_start.php');
    safe_include('script/languages.php');
    
    // Initialize language manager if not already done
    if (!isset($GLOBALS['lang'])) {
        $GLOBALS['lang'] = new LanguageManager();
    }
    
    return $GLOBALS['lang'];
}

/**
 * Initialize authentication system
 */
function init_auth() {
    if (!isset($GLOBALS['auth'])) {
        safe_include('script/auth.php');
        $GLOBALS['auth'] = new Auth($GLOBALS['pdo'], $GLOBALS['lang']);
    }
    return $GLOBALS['auth'];
}

/**
 * Initialize platform manager
 */
function init_platform_manager($user_id = null) {
    if (!isset($GLOBALS['platform_manager'])) {
        safe_include('script/PlatformManager.php');
        $user_id = $user_id ?: ($_SESSION['user_id'] ?? null);
        if ($user_id) {
            $GLOBALS['platform_manager'] = new PlatformManager($GLOBALS['pdo'], $GLOBALS['lang'], $user_id);
        }
    }
    return $GLOBALS['platform_manager'] ?? null;
}

/**
 * Include language utilities
 */
function init_language_utils() {
    safe_include('script/language_utils.php');
}

/**
 * Get current user info safely
 */
function get_current_user_info() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'login' => $_SESSION['login'] ?? 'User',
        'team' => $_SESSION['team'] ?? 'N/A',
        'role' => $_SESSION['role'] ?? 'user',
        'email' => $_SESSION['email'] ?? null
    ];
}

/**
 * Check if user is authenticated
 */
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin']);
}

/**
 * Require authentication (redirect if not authenticated)
 */
function require_auth() {
    if (!is_authenticated()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require admin access (redirect if not admin)
 */
function require_admin() {
    require_auth();
    if (!is_admin()) {
        header('Location: account.php');
        exit;
    }
}

/**
 * Include component safely
 */
function include_component($component, $data = []) {
    $componentPath = APP_ROOT . '/components/' . $component . '.php';
    
    if (!file_exists($componentPath)) {
        error_log("Component not found: $componentPath");
        return false;
    }
    
    // Extract data to variables for use in component
    if (!empty($data)) {
        extract($data);
    }
    
    include $componentPath;
    return true;
}

/**
 * Get database connection
 */
function get_database() {
    if (!isset($GLOBALS['pdo'])) {
        safe_include('script/database.php');
        try {
            $db = Database::getInstance();
            $GLOBALS['pdo'] = $db->getPDO();
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            $GLOBALS['pdo'] = null;
        }
    }
    return $GLOBALS['pdo'];
}

/**
 * Debug function to show what's been included
 */
function debug_includes() {
    if (!isset($GLOBALS['_INCLUDED_FILES'])) {
        return [];
    }
    return $GLOBALS['_INCLUDED_FILES'];
}

/**
 * Clean up includes (for testing)
 */
function reset_includes() {
    $GLOBALS['_INCLUDED_FILES'] = [];
    $GLOBALS['lang'] = null;
    $GLOBALS['auth'] = null;
    $GLOBALS['platform_manager'] = null;
    $GLOBALS['pdo'] = null;
}
?> 