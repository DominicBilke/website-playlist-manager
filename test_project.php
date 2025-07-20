<?php
/**
 * Project Test File
 * Tests all major components to ensure they're working correctly
 */

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Project Test - Playlist Manager</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <link href='assets/css/main.css' rel='stylesheet'>
</head>
<body>
    <div class='container'>
        <h1 class='text-center mb-8'>Project Test Results</h1>";

// Test 1: Check if init.php works
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 1: Initialization System</h2>
    </div>
    <div class='card-body'>";

try {
    require 'script/init.php';
    echo "<div class='alert alert-success'>
        <i class='fas fa-check-circle'></i>
        ✅ Initialization system loaded successfully
    </div>";
    
    // Check if global variables are set
    if (isset($pdo) && $pdo) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Database connection established
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Database connection failed
        </div>";
    }
    
    if (isset($lang) && $lang) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Language manager loaded
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Language manager failed to load
        </div>";
    }
    
    if (isset($auth) && $auth) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Authentication system loaded
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Authentication system failed to load
        </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-error'>
        <i class='fas fa-exclamation-circle'></i>
        ❌ Initialization failed: " . htmlspecialchars($e->getMessage()) . "
    </div>";
}

echo "</div></div>";

// Test 2: Check language system
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 2: Language System</h2>
    </div>
    <div class='card-body'>";

if (isset($lang)) {
    try {
        $testKey = 'dashboard';
        $result = $lang->get($testKey);
        
        if ($result && $result !== $testKey) {
            echo "<div class='alert alert-success'>
                <i class='fas fa-check-circle'></i>
                ✅ Language system working - '$testKey' = '$result'
            </div>";
        } else {
            echo "<div class='alert alert-error'>
                <i class='fas fa-exclamation-circle'></i>
                ❌ Language system not working properly
            </div>";
        }
        
        $currentLang = $lang->getCurrentLanguage();
        echo "<div class='alert alert-info'>
            <i class='fas fa-info-circle'></i>
            ℹ️ Current language: $currentLang
        </div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Language system error: " . htmlspecialchars($e->getMessage()) . "
        </div>";
    }
} else {
    echo "<div class='alert alert-error'>
        <i class='fas fa-exclamation-circle'></i>
        ❌ Language manager not available
    </div>";
}

echo "</div></div>";

// Test 3: Check database tables
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 3: Database Tables</h2>
    </div>
    <div class='card-body'>";

if (isset($pdo)) {
    $requiredTables = ['users', 'user_settings', 'api_tokens', 'admin_audit_log', 'user_sessions', 'system_settings'];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='alert alert-success'>
                    <i class='fas fa-check-circle'></i>
                    ✅ Table '$table' exists
                </div>";
            } else {
                echo "<div class='alert alert-error'>
                    <i class='fas fa-exclamation-circle'></i>
                    ❌ Table '$table' missing
                </div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-error'>
                <i class='fas fa-exclamation-circle'></i>
                ❌ Error checking table '$table': " . htmlspecialchars($e->getMessage()) . "
            </div>";
        }
    }
} else {
    echo "<div class='alert alert-error'>
        <i class='fas fa-exclamation-circle'></i>
        ❌ Database connection not available
    </div>";
}

echo "</div></div>";

// Test 4: Check CSS file
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 4: CSS Design System</h2>
    </div>
    <div class='card-body'>";

if (file_exists('assets/css/main.css')) {
    $cssSize = filesize('assets/css/main.css');
    echo "<div class='alert alert-success'>
        <i class='fas fa-check-circle'></i>
        ✅ CSS file exists (Size: " . number_format($cssSize) . " bytes)
    </div>";
    
    // Test CSS classes
    echo "<div class='grid grid-cols-1 md:grid-cols-3 gap-4 mt-4'>
        <div class='card'>
            <div class='card-body text-center'>
                <i class='fas fa-check text-success text-2xl mb-2'></i>
                <p>Card Component</p>
            </div>
        </div>
        <button class='btn btn-primary'>Primary Button</button>
        <button class='btn btn-secondary'>Secondary Button</button>
    </div>";
    
} else {
    echo "<div class='alert alert-error'>
        <i class='fas fa-exclamation-circle'></i>
        ❌ CSS file missing
    </div>";
}

echo "</div></div>";

// Test 5: Check components
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 5: Components</h2>
    </div>
    <div class='card-body'>";

$components = ['header.php', 'footer.php', 'language_switcher.php'];
foreach ($components as $component) {
    if (file_exists("components/$component")) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Component '$component' exists
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Component '$component' missing
        </div>";
    }
}

echo "</div></div>";

// Test 6: Check main pages
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 6: Main Pages</h2>
    </div>
    <div class='card-body'>";

$pages = ['index.php', 'login.php', 'signup.php', 'account.php', 'admin.php', 'player.php'];
foreach ($pages as $page) {
    if (file_exists($page)) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Page '$page' exists
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Page '$page' missing
        </div>";
    }
}

echo "</div></div>";

// Test 7: Check platform pages
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test 7: Platform Pages</h2>
    </div>
    <div class='card-body'>";

$platformPages = ['spotify_play.php', 'applemusic_play.php', 'youtube_play.php', 'amazon_play.php'];
foreach ($platformPages as $page) {
    if (file_exists($page)) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i>
            ✅ Platform page '$page' exists
        </div>";
    } else {
        echo "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i>
            ❌ Platform page '$page' missing
        </div>";
    }
}

echo "</div></div>";

// Summary
echo "<div class='card mb-4'>
    <div class='card-header'>
        <h2>Test Summary</h2>
    </div>
    <div class='card-body'>
        <p>All tests completed. Check the results above to identify any issues.</p>
        <div class='flex gap-4 mt-4'>
            <a href='index.php' class='btn btn-primary'>Go to Homepage</a>
            <a href='login.php' class='btn btn-secondary'>Go to Login</a>
            <a href='test_design_system.html' class='btn btn-secondary'>Design System Test</a>
        </div>
    </div>
</div>";

echo "</div></body></html>";
?> 