<?php
// Basic functionality test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Playlist Manager - Basic Functionality Test</h1>";

// Test 1: Check if init.php can be loaded
echo "<h2>Test 1: Loading init.php</h2>";
try {
    require 'script/init.php';
    echo "✅ init.php loaded successfully<br>";
    echo "Database connection: " . ($pdo ? "✅ Connected" : "❌ Failed") . "<br>";
    echo "Language manager: " . ($lang ? "✅ Loaded" : "❌ Failed") . "<br>";
    echo "Auth system: " . ($auth ? "✅ Loaded" : "❌ Failed") . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading init.php: " . $e->getMessage() . "<br>";
}

// Test 2: Check if CSS file exists
echo "<h2>Test 2: CSS File</h2>";
if (file_exists('assets/css/main.css')) {
    echo "✅ CSS file exists<br>";
    $cssSize = filesize('assets/css/main.css');
    echo "CSS file size: " . number_format($cssSize) . " bytes<br>";
} else {
    echo "❌ CSS file missing<br>";
}

// Test 3: Check if database schema exists
echo "<h2>Test 3: Database Schema</h2>";
if (file_exists('database/schema.sql')) {
    echo "✅ Database schema exists<br>";
    $schemaSize = filesize('database/schema.sql');
    echo "Schema file size: " . number_format($schemaSize) . " bytes<br>";
} else {
    echo "❌ Database schema missing<br>";
}

// Test 4: Check if components exist
echo "<h2>Test 4: Components</h2>";
$components = ['header.php', 'footer.php', 'language_switcher.php'];
foreach ($components as $component) {
    if (file_exists("components/$component")) {
        echo "✅ $component exists<br>";
    } else {
        echo "❌ $component missing<br>";
    }
}

// Test 5: Check if main pages exist
echo "<h2>Test 5: Main Pages</h2>";
$pages = ['index.php', 'login.php', 'signup.php', 'account.php', 'admin.php'];
foreach ($pages as $page) {
    if (file_exists($page)) {
        echo "✅ $page exists<br>";
    } else {
        echo "❌ $page missing<br>";
    }
}

// Test 6: Check language functionality
echo "<h2>Test 6: Language System</h2>";
if (isset($lang)) {
    echo "✅ Language manager available<br>";
    echo "Current language: " . $lang->getCurrentLanguage() . "<br>";
    echo "Test translation: " . $lang->get('dashboard') . "<br>";
} else {
    echo "❌ Language manager not available<br>";
}

// Test 7: Check database tables
echo "<h2>Test 7: Database Tables</h2>";
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✅ Database accessible<br>";
        echo "Tables found: " . count($tables) . "<br>";
        foreach ($tables as $table) {
            echo "- $table<br>";
        }
    } catch (PDOException $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database not available<br>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If all tests show ✅, the basic functionality should be working.</p>";
?> 