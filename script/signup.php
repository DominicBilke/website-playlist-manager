<?php
/*
 * Secure User Registration Script
 * Handles user registration with proper validation and security
 */

session_start();

// Include language system
require_once '../script/languages.php';

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";
$dbname = "d03c87b1";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $meldung = "meldung=" . urlencode($lang->get('database_error'));
    header("Location: ../index.php?" . $meldung);
    exit;
}

// Validate input
$login = trim($_GET['login'] ?? '');
$password = $_GET['password'] ?? '';
$team = (int)($_GET['team'] ?? 0);
$office = trim($_GET['office'] ?? '');

$errors = [];

// Validate username
if (empty($login) || strlen($login) < 3) {
    $errors[] = $lang->get('username_too_short');
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
    $errors[] = $lang->get('username_invalid');
}

// Validate password
if (empty($password) || strlen($password) < 6) {
    $errors[] = $lang->get('password_too_short');
}

// Validate team number
if ($team <= 0) {
    $errors[] = $lang->get('team_number_required');
}

// Validate office
if (empty($office)) {
    $errors[] = $lang->get('office_required');
}

// Check if username already exists
if (empty($errors)) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        
        if ($stmt->fetch()) {
            $errors[] = $lang->get('username_taken');
        }
    } catch(PDOException $e) {
        $errors[] = $lang->get('database_error');
    }
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $meldung = "meldung=" . urlencode(implode(', ', $errors));
    header("Location: ../signup.php?" . $meldung);
    exit;
}

// Create new user
try {
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (login, password, team, office, days, daytime_from, daytime_to) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $login,
        $hashedPassword,
        $team,
        $office,
        '1,2,3,4,5', // Default: Monday to Friday
        '09:00',     // Default start time
        '17:00'      // Default end time
    ]);
    
    // Get the new user's ID
    $userId = $pdo->lastInsertId();
    
    // Set session variables
    $_SESSION['id'] = $userId;
    $_SESSION['login'] = $login;
    $_SESSION['team'] = $team;
    $_SESSION['office'] = $office;
    $_SESSION['days'] = '1,2,3,4,5';
    $_SESSION['daytime_from'] = '09:00';
    $_SESSION['daytime_to'] = '17:00';
    
    $meldung = "meldung=" . urlencode($lang->get('signup_successful'));
    
} catch(PDOException $e) {
    $meldung = "meldung=" . urlencode($lang->get('signup_error'));
}

// Redirect to dashboard
header("Location: ../account.php?" . $meldung);
exit;
?>