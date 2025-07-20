<?php
/**
 * Secure User Registration Script
 * Handles user registration with proper validation and security
 */

session_start();

// Include required files
require_once '../script/languages.php';
require_once '../script/inc_start.php';
require_once 'auth.php';

// Initialize language and auth
$lang = new LanguageManager();
$auth = new Auth($pdo, $lang);

// Check if registration is enabled
try {
    $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'registration_enabled'");
    $stmt->execute();
    $result = $stmt->fetch();
    $registrationEnabled = $result && $result['setting_value'] == '1';
} catch (PDOException $e) {
    $registrationEnabled = true; // Default to enabled
}

if (!$registrationEnabled) {
    header("Location: ../signup.php?error=" . urlencode($lang->get('registration_disabled')));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'login' => trim($_POST['login'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'email' => trim($_POST['email'] ?? ''),
        'team' => (int)($_POST['team'] ?? 0),
        'office' => trim($_POST['office'] ?? '')
    ];
    
    // Validate password confirmation
    if ($data['password'] !== ($_POST['password_confirm'] ?? '')) {
        header("Location: ../signup.php?error=" . urlencode($lang->get('passwords_dont_match')));
        exit;
    }
    
    // Attempt registration
    $result = $auth->register($data);
    
    if ($result['success']) {
        // Auto-login after successful registration
        $loginResult = $auth->login($data['login'], $_POST['password']);
        
        if ($loginResult['success']) {
            header("Location: ../account.php?success=" . urlencode($lang->get('signup_successful')));
        } else {
            header("Location: ../login.php?success=" . urlencode($lang->get('signup_successful')));
        }
    } else {
        header("Location: ../signup.php?error=" . urlencode($result['message']));
    }
    exit;
}

// If not POST request, redirect to signup page
header("Location: ../signup.php");
exit;
?>