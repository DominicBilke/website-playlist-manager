<?php
// Test file for iframe players
define('APP_ROOT', __DIR__);
require_once 'script/includes.php';

// Initialize application
$lang = init_app();

// Check if user is logged in
require_auth();

// Get current user info
$currentUser = get_current_user_info();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iframe Players Test - Playlist Manager</title>
    
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
                        <i class="fas fa-play-circle text-blue-600 mr-3"></i>Iframe Players Test
                    </h1>
                    <p class="text-gray-600">
                        <?php echo $lang->getCurrentLanguage() === 'de' 
                            ? 'Test der eingebetteten Player für alle Plattformen'
                            : 'Test of embedded players for all platforms'; ?>
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500"><?php echo $lang->get('welcome'); ?></p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['login']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-play-circle text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Iframe Players Test -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <?php
            require_once 'components/iframe_player.php';
            
            // Test all platforms
            $platforms = ['spotify', 'apple_music', 'youtube', 'amazon'];
            
            foreach ($platforms as $platform):
                $iframePlayer = new IframePlayer($platform, $lang);
            ?>
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="<?php echo $iframePlayer->getPlatformIcon(); ?> mr-2"></i>
                            <?php echo ucfirst(str_replace('_', ' ', $platform)); ?> Player Test
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php echo $iframePlayer->getPlayerControls(); ?>
                        
                        <!-- Test Controls -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Test Controls</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="loadIframePlayer('<?php echo $platform; ?>', '37i9dQZF1DXcBWIGoYBM5M', null, false)" class="btn btn-secondary btn-sm">
                                    Load Default Playlist
                                </button>
                                <button onclick="toggleIframePlayer('<?php echo $platform; ?>')" class="btn btn-secondary btn-sm">
                                    Toggle Player
                                </button>
                                <button onclick="refreshIframePlayer('<?php echo $platform; ?>')" class="btn btn-secondary btn-sm">
                                    Refresh Player
                                </button>
                                <button onclick="loadIframePlayer('<?php echo $platform; ?>', null, '4iV5W9uY0Yuv0IoDgPiPZ2', false)" class="btn btn-secondary btn-sm">
                                    Load Sample Track
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Player Information -->
        <div class="mt-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Player Information
                    </h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Supported Platforms</h3>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fab fa-spotify text-green-600 mr-2"></i>
                                    <span>Spotify - Full iframe support with playlists and tracks</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fab fa-apple text-pink-600 mr-2"></i>
                                    <span>Apple Music - Full iframe support with playlists and tracks</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fab fa-youtube text-red-600 mr-2"></i>
                                    <span>YouTube Music - Full iframe support with playlists and videos</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fab fa-amazon text-orange-600 mr-2"></i>
                                    <span>Amazon Music - Limited iframe support (redirect to website)</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Features</h3>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span>Direct playlist playback</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span>Individual track playback</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span>Autoplay support</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span>Responsive design</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span>Toggle and refresh controls</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script>
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
            // For now, we'll just refresh the current player
            refreshIframePlayer(platform);
        }
    }
    </script>
</body>
</html> 