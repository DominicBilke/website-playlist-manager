<?php
require 'inc_start.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Get current timestamp
$timestamp = date('Y-m-d H:i:s');
$user_id = $_SESSION['user_id'];
$platform = 'amazon_music';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=localhost;dbname=playlist_manager", "username", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert listening log
    $stmt = $pdo->prepare("INSERT INTO listening_logs (user_id, platform, timestamp, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $platform, $timestamp]);
    
    // Update user statistics
    $stmt = $pdo->prepare("
        UPDATE user_statistics 
        SET amazon_music_minutes = amazon_music_minutes + 1,
            total_minutes = total_minutes + 1,
            last_updated = NOW()
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    
    // If no row was updated, insert a new one
    if($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO user_statistics (user_id, amazon_music_minutes, total_minutes, last_updated)
            VALUES (?, 1, 1, NOW())
        ");
        $stmt->execute([$user_id]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Amazon Music statistics logged']);
    
} catch(PDOException $e) {
    error_log("Amazon Music logging error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?> 