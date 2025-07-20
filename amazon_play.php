<?php
require 'script/inc_start.php';
require 'script/languages.php';

// Check if user is logged in
if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require 'script/Amazon.php';

$amazon = new Amazon();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon Music Player - Playlist Manager</title>
    <meta name="description" content="Play your Amazon Music playlists with automated scheduling">
    
    <!-- Modern CSS Framework -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #FF9900 0%, #FFB84D 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .sidebar { transition: all 0.3s ease; }
        .sidebar.collapsed { width: 4rem; }
        .main-content { transition: all 0.3s ease; }
        .main-content.expanded { margin-left: 4rem; }
        .amazon-orange { background-color: #FF9900; }
        .amazon-orange:hover { background-color: #FFB84D; }
        .player-container { min-height: 600px; }
        .status-indicator { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .amazon-player { border-radius: 12px; overflow: hidden; }
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
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="spotify_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-spotify w-5 h-5"></i>
                    <span class="ml-3">Spotify</span>
                </a>
                <a href="applemusic_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-apple w-5 h-5"></i>
                    <span class="ml-3">Apple Music</span>
                </a>
                <a href="youtube_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-youtube w-5 h-5"></i>
                    <span class="ml-3">YouTube Music</span>
                </a>
                <a href="amazon_play.php" class="flex items-center px-4 py-3 text-purple-600 bg-purple-50 rounded-lg">
                    <i class="fab fa-amazon w-5 h-5"></i>
                    <span class="ml-3 font-medium">Amazon Music</span>
                </a>
                <a href="editaccount.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span class="ml-3">Settings</span>
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
                <span class="ml-3">Sign out</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="main-content ml-64 min-h-screen">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fab fa-amazon text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Amazon Music Player</h1>
                        <p class="text-sm text-gray-600">Manual playlist playback with smart scheduling</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-2 status-indicator"></div>
                        <span class="text-sm text-orange-600 font-medium">Live</span>
                    </div>
                    <a href="amazon_manage.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i>Settings
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
                            <span class="text-sm text-gray-700">Manual play control required</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-pause text-orange-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Manual pause control required</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Amazon Music API limitations</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-external-link-alt text-blue-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Opens in new window/tab</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-purple-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Requires Amazon Music Unlimited</span>
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
                            <p class="text-lg font-semibold text-gray-900" id="player-status">Ready</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-amazon text-orange-600 text-xl"></i>
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
                    <h3 class="text-lg font-semibold text-gray-900">Amazon Music Player</h3>
                    <div class="flex space-x-2">
                        <button id="amazon-logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>New Login
                        </button>
                    </div>
                </div>

                <?php if(isset($_SESSION['amazon_playlist_id']) && $_SESSION['amazon_playlist_id']): ?>
                <div class="player-container">
                    <div class="amazon-player w-full bg-gray-100 rounded-lg p-8">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fab fa-amazon text-orange-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Amazon Music Playlist</h3>
                            <p class="text-gray-600 mb-6">Click the button below to open your Amazon Music playlist in a new window.</p>
                            <button id="open-amazon-playlist" class="amazon-orange hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-medium transition-colors text-lg">
                                <i class="fab fa-amazon mr-3"></i>Open Amazon Music Playlist
                            </button>
                            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                                    <div class="text-sm text-yellow-800">
                                        <p class="font-medium mb-1">Important Note:</p>
                                        <p>Due to Amazon Music API limitations, automatic playback control is not available. You'll need to manually control play/pause in the Amazon Music window. The system will still log your listening statistics during your scheduled time windows.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Playlist Selected</h3>
                    <p class="text-gray-600 mb-6">Please select an Amazon Music playlist in your account settings.</p>
                    <a href="editaccount.php" class="amazon-orange hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
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
                            <p class="text-sm font-medium text-gray-900">Manual Control</p>
                            <p class="text-xs text-gray-600">User-controlled playback</p>
                        </div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Statistics</p>
                            <p class="text-xs text-gray-600">Listening time tracking</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Scheduling</p>
                            <p class="text-xs text-gray-600">Time window monitoring</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Limitations Notice -->
            <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100 mt-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Amazon Music Limitations</h3>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p>• Amazon Music does not provide a public web API for automated playback control</p>
                            <p>• Playback must be manually controlled in the Amazon Music window</p>
                            <p>• The system can still track your listening statistics during scheduled time windows</p>
                            <p>• This limitation is due to Amazon's API restrictions, not our system</p>
                        </div>
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

        // Amazon logout function
        function Amazon_Logout() {
            // Clear any stored Amazon tokens
            localStorage.removeItem('amazon_auth_token');
            sessionStorage.removeItem('amazon_auth_token');
            
            // Redirect to Amazon logout
            const url = "https://www.amazon.com/ap/signin?openid.return_to=https%3A%2F%2Fwww.amazon.com%2F&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.assoc_handle=usflex&openid.mode=checkid_setup&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&";
            const amazonLogoutWindow = window.open(url, "Amazon Logout", "width=700,height=500,top=40,left=40");
            setTimeout(() => amazonLogoutWindow.close(), 2000);
            setTimeout(() => {window.location.href = "<?php echo $_SESSION['url'] ?? 'account.php'; ?>"; }, 3000);
        }

        document.getElementById('amazon-logout').addEventListener('click', Amazon_Logout);

        <?php if(isset($_SESSION['amazon_playlist_id']) && $_SESSION['amazon_playlist_id']): ?>
        // Open Amazon Music playlist
        document.getElementById('open-amazon-playlist').addEventListener('click', function() {
            const playlistUrl = '<?php echo htmlspecialchars($_SESSION['amazon_playlist_id']); ?>';
            const amazonWindow = window.open(playlistUrl, 'AmazonMusic', 'width=1200,height=800,scrollbars=yes,resizable=yes');
            
            if (amazonWindow) {
                document.getElementById('player-status').textContent = 'Playlist Opened';
                
                // Start monitoring for scheduled time windows
                startSchedulingMonitor();
            } else {
                alert('Please allow pop-ups for this site to open Amazon Music.');
            }
        });

        // Scheduling monitor for Amazon Music
        function startSchedulingMonitor() {
            setInterval(function() {
                const dat = new Date();
                let h = dat.getHours();
                let m = dat.getMinutes();
                let d = dat.getDay();
                const datfrom = new Date();
                datfrom.setHours(<?php echo date_parse($_SESSION["daytime_from"])["hour"]; ?>);
                datfrom.setMinutes(<?php echo date_parse($_SESSION["daytime_from"])["minute"]; ?>);
                const datto = new Date();
                datto.setHours(<?php echo date_parse($_SESSION["daytime_to"])["hour"]; ?>);
                datto.setMinutes(<?php echo date_parse($_SESSION["daytime_to"])["minute"]; ?>);

                // Check if we're in the scheduled time window
                if('<?php echo $_SESSION["days"]; ?>'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime()) {
                    // Log statistics during scheduled time
                    $.ajax({
                        method: "POST",
                        url: "script/log_amazon.php",
                        data: { }
                    })
                    .done(function(response) {
                        console.log('Amazon Music statistics logged');
                    })
                    .fail(function(xhr, status, error) {
                        console.log('Failed to log Amazon Music statistics');
                    });
                }
            }, 5000);
        }
        <?php endif; ?>
    </script>
</body>
</html>