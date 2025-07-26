<?php
/**
 * Test Database Connection Fix
 */

define('APP_ROOT', __DIR__);
require_once 'script/includes.php';

echo "<h1>Database Connection Test</h1>";

// Test 1: Basic initialization
echo "<h2>1. Testing init_app()</h2>";
try {
    $lang = init_app();
    echo "✅ Language manager initialized<br>";
    echo "Current language: " . $lang->getCurrentLanguage() . "<br>";
} catch (Exception $e) {
    echo "❌ Language manager failed: " . $e->getMessage() . "<br>";
}

// Test 2: Database connection
echo "<h2>2. Testing database connection</h2>";
try {
    $pdo = get_database();
    if ($pdo) {
        echo "✅ Database connection successful<br>";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "Total users in database: " . $result['count'] . "<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Auth initialization
echo "<h2>3. Testing auth initialization</h2>";
try {
    $auth = init_auth();
    if ($auth) {
        echo "✅ Auth system initialized successfully<br>";
        
        // Test login method (this should not fail with null PDO)
        $result = $auth->login('test_user', 'test_password');
        echo "Login test completed (expected to fail with invalid credentials)<br>";
        echo "Result: " . ($result['success'] ? 'Success' : 'Failed: ' . $result['message']) . "<br>";
    } else {
        echo "❌ Auth system initialization failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Auth error: " . $e->getMessage() . "<br>";
}

// Test 4: Global variables
echo "<h2>4. Testing global variables</h2>";
echo "GLOBALS['pdo']: " . (isset($GLOBALS['pdo']) ? 'Set' : 'Not set') . "<br>";
echo "GLOBALS['lang']: " . (isset($GLOBALS['lang']) ? 'Set' : 'Not set') . "<br>";
echo "GLOBALS['auth']: " . (isset($GLOBALS['auth']) ? 'Set' : 'Not set') . "<br>";

// Test 5: Platform manager
echo "<h2>5. Testing platform manager</h2>";
try {
    $platformManager = init_platform_manager();
    if ($platformManager) {
        echo "✅ Platform manager initialized successfully<br>";
    } else {
        echo "ℹ️ Platform manager not initialized (no user ID)<br>";
    }
} catch (Exception $e) {
    echo "❌ Platform manager error: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Test Complete</h2>";
echo "<p>If you see all green checkmarks, the database connection fix is working correctly!</p>";
?> 