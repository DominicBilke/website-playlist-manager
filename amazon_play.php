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
    $amazonPlatform = $platformManager->getPlatform('amazon');
    
    if ($amazonPlatform) {
        $status = $amazonPlatform->getStatus();
        $playlists = $status['connected'] ? $amazonPlatform->getPlaylists() : [];
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
        if ($amazonPlatform) {
            $result = $amazonPlatform->authenticate($_GET['code']);
            if ($result['success']) {
                $success_message = $lang->get('amazon_connected_successfully');
                $status = $amazonPlatform->getStatus();
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
        if (!$amazonPlatform) {
            echo json_encode(['success' => false, 'message' => 'Platform not available']);
            exit;
        }
        
        $action = $_POST['action'];
        $playlist_id = $_POST['playlist_id'] ?? null;
        $track_uri = $_POST['track_uri'] ?? null;
        
        switch ($action) {
            case 'start_playback':
                $result = $amazonPlatform->startPlayback($playlist_id);
                break;
            case 'stop_playback':
                $result = $amazonPlatform->stopPlayback();
                break;
            case 'get_status':
                $result = $amazonPlatform->getPlaybackStatus();
                break;
            case 'get_playlists':
                $result = $amazonPlatform->getPlaylists();
                break;
            case 'next_track':
                $result = $amazonPlatform->nextTrack();
                break;
            case 'previous_track':
                $result = $amazonPlatform->previousTrack();
                break;
            case 'set_volume':
                $volume = $_POST['volume'] ?? 50;
                $result = $amazonPlatform->setVolume($volume);
                break;
            case 'seek':
                $position = $_POST['position'] ?? 0;
                $result = $amazonPlatform->seek($position);
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
    <title><?php echo $lang->get('amazon_music'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music Integration und Playlist-Management' : 'Amazon Music Integration and Playlist Management'; ?>">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/main.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    
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
            background: linear-gradient(90deg, #ff9900 0%, #ffb84d 100%);
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
            background: #ff9900;
            cursor: pointer;
        }
        
        .volume-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ff9900;
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
            background-color: #fed7aa;
            border-left: 4px solid #ff9900;
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
                        <i class="fab fa-amazon text-orange-600 mr-3"></i><?php echo $lang->get('amazon_music'); ?>
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
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-amazon text-orange-600 text-xl"></i>
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
                                <i class="fab fa-amazon text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-4">
                                <?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music verbinden' : 'Connect Amazon Music'; ?>
                            </h3>
                            <p class="text-gray-600 mb-6 text-lg">
                                <?php echo $lang->getCurrentLanguage() === 'de' 
                                    ? 'Verbinden Sie Ihr Amazon Music-Konto, um auf Ihre Playlists zuzugreifen und Musik abzuspielen.'
                                    : 'Connect your Amazon Music account to access your playlists and play music.'; ?>
                            </p>
                            <div class="space-y-4">
                                <button class="btn btn-primary btn-lg" onclick="connectAmazonMusic()">
                                    <i class="fab fa-amazon mr-3"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music verbinden' : 'Connect Amazon Music'; ?>
                                </button>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Sichere Verbindung über Amazon OAuth'
                                        : 'Secure connection via Amazon OAuth'; ?>
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
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-play text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Fernsteuerung' : 'Remote Control'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Steuern Sie Ihre Amazon Music-Wiedergabe von überall' : 'Control your Amazon Music playback from anywhere'; ?></p>
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
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Analytics' : 'Analytics'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verfolgen Sie Ihre Hörgewohnheiten' : 'Track your listening habits'; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-magic text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?php echo $lang->getCurrentLanguage() === 'de' ? 'KI-Playlists' : 'AI Playlists'; ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Automatisch generierte Playlists' : 'Automatically generated playlists'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Quick Actions -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-bolt mr-2"></i><?php echo $lang->get('quick_actions'); ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <a href="https://music.amazon.com" target="_blank" class="btn btn-secondary w-full">
                                    <i class="fab fa-amazon mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music öffnen' : 'Open Amazon Music'; ?>
                                </a>
                                <a href="player.php" class="btn btn-outline w-full">
                                    <i class="fas fa-arrow-left mr-2"></i><?php echo $lang->get('back_to_player'); ?>
                                </a>
                                <a href="editaccount.php" class="btn btn-outline w-full">
                                    <i class="fas fa-cog mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Einstellungen' : 'Settings'; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Help & Support -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-question-circle mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Hilfe & Support' : 'Help & Support'; ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <div class="text-sm text-gray-600">
                                    <p class="mb-2"><strong><?php echo $lang->getCurrentLanguage() === 'de' ? 'Benötigte Berechtigungen:' : 'Required permissions:'; ?></strong></p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li><?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlists lesen & bearbeiten' : 'Read & modify playlists'; ?></li>
                                        <li><?php echo $lang->getCurrentLanguage() === 'de' ? 'Wiedergabe steuern' : 'Control playback'; ?></li>
                                        <li><?php echo $lang->getCurrentLanguage() === 'de' ? 'Benutzerdaten lesen' : 'Read user data'; ?></li>
                                    </ul>
                                </div>
                                <div class="pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        <?php echo $lang->getCurrentLanguage() === 'de' 
                                            ? 'Haben Sie Probleme? Kontaktieren Sie den Support.'
                                            : 'Having issues? Contact support.'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Connected User Info -->
            <div class="card mb-6 animate-fade-in">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-amazon text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($status['user'] ?? 'Amazon Music User'); ?></h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $status['premium'] ? 'Amazon Music Unlimited' : 'Amazon Music'; ?>
                                    <?php if ($status['email']): ?>
                                        • <?php echo htmlspecialchars($status['email']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full status-indicator"></div>
                            <span class="text-sm text-green-600 font-medium">Connected</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Player Section -->
                <div class="lg:col-span-2">
                    <!-- Player Card -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fab fa-amazon mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music Player' : 'Amazon Music Player'; ?>
                            </h2>
                            <p class="text-gray-600 mt-1">
                                <?php echo $lang->getCurrentLanguage() === 'de' 
                                    ? 'Steuern Sie Ihre Amazon Music-Wiedergabe direkt von hier aus'
                                    : 'Control your Amazon Music playback directly from here'; ?>
                            </p>
                        </div>
                        <div class="card-body">
                            <!-- Current Track Display -->
                            <div id="current-track" class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg border border-orange-200" style="display: none;">
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

                            <!-- Playlist Selection -->
                            <div class="mb-6">
                                <label class="form-label"><?php echo $lang->get('select_playlist'); ?></label>
                                <select id="playlist-select" class="form-input">
                                    <option value=""><?php echo $lang->get('choose_playlist'); ?></option>
                                    <?php foreach ($playlists as $playlist): ?>
                                        <option value="<?php echo htmlspecialchars($playlist['id']); ?>">
                                            <?php echo htmlspecialchars($playlist['name']); ?> 
                                            (<?php echo $playlist['tracks']; ?> tracks)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Player Controls -->
                            <div class="flex items-center justify-center space-x-4 mb-6">
                                <button id="prev-btn" class="control-btn w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600" onclick="previousTrack()">
                                    <i class="fas fa-step-backward"></i>
                                </button>
                                
                                <button id="play-btn" class="control-btn w-16 h-16 bg-orange-600 hover:bg-orange-700 rounded-full flex items-center justify-center text-white shadow-lg" onclick="togglePlayback()">
                                    <i class="fas fa-play text-xl"></i>
                                </button>
                                
                                <button id="next-btn" class="control-btn w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-600" onclick="nextTrack()">
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

                    <!-- Playlist Management -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-list mr-2"></i><?php echo $lang->get('playlist_management'); ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Create Playlist -->
                                <div class="bg-orange-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                        <i class="fas fa-plus mr-2 text-orange-600"></i><?php echo $lang->get('create_playlist'); ?>
                                    </h3>
                                    <p class="text-gray-600 mb-4">
                                        <?php echo $lang->getCurrentLanguage() === 'de' 
                                            ? 'Erstellen Sie eine neue Playlist basierend auf Ihren Top-Tracks'
                                            : 'Create a new playlist based on your top tracks'; ?>
                                    </p>
                                    <button class="btn btn-success w-full" onclick="createPlaylist()">
                                        <i class="fas fa-magic mr-2"></i><?php echo $lang->get('generate_playlist'); ?>
                                    </button>
                                </div>

                                <!-- Import Playlist -->
                                <div class="bg-blue-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                        <i class="fas fa-download mr-2 text-blue-600"></i><?php echo $lang->get('import_playlist'); ?>
                                    </h3>
                                    <p class="text-gray-600 mb-4">
                                        <?php echo $lang->getCurrentLanguage() === 'de' 
                                            ? 'Importieren Sie eine bestehende Playlist von Amazon Music'
                                            : 'Import an existing playlist from Amazon Music'; ?>
                                    </p>
                                    <button class="btn btn-primary w-full" onclick="importPlaylist()">
                                        <i class="fas fa-link mr-2"></i><?php echo $lang->get('import'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- User Settings -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-cog mr-2"></i><?php echo $lang->get('settings'); ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div>
                                    <label class="form-label"><?php echo $lang->get('time_range'); ?></label>
                                    <select id="time-range" class="form-input">
                                        <option value="short_term"><?php echo $lang->get('last_4_weeks'); ?></option>
                                        <option value="medium_term" selected><?php echo $lang->get('last_6_months'); ?></option>
                                        <option value="long_term"><?php echo $lang->get('all_time'); ?></option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="form-label"><?php echo $lang->get('active_days'); ?></label>
                                    <div class="space-y-2">
                                        <?php
                                        $days = [
                                            '1' => $lang->get('monday'),
                                            '2' => $lang->get('tuesday'),
                                            '3' => $lang->get('wednesday'),
                                            '4' => $lang->get('thursday'),
                                            '5' => $lang->get('friday'),
                                            '6' => $lang->get('saturday'),
                                            '7' => $lang->get('sunday')
                                        ];
                                        foreach ($days as $day_num => $day_name):
                                        ?>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="active_days[]" value="<?php echo $day_num; ?>" 
                                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                                   <?php echo in_array($day_num, [1,2,3,4,5]) ? 'checked' : ''; ?>>
                                            <span class="ml-2 text-sm text-gray-700"><?php echo $day_name; ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <button class="btn btn-primary w-full" onclick="saveSettings()">
                                    <i class="fas fa-save mr-2"></i><?php echo $lang->get('save_settings'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-bolt mr-2"></i><?php echo $lang->get('quick_actions'); ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <button class="btn btn-secondary w-full" onclick="openAmazonMusic()">
                                    <i class="fab fa-amazon mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Amazon Music öffnen' : 'Open Amazon Music'; ?>
                                </button>
                                <button class="btn btn-secondary w-full" onclick="refreshPlaylists()">
                                    <i class="fas fa-sync mr-2"></i><?php echo $lang->get('refresh_playlists'); ?>
                                </button>
                                <a href="player.php" class="btn btn-outline w-full">
                                    <i class="fas fa-arrow-left mr-2"></i><?php echo $lang->get('back_to_player'); ?>
                                </a>
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
    let playbackStatus = null;
    let isPlaying = false;
    let currentPlaylist = '';
    let updateInterval = null;
    
    // Initialize player
    document.addEventListener('DOMContentLoaded', function() {
        initializePlayer();
        setupEventListeners();
    });
    
    function initializePlayer() {
        // Load initial status
        updatePlaybackStatus();
        
        // Start periodic updates
        updateInterval = setInterval(updatePlaybackStatus, 2000);
    }
    
    function setupEventListeners() {
        // Volume slider
        const volumeSlider = document.getElementById('volume-slider');
        const volumeValue = document.getElementById('volume-value');
        
        if (volumeSlider && volumeValue) {
            volumeSlider.addEventListener('input', function() {
                const volume = this.value;
                volumeValue.textContent = volume + '%';
                setVolume(volume);
            });
        }
        
        // Progress slider
        const progressSlider = document.getElementById('progress-slider');
        if (progressSlider) {
            progressSlider.addEventListener('input', function() {
                const progress = this.value;
                document.getElementById('progress-bar').style.width = progress + '%';
            });
            
            progressSlider.addEventListener('change', function() {
                const position = (this.value / 100) * (playbackStatus?.duration || 0);
                seek(position);
            });
        }
    }
    
    // Connect Amazon Music
    function connectAmazonMusic() {
        // Amazon Music uses OAuth for authentication
        // This would typically open the Amazon authorization flow
        alert('<?php echo $lang->getCurrentLanguage() === "de" ? "Amazon Music Verbindung wird implementiert..." : "Amazon Music connection being implemented..."; ?>');
    }
    
    // Toggle playback (play/pause)
    function togglePlayback() {
        if (!currentPlaylist && !playbackStatus?.playing) {
            alert('<?php echo $lang->get("please_select_playlist"); ?>');
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
        const playlistId = document.getElementById('playlist-select').value;
        
        if (!playlistId && !playbackStatus?.playing) {
            alert('<?php echo $lang->get("please_select_playlist"); ?>');
            return;
        }
        
        showLoading();
        
        fetch('amazon_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=start_playback&playlist_id=${playlistId}`
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
        showLoading();
        
        fetch('amazon_play.php', {
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
        fetch('amazon_play.php', {
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
        fetch('amazon_play.php', {
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
        fetch('amazon_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=set_volume&volume=${volume}`
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
        fetch('amazon_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=seek&position=${position}`
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
        fetch('amazon_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_status'
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
    
    // Create playlist
    function createPlaylist() {
        alert('<?php echo $lang->get("create_playlist_feature"); ?>');
    }
    
    // Import playlist
    function importPlaylist() {
        alert('<?php echo $lang->get("import_playlist_feature"); ?>');
    }
    
    // Open Amazon Music
    function openAmazonMusic() {
        window.open('https://music.amazon.com', '_blank');
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