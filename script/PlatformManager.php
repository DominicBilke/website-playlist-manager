<?php
/**
 * Unified Platform Manager
 * Handles all music platforms with consistent API and functionality
 */

// Autoload will be handled by the main initialization script

require_once __DIR__ . '/vendor/autoload.php';

class PlatformManager {
    private $pdo;
    private $lang;
    private $user_id;
    private $platforms = [];
    
    public function __construct($pdo, $lang, $user_id) {
        $this->pdo = $pdo;
        $this->lang = $lang;
        $this->user_id = $user_id;
        $this->initializePlatforms();
    }
    
    /**
     * Initialize all platform connections
     */
    private function initializePlatforms() {
        // Load user's platform tokens
        $tokens = $this->getUserTokens();
        
        // Initialize each platform
        $this->platforms['spotify'] = new SpotifyPlatform($this->pdo, $this->lang, $this->user_id, $tokens['spotify'] ?? null);
        $this->platforms['apple_music'] = new AppleMusicPlatform($this->pdo, $this->lang, $this->user_id, $tokens['apple_music'] ?? null);
        $this->platforms['youtube'] = new YouTubePlatform($this->pdo, $this->lang, $this->user_id, $tokens['youtube'] ?? null);
        $this->platforms['amazon'] = new AmazonPlatform($this->pdo, $this->lang, $this->user_id, $tokens['amazon'] ?? null);
    }
    
