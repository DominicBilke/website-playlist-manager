<?php
/**
 * Unified Platform Manager
 * Handles all music platforms with consistent API and functionality
 */

// Autoload will be handled by the main initialization script

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
        if (!$platformInstance) {
            return ['connected' => false, 'message' => 'Platform not available'];
        }
        return $platformInstance->getStatus();
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
        
        if (isset($this->tokens['expires_at']) && $this->tokens['expires_at']) {
            return strtotime($this->tokens['expires_at']) > time();
        }
        
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
            return [];
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
            return ['success' => false, 'message' => 'Not connected'];
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
            
            return [
                'success' => true,
                'playing' => $playback->is_playing,
                'track' => $playback->item->name ?? null,
                'artist' => $playback->item->artists[0]->name ?? null,
                'progress' => $playback->progress_ms ?? 0,
                'duration' => $playback->item->duration_ms ?? 0
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function authenticate($code = null) {
        // Spotify authentication logic
        $client_id = '4078ed7dc1264188a9e83dfd459a94a0';
        $client_secret = 'b9e4f66dbe5d4b659bdc635df002ed34';
        $redirect_uri = 'https://playlist-manager.de/spotify_play.php';
        
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
}

/**
 * Apple Music Platform Implementation
 */
class AppleMusicPlatform extends BasePlatform {
    private $api = null;
    
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
            // Apple Music API initialization
            $this->connected = true;
        } catch (Exception $e) {
            $this->connected = false;
        }
    }
    
    public function getStatus() {
        return ['connected' => $this->connected, 'message' => $this->connected ? 'Connected' : 'Not connected'];
    }
    
    public function getPlaylists() {
        // Apple Music playlist retrieval
        return [];
    }
    
    public function startPlayback($playlist_id = null) {
        return ['success' => false, 'message' => 'Apple Music API not fully implemented'];
    }
    
    public function stopPlayback() {
        return ['success' => false, 'message' => 'Apple Music API not fully implemented'];
    }
    
    public function getPlaybackStatus() {
        return ['success' => false, 'message' => 'Apple Music API not fully implemented'];
    }
    
    public function authenticate($code = null) {
        return ['success' => false, 'message' => 'Apple Music authentication not implemented'];
    }
}

/**
 * YouTube Platform Implementation
 */
class YouTubePlatform extends BasePlatform {
    private $api = null;
    
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
            // YouTube API initialization
            $this->connected = true;
        } catch (Exception $e) {
            $this->connected = false;
        }
    }
    
    public function getStatus() {
        return ['connected' => $this->connected, 'message' => $this->connected ? 'Connected' : 'Not connected'];
    }
    
    public function getPlaylists() {
        // YouTube playlist retrieval
        return [];
    }
    
    public function startPlayback($playlist_id = null) {
        return ['success' => false, 'message' => 'YouTube API not fully implemented'];
    }
    
    public function stopPlayback() {
        return ['success' => false, 'message' => 'YouTube API not fully implemented'];
    }
    
    public function getPlaybackStatus() {
        return ['success' => false, 'message' => 'YouTube API not fully implemented'];
    }
    
    public function authenticate($code = null) {
        return ['success' => false, 'message' => 'YouTube authentication not implemented'];
    }
}

/**
 * Amazon Platform Implementation
 */
class AmazonPlatform extends BasePlatform {
    protected function getPlatformName() {
        return 'amazon';
    }
    
    protected function initialize() {
        // Amazon Music has limited API access
        $this->connected = false;
    }
    
    public function getStatus() {
        return [
            'connected' => false, 
            'message' => 'Amazon Music requires manual control due to API limitations'
        ];
    }
    
    public function getPlaylists() {
        return [];
    }
    
    public function startPlayback($playlist_id = null) {
        return [
            'success' => false, 
            'message' => 'Amazon Music requires manual control. Please open Amazon Music in a new window.'
        ];
    }
    
    public function stopPlayback() {
        return [
            'success' => false, 
            'message' => 'Amazon Music requires manual control'
        ];
    }
    
    public function getPlaybackStatus() {
        return [
            'success' => false, 
            'message' => 'Amazon Music status cannot be determined automatically'
        ];
    }
    
    public function authenticate($code = null) {
        return [
            'success' => false, 
            'message' => 'Amazon Music authentication not available'
        ];
    }
}
?> 