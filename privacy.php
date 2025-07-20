<?php
// Initialize language system
require_once 'script/languages.php';
$lang = new LanguageManager();

// Get current language
$currentLang = $lang->getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('privacy_title'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->get('privacy_description'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <!-- Header -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 mt-20">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <?php echo $lang->get('privacy_title'); ?>
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                <?php echo $lang->get('privacy_subtitle'); ?>
            </p>
            <div class="mt-4 text-sm text-gray-400">
                <p><?php echo $lang->get('privacy_last_updated'); ?>: <?php echo date('d.m.Y'); ?></p>
            </div>
        </div>

        <!-- Content Container -->
        <div class="max-w-4xl mx-auto">
            <!-- Introduction Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_introduction'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <p><?php echo $lang->get('privacy_introduction_content'); ?></p>
                    
                    <div class="mt-6 p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('privacy_controller'); ?>
                        </h3>
                        <div class="text-sm space-y-1">
                            <p><strong>Playlist Manager GmbH</strong></p>
                            <p>Musterstraße 123</p>
                            <p>12345 Musterstadt</p>
                            <p>Deutschland</p>
                            <p class="mt-2">
                                <i class="fas fa-envelope mr-2"></i>privacy@playlist-manager.de
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Collection Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_data_collection'); ?>
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('privacy_personal_data'); ?>
                        </h3>
                        <div class="text-gray-300 space-y-2">
                            <p><?php echo $lang->get('privacy_personal_data_desc'); ?></p>
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li><?php echo $lang->get('privacy_data_name'); ?></li>
                                <li><?php echo $lang->get('privacy_data_email'); ?></li>
                                <li><?php echo $lang->get('privacy_data_usage'); ?></li>
                                <li><?php echo $lang->get('privacy_data_playlists'); ?></li>
                                <li><?php echo $lang->get('privacy_data_preferences'); ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('privacy_automatically_collected'); ?>
                        </h3>
                        <div class="text-gray-300 space-y-2">
                            <p><?php echo $lang->get('privacy_automatically_collected_desc'); ?></p>
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li><?php echo $lang->get('privacy_data_ip'); ?></li>
                                <li><?php echo $lang->get('privacy_data_browser'); ?></li>
                                <li><?php echo $lang->get('privacy_data_device'); ?></li>
                                <li><?php echo $lang->get('privacy_data_cookies'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purpose of Data Processing Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_purpose'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-user-check mr-2"></i><?php echo $lang->get('privacy_purpose_account'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_purpose_account_desc'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-music mr-2"></i><?php echo $lang->get('privacy_purpose_playlists'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_purpose_playlists_desc'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-chart-line mr-2"></i><?php echo $lang->get('privacy_purpose_analytics'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_purpose_analytics_desc'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-shield-alt mr-2"></i><?php echo $lang->get('privacy_purpose_security'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_purpose_security_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legal Basis Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_legal_basis'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <div class="p-4 bg-green-900/30 rounded-lg border border-green-500/20">
                        <h3 class="text-lg font-medium text-green-300 mb-2">
                            <i class="fas fa-check-circle mr-2"></i><?php echo $lang->get('privacy_consent'); ?>
                        </h3>
                        <p class="text-sm"><?php echo $lang->get('privacy_consent_desc'); ?></p>
                    </div>
                    
                    <div class="p-4 bg-blue-900/30 rounded-lg border border-blue-500/20">
                        <h3 class="text-lg font-medium text-blue-300 mb-2">
                            <i class="fas fa-file-contract mr-2"></i><?php echo $lang->get('privacy_contract'); ?>
                        </h3>
                        <p class="text-sm"><?php echo $lang->get('privacy_contract_desc'); ?></p>
                    </div>
                    
                    <div class="p-4 bg-yellow-900/30 rounded-lg border border-yellow-500/20">
                        <h3 class="text-lg font-medium text-yellow-300 mb-2">
                            <i class="fas fa-balance-scale mr-2"></i><?php echo $lang->get('privacy_legitimate_interest'); ?>
                        </h3>
                        <p class="text-sm"><?php echo $lang->get('privacy_legitimate_interest_desc'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Data Sharing Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_data_sharing'); ?>
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('privacy_third_parties'); ?>
                        </h3>
                        <div class="text-gray-300 space-y-3">
                            <p><?php echo $lang->get('privacy_third_parties_desc'); ?></p>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-800/50 rounded-lg">
                                    <h4 class="font-medium text-purple-300 mb-1">Spotify</h4>
                                    <p class="text-sm"><?php echo $lang->get('privacy_spotify_desc'); ?></p>
                                </div>
                                
                                <div class="p-3 bg-gray-800/50 rounded-lg">
                                    <h4 class="font-medium text-purple-300 mb-1">Apple Music</h4>
                                    <p class="text-sm"><?php echo $lang->get('privacy_apple_desc'); ?></p>
                                </div>
                                
                                <div class="p-3 bg-gray-800/50 rounded-lg">
                                    <h4 class="font-medium text-purple-300 mb-1">YouTube Music</h4>
                                    <p class="text-sm"><?php echo $lang->get('privacy_youtube_desc'); ?></p>
                                </div>
                                
                                <div class="p-3 bg-gray-800/50 rounded-lg">
                                    <h4 class="font-medium text-purple-300 mb-1">Amazon Music</h4>
                                    <p class="text-sm"><?php echo $lang->get('privacy_amazon_desc'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('privacy_no_sale'); ?>
                        </h3>
                        <p class="text-gray-300"><?php echo $lang->get('privacy_no_sale_desc'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Data Retention Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_data_retention'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <?php echo $lang->get('privacy_account_data'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_account_data_retention'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <?php echo $lang->get('privacy_usage_data'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_usage_data_retention'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <?php echo $lang->get('privacy_logs'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_logs_retention'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <?php echo $lang->get('privacy_cookies'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_cookies_retention'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Rights Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_your_rights'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-eye mr-2"></i><?php echo $lang->get('privacy_right_access'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_right_access_desc'); ?></p>
                        </div>
                        
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-edit mr-2"></i><?php echo $lang->get('privacy_right_rectification'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_right_rectification_desc'); ?></p>
                        </div>
                        
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-trash mr-2"></i><?php echo $lang->get('privacy_right_erasure'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_right_erasure_desc'); ?></p>
                        </div>
                        
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                            <h3 class="text-lg font-medium text-purple-300 mb-2">
                                <i class="fas fa-download mr-2"></i><?php echo $lang->get('privacy_right_portability'); ?>
                            </h3>
                            <p class="text-sm"><?php echo $lang->get('privacy_right_portability_desc'); ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-900/30 rounded-lg border border-blue-500/20">
                        <h3 class="text-lg font-medium text-blue-300 mb-2">
                            <?php echo $lang->get('privacy_contact_rights'); ?>
                        </h3>
                        <p class="text-sm"><?php echo $lang->get('privacy_contact_rights_desc'); ?></p>
                        <div class="mt-3 space-y-1 text-sm">
                            <p><i class="fas fa-envelope mr-2"></i>privacy@playlist-manager.de</p>
                            <p><i class="fas fa-phone mr-2"></i>+49 (0) 123 456789</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cookies Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_cookies_title'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <p><?php echo $lang->get('privacy_cookies_desc'); ?></p>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-800/50 rounded-lg">
                            <h4 class="font-medium text-green-300 mb-1"><?php echo $lang->get('privacy_cookies_essential'); ?></h4>
                            <p class="text-sm"><?php echo $lang->get('privacy_cookies_essential_desc'); ?></p>
                        </div>
                        
                        <div class="p-3 bg-gray-800/50 rounded-lg">
                            <h4 class="font-medium text-yellow-300 mb-1"><?php echo $lang->get('privacy_cookies_functional'); ?></h4>
                            <p class="text-sm"><?php echo $lang->get('privacy_cookies_functional_desc'); ?></p>
                        </div>
                        
                        <div class="p-3 bg-gray-800/50 rounded-lg">
                            <h4 class="font-medium text-red-300 mb-1"><?php echo $lang->get('privacy_cookies_analytics'); ?></h4>
                            <p class="text-sm"><?php echo $lang->get('privacy_cookies_analytics_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="glass-card p-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('privacy_contact_title'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <p><?php echo $lang->get('privacy_contact_desc'); ?></p>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-3">
                                <?php echo $lang->get('privacy_data_protection_officer'); ?>
                            </h3>
                            <div class="text-sm space-y-1">
                                <p>Dr. Sarah Weber</p>
                                <p>Datenschutzbeauftragte</p>
                                <p><i class="fas fa-envelope mr-2"></i>dpo@playlist-manager.de</p>
                                <p><i class="fas fa-phone mr-2"></i>+49 (0) 123 456789</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-3">
                                <?php echo $lang->get('privacy_supervisory_authority'); ?>
                            </h3>
                            <div class="text-sm space-y-1">
                                <p>Landesbeauftragte für Datenschutz</p>
                                <p>Musterstraße 456</p>
                                <p>12345 Musterstadt</p>
                                <p><i class="fas fa-envelope mr-2"></i>poststelle@datenschutz.de</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html> 