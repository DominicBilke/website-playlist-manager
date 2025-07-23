<?php
require 'script/init.php';

// Require admin access
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (!$pdo) {
    echo '<div style="color:red;">Database connection failed. Please contact the administrator.</div>';
    exit;
}

// Get current user
$currentUser = $auth->getCurrentUser();

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);
    $tabAfter = '';
    
    switch ($action) {
        case 'update_user_status':
            $status = $_POST['status'] ?? 'active';
            $role = $_POST['role'] ?? 'user';
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET status = ?, role = ? WHERE id = ?");
                $stmt->execute([$status, $role, $userId]);
                
                // Log admin action
                $auth->logAdminAction($currentUser['id'], 'update_user', 'user', $userId, "Updated user status to $status and role to $role");
                
                $success = $lang->get('user_updated_successfully');
            } catch (PDOException $e) {
                $error = $lang->get('update_error');
            }
            break;
            
        case 'delete_user':
            if ($auth->isSuperAdmin()) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
                    $stmt->execute([$userId, $currentUser['id']]);
                    
                    // Log admin action
                    $auth->logAdminAction($currentUser['id'], 'delete_user', 'user', $userId, 'Deleted user');
                    
                    $success = $lang->get('user_deleted_successfully');
                } catch (PDOException $e) {
                    $error = $lang->get('delete_error');
                }
            } else {
                $error = $lang->get('access_denied');
            }
            break;
            
        case 'update_system_setting':
            // Support saving multiple settings at once (array input)
            if (is_array($_POST['setting_value'])) {
                $successCount = 0;
                $errorCount = 0;
                foreach ($_POST['setting_value'] as $settingKey => $settingValue) {
                    try {
                        $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                        $stmt->execute([$settingValue, $settingKey]);
                        $auth->logAdminAction($currentUser['id'], 'update_setting', 'system', 0, "Updated setting: $settingKey = $settingValue");
                        $successCount++;
                    } catch (PDOException $e) {
                        $errorCount++;
                    }
                }
                if ($successCount > 0) {
                    $success = $lang->get('setting_updated_successfully');
                }
                if ($errorCount > 0) {
                    $error = $lang->get('update_error');
                }
            } else {
                $settingKey = $_POST['setting_key'] ?? '';
                $settingValue = $_POST['setting_value'] ?? '';
                try {
                    $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                    $stmt->execute([$settingValue, $settingKey]);
                    $auth->logAdminAction($currentUser['id'], 'update_setting', 'system', 0, "Updated setting: $settingKey = $settingValue");
                    $success = $lang->get('setting_updated_successfully');
                } catch (PDOException $e) {
                    $error = $lang->get('update_error');
                }
            }
            $tabAfter = 'settings';
            break;
    }
    // After saving system settings, redirect to ?tab=settings for better UX
    if ($tabAfter) {
        header('Location: admin.php?tab=' . $tabAfter);
        exit;
    }
}

