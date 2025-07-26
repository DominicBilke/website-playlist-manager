<?php
// Basic initialization
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once 'script/inc_start.php';
require_once 'script/languages.php';

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
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Test - Playlist Manager</title>
    
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
                        <i class="fas fa-test text-blue-600 mr-3"></i>Platform Test
                    </h1>
                    <p class="text-gray-600">
                        Test page to verify platform functionality
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Welcome</p>
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['login']); ?></p>
                        <p class="text-xs text-gray-500">Team: <?php echo htmlspecialchars($currentUser['team']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Spotify -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-spotify text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Spotify</h3>
                    <p class="text-sm text-gray-600 mb-4">Test Spotify integration</p>
                    <a href="spotify_play.php" class="btn btn-primary btn-sm w-full">
                        <i class="fab fa-spotify mr-2"></i>Test Spotify
                    </a>
                </div>
            </div>

            <!-- Apple Music -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-apple text-pink-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Apple Music</h3>
                    <p class="text-sm text-gray-600 mb-4">Test Apple Music integration</p>
                    <a href="applemusic_play.php" class="btn btn-primary btn-sm w-full">
                        <i class="fab fa-apple mr-2"></i>Test Apple Music
                    </a>
                </div>
            </div>

            <!-- YouTube -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-youtube text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">YouTube</h3>
                    <p class="text-sm text-gray-600 mb-4">Test YouTube integration</p>
                    <a href="youtube_play.php" class="btn btn-primary btn-sm w-full">
                        <i class="fab fa-youtube mr-2"></i>Test YouTube
                    </a>
                </div>
            </div>

            <!-- Amazon -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-music text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Amazon Music</h3>
                    <p class="text-sm text-gray-600 mb-4">Test Amazon Music integration</p>
                    <a href="amazon_play.php" class="btn btn-primary btn-sm w-full">
                        <i class="fas fa-music mr-2"></i>Test Amazon
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="mt-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-server mr-2 text-blue-600"></i>System Status
                    </h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">Database</h3>
                                <p class="text-sm text-gray-600">Connection status</p>
                            </div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">Session</h3>
                                <p class="text-sm text-gray-600">User authentication</p>
                            </div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">Language</h3>
                                <p class="text-sm text-gray-600">Current: <?php echo $lang->getCurrentLanguage(); ?></p>
                            </div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Information -->
        <div class="mt-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-bug mr-2 text-red-600"></i>Debug Information
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Session Data:</h3>
                            <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto"><?php print_r($_SESSION); ?></pre>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">User Info:</h3>
                            <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto"><?php print_r($currentUser); ?></pre>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">PHP Info:</h3>
                            <div class="bg-gray-100 p-4 rounded text-sm">
                                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                                <p><strong>Error Reporting:</strong> <?php echo error_reporting(); ?></p>
                                <p><strong>Display Errors:</strong> <?php echo ini_get('display_errors'); ?></p>
                                <p><strong>Session Status:</strong> <?php echo session_status(); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html> 