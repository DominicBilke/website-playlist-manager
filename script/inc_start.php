<?php
/**
 * Application Initialization
 * Handles session management, database connection, and utility functions
 */

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

// Include database configuration
require_once __DIR__ . '/database.php';

// Initialize database connection
try {
    $db = Database::getInstance();
    $pdo = $db->getPDO();
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    // Continue without database for basic functionality
    $pdo = null;
}

/**
 * Check if current time matches user's playing schedule
 */
function matching_time($stamp_from, $stamp_to, $time_from, $time_to, $days) {
    $stamp_from_d = date('w', $stamp_from);
    $stamp_from_h = date('H', $stamp_from);
    $stamp_from_m = date('i', $stamp_from);
    $stamp_to_d = date('w', $stamp_to);
    $stamp_to_h = date('H', $stamp_to);
    $stamp_to_m = date('i', $stamp_to);
    
    $arr_from = explode(':', $time_from);
    $from_h = $arr_from[0];
    $from_m = $arr_from[1];
    $arr_to = explode(':', $time_to);
    $to_h = $arr_to[0];
    $to_m = $arr_to[1];

    // Within playing time
    if (strpos($days, $stamp_to_d) !== FALSE && strpos($days, $stamp_from_d) !== FALSE && 
        ($stamp_from_h >= $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && 
        ($stamp_to_h <= $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
        return TRUE;
    }

    // Start in playing time
    if (strpos($days, $stamp_from_d) !== FALSE && 
        ($stamp_from_h >= $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && 
        ($stamp_from_h <= $to_h || ($stamp_from_h <= $to_h && $stamp_from_m <= $to_m))) {
        return TRUE;
    }

    // End in playing time
    if (strpos($days, $stamp_to_d) !== FALSE && 
        ($stamp_to_h >= $from_h || ($stamp_to_h >= $from_h && $stamp_to_m >= $from_m)) && 
        ($stamp_to_h <= $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
        return TRUE;
    }

    // Start before playing time, end after playing time
    if (($stamp_from_h < $from_h || ($stamp_from_h < $from_h && $stamp_from_m < $from_m)) && 
        ($stamp_to_h > $to_h || ($stamp_to_h > $to_h && $stamp_to_m > $to_m))) {
        return TRUE;
    }

    return FALSE;
}

/**
 * Convert array to string with keys
 */
function array_implode_with_keys($array) {
    $return = '';
    if (count($array) > 0) {
        foreach ($array as $key => $value) {
            $return .= $key . '||||' . $value . '----';
        }
        $return = substr($return, 0, strlen($return) - 4);
    }
    return $return;
}

/**
 * Convert string back to array with keys
 */
function array_explode_with_keys($string) {
    $return = array();
    if (empty($string)) {
        return $return;
    }
    
    $pieces = explode('----', $string);
    foreach ($pieces as $piece) {
        $keyval = explode('||||', $piece);
        if (count($keyval) > 1) {
            $return[$keyval[0]] = $keyval[1];
        } else {
            $return[$keyval[0]] = '';
        }
    }
    return $return;
}

/**
 * Build URL for language switching
 */
function buildLanguageUrl($language) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $path = parse_url($currentUrl, PHP_URL_PATH);
    $query = parse_url($currentUrl, PHP_URL_QUERY);
    
    // Remove language prefix if present
    $path = preg_replace('/^\/de_/', '/', $path);
    $path = preg_replace('/^\/en_/', '/', $path);
    
    // Add language prefix
    if ($language === 'de') {
        $path = '/de_' . ltrim($path, '/');
    }
    
    $url = $path;
    if ($query) {
        $url .= '?' . $query;
    }
    
    return $url;
}

// URL handling for language switching
$url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
$url .= $_SERVER['HTTP_HOST'];
$url .= $_SERVER['REQUEST_URI'];

if (strpos($url, "?") !== FALSE) {
    $url = strstr($url, "?", true);
    $url .= "?";
} else {
    $url .= "?";
}

if (basename($url, ".php") == "?") {
    $url = str_replace("?", "index.php?", $url);
}

$filename = str_replace("de_", "", basename($url, ".php"));
$filename_de = "de_" . $filename;

$_SESSION['url'] = $url;
$_SESSION['de_url'] = str_replace($filename_de, $filename, $url);
$_SESSION['de_url'] = str_replace($filename, $filename_de, $_SESSION['de_url']);
$_SESSION['en_url'] = str_replace($filename_de, $filename, $url);

if (strpos($url, "spotify") === FALSE && strpos($url, "applemusic") === FALSE && strpos($url, "editaccount") === FALSE) {
    $_SESSION['de_url'] .= $_SERVER['QUERY_STRING'];
    $_SESSION['en_url'] .= $_SERVER['QUERY_STRING'];
}

// Playing time management
if (strpos($url, "spotify_play") === FALSE) {
    $playing = 0;
    if (isset($_SESSION['playing_time']) && $_SESSION['playing_time'][1] == 0) {
        $_SESSION['playing_time'][1] = time();
    }
} else {
    $playing = 1;
    $_SESSION['playing_time'] = [
        time(), 
        0, 
        $_SESSION['daytime_from'] ?? '09:00:00', 
        $_SESSION['daytime_to'] ?? '17:00:00', 
        $_SESSION['days'] ?? '1,2,3,4,5'
    ];
}

// Update user playing status if logged in and database is available
if (isset($_SESSION['user_id']) && $pdo) {
    try {
        if (isset($_SESSION['playing_time'])) {
            $sql = "SELECT playing_time FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['user_id']]);
            $row = $stmt->fetch();
            
            if ($row && $row['playing_time']) {
                $playing_time = array_explode_with_keys($row['playing_time']);
                
                // Clean old entries (older than 90 days)
                foreach ($playing_time as $d => $t) {
                    if (strpos($d, 'day') === FALSE && $t < strtotime('-90 days')) {
                        unset($playing_time[$d]);
                        unset($playing_time['daytime_from_' . $d]);
                        unset($playing_time['daytime_to_' . $d]);
                        unset($playing_time['days_' . $d]);
                    }
                }
                
                // Add current session
                if (isset($_SESSION['playing_time']) && $_SESSION['playing_time'][1] != 0) {
                    $playing_time[$_SESSION['playing_time'][0]] = $_SESSION['playing_time'][1];
                    $playing_time['daytime_from_' . $_SESSION['playing_time'][0]] = $_SESSION['playing_time'][2];
                    $playing_time['daytime_to_' . $_SESSION['playing_time'][0]] = $_SESSION['playing_time'][3];
                    $playing_time['days_' . $_SESSION['playing_time'][0]] = $_SESSION['playing_time'][4];
                    unset($_SESSION['playing_time']);
                }
                
                $playing_time_str = array_implode_with_keys($playing_time);
                
                if ($playing_time_str) {
                    $sql = "UPDATE users SET currently_playing = ?, playing_time = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$playing, $playing_time_str, $_SESSION['user_id']]);
                }
            }
        } else {
            $sql = "UPDATE users SET currently_playing = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$playing, $_SESSION['user_id']]);
        }
    } catch (PDOException $e) {
        error_log("Error updating user playing status: " . $e->getMessage());
    }
}
?>