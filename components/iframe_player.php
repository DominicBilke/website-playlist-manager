<?php
/**
 * Iframe Player Component
 * Provides iframe-based players for different music platforms
 */

class IframePlayer {
    private $platform;
    private $lang;
    
    public function __construct($platform, $lang) {
        $this->platform = $platform;
        $this->lang = $lang;
    }
    
    /**
     * Get iframe player HTML for the specified platform
     */
    public function getPlayer($playlistId = null, $trackId = null, $autoplay = false) {
        switch ($this->platform) {
            case 'spotify':
                return $this->getSpotifyPlayer($playlistId, $trackId, $autoplay);
            case 'apple_music':
                return $this->getAppleMusicPlayer($playlistId, $trackId, $autoplay);
            case 'youtube':
                return $this->getYouTubePlayer($playlistId, $trackId, $autoplay);
            case 'amazon':
                return $this->getAmazonPlayer($playlistId, $trackId, $autoplay);
            default:
                return $this->getDefaultPlayer();
        }
    }
    
    /**
     * Get Spotify iframe player
     */
    private function getSpotifyPlayer($playlistId = null, $trackId = null, $autoplay = false) {
        $src = 'https://open.spotify.com/embed/';
        
        if ($trackId) {
            $src .= 'track/' . $trackId;
        } elseif ($playlistId) {
            $src .= 'playlist/' . $playlistId;
        } else {
            $src .= 'playlist/37i9dQZF1DXcBWIGoYBM5M'; // Default playlist
        }
        
        $src .= '?utm_source=generator';
        if ($autoplay) {
            $src .= '&autoplay=1';
        }
        
        return '<iframe 
            style="border-radius:12px" 
            src="' . $src . '" 
            width="100%" 
            height="352" 
            frameBorder="0" 
            allowfullscreen="" 
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
            loading="lazy">
        </iframe>';
    }
    
    /**
     * Get Apple Music iframe player
     */
    private function getAppleMusicPlayer($playlistId = null, $trackId = null, $autoplay = false) {
        $src = 'https://embed.music.apple.com/';
        
        if ($trackId) {
            $src .= 'song/' . $trackId;
        } elseif ($playlistId) {
            $src .= 'playlist/' . $playlistId;
        } else {
            $src .= 'playlist/pl.u-2aoqJzLfzJN5r'; // Default playlist
        }
        
        $src .= '?app=music';
        if ($autoplay) {
            $src .= '&autoplay=1';
        }
        
        return '<iframe 
            allow="autoplay *; encrypted-media *; fullscreen *; clipboard-write" 
            sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation" 
            src="' . $src . '" 
            style="width:100%; max-width:660px; overflow:hidden; background:transparent;" 
            height="450">
        </iframe>';
    }
    
    /**
     * Get YouTube iframe player
     */
    private function getYouTubePlayer($playlistId = null, $trackId = null, $autoplay = false) {
        $src = 'https://www.youtube.com/embed/';
        
        if ($trackId) {
            $src .= $trackId;
        } elseif ($playlistId) {
            $src .= 'videoseries?list=' . $playlistId;
        } else {
            $src .= 'dQw4w9WgXcQ'; // Default video
        }
        
        $params = [];
        if ($autoplay) {
            $params[] = 'autoplay=1';
        }
        if ($playlistId) {
            $params[] = 'loop=1';
        }
        $params[] = 'rel=0';
        $params[] = 'modestbranding=1';
        
        if (!empty($params)) {
            $src .= '?' . implode('&', $params);
        }
        
        return '<iframe 
            width="100%" 
            height="315" 
            src="' . $src . '" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            allowfullscreen>
        </iframe>';
    }
    
    /**
     * Get Amazon Music iframe player
     */
    private function getAmazonPlayer($playlistId = null, $trackId = null, $autoplay = false) {
        // Amazon Music doesn't provide public iframe embeds like other platforms
        // This is a fallback that shows a message and link to Amazon Music
        $message = $this->lang->getCurrentLanguage() === 'de' 
            ? 'Amazon Music Player wird in einem neuen Tab geöffnet'
            : 'Amazon Music Player will open in a new tab';
            
        return '<div class="bg-gray-100 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fab fa-amazon text-orange-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Amazon Music</h3>
            <p class="text-gray-600 mb-4">' . $message . '</p>
            <a href="https://music.amazon.com" target="_blank" class="btn btn-primary">
                <i class="fab fa-amazon mr-2"></i>' . $this->lang->get('open_amazon_music') . '
            </a>
        </div>';
    }
    
    /**
     * Get default player when platform is not supported
     */
    private function getDefaultPlayer() {
        return '<div class="bg-gray-100 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-music text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">' . $this->lang->get('player_not_available') . '</h3>
            <p class="text-gray-600">' . $this->lang->get('iframe_player_not_supported') . '</p>
        </div>';
    }
    
    /**
     * Get player controls HTML
     */
    public function getPlayerControls($playlistId = null, $trackId = null) {
        $platformName = $this->getPlatformDisplayName();
        
        return '<div class="player-controls bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="' . $this->getPlatformIcon() . ' mr-2"></i>' . $platformName . ' ' . $this->lang->get('player') . '
                </h3>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleIframePlayer(\'' . $this->platform . '\')" class="btn btn-secondary btn-sm">
                        <i class="fas fa-expand mr-1"></i>' . $this->lang->get('toggle_player') . '
                    </button>
                    <button onclick="refreshIframePlayer(\'' . $this->platform . '\')" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync-alt mr-1"></i>' . $this->lang->get('refresh') . '
                    </button>
                </div>
            </div>
            <div id="iframe-player-' . $this->platform . '" class="iframe-player-container">
                ' . $this->getPlayer($playlistId, $trackId) . '
            </div>
        </div>';
    }
    
    /**
     * Get platform display name
     */
    private function getPlatformDisplayName() {
        switch ($this->platform) {
            case 'spotify':
                return 'Spotify';
            case 'apple_music':
                return 'Apple Music';
            case 'youtube':
                return 'YouTube Music';
            case 'amazon':
                return 'Amazon Music';
            default:
                return ucfirst($this->platform);
        }
    }
    
    /**
     * Get platform icon class
     */
    private function getPlatformIcon() {
        switch ($this->platform) {
            case 'spotify':
                return 'fab fa-spotify text-green-600';
            case 'apple_music':
                return 'fab fa-apple text-pink-600';
            case 'youtube':
                return 'fab fa-youtube text-red-600';
            case 'amazon':
                return 'fab fa-amazon text-orange-600';
            default:
                return 'fas fa-music text-gray-600';
        }
    }
    
    /**
     * Get JavaScript for iframe player controls
     */
    public function getPlayerScript() {
        return '<script>
        // Iframe Player Controls
        function toggleIframePlayer(platform) {
            const container = document.getElementById("iframe-player-" + platform);
            if (container.style.display === "none") {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        }
        
        function refreshIframePlayer(platform) {
            const container = document.getElementById("iframe-player-" + platform);
            const iframe = container.querySelector("iframe");
            if (iframe) {
                const currentSrc = iframe.src;
                iframe.src = "";
                setTimeout(() => {
                    iframe.src = currentSrc;
                }, 100);
            }
        }
        
        function loadIframePlayer(platform, playlistId, trackId, autoplay = false) {
            const container = document.getElementById("iframe-player-" + platform);
            if (container) {
                // This would typically make an AJAX call to get the new iframe HTML
                // For now, we\'ll just refresh the current player
                refreshIframePlayer(platform);
            }
        }
        </script>';
    }
}
?> 