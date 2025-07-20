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
$spotifyPlatform = $platformManager->getPlatform('spotify');

// Handle authentication callback
if (isset($_GET['code'])) {
    $result = $spotifyPlatform->authenticate($_GET['code']);
    if ($result['success']) {
        $success_message = $lang->get('spotify_connected_successfully');
    } else {
        $error_message = $result['message'];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $playlist_id = $_POST['playlist_id'] ?? null;
    
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
        default:
            $result = ['success' => false, 'message' => 'Invalid action'];
    }
    
    echo json_encode($result);
    exit;
}

// Get platform status
$status = $spotifyPlatform->getStatus();
$playlists = $status['connected'] ? $spotifyPlatform->getPlaylists() : [];

// Get error/success messages
$error_message = $error_message ?? $_GET['error'] ?? '';
$success_message = $success_message ?? $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('spotify'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Spotify Integration und Playlist-Management' : 'Spotify Integration and Playlist Management'; ?>">
    
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
            <div class="alert alert-success mb-6">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error mb-6">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Connection Status -->
        <?php if (!$status['connected']): ?>
            <div class="card mb-6">
                <div class="card-body text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-spotify text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <?php echo $lang->get('connect_spotify'); ?>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Verbinden Sie Ihr Spotify-Konto, um auf Ihre Playlists zuzugreifen und Musik abzuspielen.'
                            : 'Connect your Spotify account to access your playlists and play music.'; ?>
                    </p>
                    <a href="https://accounts.spotify.com/authorize?client_id=4078ed7dc1264188a9e83dfd459a94a0&response_type=code&redirect_uri=<?php echo urlencode('https://playlist-manager.de/spotify_play.php'); ?>&scope=user-read-private%20user-read-email%20playlist-read-private%20playlist-modify-public%20playlist-modify-private%20user-read-playback-state%20user-modify-playback-state" 
                       class="btn btn-primary">
                        <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('connect_spotify'); ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Connected User Info -->
            <div class="card mb-6">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-spotify text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($status['user']); ?></h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $status['premium'] ? 'Spotify Premium' : 'Spotify Free'; ?>
                                    <?php if ($status['email']): ?>
                                        • <?php echo htmlspecialchars($status['email']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
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
                                <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('spotify_player'); ?>
                            </h2>
                            <p class="text-gray-600 mt-1">
                                <?php echo $lang->getCurrentLanguage() === 'de' 
                                    ? 'Steuern Sie Ihre Spotify-Wiedergabe direkt von hier aus'
                                    : 'Control your Spotify playback directly from here'; ?>
                            </p>
                        </div>
                        <div class="card-body">
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <button id="play-btn" class="btn btn-primary" onclick="startPlayback()">
                                    <i class="fas fa-play mr-2"></i><?php echo $lang->get('play'); ?>
                                </button>
                                <button id="pause-btn" class="btn btn-secondary" onclick="stopPlayback()">
                                    <i class="fas fa-pause mr-2"></i><?php echo $lang->get('pause'); ?>
                                </button>
                                <button id="next-btn" class="btn btn-secondary" onclick="nextTrack()">
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
                                <div class="bg-green-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                        <i class="fas fa-plus mr-2 text-green-600"></i><?php echo $lang->get('create_playlist'); ?>
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
                                            ? 'Importieren Sie eine bestehende Playlist von Spotify'
                                            : 'Import an existing playlist from Spotify'; ?>
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
                                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500"
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
                                <button class="btn btn-secondary w-full" onclick="openSpotify()">
                                    <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('open_spotify'); ?>
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
    
    // Start playback
    function startPlayback() {
        const playlistId = document.getElementById('playlist-select').value;
        
        if (!playlistId) {
            alert('<?php echo $lang->get("please_select_playlist"); ?>');
            return;
        }
        
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=start_playback&playlist_id=${playlistId}`
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
            console.error('Error starting playback:', error);
            alert('<?php echo $lang->get("playback_error"); ?>');
        });
    }
    
    // Stop playback
    function stopPlayback() {
        fetch('spotify_play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=stop_playback'
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
        fetch('spotify_play.php', {
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
                        <div class="bg-green-600 h-2 rounded-full" style="width: ${(playbackStatus.progress / playbackStatus.duration) * 100}%"></div>
                    </div>
                </div>
            `;
        } else {
            trackInfo.style.display = 'none';
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
    
    // Update status periodically
    setInterval(updatePlaybackStatus, 5000);
    </script>
</body>
</html>