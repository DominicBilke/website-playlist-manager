<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $error = '';
    
    if (empty($login) || empty($password)) {
        $error = $lang->get('please_fill_all_fields');
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['team'] = $user['team'];
                $_SESSION['office'] = $user['office'];
                
                // Redirect to dashboard
                header('Location: account.php');
                exit;
            } else {
                $error = $lang->get('invalid_credentials');
            }
        } catch (PDOException $e) {
            $error = $lang->get('login_error');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('sign_in'); ?> - Playlist Manager</title>
    <meta name="description" content="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Anmelden Sie sich bei Playlist Manager' : 'Sign in to Playlist Manager'; ?>">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/main.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    
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
                <i class="fas fa-music text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <?php echo $lang->get('welcome_back'); ?>
            </h1>
            <p class="text-gray-600">
                <?php echo $lang->get('sign_in_to_continue'); ?>
            </p>
        </div>

        <!-- Login Form -->
        <div class="card animate-fade-in">
            <div class="card-body p-8">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-error mb-6">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6" data-validate>
                    <!-- Username/Email Field -->
                    <div class="form-group">
                        <label for="login" class="form-label">
                            <i class="fas fa-user mr-2"></i><?php echo $lang->get('username_or_email'); ?>
                        </label>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            class="form-input" 
                            placeholder="<?php echo $lang->get('enter_username_or_email'); ?>"
                            value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock mr-2"></i><?php echo $lang->get('password'); ?>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input pr-12" 
                                placeholder="<?php echo $lang->get('enter_password'); ?>"
                                required
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                aria-label="<?php echo $lang->get('toggle_password'); ?>"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-600">
                                <?php echo $lang->get('remember_me'); ?>
                            </span>
                        </label>
                        <a href="forgot-password.php" class="text-sm text-purple-600 hover:text-purple-700 transition-colors">
                            <?php echo $lang->get('forgot_password'); ?>?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full btn-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i><?php echo $lang->get('sign_in'); ?>
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

                <!-- Social Login Options -->
                <div class="space-y-3">
                    <button class="btn btn-secondary w-full">
                        <i class="fab fa-google mr-2"></i><?php echo $lang->get('continue_with_google'); ?>
                    </button>
                    <button class="btn btn-secondary w-full">
                        <i class="fab fa-apple mr-2"></i><?php echo $lang->get('continue_with_apple'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center mt-6">
            <p class="text-gray-600">
                <?php echo $lang->get('dont_have_account'); ?> 
                <a href="signup.php" class="text-purple-600 hover:text-purple-700 font-medium transition-colors">
                    <?php echo $lang->get('sign_up'); ?>
                </a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-4">
            <a href="index.php" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i><?php echo $lang->get('back_to_home'); ?>
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <style>
    /* Login page specific styles */
    .bg-grid-pattern {
        background-image: 
            linear-gradient(rgba(147, 51, 234, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(147, 51, 234, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    /* Form validation styles */
    .form-input.error {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    /* Loading state */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Responsive adjustments */
    @media (max-width: 480px) {
        .card-body {
            padding: 1.5rem;
        }
    }
    </style>
</body>
</html>