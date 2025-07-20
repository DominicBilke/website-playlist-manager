<?php
require 'script/inc_start.php';
require 'script/languages.php';

// Check if user is logged in
if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require 'script/AppleMusic.php';

$applemusic = new AppleMusic();
$applemusic->Connect();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Music Player - Playlist Manager</title>
    <meta name="description" content="Play your Apple Music playlists with automated scheduling">
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Apple MusicKit -->
    <script src="https://js-cdn.music.apple.com/musickit/v3/musickit.js" data-web-components async></script>
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #FA243C 0%, #FF6B6B 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .sidebar { transition: all 0.3s ease; }
        .sidebar.collapsed { width: 4rem; }
        .main-content { transition: all 0.3s ease; }
        .main-content.expanded { margin-left: 4rem; }
        .apple-pink { background-color: #FA243C; }
        .apple-pink:hover { background-color: #FF6B6B; }
        .player-container { min-height: 600px; }
        .status-indicator { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .music-player { border-radius: 12px; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-music text-2xl text-purple-600"></i>
                <span class="ml-2 text-xl font-bold text-gray-900">Playlist Manager</span>
            </div>
            <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="mt-8 px-4">
            <div class="space-y-2">
                <a href="account.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('dashboard'); ?></span>
                </a>
                <a href="spotify_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-spotify w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('spotify'); ?></span>
                </a>
                <a href="applemusic_play.php" class="flex items-center px-4 py-3 text-purple-600 bg-purple-50 rounded-lg">
                    <i class="fab fa-apple w-5 h-5"></i>
                    <span class="ml-3 font-medium"><?php echo $lang->get('apple_music'); ?></span>
                </a>
                <a href="youtube_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-youtube w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('youtube_music'); ?></span>
                </a>
                <a href="amazon_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-amazon w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('amazon_music'); ?></span>
                </a>
                <a href="editaccount.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('settings'); ?></span>
                </a>
            </div>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['login']); ?></p>
                    <p class="text-xs text-gray-500">Team <?php echo htmlspecialchars($_SESSION['team'] ?? 'N/A'); ?></p>
                </div>
            </div>
            <a href="script/logout.php" class="mt-3 flex items-center px-4 py-2 text-sm text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt w-4 h-4"></i>
                <span class="ml-3"><?php echo $lang->get('sign_out'); ?></span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="main-content ml-64 min-h-screen">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fab fa-apple text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo $lang->get('apple_music'); ?> <?php echo $lang->get('player'); ?></h1>
                        <p class="text-sm text-gray-600"><?php echo $lang->get('automated_playback'); ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-pink-500 rounded-full mr-2 status-indicator"></div>
                        <span class="text-sm text-pink-600 font-medium">Live</span>
                    </div>
                    <a href="applemusic_manage.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i><?php echo $lang->get('settings'); ?>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Algorithm Information -->
            <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-brain text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Automation Algorithm</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Playing time defined in user account</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-play text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Random play time: 61-600 seconds</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-pause text-orange-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Random pause time: 0-600 seconds</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-magic text-purple-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Automatic playback based on settings</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-random text-blue-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Shuffle and repeat all songs</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Requires paid Apple Music account</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Current Status</p>
                            <p class="text-lg font-semibold text-gray-900" id="player-status">Initializing...</p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-apple text-pink-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Playing Time</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($_SESSION['daytime_from'] ?? '00:00'); ?> - 
                                <?php echo htmlspecialchars($_SESSION['daytime_to'] ?? '00:00'); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Active Days</p>
                            <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['days'] ?? 'Not set'); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Player Section -->
            <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Apple Music Player</h3>
                    <div class="flex space-x-2">
                        <button id="apple-logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>New Login
                        </button>
                    </div>
                </div>

                <?php if(isset($_SESSION['apple_playlist_id']) && $_SESSION['apple_playlist_id']): ?>
                <div class="player-container">
                    <div id="apple-music-player" class="music-player w-full h-600 bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-apple text-pink-600 text-2xl"></i>
                            </div>
                            <p class="text-gray-600">Loading Apple Music Player...</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Playlist Selected</h3>
                    <p class="text-gray-600 mb-6">Please select an Apple Music playlist in your account settings.</p>
                    <a href="editaccount.php" class="apple-pink hover:bg-pink-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i>Configure Playlist
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Control Panel -->
            <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Control Panel</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Auto Play</p>
                            <p class="text-xs text-gray-600">Automated scheduling</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full status-indicator"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Shuffle</p>
                            <p class="text-xs text-gray-600">Random track order</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Repeat</p>
                            <p class="text-xs text-gray-600">Loop all tracks</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modern JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.3.11/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Apple Music logout function
        function Apple_Logout() {
            if (window.MusicKit && MusicKit.getInstance()) {
                MusicKit.getInstance().unauthorize();
            }
            setTimeout(() => {window.location.href = "<?php echo $_SESSION['url']; ?>"; }, 3000);
        }

        document.getElementById('apple-logout').addEventListener('click', Apple_Logout);

        <?php if(isset($_SESSION['apple_playlist_id']) && $_SESSION['apple_playlist_id']): ?>
        // Apple Music Integration
        let music = null;
        let isPlaying = false;
        let playUntil = new Date();
        let pauseUntil = new Date();

        document.addEventListener('musickitloaded', async function () {
            try {
                await MusicKit.configure({
                    developerToken: '<?php echo $applemusic->jwtToken; ?>',
                    app: {
                        name: 'Playlist-Manager',
                        build: '2023.06.07',
                    },
                });
                
                music = MusicKit.getInstance();
                await music.authorize();
                
                // Initialize player
                initializePlayer();
                
            } catch (err) {
                console.log('Error in configuring Musickit!');
                console.log(err);
                document.getElementById('player-status').textContent = 'Configuration Error';
            }
        });

        function initializePlayer() {
            const playerContainer = document.getElementById('apple-music-player');
            playerContainer.innerHTML = `
                <div class="w-full h-full flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-apple text-pink-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Apple Music Ready</h3>
                        <p class="text-gray-600 mb-4">Playlist: <?php echo htmlspecialchars($_SESSION['apple_playlist_id']); ?></p>
                        <button id="play-playlist" class="apple-pink hover:bg-pink-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <i class="fas fa-play mr-2"></i>Start Playlist
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('play-playlist').addEventListener('click', function() {
                startPlaylist();
            });

            document.getElementById('player-status').textContent = 'Ready';
        }

        async function startPlaylist() {
            try {
                // Load the playlist
                await music.setQueue({
                    playlist: '<?php echo $_SESSION['apple_playlist_id']; ?>'
                });
                
                // Start playing
                await music.play();
                isPlaying = true;
                
                document.getElementById('player-status').textContent = 'Playing';
                
                // Start automation
                startAutomation();
                
            } catch (error) {
                console.error('Error starting playlist:', error);
                document.getElementById('player-status').textContent = 'Error: ' + error.message;
            }
        }

        function startAutomation() {
            setInterval(function() {
                const now = new Date();
                const currentDay = now.getDay();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                
                const fromTime = '<?php echo $_SESSION['daytime_from']; ?>'.split(':');
                const toTime = '<?php echo $_SESSION['daytime_to']; ?>'.split(':');
                const fromHour = parseInt(fromTime[0]);
                const fromMinute = parseInt(fromTime[1]);
                const toHour = parseInt(toTime[0]);
                const toMinute = parseInt(toTime[1]);
                
                const currentTime = currentHour * 60 + currentMinute;
                const fromTimeMinutes = fromHour * 60 + fromMinute;
                const toTimeMinutes = toHour * 60 + toMinute;
                
                const activeDays = '<?php echo $_SESSION['days']; ?>'.split(', ');
                const isActiveDay = activeDays.includes(currentDay.toString());
                const isActiveTime = currentTime >= fromTimeMinutes && currentTime <= toTimeMinutes;
                
                if (isActiveDay && isActiveTime) {
                    // Pause before desired time
                    if (currentTime < fromTimeMinutes && isPlaying) {
                        music.pause();
                        isPlaying = false;
                        document.getElementById('player-status').textContent = 'Paused (before time)';
                    }
                    
                    // Play with algorithm
                    if (!isPlaying && currentTime >= fromTimeMinutes && currentTime <= toTimeMinutes && now > pauseUntil) {
                        const playDuration = Math.floor(Math.random() * 600) + 61;
                        playUntil = new Date(now.getTime() + playDuration * 1000);
                        
                        music.play();
                        isPlaying = true;
                        document.getElementById('player-status').textContent = `Playing (${playDuration}s)`;
                        console.log('play for ' + playDuration + ' seconds');
                    }
                    
                    // Pause with algorithm
                    if (isPlaying && currentTime >= fromTimeMinutes && currentTime <= toTimeMinutes && now > playUntil) {
                        const pauseDuration = Math.floor(Math.random() * 600) + 61;
                        pauseUntil = new Date(now.getTime() + pauseDuration * 1000);
                        
                        music.pause();
                        isPlaying = false;
                        document.getElementById('player-status').textContent = `Paused (${pauseDuration}s)`;
                        console.log('pause for ' + pauseDuration + ' seconds');
                    }
                    
                    // Pause after desired time
                    if (currentTime > toTimeMinutes && isPlaying) {
                        music.pause();
                        isPlaying = false;
                        document.getElementById('player-status').textContent = 'Paused (after time)';
                    }
                    
                    // Log statistics during regular play time
                    $.ajax({
                        method: "POST",
                        url: "script/log_applemusic.php",
                        data: { }
                    })
                    .done(function(response) {
                        console.log('Apple Music statistics logged');
                    })
                    .fail(function(xhr, status, error) {
                        console.log('Failed to log Apple Music statistics');
                    });
                } else {
                    // Outside active time, pause if playing
                    if (isPlaying) {
                        music.pause();
                        isPlaying = false;
                        document.getElementById('player-status').textContent = 'Paused (outside schedule)';
                    }
                }
            }, 5000);
        }
        <?php endif; ?>
    </script>
</body>
</html>