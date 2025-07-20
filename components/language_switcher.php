<?php
// Language Switcher Component
// Include this file in any page where you want the language switcher

// Ensure language manager is loaded
if (!isset($lang)) {
    require_once 'script/languages.php';
}

// Include language utilities
require_once 'script/language_utils.php';
?>

<div class="language-switcher relative">
    <button id="language-toggle" class="language-toggle flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors">
        <i class="fas fa-globe"></i>
        <span><?php echo $lang->getCurrentLanguage() === 'de' ? 'DE' : 'EN'; ?></span>
        <i class="fas fa-chevron-down text-xs"></i>
    </button>
    
    <div id="language-dropdown" class="language-dropdown absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible transition-all duration-200 z-50">
        <a href="<?php echo buildLanguageUrl('en'); ?>" class="language-option block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors <?php echo $lang->getCurrentLanguage() === 'en' ? 'bg-purple-50 text-purple-600' : ''; ?>">
            <i class="fas fa-flag mr-2"></i>English
        </a>
        <a href="<?php echo buildLanguageUrl('de'); ?>" class="language-option block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors <?php echo $lang->getCurrentLanguage() === 'de' ? 'bg-purple-50 text-purple-600' : ''; ?>">
            <i class="fas fa-flag mr-2"></i>Deutsch
        </a>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
}

.language-toggle {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    transition: color 0.2s ease;
}

.language-toggle:hover {
    color: #9333ea;
}

.language-dropdown {
    transform: translateY(-10px);
    transition: all 0.2s ease;
}

.language-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.language-option {
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: #374151;
    transition: all 0.2s ease;
}

.language-option:hover {
    background-color: #faf5ff;
    color: #9333ea;
}

.language-option.active {
    background-color: #faf5ff;
    color: #9333ea;
}
</style>

<script>
// Language dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const languageToggle = document.getElementById('language-toggle');
    const languageDropdown = document.getElementById('language-dropdown');
    
    if (languageToggle && languageDropdown) {
        languageToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            languageDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageDropdown.contains(e.target)) {
                languageDropdown.classList.remove('show');
            }
        });
    }
});
</script> 