// Get statistics
try {
    // User statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch()['total_users'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as active_users FROM users WHERE status = 'active'");
    $stmt->execute();
    $activeUsers = $stmt->fetch()['active_users'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as admin_users FROM users WHERE role IN ('admin', 'super_admin')");
    $stmt->execute();
    $adminUsers = $stmt->fetch()['admin_users'];
    
    // Recent activity
    $stmt = $pdo->prepare("
        SELECT aal.*, u.login as admin_name 
        FROM admin_audit_log aal 
        JOIN users u ON aal.admin_id = u.id 
        ORDER BY aal.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentActivity = $stmt->fetchAll();
    
    // System settings
    $stmt = $pdo->prepare("SELECT * FROM system_settings ORDER BY setting_key");
    $stmt->execute();
    $systemSettings = $stmt->fetchAll();
    
    // Users list
    $stmt = $pdo->prepare("
        SELECT u.*, 
               COUNT(ls.id) as listening_sessions,
               MAX(u.last_login) as last_login
        FROM users u 
        LEFT JOIN listening_stats ls ON u.id = ls.user_id 
        GROUP BY u.id 
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = $lang->get('database_error');
}

// Get error/success messages
$error = $error ?? $_GET['error'] ?? '';
$success = $success ?? $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('admin_panel'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Admin-Panel für Playlist Manager' : 'Admin panel for Playlist Manager'; ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'components/header.php'; ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-shield-alt text-primary mr-3"></i><?php echo $lang->get('admin_panel'); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo $lang->getCurrentLanguage() === 'de' ? 'Verwalten Sie Benutzer, Einstellungen und Systemaktivitäten' : 'Manage users, settings, and system activities'; ?>
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">
                        <?php echo $lang->get('logged_in_as'); ?>: <strong><?php echo htmlspecialchars($currentUser['login']); ?></strong>
                    </span>
                    <span class="px-2 py-1 text-xs font-medium bg-primary-100 text-primary-800 rounded-full">
                        <?php echo ucfirst($currentUser['role']); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error mb-6">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo $totalUsers; ?></h3>
                            <p class="text-gray-600"><?php echo $lang->get('total_users'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo $activeUsers; ?></h3>
                            <p class="text-gray-600"><?php echo $lang->get('active_users'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo $adminUsers; ?></h3>
                            <p class="text-gray-600"><?php echo $lang->get('admin_users'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button class="tab-button active" data-tab="users">
                    <i class="fas fa-users mr-2"></i><?php echo $lang->get('user_management'); ?>
                </button>
                <button class="tab-button" data-tab="settings">
                    <i class="fas fa-cog mr-2"></i><?php echo $lang->get('system_settings'); ?>
                </button>
                <button class="tab-button" data-tab="activity">
                    <i class="fas fa-history mr-2"></i><?php echo $lang->get('recent_activity'); ?>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Users Tab -->
            <div id="users" class="tab-panel active">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-users mr-2"></i><?php echo $lang->get('user_management'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('user'); ?>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('team'); ?>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('role'); ?>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('status'); ?>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('last_login'); ?>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo $lang->get('actions'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-purple-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($user['login']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($user['team']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $user['role'] === 'super_admin' ? 'bg-red-100 text-red-800' : 
                                                      ($user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'); ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                                      ($user['status'] === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'); ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : '-'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="text-purple-600 hover:text-purple-900 mr-3" 
                                                    onclick="editUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($auth->isSuperAdmin() && $user['id'] !== $currentUser['id']): ?>
                                            <button class="text-red-600 hover:text-red-900" 
                                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['login']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-panel hidden">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-cog mr-2"></i><?php echo $lang->get('system_settings'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Platform API Credentials Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform API Credentials</h3>
                            <?php
                            $platforms = [
                                'spotify' => [
                                    'label' => 'Spotify',
                                    'fields' => [
                                        'spotify_client_id' => 'Client ID',
                                        'spotify_client_secret' => 'Client Secret',
                                    ],
                                    'redirect_uri' => 'spotify_redirect_uri',
                                ],
                                'apple_music' => [
                                    'label' => 'Apple Music',
                                    'fields' => [
                                        'apple_music_team_id' => 'Team ID',
                                        'apple_music_key_id' => 'Key ID',
                                        'apple_music_private_key_path' => 'Private Key Path',
                                    ],
                                    'redirect_uri' => null,
                                ],
                                'youtube' => [
                                    'label' => 'YouTube Music',
                                    'fields' => [
                                        'youtube_client_id' => 'Client ID',
                                        'youtube_client_secret' => 'Client Secret',
                                    ],
                                    'redirect_uri' => 'youtube_redirect_uri',
                                ],
                                'amazon' => [
                                    'label' => 'Amazon Music',
                                    'fields' => [
                                        'amazon_client_id' => 'Client ID',
                                        'amazon_client_secret' => 'Client Secret',
                                    ],
                                    'redirect_uri' => 'amazon_redirect_uri',
                                ],
                            ];
                            $systemSettingsAssoc = [];
                            foreach ($systemSettings as $setting) {
                                $systemSettingsAssoc[$setting['setting_key']] = $setting['setting_value'];
                            }
                            foreach ($platforms as $platformKey => $platform):
                            ?>
                            <form method="POST" class="mb-8 bg-white rounded-lg shadow p-4 space-y-4">
                                <input type="hidden" name="action" value="update_system_setting">
                                <h4 class="text-md font-semibold text-gray-800 mb-2"><?php echo $platform['label']; ?></h4>
                                <?php foreach ($platform['fields'] as $key => $label): ?>
                                    <div class="form-group">
                                        <label for="<?php echo $key; ?>" class="form-label"><?php echo $label; ?></label>
                                        <input type="text" id="<?php echo $key; ?>" name="setting_value[<?php echo $key; ?>]" class="form-input w-full" value="<?php echo htmlspecialchars($systemSettingsAssoc[$key] ?? ''); ?>">
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($platform['redirect_uri']): ?>
                                    <div class="form-group">
                                        <label class="form-label">Redirect URI</label>
                                        <div class="flex space-x-2">
                                            <input type="text" class="form-input w-full bg-gray-100" value="<?php echo htmlspecialchars($systemSettingsAssoc[$platform['redirect_uri']] ?? ''); ?>" readonly id="<?php echo $platform['redirect_uri']; ?>-readonly">
                                            <button type="button" class="btn btn-secondary" onclick="navigator.clipboard.writeText(document.getElementById('<?php echo $platform['redirect_uri']; ?>-readonly').value)"><i class="fas fa-copy"></i> Copy</button>
                                        </div>
                                        <p class="form-help">Paste this URI into your <?php echo $platform['label']; ?> developer dashboard. This value is generated by the system and cannot be changed.</p>
                                    </div>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-save"></i> Save <?php echo $platform['label']; ?> Credentials</button>
                            </form>
                            <?php endforeach; ?>
                        </div>
                        <!-- End Platform API Credentials Section -->

                        <!-- Other System Settings -->
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="update_system_setting">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 mt-8">Other System Settings</h3>
                            <?php
                            $platform_keys = [];
                            foreach ($platforms as $platform) {
                                foreach ($platform['fields'] as $key => $label) {
                                    $platform_keys[$key] = true;
                                }
                                if ($platform['redirect_uri']) {
                                    $platform_keys[$platform['redirect_uri']] = true;
                                }
                            }
                            ?>
                            <?php foreach ($systemSettings as $setting): ?>
                                <?php if (!array_key_exists($setting['setting_key'], $platform_keys)): ?>
                                <div class="form-group">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                        <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input 
                                            type="text" 
                                            id="<?php echo $setting['setting_key']; ?>" 
                                            name="setting_value" 
                                            class="form-input flex-1" 
                                            value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                        >
                                        <input type="hidden" name="setting_key" value="<?php echo $setting['setting_key']; ?>">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                    <p class="form-help"><?php echo htmlspecialchars($setting['description'] ?? ''); ?></p>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div id="activity" class="tab-panel hidden">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-history mr-2"></i><?php echo $lang->get('recent_activity'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <?php foreach ($recentActivity as $activity): ?>
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-shield text-purple-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($activity['admin_name']); ?>
                                        <span class="text-gray-500"><?php echo htmlspecialchars($activity['action']); ?></span>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($activity['details'] ?? ''); ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?php echo date('d.m.Y H:i:s', strtotime($activity['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-gray-900"><?php echo $lang->get('edit_user'); ?></h3>
                <button class="modal-close" onclick="closeModal('editUserModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="modal-body">
                <input type="hidden" name="action" value="update_user_status">
                <input type="hidden" name="user_id" id="editUserId">
                
                <div class="form-group">
                    <label for="editUserRole" class="form-label"><?php echo $lang->get('role'); ?></label>
                    <select id="editUserRole" name="role" class="form-input">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <?php if ($auth->isSuperAdmin()): ?>
                        <option value="super_admin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editUserStatus" class="form-label"><?php echo $lang->get('status'); ?></label>
                    <select id="editUserStatus" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">
                        <?php echo $lang->get('cancel'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $lang->get('save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <script>
    // Tab functionality
    function activateTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
        document.querySelector('.tab-button[data-tab="' + tabName + '"]').classList.add('active');
        document.getElementById(tabName).classList.remove('hidden');
    }
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            activateTab(this.dataset.tab);
        });
    });
    // On page load, show settings tab if ?tab=settings is in the URL
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && document.getElementById(tab)) {
            activateTab(tab);
        }
    })();
    
    // Modal functionality
    function editUser(userId) {
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUserModal').classList.remove('hidden');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    
    function deleteUser(userId, username) {
        if (confirm('<?php echo $lang->getCurrentLanguage() === 'de' ? 'Sind Sie sicher, dass Sie den Benutzer' : 'Are you sure you want to delete user'; ?> "' + username + '" <?php echo $lang->getCurrentLanguage() === 'de' ? 'löschen möchten?' : '?'; ?>')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="${userId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html> 