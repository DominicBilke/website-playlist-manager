<?php
// Language Switcher Component
// Include this file in any page where you want the language switcher
?>
<div class="relative">
    <button id="language-dropdown" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
        <i class="fas fa-globe mr-2"></i><?php echo $lang->getLanguageName($lang->getCurrentLanguage()); ?>
        <i class="fas fa-chevron-down ml-1"></i>
    </button>
    <div id="language-menu" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-50 hidden">
        <a href="?lang=en<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo $lang->getCurrentLanguage() === 'en' ? 'bg-purple-50 text-purple-600' : ''; ?>">
            English
        </a>
        <a href="?lang=de<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo $lang->getCurrentLanguage() === 'de' ? 'bg-purple-50 text-purple-600' : ''; ?>">
            Deutsch
        </a>
    </div>
</div>

<script>
// Language dropdown functionality
document.getElementById('language-dropdown').addEventListener('click', function() {
    const menu = document.getElementById('language-menu');
    menu.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('language-dropdown');
    const menu = document.getElementById('language-menu');
    
    if (!dropdown.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script> 