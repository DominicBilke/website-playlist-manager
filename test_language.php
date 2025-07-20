<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Test - Playlist Manager</title>
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Language Test Page</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Language Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Current Language:</strong> <?php echo $lang->getCurrentLanguage(); ?></p>
                        <p><strong>Language Name:</strong> <?php echo $lang->getLanguageName($lang->getCurrentLanguage()); ?></p>
                        <p><strong>Session Language:</strong> <?php echo $_SESSION['language'] ?? 'Not set'; ?></p>
                    </div>
                    <div>
                        <p><strong>Available Languages:</strong> <?php echo implode(', ', $lang->getAvailableLanguages()); ?></p>
                        <p><strong>Is RTL:</strong> <?php echo $lang->isRTL() ? 'Yes' : 'No'; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Translation Test</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Common Terms:</h3>
                        <ul class="space-y-1 text-sm">
                            <li><strong>Home:</strong> <?php echo $lang->get('home'); ?></li>
                            <li><strong>Dashboard:</strong> <?php echo $lang->get('dashboard'); ?></li>
                            <li><strong>Settings:</strong> <?php echo $lang->get('settings'); ?></li>
                            <li><strong>Sign In:</strong> <?php echo $lang->get('sign_in'); ?></li>
                            <li><strong>Sign Up:</strong> <?php echo $lang->get('sign_up'); ?></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Platform Names:</h3>
                        <ul class="space-y-1 text-sm">
                            <li><strong>Spotify:</strong> <?php echo $lang->get('spotify'); ?></li>
                            <li><strong>Apple Music:</strong> <?php echo $lang->get('apple_music'); ?></li>
                            <li><strong>YouTube Music:</strong> <?php echo $lang->get('youtube_music'); ?></li>
                            <li><strong>Amazon Music:</strong> <?php echo $lang->get('amazon_music'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Language Switcher Test</h2>
                <p class="text-gray-600 mb-4">Use the language switcher in the header to change languages and see the translations update.</p>
                
                <div class="flex space-x-4">
                    <a href="?lang=en" class="btn btn-primary">
                        <i class="fas fa-flag mr-2"></i>Switch to English
                    </a>
                    <a href="?lang=de" class="btn btn-secondary">
                        <i class="fas fa-flag mr-2"></i>Zu Deutsch wechseln
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">URL Parameters</h2>
                <div class="bg-gray-100 p-4 rounded">
                    <p><strong>Current URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
                    <p><strong>GET Parameters:</strong> <?php echo !empty($_GET) ? json_encode($_GET) : 'None'; ?></p>
                    <p><strong>Language Parameter:</strong> <?php echo $_GET['lang'] ?? 'Not set'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html> 