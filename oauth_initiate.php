<?php
/**
 * OAuth Initiation Handler
 * Redirects users to OAuth providers for authentication
 */

// Use new include system
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}
require_once 'script/includes.php';

// Initialize language manager and authentication
$lang = init_app();
$auth = init_auth();

// Initialize OAuth manager
require_once 'script/OAuthManager.php';
$oauthManager = new OAuthManager($GLOBALS['pdo'], $lang);

try {
    // Get provider from URL parameter
    $provider = $_GET['provider'] ?? '';
    
    if (empty($provider)) {
        throw new Exception('No OAuth provider specified');
    }
    
    // Check if this is a platform connection or user login
    $isPlatformConnection = isset($_GET['platform']) && $_GET['platform'] === 'true';
    
    // Generate OAuth state for CSRF protection
    $state = bin2hex(random_bytes(32));
    
    // Store state in database for verification
    try {
        $stmt = $GLOBALS['pdo']->prepare("
            INSERT INTO oauth_states (state, provider, user_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $state,
            $provider,
            $isPlatformConnection ? ($_SESSION['user_id'] ?? null) : null
        ]);
    } catch (PDOException $e) {
        error_log("Error storing OAuth state: " . $e->getMessage());
        // Continue anyway, state will be stored in session as fallback
        $_SESSION['oauth_state'] = $state;
    }
    
    // Generate OAuth URL
    $authUrl = $oauthManager->getAuthUrl($provider, $state);
    
    // Redirect to OAuth provider
    header('Location: ' . $authUrl);
    exit;
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    error_log("OAuth initiation error: " . $e->getMessage());
    
    // Redirect back to login with error
    $redirect_url = $isPlatformConnection ? 'editaccount.php' : 'login.php';
    header('Location: ' . $redirect_url . '?error=' . urlencode($error_message));
    exit;
}
?> 