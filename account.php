<?php
require 'script/inc_start.php';
require 'script/languages.php';

// Check if user is logged in
if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Playlist Manager</title>
    <meta name="description" content="Manage your playlists and account settings">
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .sidebar { transition: all 0.3s ease; }
        .sidebar.collapsed { width: 4rem; }
        .main-content { transition: all 0.3s ease; }
        .main-content.expanded { margin-left: 4rem; }
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
                <a href="account.php" class="flex items-center px-4 py-3 text-purple-600 bg-purple-50 rounded-lg">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span class="ml-3 font-medium"><?php echo $lang->get('dashboard'); ?></span>
                </a>
                <a href="spotify_manage.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-spotify w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('spotify'); ?></span>
                </a>
                <a href="applemusic_manage.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-apple w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('apple_music'); ?></span>
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
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo $lang->get('dashboard'); ?></h1>
                    <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Willkommen zurück,' : 'Welcome back,'; ?> <?php echo htmlspecialchars($_SESSION['login']); ?>!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['login']); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-spotify text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Spotify Status</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo isset($_SESSION['playlist_id']) && $_SESSION['playlist_id'] ? 'Connected' : 'Not Connected'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-apple text-2xl text-pink-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Apple Music</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo isset($_SESSION['apple_playlist_id']) && $_SESSION['apple_playlist_id'] ? 'Connected' : 'Not Connected'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-youtube text-2xl text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">YouTube Music</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo isset($_SESSION['youtube_playlist_id']) && $_SESSION['youtube_playlist_id'] ? 'Connected' : 'Not Connected'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fab fa-amazon text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Amazon Music</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo isset($_SESSION['amazon_playlist_id']) && $_SESSION['amazon_playlist_id'] ? 'Connected' : 'Not Connected'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Current Playing Status -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Current Status</h3>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Active</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Playing Days:</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['days'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Time Range:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($_SESSION['daytime_from'] ?? '00:00'); ?> - 
                                <?php echo htmlspecialchars($_SESSION['daytime_to'] ?? '00:00'); ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Office:</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['office'] ?? 'Not set'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="spotify_play.php" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fab fa-spotify mr-2"></i>
                            <span class="text-sm font-medium">Play Spotify</span>
                        </a>
                        <a href="applemusic_play.php" class="flex items-center justify-center px-4 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                            <i class="fab fa-apple mr-2"></i>
                            <span class="text-sm font-medium">Play Apple Music</span>
                        </a>
                        <a href="youtube_play.php" class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fab fa-youtube mr-2"></i>
                            <span class="text-sm font-medium">Play YouTube</span>
                        </a>
                        <a href="amazon_play.php" class="flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fab fa-amazon mr-2"></i>
                            <span class="text-sm font-medium">Play Amazon</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Analytics Chart -->
            <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Listening Analytics</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded-lg">Week</button>
                        <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Month</button>
                        <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Year</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-spotify text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Spotify playlist updated</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-apple text-pink-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Apple Music connected</p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-cog text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Settings updated</p>
                                <p class="text-xs text-gray-500">3 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Database</span>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600">Online</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">API Services</span>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600">All Operational</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Last Backup</span>
                            <span class="text-sm text-gray-900">2 hours ago</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Uptime</span>
                            <span class="text-sm text-gray-900">99.9%</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modern JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.3.11/dist/alpine.min.js" defer></script>
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Analytics Chart
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Listening Hours',
                    data: [4.5, 6.2, 3.8, 5.1, 7.3, 8.0, 6.5],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Real-time updates simulation
        setInterval(() => {
            // Update chart with new data
            const newData = analyticsChart.data.datasets[0].data.map(value => 
                value + (Math.random() - 0.5) * 2
            );
            analyticsChart.data.datasets[0].data = newData;
            analyticsChart.update('none');
        }, 30000); // Update every 30 seconds
    </script>
</body>
</html>