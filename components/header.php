<?php
// Ensure language manager is loaded
if (!isset($lang)) {
    require_once 'script/languages.php';
    $lang = new LanguageManager();
}
?>

<header class="header">
    <div class="container">
        <div class="header-content">
            <!-- Brand/Logo -->
            <div class="header-brand">
                <i class="fas fa-music text-primary"></i>
                <span>Playlist Manager</span>
            </div>
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open menu" style="display:none;">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Navigation -->
            <nav class="header-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged in user navigation -->
                    <a href="account.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-user mr-2"></i><?php echo $lang->get('dashboard'); ?>
                    </a>
                    
                    <a href="player.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-play mr-2"></i><?php echo $lang->get('music_player'); ?>
                    </a>
                    
                    <!-- Platform Links -->
                    <div class="flex items-center gap-2">
                        <a href="spotify_play.php" class="btn btn-sm" style="background: var(--spotify-green); color: white; border-color: var(--spotify-green);">
                            <i class="fab fa-spotify"></i>
                        </a>
                        <a href="applemusic_play.php" class="btn btn-sm" style="background: var(--apple-pink); color: white; border-color: var(--apple-pink);">
                            <i class="fab fa-apple"></i>
                        </a>
                        <a href="youtube_play.php" class="btn btn-sm" style="background: var(--youtube-red); color: white; border-color: var(--youtube-red);">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="amazon_play.php" class="btn btn-sm" style="background: var(--amazon-orange); color: white; border-color: var(--amazon-orange);">
                            <i class="fas fa-music"></i>
                        </a>
                    </div>
                    
                    <!-- Admin Panel Link (if user is admin) -->
                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin'])): ?>
                        <a href="admin.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-shield-alt mr-2"></i><?php echo $lang->get('admin_panel'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Language Switcher -->
                    <?php include 'components/language_switcher.php'; ?>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="btn btn-secondary btn-sm" onclick="toggleUserMenu()">
                            <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['login'] ?? 'User'); ?>
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                            <div class="p-2">
                                <div class="text-sm text-gray-500 mb-2"><?php echo $lang->get('logged_in_as'); ?></div>
                                <div class="font-medium text-gray-900 mb-2"><?php echo htmlspecialchars($_SESSION['login'] ?? 'User'); ?></div>
                                <div class="text-xs text-gray-500 mb-3"><?php echo $lang->get('team'); ?>: <?php echo htmlspecialchars($_SESSION['team'] ?? 'N/A'); ?></div>
                                
                                <a href="account.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    <i class="fas fa-cog mr-2"></i><?php echo $lang->get('settings'); ?>
                                </a>
                                
                                <a href="script/logout.php" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded">
                                    <i class="fas fa-sign-out-alt mr-2"></i><?php echo $lang->get('sign_out'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Guest navigation -->
                    <a href="login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i><?php echo $lang->get('sign_in'); ?>
                    </a>
                    
                    <a href="signup.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-user-plus mr-2"></i><?php echo $lang->get('sign_up'); ?>
                    </a>
                    
                    <!-- Language Switcher -->
                    <?php include 'components/language_switcher.php'; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('hidden');
}

// Close user menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('userMenu');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().includes('toggleUserMenu')) {
        return;
    }
    
    if (!menu.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script> 