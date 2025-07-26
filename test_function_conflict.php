<?php
// Test file to verify function conflict resolution

echo "Testing function conflict resolution...\n";

// Test 1: Include inc_start.php
echo "1. Including inc_start.php...\n";
require_once 'script/inc_start.php';
echo "✓ inc_start.php loaded successfully\n";

// Test 2: Include language_utils.php
echo "2. Including language_utils.php...\n";
require_once 'script/language_utils.php';
echo "✓ language_utils.php loaded successfully\n";

// Test 3: Test both functions
echo "3. Testing functions...\n";

// Test the URL prefix version (from inc_start.php)
$url1 = buildLanguageUrl('de');
echo "✓ buildLanguageUrl('de') = $url1\n";

// Test the query parameter version (from language_utils.php)
$url2 = buildLanguageUrlQuery('en');
echo "✓ buildLanguageUrlQuery('en') = $url2\n";

// Test other utility functions
$currentUrl = getCurrentPageUrl();
echo "✓ getCurrentPageUrl() = $currentUrl\n";

$hasLang = hasLanguageParameter() ? 'true' : 'false';
echo "✓ hasLanguageParameter() = $hasLang\n";

$langFromRequest = getLanguageFromRequest();
echo "✓ getLanguageFromRequest() = " . ($langFromRequest ?? 'null') . "\n";

echo "\n🎉 All tests passed! Function conflict resolved successfully.\n";
?> 