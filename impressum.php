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
                <?php echo $lang->get('impressum_title'); ?>
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                <?php echo $lang->get('impressum_subtitle'); ?>
            </p>
        </div>

        <!-- Content Container -->
        <div class="max-w-4xl mx-auto">
            <!-- Legal Information Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('impressum_legal_info'); ?>
                </h2>
                
                <div class="space-y-6">
                    <!-- Company Information -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-3">
                                <?php echo $lang->get('impressum_company'); ?>
                            </h3>
                            <div class="text-gray-300 space-y-2">
                                <p><strong>Playlist Manager GmbH</strong></p>
                                <p>Musterstraße 123</p>
                                <p>12345 Musterstadt</p>
                                <p>Deutschland</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-purple-300 mb-3">
                                <?php echo $lang->get('impressum_contact'); ?>
                            </h3>
                            <div class="text-gray-300 space-y-2">
                                <p><i class="fas fa-phone mr-2"></i>+49 (0) 123 456789</p>
                                <p><i class="fas fa-envelope mr-2"></i>info@playlist-manager.de</p>
                                <p><i class="fas fa-globe mr-2"></i>www.playlist-manager.de</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('impressum_management'); ?>
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('impressum_managing_director'); ?>
                        </h3>
                        <div class="text-gray-300">
                            <p>Max Mustermann</p>
                            <p class="text-sm text-gray-400"><?php echo $lang->get('impressum_managing_director_desc'); ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-3">
                            <?php echo $lang->get('impressum_supervisory_board'); ?>
                        </h3>
                        <div class="text-gray-300">
                            <p>Dr. Anna Schmidt</p>
                            <p class="text-sm text-gray-400"><?php echo $lang->get('impressum_supervisory_board_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Information Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('impressum_registration'); ?>
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_court'); ?>
                        </h3>
                        <p class="text-gray-300">Amtsgericht Musterstadt</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_registration_number'); ?>
                        </h3>
                        <p class="text-gray-300">HRB 12345</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_tax_id'); ?>
                        </h3>
                        <p class="text-gray-300">DE123456789</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_vat_id'); ?>
                        </h3>
                        <p class="text-gray-300">DE123456789</p>
                    </div>
                </div>
            </div>

            <!-- Professional Information Card -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('impressum_professional_info'); ?>
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_professional_title'); ?>
                        </h3>
                        <p class="text-gray-300"><?php echo $lang->get('impressum_professional_title_desc'); ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_professional_authority'); ?>
                        </h3>
                        <p class="text-gray-300"><?php echo $lang->get('impressum_professional_authority_desc'); ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_professional_regulation'); ?>
                        </h3>
                        <p class="text-gray-300"><?php echo $lang->get('impressum_professional_regulation_desc'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Disclaimer Card -->
            <div class="glass-card p-8">
                <h2 class="text-2xl font-semibold text-white mb-6">
                    <?php echo $lang->get('impressum_disclaimer'); ?>
                </h2>
                
                <div class="space-y-4 text-gray-300">
                    <p><?php echo $lang->get('impressum_disclaimer_content'); ?></p>
                    
                    <div class="mt-6 p-4 bg-purple-900/30 rounded-lg border border-purple-500/20">
                        <h3 class="text-lg font-medium text-purple-300 mb-2">
                            <?php echo $lang->get('impressum_liability'); ?>
                        </h3>
                        <p class="text-sm"><?php echo $lang->get('impressum_liability_content'); ?></p>
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