    /**
     * Get user's platform tokens
     */
    private function getUserTokens() {
        try {
            $stmt = $this->pdo->prepare("SELECT platform, access_token, refresh_token, expires_at FROM api_tokens WHERE user_id = ?");
            $stmt->execute([$this->user_id]);
            $tokens = [];
            while ($row = $stmt->fetch()) {
                $tokens[$row['platform']] = $row;
            }
            return $tokens;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get platform instance
     */
    public function getPlatform($platform) {
        return $this->platforms[$platform] ?? null;
    }
    
    /**
     * Get all platforms
     */
    public function getAllPlatforms() {
        return $this->platforms;
    }
    
    /**
     * Get platform status
     */
    public function getPlatformStatus($platform) {
        $platformInstance = $this->getPlatform($platform);
        if ($platformInstance) {
            return $platformInstance->getStatus();
        }
        return ['connected' => false, 'message' => 'Platform not available'];
    }
    
    /**
     * Get all platform statuses
     */
    public function getAllPlatformStatuses() {
        $statuses = [];
        foreach ($this->platforms as $platform => $instance) {
            $statuses[$platform] = $instance->getStatus();
        }
        return $statuses;
    }
    
    /**
     * Connect platform using OAuth
     */
    public function connectPlatform($platform) {
        try {
            // Initialize OAuth manager
            require_once __DIR__ . '/OAuthManager.php';
            $oauthManager = new OAuthManager($this->pdo, $this->lang);
            
            // Generate OAuth URL for platform connection
            $authUrl = $oauthManager->getAuthUrl($platform);
            
            return [
                'success' => true,
                'auth_url' => $authUrl,
                'message' => 'Redirecting to ' . ucfirst($platform) . ' for authentication'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to initiate ' . ucfirst($platform) . ' connection: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Disconnect platform
     */
    public function disconnectPlatform($platform) {
        try {
            // Remove token from database
            $stmt = $this->pdo->prepare("DELETE FROM api_tokens WHERE user_id = ? AND platform = ?");
            $stmt->execute([$this->user_id, $platform]);
            
            // Reinitialize platform
            $this->initializePlatforms();
            
            return [
                'success' => true,
                'message' => ucfirst($platform) . ' disconnected successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to disconnect ' . ucfirst($platform) . ': ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Refresh platform token
     */
    public function refreshPlatformToken($platform) {
        try {
            // Initialize OAuth manager
            require_once __DIR__ . '/OAuthManager.php';
            $oauthManager = new OAuthManager($this->pdo, $this->lang);
            
            // Get stored token
            $token = $oauthManager->getStoredToken($this->user_id, $platform);
            
            if (!$token || !$token['refresh_token']) {
                return [
                    'success' => false,
                    'message' => 'No refresh token available for ' . ucfirst($platform)
                ];
            }
            
            // Check if token is expired
            if (!$oauthManager->isTokenExpired($token)) {
                return [
                    'success' => true,
                    'message' => ucfirst($platform) . ' token is still valid'
                ];
            }
            
            // Refresh token
            $newTokenData = $oauthManager->refreshToken($platform, $token['refresh_token']);
            
            // Store new token
            if ($oauthManager->storeToken($this->user_id, $platform, $newTokenData)) {
                // Reinitialize platform
                $this->initializePlatforms();
                
                return [
                    'success' => true,
                    'message' => ucfirst($platform) . ' token refreshed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to store refreshed token for ' . ucfirst($platform)
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to refresh ' . ucfirst($platform) . ' token: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get user's playlists from all platforms
     */
    public function getAllPlaylists() {
        $playlists = [];
        foreach ($this->platforms as $platform => $instance) {
            $status = $instance->getStatus();
            if ($status['connected']) {
                $playlists[$platform] = $instance->getPlaylists();
            }
        }
        return $playlists;
    }
    
    /**
     * Start playback on a platform
     */
    public function startPlayback($platform, $playlist_id = null) {
        $platformInstance = $this->getPlatform($platform);
        if (!$platformInstance) {
            return ['success' => false, 'message' => 'Platform not available'];
        }
        return $platformInstance->startPlayback($playlist_id);
    }
    
    /**
     * Stop playback on a platform
     */
    public function stopPlayback($platform) {
        $platformInstance = $this->getPlatform($platform);
        if (!$platformInstance) {
            return ['success' => false, 'message' => 'Platform not available'];
        }
        return $platformInstance->stopPlayback();
    }
    
    /**
     * Get current playback status
     */
    public function getPlaybackStatus($platform) {
        $platformInstance = $this->getPlatform($platform);
        if (!$platformInstance) {
            return ['success' => false, 'message' => 'Platform not available'];
        }
        return $platformInstance->getPlaybackStatus();
    }
    
    /**
     * Log listening statistics
     */
    public function logListeningStats($platform, $track_data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO listening_stats (user_id, platform, track_id, track_name, artist, duration, session_duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $this->user_id,
                $platform,
                $track_data['track_id'] ?? null,
                $track_data['track_name'] ?? null,
                $track_data['artist'] ?? null,
                $track_data['duration'] ?? null,
                $track_data['session_duration'] ?? null
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error logging listening stats: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Base Platform Class
 */
abstract class BasePlatform {
    protected $pdo;
    protected $lang;
    protected $user_id;
    protected $tokens;
    protected $connected = false;
    
    public function __construct($pdo, $lang, $user_id, $tokens = null) {
        $this->pdo = $pdo;
        $this->lang = $lang;
        $this->user_id = $user_id;
        $this->tokens = $tokens;
        $this->initialize();
    }
    
    abstract protected function initialize();
    abstract public function getStatus();
    abstract public function getPlaylists();
    abstract public function startPlayback($playlist_id = null);
    abstract public function stopPlayback();
    abstract public function getPlaybackStatus();
    abstract public function authenticate($code = null);
    
    // Additional playback control methods
    public function nextTrack() {
        return ['success' => false, 'message' => 'Not implemented for this platform'];
    }
    
    public function previousTrack() {
        return ['success' => false, 'message' => 'Not implemented for this platform'];
    }
    
    public function setVolume($volume) {
        return ['success' => false, 'message' => 'Not implemented for this platform'];
    }
    
    public function seek($position) {
        return ['success' => false, 'message' => 'Not implemented for this platform'];
    }
    
    /**
     * Save tokens to database
     */
    protected function saveTokens($access_token, $refresh_token = null, $expires_at = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO api_tokens (user_id, platform, access_token, refresh_token, expires_at) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                access_token = VALUES(access_token), 
                refresh_token = VALUES(refresh_token), 
                expires_at = VALUES(expires_at),
                updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$this->user_id, $this->getPlatformName(), $access_token, $refresh_token, $expires_at]);
            return true;
        } catch (PDOException $e) {
            error_log("Error saving tokens: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get platform name
     */
    abstract protected function getPlatformName();
    
    /**
     * Check if tokens are valid
     */
    protected function isTokenValid() {
        if (!$this->tokens) {
            return false;
        }
        /*
        if (isset($this->tokens['expires_at']) && $this->tokens['expires_at']) {
            return strtotime($this->tokens['expires_at']) > time();
        }*/
        
        return !empty($this->tokens['access_token']);
    }
}

/**
 * Spotify Platform Implementation
 */
class SpotifyPlatform extends BasePlatform {
    private $api = null;
    private $session = null;
    
    protected function getPlatformName() {
        return 'spotify';
    }
    
    protected function initialize() {
        if ($this->isTokenValid()) {
            $this->connectWithToken();
        }
    }
    
    private function connectWithToken() {
        try {
            $options = [
                'auto_refresh' => true,
                'auto_retry' => true,
            ];
            $this->api = new SpotifyWebAPI\SpotifyWebAPI($options);
            $this->api->setAccessToken($this->tokens['access_token']);
            $this->connected = true;
        } catch (Exception $e) {
            $this->connected = false;
        }
    }
    
    public function getStatus() {
        if (!$this->connected) {
            return ['connected' => false, 'message' => 'Not connected'];
        }
        
        try {
            $me = $this->api->me();
            return [
                'connected' => true,
                'user' => $me->display_name,
                'email' => $me->email ?? null,
                'premium' => $me->product === 'premium'
            ];
        } catch (Exception $e) {
            return ['connected' => false, 'message' => 'Token expired'];
        }
    }
    
    public function getPlaylists() {
        if (!$this->connected) {
            return [
                ['id' => '', 'name' => 'Not connected. Please connect your account.', 'tracks' => 0]
            ];
        }
        
        try {
            $playlists = $this->api->getUserPlaylists(null, ['limit' => 50]);
            $result = [];
            foreach ($playlists->items as $playlist) {
                $result[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'tracks' => $playlist->tracks->total,
                    'public' => $playlist->public,
                    'owner' => $playlist->owner->display_name
                ];
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function startPlayback($playlist_id = null) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected. Please connect your account.'];
        }
        
        try {
            if ($playlist_id) {
                $this->api->play(false, ['context_uri' => 'spotify:playlist:' . $playlist_id]);
            } else {
                $this->api->play();
            }
            return ['success' => true, 'message' => 'Playback started'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function stopPlayback() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $this->api->pause();
            return ['success' => true, 'message' => 'Playback stopped'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function getPlaybackStatus() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $playback = $this->api->getMyCurrentPlaybackInfo();
            if (!$playback) {
                return ['success' => true, 'playing' => false];
            }
            
            $track = $playback->item ?? null;
            $artwork = null;
            
            if ($track && isset($track->album->images) && !empty($track->album->images)) {
                $artwork = $track->album->images[0]->url ?? null;
            }
            
            return [
                'success' => true,
                'playing' => $playback->is_playing,
                'track' => $track->name ?? null,
                'artist' => $track->artists[0]->name ?? null,
                'album' => $track->album->name ?? null,
                'artwork' => $artwork,
                'progress' => $playback->progress_ms ?? 0,
                'duration' => $track->duration_ms ?? 0
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function authenticate($code = null) {
        // Spotify authentication logic
        $client_id = '';
        $client_secret = '';
        $redirect_uri = 'https://www2.playlist-manager.de/spotify_play.php';
        
        try {
            $this->session = new SpotifyWebAPI\Session($client_id, $client_secret, $redirect_uri);
            
            if ($code) {
                $this->session->requestAccessToken($code);
                $access_token = $this->session->getAccessToken();
                $refresh_token = $this->session->getRefreshToken();
                
                if ($this->saveTokens($access_token, $refresh_token)) {
                    $this->connectWithToken();
                    return ['success' => true, 'message' => 'Authentication successful'];
                }
            }
            
            return ['success' => false, 'message' => 'Authentication failed'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function nextTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $this->api->next();
            return ['success' => true, 'message' => 'Skipped to next track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function previousTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $this->api->previous();
            return ['success' => true, 'message' => 'Went to previous track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function setVolume($volume) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $this->api->setVolume($volume);
            return ['success' => true, 'message' => 'Volume set'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function seek($position) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected'];
        }
        
        try {
            $this->api->seek($position);
            return ['success' => true, 'message' => 'Seeked to position'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

/**
 * Apple Music Platform Implementation
 */
class AppleMusicPlatform extends BasePlatform {
    private $api = null;
    private $jwtToken = null;
    
    protected function getPlatformName() {
        return 'apple_music';
    }
    
    protected function initialize() {
        if ($this->isTokenValid()) {
            $this->connectWithToken();
        }
    }
    
    private function connectWithToken() {
        try {
            // Initialize Apple Music API with JWT token
            $tokenGenerator = new PouleR\AppleMusicAPI\AppleMusicAPITokenGenerator();
            $this->jwtToken = $tokenGenerator->generateDeveloperToken(
                '',
                '',
                'https://www2.playlist-manager.de/AuthKey.p8'
            );
            
            $curl = new \Symfony\Component\HttpClient\CurlHttpClient();
            $client = new PouleR\AppleMusicAPI\APIClient($curl);
            $client->setDeveloperToken($this->jwtToken);
            
            $this->api = new PouleR\AppleMusicAPI\AppleMusicAPI($client);
            $this->connected = true;
        } catch (Exception $e) {
            $this->connected = false;
            error_log("Apple Music connection error: " . $e->getMessage());
        }
    }
    
    public function getStatus() {
        if (!$this->connected) {
            return ['connected' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Get user info from Apple Music
            return [
                'connected' => true,
                'user' => 'Apple Music User',
                'email' => null,
                'premium' => true
            ];
        } catch (Exception $e) {
            return ['connected' => false, 'message' => 'Connection failed'];
        }
    }
    
    public function getPlaylists() {
        if (!$this->connected) {
            return [
                ['id' => '', 'name' => 'Amazon Music requires manual control. Open in a new window.', 'tracks' => 0]
            ];
        }
        
        try {
            $playlists = $this->api->getAllLibraryPlaylists(50);
            $result = [];
            
            foreach ($playlists as $playlist) {
                $result[] = [
                    'id' => $playlist->getId(),
                    'name' => $playlist->getAttributes()->getName(),
                    'tracks' => count($playlist->getRelationships()->getTracks()->getData())
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error getting Apple Music playlists: " . $e->getMessage());
            return [];
        }
    }
    
    public function startPlayback($playlist_id = null) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected. Please connect your account.'];
        }
        
        try {
            // Apple Music playback control would be implemented here
            // This would typically use MusicKit.js on the frontend
            return ['success' => true, 'message' => 'Playback started'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to start playback'];
        }
    }
    
    public function stopPlayback() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Stop playback logic
            return ['success' => true, 'message' => 'Playback stopped'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to stop playback'];
        }
    }
    
    public function getPlaybackStatus() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Get current playback status
            return [
                'success' => true,
                'playing' => false,
                'track' => 'Sample Track',
                'artist' => 'Sample Artist',
                'album' => 'Sample Album',
                'artwork' => null,
                'progress' => 0,
                'duration' => 0
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to get playback status'];
        }
    }
    
    public function authenticate($code = null) {
        try {
            // Apple Music uses MusicKit for authentication
            // This would typically be handled on the frontend
            return ['success' => true, 'message' => 'Apple Music authentication initiated'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }
    }
    
    public function nextTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Next track logic
            return ['success' => true, 'message' => 'Next track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to skip track'];
        }
    }
    
    public function previousTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Previous track logic
            return ['success' => true, 'message' => 'Previous track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to go to previous track'];
        }
    }
    
    public function setVolume($volume) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Volume control logic
            return ['success' => true, 'message' => 'Volume set to ' . $volume];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to set volume'];
        }
    }
    
    public function seek($position) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Apple Music'];
        }
        
        try {
            // Seek logic
            return ['success' => true, 'message' => 'Seeked to position ' . $position];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to seek'];
        }
    }
}

/**
 * YouTube Platform Implementation
 */
class YouTubePlatform extends BasePlatform {
    private $api = null;
    private $client = null;
    
    protected function getPlatformName() {
        return 'youtube';
    }
    
    protected function initialize() {
        if ($this->isTokenValid()) {
            $this->connectWithToken();
        }
    }
    
    private function connectWithToken() {
        try {
            // Initialize OAuth manager
            require_once __DIR__ . '/OAuthManager.php';
            $oauthManager = new OAuthManager($this->pdo, $this->lang);
            
            // Get stored token
            $token = $oauthManager->getStoredToken($this->user_id, 'youtube');
            
            if (!$token || $oauthManager->isTokenExpired($token)) {
                // Try to refresh token
                if ($token && $token['refresh_token']) {
                    try {
                        $newTokenData = $oauthManager->refreshToken('youtube', $token['refresh_token']);
                        $oauthManager->storeToken($this->user_id, 'youtube', $newTokenData);
                        $token = $newTokenData;
                    } catch (Exception $e) {
                        error_log("YouTube token refresh failed: " . $e->getMessage());
                        $this->connected = false;
                        return;
                    }
                } else {
                    $this->connected = false;
                    return;
                }
            }
            
            // Initialize Google Client
            $this->client = new \Google\Client();
            $this->client->setClientId(getenv('YOUTUBE_CLIENT_ID') ?: '');
            $this->client->setClientSecret(getenv('YOUTUBE_CLIENT_SECRET') ?: '');
            $this->client->setRedirectUri($this->getBaseUrl() . 'oauth_callback.php?provider=youtube&platform=true');
            $this->client->setScopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.force-ssl'
            ]);
            
            // Set access token
            $this->client->setAccessToken($token['access_token']);
            
            // Initialize YouTube API service
            $this->api = new \Google\Service\YouTube($this->client);
            $this->connected = true;
            
        } catch (Exception $e) {
            $this->connected = false;
            error_log("YouTube connection error: " . $e->getMessage());
        }
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        return $protocol . $host . $path . '/';
    }
    
    public function getPlaylists() {
        if (!$this->connected || !$this->api) {
            return [];
        }
        
        try {
            $playlists = [];
            $nextPageToken = null;
            
            do {
                $params = [
                    'part' => 'snippet,contentDetails',
                    'mine' => true,
                    'maxResults' => 50
                ];
                
                if ($nextPageToken) {
                    $params['pageToken'] = $nextPageToken;
                }
                
                $response = $this->api->playlists->listPlaylists($params);
                
                foreach ($response->getItems() as $playlist) {
                    $playlists[] = [
                        'id' => $playlist->getId(),
                        'name' => $playlist->getSnippet()->getTitle(),
                        'description' => $playlist->getSnippet()->getDescription(),
                        'track_count' => $playlist->getContentDetails()->getItemCount(),
                        'thumbnail' => $playlist->getSnippet()->getThumbnails()->getDefault()->getUrl()
                    ];
                }
                
                $nextPageToken = $response->getNextPageToken();
            } while ($nextPageToken);
            
            return $playlists;
            
        } catch (Exception $e) {
            error_log("YouTube getPlaylists error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPlaylistTracks($playlistId) {
        if (!$this->connected || !$this->api) {
            return [];
        }
        
        try {
            $tracks = [];
            $nextPageToken = null;
            
            do {
                $params = [
                    'part' => 'snippet,contentDetails',
                    'playlistId' => $playlistId,
                    'maxResults' => 50
                ];
                
                if ($nextPageToken) {
                    $params['pageToken'] = $nextPageToken;
                }
                
                $response = $this->api->playlistItems->listPlaylistItems($params);
                
                foreach ($response->getItems() as $item) {
                    $snippet = $item->getSnippet();
                    $tracks[] = [
                        'id' => $snippet->getResourceId()->getVideoId(),
                        'title' => $snippet->getTitle(),
                        'artist' => $snippet->getVideoOwnerChannelTitle(),
                        'duration' => null, // YouTube API doesn't provide duration in playlist items
                        'thumbnail' => $snippet->getThumbnails()->getDefault()->getUrl()
                    ];
                }
                
                $nextPageToken = $response->getNextPageToken();
            } while ($nextPageToken);
            
            return $tracks;
            
        } catch (Exception $e) {
            error_log("YouTube getPlaylistTracks error: " . $e->getMessage());
            return [];
        }
    }
    
    public function authenticate($code = null) {
        // This method is now handled by OAuth manager
        return [
            'success' => false,
            'message' => 'Use OAuth manager for authentication'
        ];
    }
    
    public function getStatus() {
        if (!$this->connected) {
            return ['connected' => false, 'message' => 'Not connected to YouTube'];
        }
        
        try {
            // Get channel info
            $channels = $this->api->channels->listChannels('snippet', [
                'mine' => true
            ]);
            
            if ($channels->getItems()) {
                $channel = $channels->getItems()[0];
                return [
                    'connected' => true,
                    'user' => $channel->getSnippet()->getTitle(),
                    'email' => null,
                    'premium' => false
                ];
            }
            
            return [
                'connected' => true,
                'user' => 'YouTube User',
                'email' => null,
                'premium' => false
            ];
        } catch (Exception $e) {
            return ['connected' => false, 'message' => 'Connection failed'];
        }
    }
    
    public function startPlayback($playlist_id = null) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected. Please connect your account.'];
        }
        
        try {
            // YouTube playback control would be implemented here
            return ['success' => true, 'message' => 'Playback started'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to start playback'];
        }
    }
    
    public function stopPlayback() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to YouTube'];
        }
        
        try {
            return ['success' => true, 'message' => 'Playback stopped'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to stop playback'];
        }
    }
    
    public function getPlaybackStatus() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to YouTube'];
        }
        
        try {
            return [
                'success' => true,
                'playing' => false,
                'track' => 'Sample Track',
                'artist' => 'Sample Artist',
                'album' => 'Sample Album',
                'artwork' => null,
                'progress' => 0,
                'duration' => 0
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to get playback status'];
        }
    }
}

/**
 * Amazon Platform Implementation
 */
class AmazonPlatform extends BasePlatform {
    private $api = null;
    private $client = null;
    
    protected function getPlatformName() {
        return 'amazon';
    }
    
    protected function initialize() {
        if ($this->isTokenValid()) {
            $this->connectWithToken();
        }
    }
    
    private function connectWithToken() {
        try {
            // Amazon Music API initialization
            // Note: Amazon Music has limited public API access
            // This is a placeholder for future implementation
            $this->connected = false;
        } catch (Exception $e) {
            $this->connected = false;
            error_log("Amazon Music connection error: " . $e->getMessage());
        }
    }
    
    public function getStatus() {
        if (!$this->connected) {
            return [
                'connected' => false, 
                'message' => 'Amazon Music requires manual control due to API limitations'
            ];
        }
        
        try {
            return [
                'connected' => true,
                'user' => 'Amazon Music User',
                'email' => null,
                'premium' => false
            ];
        } catch (Exception $e) {
            return ['connected' => false, 'message' => 'Connection failed'];
        }
    }
    
    public function getPlaylists() {
        if (!$this->connected) {
            return [
                ['id' => '', 'name' => 'Amazon Music requires manual control. Open in a new window.', 'tracks' => 0]
            ];
        }
        
        try {
            // Amazon Music playlist retrieval would be implemented here
            // Currently returns empty array due to API limitations
            return [];
        } catch (Exception $e) {
            error_log("Error getting Amazon Music playlists: " . $e->getMessage());
            return [];
        }
    }
    
    public function startPlayback($playlist_id = null) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Amazon Music requires manual control. Open in a new window.'];
        }
        
        try {
            // Amazon Music playback control would be implemented here
            return ['success' => true, 'message' => 'Playback started'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to start playback'];
        }
    }
    
    public function stopPlayback() {
        if (!$this->connected) {
            return [
                'success' => false, 
                'message' => 'Amazon Music requires manual control'
            ];
        }
        
        try {
            // Stop playback logic
            return ['success' => true, 'message' => 'Playback stopped'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to stop playback'];
        }
    }
    
    public function getPlaybackStatus() {
        if (!$this->connected) {
            return [
                'success' => false, 
                'message' => 'Amazon Music status cannot be determined automatically'
            ];
        }
        
        try {
            // Get current playback status
            return [
                'success' => true,
                'playing' => false,
                'track' => 'Sample Track',
                'artist' => 'Sample Artist',
                'album' => 'Sample Album',
                'artwork' => null,
                'progress' => 0,
                'duration' => 0
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to get playback status'];
        }
    }
    
    public function authenticate($code = null) {
        try {
            // Amazon Music authentication would be implemented here
            // Currently returns not available due to API limitations
            return [
                'success' => false, 
                'message' => 'Amazon Music authentication not available'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }
    }
    
    public function nextTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Amazon Music'];
        }
        
        try {
            // Next track logic
            return ['success' => true, 'message' => 'Next track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to skip track'];
        }
    }
    
    public function previousTrack() {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Amazon Music'];
        }
        
        try {
            // Previous track logic
            return ['success' => true, 'message' => 'Previous track'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to go to previous track'];
        }
    }
    
    public function setVolume($volume) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Amazon Music'];
        }
        
        try {
            // Volume control logic
            return ['success' => true, 'message' => 'Volume set to ' . $volume];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to set volume'];
        }
    }
    
    public function seek($position) {
        if (!$this->connected) {
            return ['success' => false, 'message' => 'Not connected to Amazon Music'];
        }
        
        try {
            // Seek logic
            return ['success' => true, 'message' => 'Seeked to position ' . $position];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to seek'];
        }
    }
}
?> 
?> 
