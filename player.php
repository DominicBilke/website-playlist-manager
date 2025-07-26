<?php
// Use new include system
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}
require_once 'script/includes.php';

// Initialize language manager and authentication
$lang = init_app();
$auth = init_auth();

// Require authentication
require_auth();

// Get current user
$currentUser = get_current_user_info();

// Initialize platform manager
$platformManager = init_platform_manager($currentUser['id']);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $platform = $_POST['platform'] ?? '';
    $playlist_id = $_POST['playlist_id'] ?? null;
    $track_uri = $_POST['track_uri'] ?? null;
    $volume = $_POST['volume'] ?? 50;
    $position = $_POST['position'] ?? 0;
    
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
        case 'next_track':
            $platformInstance = $platformManager->getPlatform($platform);
            $result = $platformInstance ? $platformInstance->nextTrack() : ['success' => false, 'message' => 'Platform not available'];
            break;
        case 'previous_track':
            $platformInstance = $platformManager->getPlatform($platform);
            $result = $platformInstance ? $platformInstance->previousTrack() : ['success' => false, 'message' => 'Platform not available'];
            break;
        case 'set_volume':
            $platformInstance = $platformManager->getPlatform($platform);
            $result = $platformInstance ? $platformInstance->setVolume($volume) : ['success' => false, 'message' => 'Platform not available'];
            break;
        case 'seek':
            $platformInstance = $platformManager->getPlatform($platform);
            $result = $platformInstance ? $platformInstance->seek($position) : ['success' => false, 'message' => 'Platform not available'];
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
    
    <style>
        .platform-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .platform-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .platform-card.connected {
            border-color: #10b981;
        }
        
        .platform-card.disconnected {
            border-color: #e5e7eb;
        }
        
        .platform-card.manual {
            border-color: #f59e0b;
        }
        
        .player-progress {
            background: linear-gradient(90deg, #8b5cf6 0%, #a855f7 100%);
            transition: width 0.3s ease;
        }
        
        .volume-slider {
            -webkit-appearance: none;
            appearance: none;
            height: 4px;
            border-radius: 2px;
            background: #e5e7eb;
            outline: none;
        }
        
        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #8b5cf6;
            cursor: pointer;
        }
        
        .volume-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #8b5cf6;
            cursor: pointer;
            border: none;
        }
        
        .control-btn {
            transition: all 0.2s ease;
        }
        
        .control-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }
        
        .control-btn:active {
            transform: scale(0.95);
        }
        
        .status-indicator {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .track-artwork {
            transition: transform 0.2s ease;
        }
        
        .track-artwork:hover {
            transform: scale(1.05);
        }
        
        .platform-icon {
            transition: all 0.3s ease;
        }
        
        .platform-card:hover .platform-icon {
            transform: scale(1.1);
        }
    </style>
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
            <div class="alert alert-error mb-6 animate-fade-in">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6 animate-fade-in">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Platform Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Spotify -->
            <div class="platform-card card <?php echo $platformStatuses['spotify']['connected'] ? 'connected' : 'disconnected'; ?>">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-spotify text-green-600 text-xl platform-icon"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Spotify</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['spotify']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['spotify']['connected'] ? 'bg-green-500 status-indicator' : 'bg-gray-300'; ?>"></div>
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
            <div class="platform-card card <?php echo $platformStatuses['apple_music']['connected'] ? 'connected' : 'disconnected'; ?>">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-apple text-pink-600 text-xl platform-icon"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Apple Music</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['apple_music']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['apple_music']['connected'] ? 'bg-green-500 status-indicator' : 'bg-gray-300'; ?>"></div>
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
            <div class="platform-card card <?php echo $platformStatuses['youtube']['connected'] ? 'connected' : 'disconnected'; ?>">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-youtube text-red-600 text-xl platform-icon"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">YouTube Music</h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $platformStatuses['youtube']['connected'] ? 'Connected' : 'Not Connected'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full <?php echo $platformStatuses['youtube']['connected'] ? 'bg-green-500 status-indicator' : 'bg-gray-300'; ?>"></div>
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
            <div class="platform-card card manual">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fab fa-amazon text-orange-600 text-xl platform-icon"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-900">Amazon Music</h3>
                                <p class="text-sm text-gray-500">Manual Control</p>
                            </div>
                        </div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500 status-indicator"></div>
                    </div>
                    <div class="space-y-2">
                        <button class="btn btn-warning btn-sm w-full" onclick="openPlatform('amazon')">
                            <i class="fas fa-external-link-alt mr-2"></i><?php echo $lang->get('open_external'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Check if any platforms are connected
        $anyConnected = false;
        foreach ($platformStatuses as $platform => $status) {
            if ($status['connected']) {
                $anyConnected = true;
                break;
            }
        }
        ?>

        <?php if (!$anyConnected): ?>
        <!-- No Platforms Connected State -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Welcome Card -->
            <div class="card animate-fade-in">
                <div class="card-body text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-music text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">
                        <?php echo $lang->getCurrentLanguage() === 'de' ? 'Willkommen beim Musik-Player' : 'Welcome to Music Player'; ?>
                    </h3>
                    <p class="text-gray-600 mb-6 text-lg">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Verbinden Sie Ihre Musikplattformen, um mit dem Abspielen zu beginnen.'
                            : 'Connect your music platforms to start playing.'; ?>
                    </p>
                    <div class="space-y-3">
                        <a href="spotify_play.php" class="btn btn-primary btn-lg w-full">
                            <i class="fab fa-spotify mr-3"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify verbinden' : 'Connect Spotify'; ?>
                        </a>
                        <p class="text-sm text-gray-500">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Weitere Plattformen werden bald verfügbar sein'
                                : 'More platforms coming soon'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features Overview -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-star mr-2 text-yellow-500"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verfügbare Features' : 'Available Features'; ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-play text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Einheitliche Steuerung' : 'Unified Control'; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Steuern Sie alle Ihre Musikplattformen von einem Ort aus' : 'Control all your music platforms from one place'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-list text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlist-Management' : 'Playlist Management'; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verwalten Sie Ihre Playlists über alle Plattformen hinweg' : 'Manage your playlists across all platforms'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Analytics' : 'Analytics'; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verfolgen Sie Ihre Hörgewohnheiten und Statistiken' : 'Track your listening habits and statistics'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-magic text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'KI-Playlists' : 'AI Playlists'; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Automatisch generierte Playlists basierend auf Ihren Vorlieben' : 'Automatically generated playlists based on your preferences'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-bolt mr-2"></i><?php echo $lang->get('quick_actions'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="spotify_play.php" class="btn btn-secondary w-full">
                            <i class="fab fa-spotify mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify einrichten' : 'Setup Spotify'; ?>
                        </a>
                        <a href="editaccount.php" class="btn btn-outline w-full">
                            <i class="fas fa-cog mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Einstellungen' : 'Settings'; ?>
                        </a>
                        <a href="https://open.spotify.com" target="_blank" class="btn btn-outline w-full">
                            <i class="fas fa-external-link-alt mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify öffnen' : 'Open Spotify'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
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
                        <!-- Current Track Display -->
                        <div id="current-track" class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-200" style="display: none;">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    <img id="track-artwork" src="" alt="Track Artwork" class="w-full h-full object-cover track-artwork">
                                </div>
                                <div class="flex-1">
                                    <h3 id="track-title" class="font-semibold text-gray-900 text-lg">Track Title</h3>
                                    <p id="track-artist" class="text-gray-600">Artist Name</p>
                                    <p id="track-album" class="text-sm text-gray-500">Album Name</p>
                                </div>
                                <div class="text-right">
                                    <div id="track-duration" class="text-sm text-gray-500">0:00 / 0:00</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <span id="playback-status">Paused</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex items-center space-x-3">
                                    <span id="current-time" class="text-xs text-gray-500 w-8">0:00</span>
                                    <div class="flex-1 relative">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div id="progress-bar" class="player-progress h-2 rounded-full" style="width: 0%"></div>
                                        </div>
                                        <input type="range" id="progress-slider" class="absolute inset-0 w-full h-2 opacity-0 cursor-pointer" min="0" max="100" value="0">
                                    </div>
                                    <span id="total-time" class="text-xs text-gray-500 w-8">0:00</span>
                                </div>
                            </div>
                        </div>

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
                        <div id="playlist-section" class="mb-6" style="display: none;">
                            <label class="form-label"><?php echo $lang->get('select_playlist'); ?></label>
                            <select id="playlist-select" class="form-input">
                                <option value=""><?php echo $lang->get('choose_playlist'); ?></option>
                            </select>
                            <div id="playlist-empty-message" class="text-center text-gray-500 mt-4" style="display:none;"></div>
                            <div id="connect-prompt" class="text-center mt-4" style="display:none;">
                                <button id="connect-btn" class="btn btn-primary"></button>
                            </div>
                        </div>

                        <!-- Player Controls -->
                        <div class="flex items-center justify-center space-x-4 mb-6">
                            <button id="prev-btn" class="control-btn w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600" onclick="previousTrack()" disabled>
                                <i class="fas fa-step-backward"></i>
                            </button>
                            
                            <button id="play-btn" class="control-btn w-16 h-16 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center text-white shadow-lg" onclick="togglePlayback()" disabled>
                                <i class="fas fa-play text-xl"></i>
                            </button>
                            
                            <button id="next-btn" class="control-btn w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600" onclick="nextTrack()" disabled>
                                <i class="fas fa-step-forward"></i>
                            </button>
                        </div>

                        <!-- Volume Control -->
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-volume-down text-gray-500 w-4"></i>
                            <input type="range" id="volume-slider" class="volume-slider flex-1" min="0" max="100" value="50">
                            <i class="fas fa-volume-up text-gray-500 w-4"></i>
                            <span id="volume-value" class="text-sm text-gray-500 w-8">50%</span>
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
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <script>
    let currentPlatform = '';
    let currentPlaylist = '';
    let playbackStatus = null;
    let isPlaying = false;
    let updateInterval = null;
    
    // Initialize player
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
    });
    
    function setupEventListeners() {
        // Volume slider
        const volumeSlider = document.getElementById('volume-slider');
        const volumeValue = document.getElementById('volume-value');
        
        volumeSlider.addEventListener('input', function() {
            const volume = this.value;
            volumeValue.textContent = volume + '%';
            if (currentPlatform) {
                setVolume(volume);
            }
        });
        
        // Progress slider
        const progressSlider = document.getElementById('progress-slider');
        progressSlider.addEventListener('input', function() {
            const progress = this.value;
            document.getElementById('progress-bar').style.width = progress + '%';
        });
        
        progressSlider.addEventListener('change', function() {
            if (currentPlatform) {
                const position = (this.value / 100) * (playbackStatus?.duration || 0);
                seek(position);
            }
        });
    }
    
    // Load playlists when platform is selected
    function loadPlaylists() {
        const platform = document.getElementById('platform-select').value;
        const playlistSection = document.getElementById('playlist-section');
        const playlistSelect = document.getElementById('playlist-select');
        const playlistEmptyMessage = document.getElementById('playlist-empty-message');
        const connectPrompt = document.getElementById('connect-prompt');
        const connectBtn = document.getElementById('connect-btn');

        hideEmptyMessage();
        hideConnectPrompt();

        if (!platform) {
            playlistSection.style.display = 'none';
            disableControls();
            return;
        }
        
        currentPlatform = platform;
        
        if (platform === 'amazon') {
            // Amazon Music - show manual control message
            playlistSection.style.display = 'block';
            playlistSelect.innerHTML = '<option value="">Amazon Music requires manual control</option>';
            showEmptyMessage('Amazon Music requires manual control. Open in a new window.');
            showConnectPrompt(platform);
            enableControls();
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
            
            if (Array.isArray(data) && data.length === 0) {
                showEmptyMessage('No playlists found. Connect your ' + platform.charAt(0).toUpperCase() + platform.slice(1) + ' account to view playlists.');
                showConnectPrompt(platform);
            } else {
                hideEmptyMessage();
                hideConnectPrompt();
                data.forEach(playlist => {
                    const option = document.createElement('option');
                    option.value = playlist.id;
                    option.textContent = `${playlist.name} (${playlist.tracks} tracks)`;
                    playlistSelect.appendChild(option);
                });
            }
            
            enableControls();
            
            // Start status updates
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            updateInterval = setInterval(updatePlaybackStatus, 2000);
        })
        .catch(error => {
            console.error('Error loading playlists:', error);
            showEmptyMessage('Error loading playlists.');
            showConnectPrompt(platform);
            playlistSelect.innerHTML = '<option value="">Error loading playlists</option>';
        });
    }
    
    // Toggle playback (play/pause)
    function togglePlayback() {
        if (!currentPlatform) {
            alert('<?php echo $lang->get("please_select_platform"); ?>');
            return;
        }
        
        if (currentPlatform === 'amazon') {
            // Open Amazon Music in new window
            window.open('https://music.amazon.com', '_blank');
            return;
        }
        
        if (isPlaying) {
            stopPlayback();
        } else {
            startPlayback();
        }
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
        
        showLoading();
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=start_playback&platform=${platform}&playlist_id=${playlist}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                isPlaying = true;
                updatePlaybackStatus();
                updatePlayButton();
            } else {
                showError(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error starting playback:', error);
            showError('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Stop playback
    function stopPlayback() {
        if (!currentPlatform) return;
        
        showLoading();
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=stop_playback&platform=${currentPlatform}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                isPlaying = false;
                updatePlaybackStatus();
                updatePlayButton();
            } else {
                showError(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error stopping playback:', error);
            showError('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Next track
    function nextTrack() {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=next_track&platform=${currentPlatform}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
            } else {
                showError(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            console.error('Error skipping track:', error);
            showError('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Previous track
    function previousTrack() {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=previous_track&platform=${currentPlatform}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
            } else {
                showError(data.message || '<?php echo $lang->get("playback_error"); ?>');
            }
        })
        .catch(error => {
            console.error('Error going to previous track:', error);
            showError('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Set volume
    function setVolume(volume) {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=set_volume&platform=${currentPlatform}&volume=${volume}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.warn('Volume setting failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error setting volume:', error);
        });
    }
    
    // Seek to position
    function seek(position) {
        if (!currentPlatform) return;
        
        fetch('player.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=seek&platform=${currentPlatform}&position=${position}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.warn('Seek failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error seeking:', error);
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
            updatePlayButton();
        })
        .catch(error => {
            console.error('Error updating status:', error);
        });
    }
    
    // Update track information display
    function updateTrackInfo() {
        const currentTrack = document.getElementById('current-track');
        const trackTitle = document.getElementById('track-title');
        const trackArtist = document.getElementById('track-artist');
        const trackAlbum = document.getElementById('track-album');
        const trackArtwork = document.getElementById('track-artwork');
        const trackDuration = document.getElementById('track-duration');
        const playbackStatusText = document.getElementById('playback-status');
        const progressBar = document.getElementById('progress-bar');
        const progressSlider = document.getElementById('progress-slider');
        const currentTime = document.getElementById('current-time');
        const totalTime = document.getElementById('total-time');
        
        if (playbackStatus && playbackStatus.success && playbackStatus.playing) {
            currentTrack.style.display = 'block';
            
            // Update track info
            trackTitle.textContent = playbackStatus.track || 'Unknown Track';
            trackArtist.textContent = playbackStatus.artist || 'Unknown Artist';
            trackAlbum.textContent = playbackStatus.album || 'Unknown Album';
            
            // Update artwork
            if (playbackStatus.artwork) {
                trackArtwork.src = playbackStatus.artwork;
                trackArtwork.style.display = 'block';
            } else {
                trackArtwork.style.display = 'none';
            }
            
            // Update progress
            const progress = playbackStatus.progress || 0;
            const duration = playbackStatus.duration || 0;
            const progressPercent = duration > 0 ? (progress / duration) * 100 : 0;
            
            progressBar.style.width = progressPercent + '%';
            progressSlider.value = progressPercent;
            
            // Update time displays
            currentTime.textContent = formatTime(progress);
            totalTime.textContent = formatTime(duration);
            trackDuration.textContent = `${formatTime(progress)} / ${formatTime(duration)}`;
            
            // Update status
            playbackStatusText.textContent = 'Playing';
            isPlaying = true;
        } else {
            currentTrack.style.display = 'none';
            isPlaying = false;
        }
    }
    
    // Update play button
    function updatePlayButton() {
        const playBtn = document.getElementById('play-btn');
        const playIcon = playBtn.querySelector('i');
        
        if (isPlaying) {
            playIcon.className = 'fas fa-pause text-xl';
        } else {
            playIcon.className = 'fas fa-play text-xl';
        }
    }
    
    // Enable player controls
    function enableControls() {
        document.getElementById('play-btn').disabled = false;
        document.getElementById('prev-btn').disabled = false;
        document.getElementById('next-btn').disabled = false;
    }
    
    // Disable player controls
    function disableControls() {
        document.getElementById('play-btn').disabled = true;
        document.getElementById('prev-btn').disabled = true;
        document.getElementById('next-btn').disabled = true;
    }
    
    // Format time in MM:SS
    function formatTime(ms) {
        if (!ms) return '0:00';
        const seconds = Math.floor(ms / 1000);
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    // Show loading state
    function showLoading() {
        const playBtn = document.getElementById('play-btn');
        const playIcon = playBtn.querySelector('i');
        playIcon.className = 'fas fa-spinner loading-spinner text-xl';
        playBtn.disabled = true;
    }
    
    // Hide loading state
    function hideLoading() {
        const playBtn = document.getElementById('play-btn');
        playBtn.disabled = false;
        updatePlayButton();
    }
    
    // Show error message
    function showError(message) {
        // Create temporary error alert
        const alert = document.createElement('div');
        alert.className = 'alert alert-error mb-6 animate-fade-in';
        alert.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alert, container.firstChild);
        
        // Remove after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
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

    function showConnectPrompt(platform) {
        const prompt = document.getElementById('connect-prompt');
        const btn = document.getElementById('connect-btn');
        prompt.style.display = 'block';
        btn.textContent = 'Connect ' + platform.charAt(0).toUpperCase() + platform.slice(1);
        btn.onclick = function() {
            window.location.href = platform + '_play.php';
        };
    }
    function showEmptyMessage(msg) {
        document.getElementById('playlist-empty-message').textContent = msg;
        document.getElementById('playlist-empty-message').style.display = 'block';
    }
    function hideEmptyMessage() {
        document.getElementById('playlist-empty-message').style.display = 'none';
    }
    function hideConnectPrompt() {
        document.getElementById('connect-prompt').style.display = 'none';
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
    </script>
</body>
</html> 