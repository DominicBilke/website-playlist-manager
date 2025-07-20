<?php
require 'script/inc_start.php';
require 'script/languages.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('home'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Die fortschrittlichste Lösung für Musik-Streaming-Automatisierung und Playlist-Management' : 'The most advanced solution for music streaming automation and playlist management'; ?>">
    
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

    <!-- Hero Section -->
    <section class="hero-section relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-purple-700 to-purple-800"></div>
        <div class="absolute inset-0 bg-black opacity-20"></div>
        
        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 animate-fade-in">
                    <?php echo $lang->getCurrentLanguage() === 'de' ? 'Playlist Manager' : 'Playlist Manager'; ?>
                </h1>
                <p class="text-xl md:text-2xl text-purple-100 mb-8 leading-relaxed animate-fade-in" style="animation-delay: 0.2s;">
                    <?php echo $lang->getCurrentLanguage() === 'de' 
                        ? 'Die fortschrittlichste Lösung für Musik-Streaming-Automatisierung und Playlist-Management'
                        : 'The most advanced solution for music streaming automation and playlist management'; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in" style="animation-delay: 0.4s;">
                    <?php if (!isset($_SESSION['id'])): ?>
                        <a href="signup.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket mr-2"></i><?php echo $lang->get('get_started'); ?>
                        </a>
                        <a href="login.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i><?php echo $lang->get('sign_in'); ?>
                        </a>
                    <?php else: ?>
                        <a href="account.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i><?php echo $lang->get('dashboard'); ?>
                        </a>
                        <a href="spotify_play.php" class="btn btn-secondary btn-lg">
                            <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('start_listening'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="floating-element" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
            <div class="floating-element" style="top: 60%; right: 15%; animation-delay: 2s;"></div>
            <div class="floating-element" style="bottom: 20%; left: 20%; animation-delay: 4s;"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <?php echo $lang->getCurrentLanguage() === 'de' ? 'Unterstützte Plattformen' : 'Supported Platforms'; ?>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    <?php echo $lang->getCurrentLanguage() === 'de' 
                        ? 'Verbinden Sie sich mit allen großen Musik-Streaming-Plattformen für nahtlose Automatisierung'
                        : 'Connect with all major music streaming platforms for seamless automation'; ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Spotify -->
                <div class="card platform-card platform-spotify animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-spotify text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Spotify</h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Vollständige Integration mit automatischer Wiedergabe und Playlist-Management'
                                : 'Full integration with automated playback and playlist management'; ?>
                        </p>
                        <ul class="text-sm text-gray-500 space-y-1 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('automated_playback'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('playlist_management'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('statistics_tracking'); ?></li>
                        </ul>
                        <a href="spotify_play.php" class="btn btn-primary w-full">
                            <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('connect'); ?>
                        </a>
                    </div>
                </div>

                <!-- Apple Music -->
                <div class="card platform-card platform-apple animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-apple text-3xl text-pink-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo $lang->get('apple_music'); ?></h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'MusicKit JS Integration mit erweiterten Steuerungsfunktionen'
                                : 'MusicKit JS integration with advanced control features'; ?>
                        </p>
                        <ul class="text-sm text-gray-500 space-y-1 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('music_kit_integration'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('advanced_controls'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('library_access'); ?></li>
                        </ul>
                        <a href="applemusic_play.php" class="btn btn-primary w-full">
                            <i class="fab fa-apple mr-2"></i><?php echo $lang->get('connect'); ?>
                        </a>
                    </div>
                </div>

                <!-- YouTube Music -->
                <div class="card platform-card platform-youtube animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-youtube text-3xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo $lang->get('youtube_music'); ?></h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'YouTube IFrame API mit Playlist-Management und Video-Player'
                                : 'YouTube IFrame API with playlist management and video player'; ?>
                        </p>
                        <ul class="text-sm text-gray-500 space-y-1 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('iframe_integration'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('video_player'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('playlist_control'); ?></li>
                        </ul>
                        <a href="youtube_play.php" class="btn btn-primary w-full">
                            <i class="fab fa-youtube mr-2"></i><?php echo $lang->get('connect'); ?>
                        </a>
                    </div>
                </div>

                <!-- Amazon Music -->
                <div class="card platform-card platform-amazon animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-amazon text-3xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo $lang->get('amazon_music'); ?></h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Manuelle Steuerung mit Statistiken-Tracking und externem Player'
                                : 'Manual control with statistics tracking and external player'; ?>
                        </p>
                        <ul class="text-sm text-gray-500 space-y-1 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('manual_control'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('statistics_tracking'); ?></li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo $lang->get('external_player'); ?></li>
                        </ul>
                        <a href="amazon_play.php" class="btn btn-primary w-full">
                            <i class="fab fa-amazon mr-2"></i><?php echo $lang->get('connect'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <?php echo $lang->getCurrentLanguage() === 'de' ? 'Erweiterte Funktionen' : 'Advanced Features'; ?>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    <?php echo $lang->getCurrentLanguage() === 'de' 
                        ? 'Intelligente Automatisierung und umfassende Analytics für Ihre Musikforschung'
                        : 'Intelligent automation and comprehensive analytics for your music research'; ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Smart Scheduling -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Intelligente Planung' : 'Smart Scheduling'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Automatische Wiedergabe basierend auf Ihren Einstellungen mit zufälligen Intervallen'
                                : 'Automated playback based on your settings with random intervals'; ?>
                        </p>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Umfassende Analytics' : 'Comprehensive Analytics'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Detaillierte Statistiken und Berichte für Ihre Musikforschung'
                                : 'Detailed statistics and reports for your music research'; ?>
                        </p>
                    </div>
                </div>

                <!-- Multi-Platform -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-globe text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Multi-Plattform' : 'Multi-Platform'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Unterstützung für alle großen Musik-Streaming-Plattformen'
                                : 'Support for all major music streaming platforms'; ?>
                        </p>
                    </div>
                </div>

                <!-- Team Management -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Team-Management' : 'Team Management'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Multi-User-Support mit Büro-Integration und administrativen Kontrollen'
                                : 'Multi-user support with office integration and administrative controls'; ?>
                        </p>
                    </div>
                </div>

                <!-- Security -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-3xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Enterprise-Sicherheit' : 'Enterprise Security'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Umfassende Sicherheitsfunktionen und Datenschutz-Compliance'
                                : 'Comprehensive security features and data protection compliance'; ?>
                        </p>
                    </div>
                </div>

                <!-- Multilingual -->
                <div class="card feature-card animate-fade-in" style="animation-delay: 0.6s;">
                    <div class="card-body text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-language text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo $lang->getCurrentLanguage() === 'de' ? 'Mehrsprachig' : 'Multilingual'; ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo $lang->getCurrentLanguage() === 'de' 
                                ? 'Vollständige Unterstützung für Deutsch und Englisch'
                                : 'Full support for German and English languages'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-purple-600 to-purple-800">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">
                <?php echo $lang->getCurrentLanguage() === 'de' ? 'Bereit zu beginnen?' : 'Ready to get started?'; ?>
            </h2>
            <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                <?php echo $lang->getCurrentLanguage() === 'de' 
                    ? 'Erstellen Sie Ihr Konto und beginnen Sie mit der Automatisierung Ihrer Musik-Playlists'
                    : 'Create your account and start automating your music playlists'; ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if (!isset($_SESSION['id'])): ?>
                    <a href="signup.php" class="btn bg-white text-purple-600 hover:bg-gray-100 btn-lg">
                        <i class="fas fa-user-plus mr-2"></i><?php echo $lang->get('create_account'); ?>
                    </a>
                    <a href="login.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i><?php echo $lang->get('sign_in'); ?>
                    </a>
                <?php else: ?>
                    <a href="account.php" class="btn bg-white text-purple-600 hover:bg-gray-100 btn-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i><?php echo $lang->get('go_to_dashboard'); ?>
                    </a>
                    <a href="spotify_play.php" class="btn btn-secondary btn-lg">
                        <i class="fab fa-spotify mr-2"></i><?php echo $lang->get('start_listening'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <style>
    /* Hero section specific styles */
    .hero-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    
    .floating-element {
        position: absolute;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    .platform-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .platform-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary-200);
    }
    
    .feature-card {
        transition: all 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-xl);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .hero-section {
            min-height: 60vh;
        }
        
        .hero-section h1 {
            font-size: 3rem;
        }
        
        .hero-section p {
            font-size: 1.125rem;
        }
    }
    </style>
</body>
</html>