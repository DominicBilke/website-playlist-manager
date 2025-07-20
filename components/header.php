<?php
// Ensure language manager is loaded
if (!isset($lang)) {
    require_once 'script/languages.php';
}

// Include language utilities
require_once 'script/language_utils.php';
?>
<header class="header bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="index.php" class="nav-brand">
                    <i class="fas fa-music text-2xl text-purple-600"></i>
                    <span class="ml-2 text-xl font-bold text-gray-900">Playlist Manager</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('home'); ?>
                </a>
                <a href="account.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'account.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('dashboard'); ?>
                </a>
                <a href="spotify_play.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'spotify_play.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('spotify'); ?>
                </a>
                <a href="applemusic_play.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'applemusic_play.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('apple_music'); ?>
                </a>
                <a href="youtube_play.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'youtube_play.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('youtube_music'); ?>
                </a>
                <a href="amazon_play.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'amazon_play.php' ? 'active' : ''; ?>">
                    <?php echo $lang->get('amazon_music'); ?>
                </a>
            </nav>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                <!-- Language Switcher -->
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

                <!-- User Menu -->
                <?php if (isset($_SESSION['id'])): ?>
                    <div class="user-menu relative">
                        <button id="user-toggle" class="user-toggle flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors">
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="hidden md:block"><?php echo htmlspecialchars($_SESSION['login']); ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div id="user-dropdown" class="user-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible transition-all duration-200 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['login']); ?></p>
                                <p class="text-xs text-gray-500">Team <?php echo htmlspecialchars($_SESSION['team'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div class="py-1">
                                <a href="account.php" class="user-menu-item">
                                    <i class="fas fa-tachometer-alt mr-3"></i><?php echo $lang->get('dashboard'); ?>
                                </a>
                                <a href="editaccount.php" class="user-menu-item">
                                    <i class="fas fa-cog mr-3"></i><?php echo $lang->get('settings'); ?>
                                </a>
                                <hr class="my-1 border-gray-100">
                                <a href="script/logout.php" class="user-menu-item text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-3"></i><?php echo $lang->get('sign_out'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Authentication Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="login.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i><?php echo $lang->get('sign_in'); ?>
                        </a>
                        <a href="signup.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus mr-2"></i><?php echo $lang->get('sign_up'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="mobile-menu-toggle md:hidden p-2 text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="mobile-menu md:hidden bg-white border-t border-gray-200 opacity-0 invisible transition-all duration-200">
        <div class="px-4 py-2 space-y-1">
            <a href="index.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home mr-3"></i><?php echo $lang->get('home'); ?>
            </a>
            <a href="account.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'account.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt mr-3"></i><?php echo $lang->get('dashboard'); ?>
            </a>
            <a href="spotify_play.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'spotify_play.php' ? 'active' : ''; ?>">
                <i class="fab fa-spotify mr-3"></i><?php echo $lang->get('spotify'); ?>
            </a>
            <a href="applemusic_play.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'applemusic_play.php' ? 'active' : ''; ?>">
                <i class="fab fa-apple mr-3"></i><?php echo $lang->get('apple_music'); ?>
            </a>
            <a href="youtube_play.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'youtube_play.php' ? 'active' : ''; ?>">
                <i class="fab fa-youtube mr-3"></i><?php echo $lang->get('youtube_music'); ?>
            </a>
            <a href="amazon_play.php" class="mobile-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'amazon_play.php' ? 'active' : ''; ?>">
                <i class="fab fa-amazon mr-3"></i><?php echo $lang->get('amazon_music'); ?>
            </a>
        </div>
    </div>
</header>



<style>
/* Header-specific styles */
.header {
    position: sticky;
    top: 0;
    z-index: 40;
}

.nav-link {
    position: relative;
    padding: 0.5rem 0;
    color: var(--gray-600);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.nav-link:hover,
.nav-link.active {
    color: var(--primary-600);
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-600);
}

.language-switcher,
.user-menu {
    position: relative;
}

.language-toggle,
.user-toggle {
    background: none;
    border: none;
    cursor: pointer;
}

.language-dropdown,
.user-dropdown {
    transform: translateY(-10px);
}

.language-dropdown.show,
.user-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.language-option {
    text-decoration: none;
    display: block;
}

.user-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.user-menu-item:hover {
    background-color: var(--gray-50);
    color: var(--primary-600);
}

.mobile-menu-toggle {
    background: none;
    border: none;
    cursor: pointer;
}

.mobile-menu {
    max-height: 0;
    overflow: hidden;
}

.mobile-menu.show {
    max-height: 300px;
    opacity: 1;
    visibility: visible;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: all var(--transition-fast);
    border-radius: var(--radius-md);
}

.mobile-nav-link:hover,
.mobile-nav-link.active {
    background-color: var(--primary-50);
    color: var(--primary-600);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .nav-brand span {
        display: none;
    }
    
    .language-toggle span,
    .user-toggle span {
        display: none;
    }
}
</style>

<script>
// Header JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Language switcher
    const languageToggle = document.getElementById('language-toggle');
    const languageDropdown = document.getElementById('language-dropdown');
    
    if (languageToggle && languageDropdown) {
        languageToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            languageDropdown.classList.toggle('show');
        });
    }
    
    // User menu
    const userToggle = document.getElementById('user-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
    }
    
    // Mobile menu
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (languageDropdown && !languageToggle.contains(e.target) && !languageDropdown.contains(e.target)) {
            languageDropdown.classList.remove('show');
        }
        
        if (userDropdown && !userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (mobileMenu && !mobileMenuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.remove('show');
        }
    });
});
</script> 