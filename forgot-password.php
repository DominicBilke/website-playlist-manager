<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $error = '';
    $success = '';
    
    if (empty($email)) {
        $error = $lang->get('please_enter_email');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang->get('invalid_email_format');
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, login FROM users WHERE login = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token (you might want to create a separate table for this)
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);
                
                // In a real application, you would send an email here
                // For now, we'll just show a success message
                $success = $lang->get('password_reset_sent');
            } else {
                // Don't reveal if email exists or not for security
                $success = $lang->get('password_reset_sent');
            }
        } catch (PDOException $e) {
            $error = $lang->get('password_reset_error');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('forgot_password'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Passwort zurücksetzen' : 'Reset your password'; ?>">
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-white to-purple-50"></div>
    <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 w-full max-w-md mx-auto px-4">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-full mb-4">
                <i class="fas fa-key text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <?php echo $lang->get('forgot_password'); ?>
            </h1>
            <p class="text-gray-600">
                <?php echo $lang->get('enter_email_for_reset'); ?>
            </p>
        </div>

        <!-- Password Reset Form -->
        <div class="card animate-fade-in">
            <div class="card-body p-8">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-error mb-6">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success mb-6">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6" data-validate>
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope mr-2"></i><?php echo $lang->get('email'); ?>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="<?php echo $lang->get('enter_your_email'); ?>"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i><?php echo $lang->get('send_reset_link'); ?>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            <?php echo $lang->get('or'); ?>
                        </span>
                    </div>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="login.php" class="btn btn-secondary w-full">
                        <i class="fas fa-arrow-left mr-2"></i><?php echo $lang->get('back_to_login'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-4">
            <a href="index.php" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-home mr-2"></i><?php echo $lang->get('back_to_home'); ?>
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <style>
    /* Forgot password page specific styles */
    .bg-grid-pattern {
        background-image: 
            linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</body>
</html> 