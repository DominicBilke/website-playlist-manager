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
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/main.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Modern CSS Framework -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <main class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                <?php echo $lang->get('privacy_title'); ?>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                <?php echo $lang->get('privacy_subtitle'); ?>
            </p>
            <div class="mt-4 text-sm text-gray-500">
                <p><?php echo $lang->get('privacy_last_updated'); ?>: <?php echo date('d.m.Y'); ?></p>
            </div>
        </div>

        <!-- Content Container -->
        <div class="max-w-4xl mx-auto">
            <!-- Introduction Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-shield-alt mr-3 text-purple-600"></i><?php echo $lang->get('privacy_introduction'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4 text-gray-700">
                        <p><?php echo $lang->get('privacy_introduction_content'); ?></p>
                        
                        <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <h3 class="text-lg font-medium text-purple-800 mb-2">
                                <?php echo $lang->get('privacy_controller'); ?>
                            </h3>
                            <div class="text-sm space-y-1 text-purple-700">
                                <p><strong>Highlight-Concerts GmbH</strong></p>
                                <p>Holstenbrücke 8-10</p>
                                <p>24103 Kiel</p>
                                <p>Deutschland</p>
                                <p class="mt-2">
                                    <i class="fas fa-envelope mr-2"></i>info [at] highlight-concerts [dot] com
                                </p>
                                <p><i class="fas fa-phone mr-2"></i>+49 (0)431 23 95 22 – 0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Information Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i><?php echo $lang->get('privacy_general_info'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-lock mr-2 text-green-600"></i><?php echo $lang->get('privacy_ssl_encryption'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_ssl_encryption_desc'); ?></p>
                                <p><?php echo $lang->get('privacy_ssl_encryption_how'); ?></p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-clock mr-2 text-orange-600"></i><?php echo $lang->get('privacy_data_retention'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_data_retention_desc'); ?></p>
                                <div class="mt-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                                    <p class="text-sm text-orange-800"><?php echo $lang->get('privacy_data_retention_exceptions'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Rights Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-user-shield mr-3 text-indigo-600"></i><?php echo $lang->get('privacy_your_rights'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-ban mr-2 text-red-600"></i><?php echo $lang->get('privacy_objection'); ?>
                            </h3>
                            <div class="text-gray-700">
                                <div class="p-4 bg-red-50 rounded-lg border border-red-200 mb-3">
                                    <p class="text-sm text-red-800 font-medium"><?php echo $lang->get('privacy_objection_important'); ?></p>
                                </div>
                                <p class="mb-3"><?php echo $lang->get('privacy_objection_desc'); ?></p>
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-700"><?php echo $lang->get('privacy_objection_exceptions'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-undo mr-2 text-blue-600"></i><?php echo $lang->get('privacy_withdrawal'); ?>
                                </h3>
                                <p class="text-gray-700 text-sm"><?php echo $lang->get('privacy_withdrawal_desc'); ?></p>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-gavel mr-2 text-purple-600"></i><?php echo $lang->get('privacy_complaint'); ?>
                                </h3>
                                <p class="text-gray-700 text-sm"><?php echo $lang->get('privacy_complaint_desc'); ?></p>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-download mr-2 text-green-600"></i><?php echo $lang->get('privacy_portability'); ?>
                                </h3>
                                <p class="text-gray-700 text-sm"><?php echo $lang->get('privacy_portability_desc'); ?></p>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-edit mr-2 text-orange-600"></i><?php echo $lang->get('privacy_correction'); ?>
                                </h3>
                                <p class="text-gray-700 text-sm"><?php echo $lang->get('privacy_correction_desc'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Collection Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-database mr-3 text-teal-600"></i><?php echo $lang->get('privacy_data_collection'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-cookie-bite mr-2 text-amber-600"></i><?php echo $lang->get('privacy_cookies'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-3">
                                <p><?php echo $lang->get('privacy_cookies_desc'); ?></p>
                                
                                <div class="grid md:grid-cols-3 gap-4">
                                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                        <h4 class="font-medium text-amber-800 mb-1"><?php echo $lang->get('privacy_cookies_necessary'); ?></h4>
                                        <p class="text-xs text-amber-700"><?php echo $lang->get('privacy_cookies_necessary_desc'); ?></p>
                                    </div>
                                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <h4 class="font-medium text-blue-800 mb-1"><?php echo $lang->get('privacy_cookies_functional'); ?></h4>
                                        <p class="text-xs text-blue-700"><?php echo $lang->get('privacy_cookies_functional_desc'); ?></p>
                                    </div>
                                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                        <h4 class="font-medium text-purple-800 mb-1"><?php echo $lang->get('privacy_cookies_analytics'); ?></h4>
                                        <p class="text-xs text-purple-700"><?php echo $lang->get('privacy_cookies_analytics_desc'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-server mr-2 text-gray-600"></i><?php echo $lang->get('privacy_server_logs'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_server_logs_desc'); ?></p>
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <h4 class="font-medium text-gray-800 mb-2"><?php echo $lang->get('privacy_server_logs_data'); ?></h4>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_browser'); ?></li>
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_os'); ?></li>
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_referrer'); ?></li>
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_hostname'); ?></li>
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_time'); ?></li>
                                        <li><i class="fas fa-circle text-xs mr-2"></i><?php echo $lang->get('privacy_logs_ip'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact and Registration Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-envelope mr-3 text-green-600"></i><?php echo $lang->get('privacy_contact_registration'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-phone mr-2 text-blue-600"></i><?php echo $lang->get('privacy_contact_methods'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_contact_methods_desc'); ?></p>
                                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="font-medium text-blue-800 mb-2"><?php echo $lang->get('privacy_contact_processing'); ?></h4>
                                    <p class="text-sm text-blue-700"><?php echo $lang->get('privacy_contact_processing_desc'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-user-plus mr-2 text-purple-600"></i><?php echo $lang->get('privacy_registration'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_registration_desc'); ?></p>
                                <div class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <h4 class="font-medium text-purple-800 mb-2"><?php echo $lang->get('privacy_registration_purpose'); ?></h4>
                                    <p class="text-sm text-purple-700"><?php echo $lang->get('privacy_registration_purpose_desc'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third-Party Services Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-external-link-alt mr-3 text-orange-600"></i><?php echo $lang->get('privacy_third_party'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fab fa-youtube mr-2 text-red-600"></i>YouTube (with extended data protection)
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_youtube_desc'); ?></p>
                                <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                    <h4 class="font-medium text-red-800 mb-2"><?php echo $lang->get('privacy_youtube_processing'); ?></h4>
                                    <p class="text-sm text-red-700"><?php echo $lang->get('privacy_youtube_processing_desc'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-cloud mr-2 text-blue-600"></i><?php echo $lang->get('privacy_hosting'); ?>
                            </h3>
                            <div class="text-gray-700 space-y-2">
                                <p><?php echo $lang->get('privacy_hosting_desc'); ?></p>
                                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="font-medium text-blue-800 mb-2">IONOS SE</h4>
                                    <p class="text-sm text-blue-700">Elgendorfer Str. 57<br>56410 Montabaur, Germany</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-address-card mr-3 text-indigo-600"></i><?php echo $lang->get('privacy_contact_info'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="inline-block p-6 bg-indigo-50 rounded-lg border border-indigo-200">
                            <h3 class="text-lg font-medium text-indigo-800 mb-3">
                                <?php echo $lang->get('privacy_questions'); ?>
                            </h3>
                            <p class="text-indigo-700 mb-4"><?php echo $lang->get('privacy_contact_us'); ?></p>
                            <div class="space-y-2 text-sm text-indigo-600">
                                <p><i class="fas fa-envelope mr-2"></i>info [at] highlight-concerts [dot] com</p>
                                <p><i class="fas fa-phone mr-2"></i>+49 (0)431 23 95 22 – 0</p>
                                <p><i class="fas fa-map-marker-alt mr-2"></i>Holstenbrücke 8-10, 24103 Kiel, Deutschland</p>
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