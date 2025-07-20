<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';
require_once 'script/auth.php';
require_once 'script/PlatformManager.php';

// Initialize auth system
$auth = new Auth($pdo, $lang);

// Require authentication
$auth->requireAuth();

// Get current user
$currentUser = $auth->getCurrentUser();

// Initialize platform manager
$platformManager = new PlatformManager($pdo, $lang, $currentUser['id']);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $platform = $_POST['platform'] ?? '';
    $playlist_id = $_POST['playlist_id'] ?? null;
    
    switch ($action) {
        case 'start_playback':
            $result = $platformManager->startPlayback($platform, $playlist_id);
            break;
        case 'stop_playback':
            $result = $platformManager->stopPlayback($platform);
            break;
        case 'get_status':
            $result = $platformManager->getPlaybackStatus($platform);
            break;
        case 'get_playlists':
            $platformInstance = $platformManager->getPlatform($platform);
            $result = $platformInstance ? $platformInstance->getPlaylists() : [];
            break;
        default:
            $result = ['success' => false, 'message' => 'Invalid action'];
    }
    
    echo json_encode($result);
    exit;
}

// Get platform statuses
$platformStatuses = $platformManager->getAllPlatformStatuses();
$allPlaylists = $platformManager->getAllPlaylists();

// Get error/success messages
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('music_player'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Einheitlicher Musik-Player für alle Plattformen' : 'Unified music player for all platforms'; ?>">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/main.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Modern CSS Framework -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'components/header.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-music text-purple-600 mr-3"></i><?php echo $lang->get('music_player'); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Einheitlicher Player für alle Musikplattformen'
                            : 'Unified player for all music platforms'; ?>
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500"><?php echo $lang->get('welcome'); ?></p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['login']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $lang->get('team'); ?>: <?php echo htmlspecialchars($currentUser['team']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-music text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error mb-6">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Platform Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Spotify -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-spotify text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Spotify</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['spotify']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['spotify']['connected'] ? 'bg-green-500' : 'bg-gray-300'; ?>"></div>
                    </div>
                    <div class="space-y-2">
                        <?php if ($platformStatuses['spotify']['connected']): ?>
                            <button class="btn btn-primary btn-sm w-full" onclick="openPlatform('spotify')">
                                <i class="fas fa-play mr-2"></i><?php echo $lang->get('open_player'); ?>
                            </button>
                        <?php else: ?>
                            <a href="spotify_play.php" class="btn btn-secondary btn-sm w-full">
                                <i class="fas fa-link mr-2"></i><?php echo $lang->get('connect'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Apple Music -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-apple text-pink-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Apple Music</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['apple_music']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['apple_music']['connected'] ? 'bg-green-500' : 'bg-gray-300'; ?>"></div>
                    </div>
                    <div class="space-y-2">
                        <?php if ($platformStatuses['apple_music']['connected']): ?>
                            <button class="btn btn-primary btn-sm w-full" onclick="openPlatform('apple_music')">
                                <i class="fas fa-play mr-2"></i><?php echo $lang->get('open_player'); ?>
                            </button>
                        <?php else: ?>
                            <a href="applemusic_play.php" class="btn btn-secondary btn-sm w-full">
                                <i class="fas fa-link mr-2"></i><?php echo $lang->get('connect'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- YouTube Music -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-youtube text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">YouTube Music</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['youtube']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['youtube']['connected'] ? 'bg-green-500' : 'bg-gray-300'; ?>"></div>
                    </div>
                    <div class="space-y-2">
                        <?php if ($platformStatuses['youtube']['connected']): ?>
                            <button class="btn btn-primary btn-sm w-full" onclick="openPlatform('youtube')">
                                <i class="fas fa-play mr-2"></i><?php echo $lang->get('open_player'); ?>
                            </button>
                        <?php else: ?>
                            <a href="youtube_play.php" class="btn btn-secondary btn-sm w-full">
                                <i class="fas fa-link mr-2"></i><?php echo $lang->get('connect'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Amazon Music -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-amazon text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Amazon Music</h3>
                                <p class="text-sm text-gray-500">Manual Control</p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    </div>
                    <div class="space-y-2">
                        <button class="btn btn-warning btn-sm w-full" onclick="openPlatform('amazon')">
                            <i class="fas fa-external-link-alt mr-2"></i><?php echo $lang->get('open_external'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Player Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Player Controls -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-play-circle mr-2"></i><?php echo $lang->get('player_controls'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Platform Selection -->
                        <div class="mb-6">
                            <label class="form-label"><?php echo $lang->get('select_platform'); ?></label>
                            <select id="platform-select" class="form-input" onchange="loadPlaylists()">
                                <option value=""><?php echo $lang->get('choose_platform'); ?></option>
                                <option value="spotify" <?php echo $platformStatuses['spotify']['connected'] ? '' : 'disabled'; ?>>Spotify</option>
                                <option value="apple_music" <?php echo $platformStatuses['apple_music']['connected'] ? '' : 'disabled'; ?>>Apple Music</option>
                                <option value="youtube" <?php echo $platformStatuses['youtube']['connected'] ? '' : 'disabled'; ?>>YouTube Music</option>
                                <option value="amazon">Amazon Music (Manual)</option>
                            </select>
                        </div>

                        <!-- Playlist Selection -->
                        <div class="mb-6" id="playlist-section" style="display: none;">
                            <label class="form-label"><?php echo $lang->get('select_playlist'); ?></label>
                            <select id="playlist-select" class="form-input">
                                <option value=""><?php echo $lang->get('choose_playlist'); ?></option>
                            </select>
                        </div>

                        <!-- Player Controls -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <button id="play-btn" class="btn btn-primary" onclick="startPlayback()" disabled>
                                <i class="fas fa-play mr-2"></i><?php echo $lang->get('play'); ?>
                            </button>
                            <button id="pause-btn" class="btn btn-secondary" onclick="stopPlayback()" disabled>
                                <i class="fas fa-pause mr-2"></i><?php echo $lang->get('pause'); ?>
                            </button>
                            <button id="next-btn" class="btn btn-secondary" onclick="nextTrack()" disabled>
                                <i class="fas fa-forward mr-2"></i><?php echo $lang->get('next'); ?>
                            </button>
                        </div>

                        <!-- Current Track Info -->
                        <div id="track-info" class="bg-gray-50 rounded-lg p-4" style="display: none;">
                            <h3 class="font-semibold text-gray-900 mb-2"><?php echo $lang->get('now_playing'); ?></h3>
                            <div id="track-details" class="text-gray-600">
                                <!-- Track details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Playlist Management -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-list mr-2"></i><?php echo $lang->get('playlist_management'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <!-- Create Playlist -->
                            <div class="bg-green-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <i class="fas fa-plus mr-2 text-green-600"></i><?php echo $lang->get('create_playlist'); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3">
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Erstellen Sie eine neue Playlist basierend auf Ihren Top-Tracks'
                                        : 'Create a new playlist based on your top tracks'; ?>
                                </p>
                                <button class="btn btn-success btn-sm w-full" onclick="createPlaylist()">
                                    <i class="fas fa-magic mr-2"></i><?php echo $lang->get('generate_playlist'); ?>
                                </button>
                            </div>

                            <!-- Import Playlist -->
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <i class="fas fa-download mr-2 text-blue-600"></i><?php echo $lang->get('import_playlist'); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3">
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Importieren Sie eine bestehende Playlist'
                                        : 'Import an existing playlist'; ?>
                                </p>
                                <button class="btn btn-primary btn-sm w-full" onclick="importPlaylist()">
                                    <i class="fas fa-link mr-2"></i><?php echo $lang->get('import'); ?>
                                </button>
                            </div>

                            <!-- Settings -->
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <i class="fas fa-cog mr-2 text-purple-600"></i><?php echo $lang->get('settings'); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3">
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Konfigurieren Sie Ihre Wiedergabeeinstellungen'
                                        : 'Configure your playback settings'; ?>
                                </p>
                                <a href="editaccount.php" class="btn btn-secondary btn-sm w-full">
                                    <i class="fas fa-cog mr-2"></i><?php echo $lang->get('open_settings'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <script>
    let currentPlatform = '';
    let currentPlaylist = '';
    let playbackStatus = null;
    
    // Load playlists when platform is selected
    function loadPlaylists() {
        const platform = document.getElementById('platform-select').value;
        const playlistSection = document.getElementById('playlist-section');
        const playlistSelect = document.getElementById('playlist-select');
        
        if (!platform) {
            playlistSection.style.display = 'none';
            return;
        }
        
        currentPlatform = platform;
        
        if (platform === 'amazon') {
            // Amazon Music - show manual control message
            playlistSection.style.display = 'block';
            playlistSelect.innerHTML = '<option value="">Amazon Music requires manual control</option>';
            return;
        }
        
        // Load playlists from server
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_playlists&platform=${platform}`
        })
        .then(response => response.json())
        .then(data => {
            playlistSection.style.display = 'block';
            playlistSelect.innerHTML = '<option value=""><?php echo $lang->get("choose_playlist"); ?></option>';
            
            if (Array.isArray(data)) {
                data.forEach(playlist => {
                    const option = document.createElement('option');
                    option.value = playlist.id;
                    option.textContent = `${playlist.name} (${playlist.tracks} tracks)`;
                    playlistSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading playlists:', error);
            playlistSection.style.display = 'block';
            playlistSelect.innerHTML = '<option value="">Error loading playlists</option>';
        });
    }
    
    // Start playback
    function startPlayback() {
        const platform = document.getElementById('platform-select').value;
        const playlist = document.getElementById('playlist-select').value;
        
        if (!platform) {
            alert('<?php echo $lang->get("please_select_platform"); ?>');
            return;
        }
        
        if (platform === 'amazon') {
            // Open Amazon Music in new window
            window.open('https://music.amazon.com', '_blank');
            return;
        }
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=start_playback&platform=${platform}&playlist_id=${playlist}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
                enableControls();
            } else {
                alert(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            console.error('Error starting playback:', error);
            alert('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Stop playback
    function stopPlayback() {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=stop_playback&platform=${currentPlatform}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
            } else {
                alert(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            console.error('Error stopping playback:', error);
            alert('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Update playback status
    function updatePlaybackStatus() {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_status&platform=${currentPlatform}`
        })
        .then(response => response.json())
        .then(data => {
            playbackStatus = data;
            updateTrackInfo();
        })
        .catch(error => {
            console.error('Error updating status:', error);
        });
    }
    
    // Update track information display
    function updateTrackInfo() {
        const trackInfo = document.getElementById('track-info');
        const trackDetails = document.getElementById('track-details');
        
        if (playbackStatus && playbackStatus.success && playbackStatus.playing) {
            trackInfo.style.display = 'block';
            trackDetails.innerHTML = `
                <p><strong>${playbackStatus.track || 'Unknown Track'}</strong></p>
                <p>${playbackStatus.artist || 'Unknown Artist'}</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: ${(playbackStatus.progress / playbackStatus.duration) * 100}%"></div>
                    </div>
                </div>
            `;
        } else {
            trackInfo.style.display = 'none';
        }
    }
    
    // Enable player controls
    function enableControls() {
        document.getElementById('play-btn').disabled = false;
        document.getElementById('pause-btn').disabled = false;
        document.getElementById('next-btn').disabled = false;
    }
    
    // Open platform-specific page
    function openPlatform(platform) {
        const urls = {
            'spotify': 'spotify_play.php',
            'apple_music': 'applemusic_play.php',
            'youtube': 'youtube_play.php',
            'amazon': 'amazon_play.php'
        };
        
        if (urls[platform]) {
            window.location.href = urls[platform];
        }
    }
    
    // Create playlist
    function createPlaylist() {
        alert('<?php echo $lang->get("create_playlist_feature"); ?>');
    }
    
    // Import playlist
    function importPlaylist() {
        alert('<?php echo $lang->get("import_playlist_feature"); ?>');
    }
    
    // Next track
    function nextTrack() {
        alert('<?php echo $lang->get("next_track_feature"); ?>');
    }
    
    // Update status periodically
    setInterval(updatePlaybackStatus, 5000);
    </script>
</body>
</html> 