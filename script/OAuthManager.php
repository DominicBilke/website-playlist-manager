<?php
/**
 * OAuth Manager
 * Handles OAuth authentication for user login and platform connections
 */

require_once __DIR__ . '/vendor/autoload.php';

class OAuthManager {
    private $pdo;
    private $lang;
    private $config;
    
    public function __construct($pdo, $lang) {
        $this->pdo = $pdo;
        $this->lang = $lang;
        $this->loadConfig();
    }
    
    /**
     * Load OAuth configuration
     */
    private function loadConfig() {
        $this->config = [
            'google' => [
                'client_id' => getenv('GOOGLE_CLIENT_ID') ?: '',
                'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=google',
                'scopes' => ['openid', 'email', 'profile'],
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'userinfo_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
            ],
            'apple' => [
                'client_id' => getenv('APPLE_CLIENT_ID') ?: '',
                'client_secret' => getenv('APPLE_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=apple',
                'scopes' => ['name', 'email'],
                'auth_url' => 'https://appleid.apple.com/auth/authorize',
                'token_url' => 'https://appleid.apple.com/auth/token',
                'key_id' => getenv('APPLE_KEY_ID') ?: '',
                'team_id' => getenv('APPLE_TEAM_ID') ?: '',
                'private_key_path' => getenv('APPLE_PRIVATE_KEY_PATH') ?: ''
            ],
            'spotify' => [
                'client_id' => getenv('SPOTIFY_CLIENT_ID') ?: '',
                'client_secret' => getenv('SPOTIFY_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=spotify',
                'scopes' => ['user-read-private', 'user-read-email', 'playlist-read-private', 'playlist-modify-public', 'playlist-modify-private'],
                'auth_url' => 'https://accounts.spotify.com/authorize',
                'token_url' => 'https://accounts.spotify.com/api/token',
                'userinfo_url' => 'https://api.spotify.com/v1/me'
            ],
            'apple_music' => [
                'client_id' => getenv('APPLE_MUSIC_CLIENT_ID') ?: '',
                'client_secret' => getenv('APPLE_MUSIC_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=apple_music',
                'scopes' => ['music'],
                'auth_url' => 'https://appleid.apple.com/auth/authorize',
                'token_url' => 'https://appleid.apple.com/auth/token',
                'key_id' => getenv('APPLE_MUSIC_KEY_ID') ?: '',
                'team_id' => getenv('APPLE_MUSIC_TEAM_ID') ?: '',
                'private_key_path' => getenv('APPLE_MUSIC_PRIVATE_KEY_PATH') ?: ''
            ],
            'youtube' => [
                'client_id' => getenv('YOUTUBE_CLIENT_ID') ?: '',
                'client_secret' => getenv('YOUTUBE_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=youtube',
                'scopes' => ['https://www.googleapis.com/auth/youtube.readonly', 'https://www.googleapis.com/auth/youtube.force-ssl'],
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'userinfo_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
            ],
            'amazon' => [
                'client_id' => getenv('AMAZON_CLIENT_ID') ?: '',
                'client_secret' => getenv('AMAZON_CLIENT_SECRET') ?: '',
                'redirect_uri' => $this->getBaseUrl() . 'oauth_callback.php?provider=amazon',
                'scopes' => ['profile'],
                'auth_url' => 'https://www.amazon.com/ap/oa',
                'token_url' => 'https://api.amazon.com/auth/o2/token',
                'userinfo_url' => 'https://api.amazon.com/user/profile'
            ]
        ];
    }
    
    /**
     * Get base URL for redirect URIs
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        return $protocol . $host . $path . '/';
    }
    
    /**
     * Generate OAuth authorization URL
     */
    public function getAuthUrl($provider, $state = null) {
        if (!isset($this->config[$provider])) {
            throw new Exception("Unsupported OAuth provider: $provider");
        }
        
        $config = $this->config[$provider];
        
        if (empty($config['client_id'])) {
            throw new Exception("OAuth client ID not configured for $provider");
        }
        
        // Generate state if not provided
        if (!$state) {
            $state = bin2hex(random_bytes(32));
            $_SESSION['oauth_state'] = $state;
        }
        
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => implode(' ', $config['scopes']),
            'state' => $state
        ];
        
        // Add provider-specific parameters
        switch ($provider) {
            case 'apple':
            case 'apple_music':
                $params['response_mode'] = 'form_post';
                break;
            case 'spotify':
                $params['show_dialog'] = 'true';
                break;
        }
        
        return $config['auth_url'] . '?' . http_build_query($params);
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function exchangeCodeForToken($provider, $code, $state = null) {
        if (!isset($this->config[$provider])) {
            throw new Exception("Unsupported OAuth provider: $provider");
        }
        
        $config = $this->config[$provider];
        
        // Verify state if provided
        if ($state && (!isset($_SESSION['oauth_state']) || $state !== $_SESSION['oauth_state'])) {
            throw new Exception("Invalid OAuth state");
        }
        
        $params = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['redirect_uri']
        ];
        
        // Add provider-specific parameters
        switch ($provider) {
            case 'apple':
            case 'apple_music':
                $params['client_secret'] = $this->generateAppleClientSecret($provider);
                break;
        }
        
        $response = $this->makeTokenRequest($config['token_url'], $params);
        
        if (!isset($response['access_token'])) {
            throw new Exception("Failed to obtain access token: " . json_encode($response));
        }
        
        return $response;
    }
    
    /**
     * Get user information from OAuth provider
     */
    public function getUserInfo($provider, $accessToken) {
        if (!isset($this->config[$provider])) {
            throw new Exception("Unsupported OAuth provider: $provider");
        }
        
        $config = $this->config[$provider];
        
        if (!isset($config['userinfo_url'])) {
            throw new Exception("User info URL not configured for $provider");
        }
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'User-Agent: PlaylistManager/1.0'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['userinfo_url']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Failed to get user info: HTTP $httpCode");
        }
        
        $userInfo = json_decode($response, true);
        
        if (!$userInfo) {
            throw new Exception("Invalid user info response");
        }
        
        return $this->normalizeUserInfo($provider, $userInfo);
    }
    
    /**
     * Normalize user info from different providers
     */
    private function normalizeUserInfo($provider, $userInfo) {
        $normalized = [
            'provider' => $provider,
            'provider_id' => null,
            'email' => null,
            'name' => null,
            'first_name' => null,
            'last_name' => null,
            'picture' => null
        ];
        
        switch ($provider) {
            case 'google':
            case 'youtube':
                $normalized['provider_id'] = $userInfo['id'];
                $normalized['email'] = $userInfo['email'];
                $normalized['name'] = $userInfo['name'];
                $normalized['first_name'] = $userInfo['given_name'];
                $normalized['last_name'] = $userInfo['family_name'];
                $normalized['picture'] = $userInfo['picture'];
                break;
                
            case 'apple':
            case 'apple_music':
                $normalized['provider_id'] = $userInfo['sub'];
                $normalized['email'] = $userInfo['email'];
                if (isset($userInfo['name'])) {
                    $normalized['name'] = $userInfo['name']['firstName'] . ' ' . $userInfo['name']['lastName'];
                    $normalized['first_name'] = $userInfo['name']['firstName'];
                    $normalized['last_name'] = $userInfo['name']['lastName'];
                }
                break;
                
            case 'spotify':
                $normalized['provider_id'] = $userInfo['id'];
                $normalized['email'] = $userInfo['email'];
                $normalized['name'] = $userInfo['display_name'];
                if (isset($userInfo['images'][0])) {
                    $normalized['picture'] = $userInfo['images'][0]['url'];
                }
                break;
                
            case 'amazon':
                $normalized['provider_id'] = $userInfo['user_id'];
                $normalized['email'] = $userInfo['email'];
                $normalized['name'] = $userInfo['name'];
                break;
        }
        
        return $normalized;
    }
    
    /**
     * Generate Apple client secret for JWT
     */
    private function generateAppleClientSecret($provider) {
        $config = $this->config[$provider];
        
        if (empty($config['key_id']) || empty($config['team_id']) || empty($config['private_key_path'])) {
            throw new Exception("Apple OAuth configuration incomplete");
        }
        
        $privateKey = file_get_contents($config['private_key_path']);
        if (!$privateKey) {
            throw new Exception("Could not read Apple private key");
        }
        
        $header = [
            'alg' => 'ES256',
            'kid' => $config['key_id']
        ];
        
        $payload = [
            'iss' => $config['team_id'],
            'iat' => time(),
            'exp' => time() + 3600,
            'aud' => 'https://appleid.apple.com',
            'sub' => $config['client_id']
        ];
        
        return \Firebase\JWT\JWT::encode($payload, $privateKey, 'ES256', null, $header);
    }
    
    /**
     * Make token request to OAuth provider
     */
    private function makeTokenRequest($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: PlaylistManager/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Token request failed: HTTP $httpCode - $response");
        }
        
        $tokenData = json_decode($response, true);
        
        if (!$tokenData) {
            throw new Exception("Invalid token response");
        }
        
        return $tokenData;
    }
    
    /**
     * Store OAuth token for user
     */
    public function storeToken($userId, $provider, $tokenData) {
        try {
            // Check if token already exists
            $stmt = $this->pdo->prepare("SELECT id FROM api_tokens WHERE user_id = ? AND platform = ?");
            $stmt->execute([$userId, $provider]);
            
            $expiresAt = null;
            if (isset($tokenData['expires_in'])) {
                $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);
            }
            
            if ($stmt->fetch()) {
                // Update existing token
                $stmt = $this->pdo->prepare("
                    UPDATE api_tokens 
                    SET access_token = ?, refresh_token = ?, expires_at = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ? AND platform = ?
                ");
                $stmt->execute([
                    $tokenData['access_token'],
                    $tokenData['refresh_token'] ?? null,
                    $expiresAt,
                    $userId,
                    $provider
                ]);
            } else {
                // Insert new token
                $stmt = $this->pdo->prepare("
                    INSERT INTO api_tokens (user_id, platform, access_token, refresh_token, expires_at) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $userId,
                    $provider,
                    $tokenData['access_token'],
                    $tokenData['refresh_token'] ?? null,
                    $expiresAt
                ]);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error storing OAuth token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get stored token for user and provider
     */
    public function getStoredToken($userId, $provider) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT access_token, refresh_token, expires_at 
                FROM api_tokens 
                WHERE user_id = ? AND platform = ?
            ");
            $stmt->execute([$userId, $provider]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Check if token is expired
     */
    public function isTokenExpired($token) {
        if (!$token || !isset($token['expires_at'])) {
            return true;
        }
        
        return strtotime($token['expires_at']) <= time();
    }
    
    /**
     * Refresh OAuth token
     */
    public function refreshToken($provider, $refreshToken) {
        if (!isset($this->config[$provider])) {
            throw new Exception("Unsupported OAuth provider: $provider");
        }
        
        $config = $this->config[$provider];
        
        $params = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        // Add provider-specific parameters
        switch ($provider) {
            case 'apple':
            case 'apple_music':
                $params['client_secret'] = $this->generateAppleClientSecret($provider);
                break;
        }
        
        return $this->makeTokenRequest($config['token_url'], $params);
    }
    
    /**
     * Handle OAuth login for user authentication
     */
    public function handleOAuthLogin($provider, $userInfo) {
        try {
            // Check if user exists by OAuth provider ID
            $stmt = $this->pdo->prepare("
                SELECT u.* FROM users u 
                JOIN oauth_connections oc ON u.id = oc.user_id 
                WHERE oc.provider = ? AND oc.provider_user_id = ?
            ");
            $stmt->execute([$provider, $userInfo['provider_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                // User exists, log them in
                return $this->loginExistingUser($user);
            } else {
                // Check if user exists by email
                if ($userInfo['email']) {
                    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$userInfo['email']]);
                    $user = $stmt->fetch();
                    
                    if ($user) {
                        // Link OAuth account to existing user
                        $this->linkOAuthAccount($user['id'], $provider, $userInfo);
                        return $this->loginExistingUser($user);
                    }
                }
                
                // Create new user
                return $this->createOAuthUser($provider, $userInfo);
            }
        } catch (PDOException $e) {
            error_log("OAuth login error: " . $e->getMessage());
            throw new Exception("OAuth login failed");
        }
    }
    
    /**
     * Login existing user
     */
    private function loginExistingUser($user) {
        // Create session
        $sessionId = bin2hex(random_bytes(32));
        
        $stmt = $this->pdo->prepare("
            INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'],
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['team'] = $user['team'];
        $_SESSION['office'] = $user['office'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['session_id'] = $sessionId;
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Create new user from OAuth
     */
    private function createOAuthUser($provider, $userInfo) {
        // Generate username from email or name
        $username = $this->generateUsername($userInfo);
        
        // Insert new user
        $stmt = $this->pdo->prepare("
            INSERT INTO users (login, email, team, office, role, status) 
            VALUES (?, ?, 1, 'OAuth', 'user', 'active')
        ");
        $stmt->execute([
            $username,
            $userInfo['email']
        ]);
        
        $userId = $this->pdo->lastInsertId();
        
        // Link OAuth account
        $this->linkOAuthAccount($userId, $provider, $userInfo);
        
        // Create user settings
        $stmt = $this->pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        
        // Get the created user
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        return $this->loginExistingUser($user);
    }
    
    /**
     * Link OAuth account to user
     */
    private function linkOAuthAccount($userId, $provider, $userInfo) {
        $stmt = $this->pdo->prepare("
            INSERT INTO oauth_connections (user_id, provider, provider_user_id, email, name, created_at) 
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE 
            email = VALUES(email), 
            name = VALUES(name), 
            updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            $userId,
            $provider,
            $userInfo['provider_id'],
            $userInfo['email'],
            $userInfo['name']
        ]);
    }
    
    /**
     * Generate unique username
     */
    private function generateUsername($userInfo) {
        $baseUsername = '';
        
        if ($userInfo['email']) {
            $baseUsername = explode('@', $userInfo['email'])[0];
        } elseif ($userInfo['name']) {
            $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($userInfo['name']));
        } else {
            $baseUsername = 'user';
        }
        
        $username = $baseUsername;
        $counter = 1;
        
        while ($this->usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
}
?> 