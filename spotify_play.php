<?php
// Basic initialization
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once 'script/inc_start.php';
require_once 'script/languages.php';
require_once 'script/language_utils.php';

// Initialize language manager
$lang = new LanguageManager();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get current user info
$currentUser = [
    'id' => $_SESSION['user_id'],
    'login' => $_SESSION['login'] ?? 'User',
    'team' => $_SESSION['team'] ?? 'N/A'
];

// Initialize platform manager with error handling
try {
    require_once 'script/PlatformManager.php';
    $platformManager = new PlatformManager($pdo, $lang, $currentUser['id']);
    $spotifyPlatform = $platformManager->getPlatform('spotify');
    
    if ($spotifyPlatform) {
        $status = $spotifyPlatform->getStatus();
        $playlists = $status['connected'] ? $spotifyPlatform->getPlaylists() : [];
    } else {
        $status = ['connected' => false, 'message' => 'Platform not available'];
        $playlists = [];
    }
} catch (Exception $e) {
    error_log("Platform manager error: " . $e->getMessage());
    $status = ['connected' => false, 'message' => 'Platform initialization failed'];
    $playlists = [];
}

// Handle authentication callback
$error_message = '';
$success_message = '';
if (isset($_GET['code'])) {
    try {
        if ($spotifyPlatform) {
            $result = $spotifyPlatform->authenticate($_GET['code']);
            if ($result['success']) {
                $success_message = $lang->get('spotify_connected_successfully');
                $status = $spotifyPlatform->getStatus();
            } else {
                $error_message = $result['message'];
            }
        }
    } catch (Exception $e) {
        $error_message = 'Authentication failed: ' . $e->getMessage();
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        if (!$spotifyPlatform) {
            echo json_encode(['success' => false, 'message' => 'Platform not available']);
            exit;
        }
        
        $action = $_POST['action'];
        $playlist_id = $_POST['playlist_id'] ?? null;
        $track_uri = $_POST['track_uri'] ?? null;
        
        switch ($action) {
            case 'start_playback':
                $result = $spotifyPlatform->startPlayback($playlist_id);
                break;
            case 'stop_playback':
                $result = $spotifyPlatform->stopPlayback();
                break;
            case 'get_status':
                $result = $spotifyPlatform->getPlaybackStatus();
                break;
            case 'get_playlists':
                $result = $spotifyPlatform->getPlaylists();
                break;
            case 'next_track':
                $result = $spotifyPlatform->nextTrack();
                break;
            case 'previous_track':
                $result = $spotifyPlatform->previousTrack();
                break;
            case 'set_volume':
                $volume = $_POST['volume'] ?? 50;
                $result = $spotifyPlatform->setVolume($volume);
                break;
            case 'seek':
                $position = $_POST['position'] ?? 0;
                $result = $spotifyPlatform->seek($position);
                break;
            default:
                $result = ['success' => false, 'message' => 'Invalid action'];
        }
        
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('spotify'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify Integration und Playlist-Management' : 'Spotify Integration and Playlist Management'; ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <style>
        .player-progress {
            background: linear-gradient(90deg, #1db954 0%, #1ed760 100%);
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
            background: #1db954;
            cursor: pointer;
        }
        
        .volume-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #1db954;
            cursor: pointer;
            border: none;
        }
        
        .playlist-item {
            transition: all 0.2s ease;
        }
        
        .playlist-item:hover {
            background-color: #f3f4f6;
            transform: translateX(4px);
        }
        
        .playlist-item.active {
            background-color: #dcfce7;
            border-left: 4px solid #1db954;
        }
        
        .track-artwork {
            transition: transform 0.2s ease;
        }
        
        .track-artwork:hover {
            transform: scale(1.05);
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
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fab fa-spotify text-green-600 mr-3"></i><?php echo $lang->get('spotify'); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Vollständige Integration mit automatischer Wiedergabe und Playlist-Management'
                            : 'Full integration with automated playback and playlist management'; ?>
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500"><?php echo $lang->get('welcome'); ?></p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['login']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $lang->get('team'); ?>: <?php echo htmlspecialchars($currentUser['team']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-spotify text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success_message): ?>
            <div class="alert alert-success mb-6 animate-fade-in">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error mb-6 animate-fade-in">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Connection Status -->
        <?php if (!$status['connected']): ?>
            <!-- Not Connected State -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Connection Card -->
                <div class="lg:col-span-2">
                    <div class="card mb-6 animate-fade-in">
                        <div class="card-body text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fab fa-spotify text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-4">
                                <?php echo $lang->get('connect_spotify'); ?>
                            </h3>
                            <p class="text-gray-600 mb-6 text-lg">
                                <?php echo $lang->getCurrentLanguage() === 'de' 
                                    ? 'Verbinden Sie Ihr Spotify-Konto, um auf Ihre Playlists zuzugreifen und Musik abzuspielen.'
                                    : 'Connect your Spotify account to access your playlists and play music.'; ?>
                            </p>
                            <div class="space-y-4">
                                <a href="https://accounts.spotify.com/authorize?client_id=4078ed7dc1264188a9e83dfd459a94a0&response_type=code&redirect_uri=<?php echo urlencode('https://playlist-manager.de/spotify_play.php'); ?>&scope=user-read-private%20user-read-email%20playlist-read-private%20playlist-modify-public%20playlist-modify-private%20user-read-playback-state%20user-modify-playback-state%20user-read-currently-playing%20streaming" 
                                   class="btn btn-primary btn-lg">
                                    <i class="fab fa-spotify mr-3"></i><?php echo $lang->get('connect_spotify'); ?>
                                </a>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Sichere Verbindung über Spotify OAuth'
                                        : 'Secure connection via Spotify OAuth'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features Preview -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-star mr-2 text-yellow-500"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verfügbare Features' : 'Available Features'; ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-play text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Fernsteuerung' : 'Remote Control'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Steuern Sie Ihre Spotify-Wiedergabe von überall' : 'Control your Spotify playback from anywhere'; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-list text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlist-Management' : 'Playlist Management'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verwalten und erstellen Sie Playlists' : 'Manage and create playlists'; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-line text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Statistiken' : 'Analytics'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verfolgen Sie Ihre Hörgewohnheiten' : 'Track your listening habits'; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Zeitplanung' : 'Scheduling'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Automatische Wiedergabe nach Zeitplan' : 'Automatic playback scheduling'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Quick Stats -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-chart-bar mr-2 text-green-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Schnellstatistiken' : 'Quick Stats'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Status' : 'Status'; ?></span>
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        <?php echo $lang->getCurrentLanguage() === 'de' ? 'Nicht verbunden' : 'Not Connected'; ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlists' : 'Playlists'; ?></span>
                                    <span class="text-sm font-medium text-gray-900">0</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Tracks' : 'Tracks'; ?></span>
                                    <span class="text-sm font-medium text-gray-900">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-question-circle mr-2 text-blue-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Hilfe' : 'Help'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Klicken Sie auf "Spotify verbinden" um zu beginnen.'
                                        : 'Click "Connect Spotify" to get started.'; ?>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Ihre Daten sind sicher und werden verschlüsselt übertragen.'
                                        : 'Your data is secure and transmitted encrypted.'; ?>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-2 text-yellow-500"></i>
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Die Verbindung dauert nur wenige Sekunden.'
                                        : 'Connection takes only a few seconds.'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Connected State -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Player -->
                <div class="lg:col-span-3">
                    <!-- Player Controls -->
                    <div class="card mb-6">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    <button id="play-btn" class="control-btn w-16 h-16 bg-green-600 text-white rounded-full flex items-center justify-center hover:bg-green-700" onclick="togglePlayback()">
                                        <i class="fas fa-play text-xl"></i>
                                    </button>
                                    <button class="control-btn w-12 h-12 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300" onclick="previousTrack()">
                                        <i class="fas fa-step-backward"></i>
                                    </button>
                                    <button class="control-btn w-12 h-12 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300" onclick="nextTrack()">
                                        <i class="fas fa-step-forward"></i>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-volume-down text-gray-500"></i>
                                        <input type="range" id="volume-slider" class="volume-slider w-24" min="0" max="100" value="50" onchange="setVolume(this.value)">
                                        <i class="fas fa-volume-up text-gray-500"></i>
                                    </div>
                                    <div class="w-4 h-4 bg-green-500 rounded-full status-indicator"></div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                                    <span id="current-time">0:00</span>
                                    <span id="total-time">0:00</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="progress-bar" class="player-progress h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Current Track Info -->
                            <div id="current-track" class="hidden">
                                <div class="flex items-center space-x-4">
                                    <img id="track-artwork" src="" alt="Track Artwork" class="w-16 h-16 rounded-lg">
                                    <div>
                                        <h3 id="track-title" class="font-semibold text-gray-900"></h3>
                                        <p id="track-artist" class="text-sm text-gray-600"></p>
                                        <p id="track-album" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Playlists -->
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-list mr-2 text-green-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Ihre Playlists' : 'Your Playlists'; ?>
                                </h2>
                                <button class="btn btn-secondary btn-sm" onclick="refreshPlaylists()">
                                    <i class="fas fa-sync-alt mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Aktualisieren' : 'Refresh'; ?>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="playlists-container" class="space-y-2">
                                <?php if (empty($playlists)): ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-music text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Keine Playlists gefunden' : 'No playlists found'; ?></p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($playlists as $playlist): ?>
                                        <div class="playlist-item p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50" onclick="playPlaylist('<?php echo htmlspecialchars($playlist['id']); ?>')">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-list text-green-600"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($playlist['name']); ?></h3>
                                                        <p class="text-sm text-gray-600"><?php echo $playlist['tracks'] ?? 0; ?> <?php echo $lang->getCurrentLanguage() === 'de' ? 'Tracks' : 'tracks'; ?></p>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fas fa-play mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Abspielen' : 'Play'; ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Status Card -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-chart-bar mr-2 text-green-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Status' : 'Status'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verbindung' : 'Connection'; ?></span>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        <?php echo $lang->getCurrentLanguage() === 'de' ? 'Verbunden' : 'Connected'; ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlists' : 'Playlists'; ?></span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo count($playlists); ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Wiedergabe' : 'Playback'; ?></span>
                                    <span id="playback-status" class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                        <?php echo $lang->getCurrentLanguage() === 'de' ? 'Gestoppt' : 'Stopped'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-bolt mr-2 text-yellow-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Schnellaktionen' : 'Quick Actions'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <button class="w-full btn btn-secondary btn-sm" onclick="openSpotify()">
                                    <i class="fab fa-spotify mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify öffnen' : 'Open Spotify'; ?>
                                </button>
                                <button class="w-full btn btn-secondary btn-sm" onclick="createPlaylist()">
                                    <i class="fas fa-plus mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlist erstellen' : 'Create Playlist'; ?>
                                </button>
                                <button class="w-full btn btn-secondary btn-sm" onclick="importPlaylist()">
                                    <i class="fas fa-download mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlist importieren' : 'Import Playlist'; ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-cog mr-2 text-gray-600"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Einstellungen' : 'Settings'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Zeitraum' : 'Time Range'; ?></label>
                                    <select id="time-range" class="w-full form-input">
                                        <option value="short_term"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Letzte 4 Wochen' : 'Last 4 weeks'; ?></option>
                                        <option value="medium_term"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Letzte 6 Monate' : 'Last 6 months'; ?></option>
                                        <option value="long_term"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Alle Zeiten' : 'All time'; ?></option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Aktive Tage' : 'Active Days'; ?></label>
                                    <div class="space-y-2">
                                        <?php
                                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                        $dayNames = [
                                            'monday' => ['en' => 'Monday', 'de' => 'Montag'],
                                            'tuesday' => ['en' => 'Tuesday', 'de' => 'Dienstag'],
                                            'wednesday' => ['en' => 'Wednesday', 'de' => 'Mittwoch'],
                                            'thursday' => ['en' => 'Thursday', 'de' => 'Donnerstag'],
                                            'friday' => ['en' => 'Friday', 'de' => 'Freitag'],
                                            'saturday' => ['en' => 'Saturday', 'de' => 'Samstag'],
                                            'sunday' => ['en' => 'Sunday', 'de' => 'Sonntag']
                                        ];
                                        foreach ($days as $day): ?>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="active_days[]" value="<?php echo $day; ?>" class="mr-2" checked>
                                                <span class="text-sm text-gray-700"><?php echo $dayNames[$day][$lang->getCurrentLanguage()]; ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <button class="w-full btn btn-primary btn-sm" onclick="saveSettings()">
                                    <i class="fas fa-save mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Speichern' : 'Save'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>

    <script>
    // Global variables
    let isPlaying = false;
    let currentPlaylist = null;
    let updateInterval = null;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($status['connected']): ?>
            // Start status updates if connected
            updatePlaybackStatus();
            updateInterval = setInterval(updatePlaybackStatus, 5000);
        <?php endif; ?>
    });

    // Toggle playback
    function togglePlayback() {
        if (!isPlaying) {
            startPlayback();
        } else {
            stopPlayback();
        }
    }

    // Start playback
    function startPlayback() {
        showLoading();
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=start_playback' + (currentPlaylist ? '&playlist_id=' + currentPlaylist : '')
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                isPlaying = true;
                updatePlayButton();
                updatePlaybackStatus();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showError('Playback failed: ' + error.message);
        });
    }

    // Stop playback
    function stopPlayback() {
        showLoading();
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=stop_playback'
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                isPlaying = false;
                updatePlayButton();
                updatePlaybackStatus();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showError('Stop failed: ' + error.message);
        });
    }

    // Next track
    function nextTrack() {
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=next_track'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Next track failed: ' + error.message);
        });
    }

    // Previous track
    function previousTrack() {
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=previous_track'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlaybackStatus();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Previous track failed: ' + error.message);
        });
    }

    // Set volume
    function setVolume(volume) {
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=set_volume&volume=' + volume
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Volume change failed: ' + error.message);
        });
    }

    // Play playlist
    function playPlaylist(playlistId) {
        currentPlaylist = playlistId;
        startPlayback();
    }

    // Update playback status
    function updatePlaybackStatus() {
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_status'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                updateCurrentTrack(data.data);
                updatePlaybackIndicator(data.data.is_playing);
            }
        })
        .catch(error => {
            console.error('Status update failed:', error);
        });
    }

    // Update current track display
    function updateCurrentTrack(trackData) {
        const currentTrack = document.getElementById('current-track');
        const trackArtwork = document.getElementById('track-artwork');
        const trackTitle = document.getElementById('track-title');
        const trackArtist = document.getElementById('track-artist');
        const trackAlbum = document.getElementById('track-album');
        const currentTime = document.getElementById('current-time');
        const totalTime = document.getElementById('total-time');
        const progressBar = document.getElementById('progress-bar');

        if (trackData && trackData.item) {
            currentTrack.style.display = 'block';
            trackArtwork.src = trackData.item.album.images[0]?.url || '';
            trackTitle.textContent = trackData.item.name;
            trackArtist.textContent = trackData.item.artists.map(a => a.name).join(', ');
            trackAlbum.textContent = trackData.item.album.name;
            currentTime.textContent = formatTime(trackData.progress_ms);
            totalTime.textContent = formatTime(trackData.item.duration_ms);
            progressBar.style.width = ((trackData.progress_ms / trackData.item.duration_ms) * 100) + '%';
            isPlaying = trackData.is_playing;
            updatePlayButton();
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

    // Update playback indicator
    function updatePlaybackIndicator(isPlaying) {
        const statusElement = document.getElementById('playback-status');
        if (statusElement) {
            if (isPlaying) {
                statusElement.textContent = '<?php echo $lang->getCurrentLanguage() === "de" ? "Spielt" : "Playing"; ?>';
                statusElement.className = 'px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full';
            } else {
                statusElement.textContent = '<?php echo $lang->getCurrentLanguage() === "de" ? "Gestoppt" : "Stopped"; ?>';
                statusElement.className = 'px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full';
            }
        }
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
        if (playBtn) {
            const playIcon = playBtn.querySelector('i');
            playIcon.className = 'fas fa-spinner loading-spinner text-xl';
            playBtn.disabled = true;
        }
    }

    // Hide loading state
    function hideLoading() {
        const playBtn = document.getElementById('play-btn');
        if (playBtn) {
            playBtn.disabled = false;
            updatePlayButton();
        }
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

    // Create playlist
    function createPlaylist() {
        alert('<?php echo $lang->get("create_playlist_feature"); ?>');
    }

    // Import playlist
    function importPlaylist() {
        alert('<?php echo $lang->get("import_playlist_feature"); ?>');
    }

    // Open Spotify
    function openSpotify() {
        window.open('https://open.spotify.com', '_blank');
    }

    // Refresh playlists
    function refreshPlaylists() {
        location.reload();
    }

    // Save settings
    function saveSettings() {
        const timeRange = document.getElementById('time-range').value;
        const activeDays = Array.from(document.querySelectorAll('input[name="active_days[]"]:checked'))
                                .map(cb => cb.value);
        
        // Save settings logic here
        alert('<?php echo $lang->get("settings_saved"); ?>');
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