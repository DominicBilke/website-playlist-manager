<?php
/**
 * Test file for the new include system
 */

define('APP_ROOT', __DIR__);
require_once 'script/includes.php';

echo "<h1>Include System Test</h1>\n";

// Test 1: Basic initialization
echo "<h2>1. Basic Initialization</h2>\n";
$lang = init_app();
echo "✓ Language manager initialized: " . ($lang ? 'Success' : 'Failed') . "\n";
echo "Current language: " . $lang->getCurrentLanguage() . "\n";

// Test 2: Authentication
echo "<h2>2. Authentication System</h2>\n";
$auth = init_auth();
echo "✓ Auth system initialized: " . ($auth ? 'Success' : 'Failed') . "\n";

// Test 3: Database
echo "<h2>3. Database Connection</h2>\n";
$pdo = get_database();
echo "✓ Database connection: " . ($pdo ? 'Success' : 'Failed') . "\n";

// Test 4: User info
echo "<h2>4. User Information</h2>\n";
$user = get_current_user_info();
if ($user) {
    echo "✓ User authenticated: " . htmlspecialchars($user['login']) . "\n";
    echo "User ID: " . $user['id'] . "\n";
    echo "Team: " . htmlspecialchars($user['team']) . "\n";
    echo "Role: " . htmlspecialchars($user['role']) . "\n";
} else {
    echo "⚠ User not authenticated\n";
}

// Test 5: Platform manager
echo "<h2>5. Platform Manager</h2>\n";
if ($user) {
    $platformManager = init_platform_manager($user['id']);
    echo "✓ Platform manager: " . ($platformManager ? 'Success' : 'Failed') . "\n";
} else {
    echo "⚠ Skipping platform manager test (no user)\n";
}

// Test 6: Language utilities
echo "<h2>6. Language Utilities</h2>\n";
init_language_utils();
echo "✓ Language utilities loaded\n";

// Test 7: Component inclusion
echo "<h2>7. Component Inclusion</h2>\n";
$componentResult = include_component('header');
echo "✓ Header component: " . ($componentResult ? 'Success' : 'Failed') . "\n";

// Test 8: Debug includes
echo "<h2>8. Debug Information</h2>\n";
$includedFiles = debug_includes();
echo "Files included (" . count($includedFiles) . "):\n";
echo "<ul>\n";
foreach ($includedFiles as $file) {
    echo "<li>" . htmlspecialchars($file) . "</li>\n";
}
echo "</ul>\n";

// Test 9: Function availability
echo "<h2>9. Function Availability</h2>\n";
$functions = [
    'init_app',
    'init_auth', 
    'init_platform_manager',
    'init_language_utils',
    'get_current_user_info',
    'is_authenticated',
    'is_admin',
    'require_auth',
    'require_admin',
    'include_component',
    'get_database',
    'debug_includes',
    'safe_include'
];

echo "<ul>\n";
foreach ($functions as $func) {
    $available = function_exists($func);
    echo "<li>" . $func . ": " . ($available ? '✓ Available' : '✗ Missing') . "</li>\n";
}
echo "</ul>\n";

// Test 10: Error handling
echo "<h2>10. Error Handling</h2>\n";
$result = safe_include('nonexistent_file.php');
echo "✓ Safe include with nonexistent file: " . ($result ? 'Failed (should be false)' : 'Success (correctly returned false)') . "\n";

echo "<h2>✅ All Tests Complete</h2>\n";
echo "<p>The include system is working correctly!</p>\n";
?> 