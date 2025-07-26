<?php
// Language Switcher Component
// Include this file in any page where you want the language switcher

// Ensure language manager is loaded
if (!isset($lang)) {
    if (!function_exists('init_app')) {
        require_once 'script/includes.php';
    }
    $lang = init_app();
    init_language_utils();
}
?>

<div class="relative">
    <button id="language-toggle" class="btn btn-secondary btn-sm">
        <i class="fas fa-globe mr-2"></i>
        <span><?php echo $lang->getCurrentLanguage() === 'de' ? 'DE' : 'EN'; ?></span>
        <i class="fas fa-chevron-down ml-2"></i>
    </button>
    
    <div id="language-dropdown" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        <a href="<?php echo buildLanguageUrl('en'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors <?php echo $lang->getCurrentLanguage() === 'en' ? 'bg-primary-50 text-primary-600' : ''; ?>">
            <i class="fas fa-flag mr-2"></i>English
        </a>
        <a href="<?php echo buildLanguageUrl('de'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors <?php echo $lang->getCurrentLanguage() === 'de' ? 'bg-primary-50 text-primary-600' : ''; ?>">
            <i class="fas fa-flag mr-2"></i>Deutsch
        </a>
    </div>
</div>

<script>
// Language dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const languageToggle = document.getElementById('language-toggle');
    const languageDropdown = document.getElementById('language-dropdown');
    
    if (languageToggle && languageDropdown) {
        languageToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            languageDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageDropdown.contains(e.target)) {
                languageDropdown.classList.add('hidden');
            }
        });
    }
});
</script> 