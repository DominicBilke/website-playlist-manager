<?php
/**
 * OAuth Callback Handler
 * Processes OAuth responses from all providers
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

$error_message = '';
$success_message = '';
$redirect_url = 'login.php';

try {
    // Get provider from URL parameter
    $provider = $_GET['provider'] ?? '';
    
    if (empty($provider)) {
        throw new Exception('No OAuth provider specified');
    }
    
    // Check if this is a platform connection or user login
    $isPlatformConnection = isset($_GET['platform']) && $_GET['platform'] === 'true';
    
    if ($isPlatformConnection) {
        // Handle platform connection
        $redirect_url = 'editaccount.php';
        
        if (!is_authenticated()) {
            throw new Exception('User must be logged in to connect platforms');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Check for authorization code
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $state = $_GET['state'] ?? null;
            
            // Exchange code for token
            $tokenData = $oauthManager->exchangeCodeForToken($provider, $code, $state);
            
            // Store token
            if ($oauthManager->storeToken($userId, $provider, $tokenData)) {
                $success_message = $lang->get($provider . '_connected_successfully');
            } else {
                throw new Exception('Failed to store platform token');
            }
        } else {
            throw new Exception('No authorization code received');
        }
        
    } else {
        // Handle user login
        $redirect_url = 'account.php';
        
        // Check for authorization code
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $state = $_GET['state'] ?? null;
            
            // Exchange code for token
            $tokenData = $oauthManager->exchangeCodeForToken($provider, $code, $state);
            
            // Get user information
            $userInfo = $oauthManager->getUserInfo($provider, $tokenData['access_token']);
            
            // Handle OAuth login
            $result = $oauthManager->handleOAuthLogin($provider, $userInfo);
            
            if ($result['success']) {
                $success_message = $lang->get('oauth_login_successful');
                
                // Store OAuth token if needed for future use
                if (isset($result['user']['id'])) {
                    $oauthManager->storeToken($result['user']['id'], $provider, $tokenData);
                }
            } else {
                throw new Exception($result['message'] ?? 'OAuth login failed');
            }
            
        } else {
            throw new Exception('No authorization code received');
        }
    }
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    error_log("OAuth callback error: " . $e->getMessage());
}

// Redirect with appropriate messages
$params = [];
if ($error_message) {
    $params['error'] = urlencode($error_message);
}
if ($success_message) {
    $params['success'] = urlencode($success_message);
}

$redirect_url .= !empty($params) ? '?' . http_build_query($params) : '';
header('Location: ' . $redirect_url);
exit;
?> 