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
    <title><?php echo $lang->get('impressum_title'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->get('impressum_description'); ?>">
    
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
    <main class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                <?php echo $lang->get('impressum_title'); ?>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                <?php echo $lang->get('impressum_subtitle'); ?>
            </p>
        </div>

        <!-- Content Container -->
        <div class="max-w-4xl mx-auto">
            <!-- Legal Information Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-building mr-3 text-purple-600"></i><?php echo $lang->get('impressum_legal_info'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-6">
                        <!-- Company Information -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-briefcase mr-2 text-blue-600"></i><?php echo $lang->get('impressum_company'); ?>
                                </h3>
                                <div class="text-gray-700 space-y-2">
                                    <p><strong>Highlight-Concerts GmbH</strong></p>
                                    <p>Holstenbrücke 8-10</p>
                                    <p>24103 Kiel</p>
                                    <p>Deutschland</p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">
                                    <i class="fas fa-address-book mr-2 text-green-600"></i><?php echo $lang->get('impressum_contact'); ?>
                                </h3>
                                <div class="text-gray-700 space-y-2">
                                    <p><i class="fas fa-phone mr-2"></i>+49 (0)431 23 95 22 – 0</p>
                                    <p><i class="fas fa-envelope mr-2"></i>info [at] highlight-concerts [dot] com</p>
                                    <p><i class="fas fa-globe mr-2"></i>www.playlist-manager.de</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-users mr-3 text-indigo-600"></i><?php echo $lang->get('impressum_management'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-user-tie mr-2 text-purple-600"></i><?php echo $lang->get('impressum_managing_director'); ?>
                            </h3>
                            <div class="text-gray-700">
                                <p>Max Mustermann</p>
                                <p class="text-sm text-gray-500"><?php echo $lang->get('impressum_managing_director_desc'); ?></p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">
                                <i class="fas fa-user-shield mr-2 text-blue-600"></i><?php echo $lang->get('impressum_supervisory_board'); ?>
                            </h3>
                            <div class="text-gray-700">
                                <p>Dr. Anna Schmidt</p>
                                <p class="text-sm text-gray-500"><?php echo $lang->get('impressum_supervisory_board_desc'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Information Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-file-contract mr-3 text-green-600"></i><?php echo $lang->get('impressum_registration'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-gavel mr-2 text-orange-600"></i><?php echo $lang->get('impressum_court'); ?>
                            </h3>
                            <p class="text-gray-700">Amtsgericht Kiel</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-hashtag mr-2 text-blue-600"></i><?php echo $lang->get('impressum_registration_number'); ?>
                            </h3>
                            <p class="text-gray-700">HRB 12345</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-receipt mr-2 text-green-600"></i><?php echo $lang->get('impressum_tax_id'); ?>
                            </h3>
                            <p class="text-gray-700">DE123456789</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-percentage mr-2 text-purple-600"></i><?php echo $lang->get('impressum_vat_id'); ?>
                            </h3>
                            <p class="text-gray-700">DE123456789</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information Card -->
            <div class="card mb-8">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-certificate mr-3 text-teal-600"></i><?php echo $lang->get('impressum_professional_info'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-medal mr-2 text-amber-600"></i><?php echo $lang->get('impressum_professional_title'); ?>
                            </h3>
                            <p class="text-gray-700"><?php echo $lang->get('impressum_professional_title_desc'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-balance-scale mr-2 text-indigo-600"></i><?php echo $lang->get('impressum_professional_authority'); ?>
                            </h3>
                            <p class="text-gray-700"><?php echo $lang->get('impressum_professional_authority_desc'); ?></p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-book mr-2 text-red-600"></i><?php echo $lang->get('impressum_professional_regulation'); ?>
                            </h3>
                            <p class="text-gray-700"><?php echo $lang->get('impressum_professional_regulation_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        <i class="fas fa-exclamation-triangle mr-3 text-orange-600"></i><?php echo $lang->get('impressum_disclaimer'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="space-y-4 text-gray-700">
                        <p><?php echo $lang->get('impressum_disclaimer_content'); ?></p>
                        
                        <div class="mt-6 p-4 bg-orange-50 rounded-lg border border-orange-200">
                            <h3 class="text-lg font-medium text-orange-800 mb-2">
                                <i class="fas fa-shield-alt mr-2"></i><?php echo $lang->get('impressum_liability'); ?>
                            </h3>
                            <p class="text-sm text-orange-700"><?php echo $lang->get('impressum_liability_content'); ?></p>
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