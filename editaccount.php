<?php
require 'script/inc_start.php';
require 'script/languages.php';

// Check if user is logged in
if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_message = '';
    $error_message = '';
    
    // Update account settings
    if (isset($_POST['update_settings'])) {
        $daytime_from = $_POST['daytime_from'] ?? '';
        $daytime_to = $_POST['daytime_to'] ?? '';
        $days = $_POST['days'] ?? '';
        
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=playlist_manager", "username", "password");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("UPDATE users SET daytime_from = ?, daytime_to = ?, days = ? WHERE id = ?");
            $stmt->execute([$daytime_from, $daytime_to, $days, $_SESSION['id']]);
            
            // Update session
            $_SESSION['daytime_from'] = $daytime_from;
            $_SESSION['daytime_to'] = $daytime_to;
            $_SESSION['days'] = $days;
            
            $success_message = $lang->getCurrentLanguage() === 'de' ? 'Einstellungen erfolgreich aktualisiert!' : 'Settings updated successfully!';
        } catch (Exception $e) {
            $error_message = $lang->getCurrentLanguage() === 'de' ? 'Fehler beim Aktualisieren der Einstellungen.' : 'Error updating settings.';
        }
    }
    
    // Change password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $error_message = $lang->getCurrentLanguage() === 'de' ? 'Neue Passwörter stimmen nicht überein.' : 'New passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $error_message = $lang->getCurrentLanguage() === 'de' ? 'Passwort muss mindestens 6 Zeichen lang sein.' : 'Password must be at least 6 characters long.';
        } else {
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=playlist_manager", "username", "password");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['id']]);
                $user = $stmt->fetch();
                
                if (password_verify($current_password, $user['password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $_SESSION['id']]);
                    
                    $success_message = $lang->getCurrentLanguage() === 'de' ? 'Passwort erfolgreich geändert!' : 'Password changed successfully!';
                } else {
                    $error_message = $lang->getCurrentLanguage() === 'de' ? 'Aktuelles Passwort ist falsch.' : 'Current password is incorrect.';
                }
            } catch (Exception $e) {
                $error_message = $lang->getCurrentLanguage() === 'de' ? 'Fehler beim Ändern des Passworts.' : 'Error changing password.';
            }
        }
    }
    
    // Delete account
    if (isset($_POST['delete_account'])) {
        $confirm_delete = $_POST['confirm_delete'] ?? '';
        
        if ($confirm_delete === 'DELETE') {
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=playlist_manager", "username", "password");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['id']]);
                
                session_destroy();
                header("Location: index.php");
                exit;
            } catch (Exception $e) {
                $error_message = $lang->getCurrentLanguage() === 'de' ? 'Fehler beim Löschen des Kontos.' : 'Error deleting account.';
            }
        } else {
            $error_message = $lang->getCurrentLanguage() === 'de' ? 'Bitte bestätigen Sie die Löschung mit "DELETE".' : 'Please confirm deletion with "DELETE".';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('account_settings'); ?> - Playlist Manager</title>
    <meta name="description" content="Manage your account settings and preferences">
    
    <!-- Modern CSS Framework -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .sidebar { transition: all 0.3s ease; }
        .sidebar.collapsed { width: 4rem; }
        .main-content { transition: all 0.3s ease; }
        .main-content.expanded { margin-left: 4rem; }
        .form-input:focus { box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-music text-2xl text-purple-600"></i>
                <span class="ml-2 text-xl font-bold text-gray-900">Playlist Manager</span>
            </div>
            <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="mt-8 px-4">
            <div class="space-y-2">
                <a href="account.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('dashboard'); ?></span>
                </a>
                <a href="spotify_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-spotify w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('spotify'); ?></span>
                </a>
                <a href="applemusic_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-apple w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('apple_music'); ?></span>
                </a>
                <a href="youtube_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-youtube w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('youtube_music'); ?></span>
                </a>
                <a href="amazon_play.php" class="flex items-center px-4 py-3 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                    <i class="fab fa-amazon w-5 h-5"></i>
                    <span class="ml-3"><?php echo $lang->get('amazon_music'); ?></span>
                </a>
                <a href="editaccount.php" class="flex items-center px-4 py-3 text-purple-600 bg-purple-50 rounded-lg">
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span class="ml-3 font-medium"><?php echo $lang->get('settings'); ?></span>
                </a>
            </div>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['login']); ?></p>
                    <p class="text-xs text-gray-500">Team <?php echo htmlspecialchars($_SESSION['team'] ?? 'N/A'); ?></p>
                </div>
            </div>
            <a href="script/logout.php" class="mt-3 flex items-center px-4 py-2 text-sm text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt w-4 h-4"></i>
                <span class="ml-3"><?php echo $lang->get('sign_out'); ?></span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="main-content ml-64 min-h-screen">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo $lang->get('account_settings'); ?></h1>
                    <p class="text-sm text-gray-600"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Verwalten Sie Ihre Kontoeinstellungen' : 'Manage your account settings'; ?></p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="account.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i><?php echo $lang->get('back'); ?>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Success/Error Messages -->
            <?php if (isset($success_message) && $success_message): ?>
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-800"><?php echo htmlspecialchars($success_message); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message) && $error_message): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-red-800"><?php echo htmlspecialchars($error_message); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Profile Information -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 ml-3"><?php echo $lang->get('profile'); ?></h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Benutzername' : 'Username'; ?></label>
                            <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($_SESSION['login']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Team' : 'Team'; ?></label>
                            <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($_SESSION['team'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Büro' : 'Office'; ?></label>
                            <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($_SESSION['office'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Playback Settings -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 ml-3"><?php echo $lang->get('playing_time'); ?></h3>
                    </div>
                    
                    <form method="POST" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="daytime_from" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Von' : 'From'; ?></label>
                                <input type="time" id="daytime_from" name="daytime_from" 
                                       value="<?php echo htmlspecialchars($_SESSION['daytime_from'] ?? '09:00'); ?>"
                                       class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="daytime_to" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Bis' : 'To'; ?></label>
                                <input type="time" id="daytime_to" name="daytime_to" 
                                       value="<?php echo htmlspecialchars($_SESSION['daytime_to'] ?? '18:00'); ?>"
                                       class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="days" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->get('active_days'); ?></label>
                            <select id="days" name="days" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="1" <?php echo ($_SESSION['days'] ?? '') === '1' ? 'selected' : ''; ?>><?php echo $lang->get('monday'); ?></option>
                                <option value="2" <?php echo ($_SESSION['days'] ?? '') === '2' ? 'selected' : ''; ?>><?php echo $lang->get('tuesday'); ?></option>
                                <option value="3" <?php echo ($_SESSION['days'] ?? '') === '3' ? 'selected' : ''; ?>><?php echo $lang->get('wednesday'); ?></option>
                                <option value="4" <?php echo ($_SESSION['days'] ?? '') === '4' ? 'selected' : ''; ?>><?php echo $lang->get('thursday'); ?></option>
                                <option value="5" <?php echo ($_SESSION['days'] ?? '') === '5' ? 'selected' : ''; ?>><?php echo $lang->get('friday'); ?></option>
                                <option value="6" <?php echo ($_SESSION['days'] ?? '') === '6' ? 'selected' : ''; ?>><?php echo $lang->get('saturday'); ?></option>
                                <option value="0" <?php echo ($_SESSION['days'] ?? '') === '0' ? 'selected' : ''; ?>><?php echo $lang->get('sunday'); ?></option>
                            </select>
                        </div>
                        
                        <button type="submit" name="update_settings" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i><?php echo $lang->get('save'); ?>
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lock text-yellow-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 ml-3"><?php echo $lang->get('change_password'); ?></h3>
                    </div>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->get('current_password'); ?></label>
                            <input type="password" id="current_password" name="current_password" required
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->get('new_password'); ?></label>
                            <input type="password" id="new_password" name="new_password" required minlength="6"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang->get('confirm_new_password'); ?></label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <button type="submit" name="change_password" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-key mr-2"></i><?php echo $lang->get('change_password'); ?>
                        </button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="card-hover bg-white rounded-xl shadow-sm p-6 border border-red-200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-red-900 ml-3"><?php echo $lang->get('delete_account'); ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4"><?php echo $lang->get('delete_account_warning'); ?></p>
                    </div>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="confirm_delete" class="block text-sm font-medium text-gray-700 mb-1">
                                <?php echo $lang->getCurrentLanguage() === 'de' ? 'Geben Sie "DELETE" ein, um zu bestätigen' : 'Type "DELETE" to confirm'; ?>
                            </label>
                            <input type="text" id="confirm_delete" name="confirm_delete" 
                                   placeholder="DELETE"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <button type="submit" name="delete_account" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-2"></i><?php echo $lang->get('delete_account'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Password confirmation validation
        document.getElementById('new_password').addEventListener('input', function() {
            const newPassword = this.value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const submitBtn = document.querySelector('button[name="change_password"]');
            
            if (confirmPassword && newPassword !== confirmPassword) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const submitBtn = document.querySelector('button[name="change_password"]');
            
            if (newPassword && newPassword !== confirmPassword) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>