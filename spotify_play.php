<?php
require 'script/inc_start.php';
require 'script/languages.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$user_id = $_SESSION['id'];
$username = $_SESSION['login'];
$team = $_SESSION['team'] ?? 'N/A';
$office = $_SESSION['office'] ?? 'N/A';

// Get user settings
try {
    $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $settings = $stmt->fetch();
} catch (PDOException $e) {
    $settings = null;
}

// Handle form submission for settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $time_range = $_POST['time_range'] ?? 'medium_term';
    $active_days = implode(',', $_POST['active_days'] ?? []);
    
    try {
        if ($settings) {
            $stmt = $pdo->prepare("UPDATE user_settings SET time_range = ?, active_days = ? WHERE user_id = ?");
            $stmt->execute([$time_range, $active_days, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, time_range, active_days) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $time_range, $active_days]);
        }
        $success_message = $lang->get('settings_updated');
    } catch (PDOException $e) {
        $error_message = $lang->get('update_error');
    }
}
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
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="bg-gray-50">
    <!-- Header -->
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
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $lang->get('team'); ?>: <?php echo htmlspecialchars($team); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-spotify text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success mb-6">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error mb-6">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

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
                        <!-- Spotify IFrame Player -->
                        <div id="spotify-player" class="w-full">
                            <div class="bg-gray-100 rounded-lg p-8 text-center">
                                <i class="fab fa-spotify text-6xl text-green-600 mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <?php echo $lang->get('spotify_integration'); ?>
                                </h3>
                                <p class="text-gray-600 mb-6">
                                    <?php echo $lang->getCurrentLanguage() === 'de' 
                                        ? 'Spotify IFrame Player wird hier geladen. Stellen Sie sicher, dass Sie bei Spotify angemeldet sind.'
                                        : 'Spotify IFrame Player will be loaded here. Make sure you are logged into Spotify.'; ?>
                                </p>
                                <div id="spotify-iframe" class="w-full max-w-md mx-auto">
                                    <!-- Spotify IFrame will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Player Controls -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="btn btn-primary" onclick="playSpotify()">
                                <i class="fas fa-play mr-2"></i><?php echo $lang->get('play'); ?>
                            </button>
                            <button class="btn btn-secondary" onclick="pauseSpotify()">
                                <i class="fas fa-pause mr-2"></i><?php echo $lang->get('pause'); ?>
                            </button>
                            <button class="btn btn-secondary" onclick="nextTrack()">
                                <i class="fas fa-forward mr-2"></i><?php echo $lang->get('next'); ?>
                            </button>
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
            <div class="space-y-6">
                <!-- User Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-cog mr-2"></i><?php echo $lang->get('settings'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="space-y-4">
                            <!-- Time Range -->
                            <div class="form-group">
                                <label class="form-label"><?php echo $lang->get('time_range'); ?></label>
                                <select name="time_range" class="form-input">
                                    <option value="short_term" <?php echo ($settings['time_range'] ?? '') === 'short_term' ? 'selected' : ''; ?>>
                                        <?php echo $lang->get('last_4_weeks'); ?>
                                    </option>
                                    <option value="medium_term" <?php echo ($settings['time_range'] ?? '') === 'medium_term' ? 'selected' : ''; ?>>
                                        <?php echo $lang->get('last_6_months'); ?>
                                    </option>
                                    <option value="long_term" <?php echo ($settings['time_range'] ?? '') === 'long_term' ? 'selected' : ''; ?>>
                                        <?php echo $lang->get('all_time'); ?>
                                    </option>
                                </select>
                            </div>

                            <!-- Active Days -->
                            <div class="form-group">
                                <label class="form-label"><?php echo $lang->get('active_days'); ?></label>
                                <div class="space-y-2">
                                    <?php 
                                    $active_days = explode(',', $settings['active_days'] ?? '');
                                    $days = [
                                        'monday' => $lang->get('monday'),
                                        'tuesday' => $lang->get('tuesday'),
                                        'wednesday' => $lang->get('wednesday'),
                                        'thursday' => $lang->get('thursday'),
                                        'friday' => $lang->get('friday'),
                                        'saturday' => $lang->get('saturday'),
                                        'sunday' => $lang->get('sunday')
                                    ];
                                    foreach ($days as $day_key => $day_name): ?>
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="active_days[]" 
                                                value="<?php echo $day_key; ?>"
                                                <?php echo in_array($day_key, $active_days) ? 'checked' : ''; ?>
                                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700"><?php echo $day_name; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit" name="update_settings" class="btn btn-primary w-full">
                                <i class="fas fa-save mr-2"></i><?php echo $lang->get('save_settings'); ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar mr-2"></i><?php echo $lang->get('statistics'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><?php echo $lang->get('total_playlists'); ?></span>
                                <span class="font-semibold text-gray-900">12</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><?php echo $lang->get('total_tracks'); ?></span>
                                <span class="font-semibold text-gray-900">1,247</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><?php echo $lang->get('listening_time'); ?></span>
                                <span class="font-semibold text-gray-900">42h</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600"><?php echo $lang->get('favorite_genre'); ?></span>
                                <span class="font-semibold text-gray-900">Pop</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-bolt mr-2"></i><?php echo $lang->get('quick_actions'); ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3">
                            <button class="btn btn-secondary w-full" onclick="openSpotify()">
                                <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('open_spotify'); ?>
                            </button>
                            <button class="btn btn-secondary w-full" onclick="syncPlaylists()">
                                <i class="fas fa-sync mr-2"></i><?php echo $lang->get('sync_playlists'); ?>
                            </button>
                            <button class="btn btn-secondary w-full" onclick="exportData()">
                                <i class="fas fa-download mr-2"></i><?php echo $lang->get('export_data'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    <script src="https://open.spotify.com/embed-podcast/iframe-api/v1" async></script>
    
    <script>
    // Spotify integration functions
    function playSpotify() {
        console.log('Playing Spotify...');
        // Spotify API integration would go here
    }

    function pauseSpotify() {
        console.log('Pausing Spotify...');
        // Spotify API integration would go here
    }

    function nextTrack() {
        console.log('Next track...');
        // Spotify API integration would go here
    }

    function createPlaylist() {
        console.log('Creating playlist...');
        // Playlist creation logic would go here
    }

    function importPlaylist() {
        console.log('Importing playlist...');
        // Playlist import logic would go here
    }

    function openSpotify() {
        window.open('https://open.spotify.com', '_blank');
    }

    function syncPlaylists() {
        console.log('Syncing playlists...');
        // Sync logic would go here
    }

    function exportData() {
        console.log('Exporting data...');
        // Export logic would go here
    }

    // Initialize Spotify IFrame API
    window.onSpotifyIframeApiReady = (IFrameAPI) => {
        const element = document.getElementById('spotify-iframe');
        const options = {
            uri: 'spotify:playlist:37i9dQZF1DXcBWIGoYBM5M'
        };
        const callback = (EmbedController) => {
            EmbedController.addListener('ready', () => {
                console.log('Spotify player ready');
            });
        };
        IFrameAPI.createController(element, options, callback);
    };
    </script>
</body>
</html>