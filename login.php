<?php
// Use new include system
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}
require_once 'script/includes.php';

// Initialize language manager and authentication
$lang = init_app();
$auth = init_auth();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error_message = $lang->get('please_fill_all_fields');
    } else {
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            header('Location: account.php');
            exit();
        } else {
            $error_message = $result['message'];
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
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <!-- Login Card -->
                    <div class="card">
                        <div class="card-header text-center">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                <?php echo $lang->get('welcome_back'); ?>
                            </h1>
                            <p class="text-gray-600">
                                <?php echo $lang->get('sign_in_to_continue'); ?>
                            </p>
                        </div>

                        <div class="card-body">
                            <?php if ($error_message): ?>
                                <div class="alert alert-error mb-4">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($success_message): ?>
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars($success_message); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Login Form -->
                            <form method="POST" action="login.php" class="space-y-4">
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <?php echo $lang->get('username_or_email'); ?>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="username" 
                                            name="username" 
                                            class="form-input pl-10" 
                                            placeholder="<?php echo $lang->get('enter_username_or_email'); ?>"
                                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                            required
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <?php echo $lang->get('password'); ?>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            id="password" 
                                            name="password" 
                                            class="form-input pl-10 pr-10" 
                                            placeholder="<?php echo $lang->get('enter_password'); ?>"
                                            required
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <button 
                                            type="button" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                            onclick="togglePassword()"
                                        >
                                            <i id="password-toggle" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="remember" 
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-600">
                                            <?php echo $lang->get('remember_me'); ?>
                                        </span>
                                    </label>
                                    
                                    <a href="forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700">
                                        <?php echo $lang->get('forgot_password'); ?>?
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-primary w-full">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    <?php echo $lang->get('sign_in'); ?>
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

                            <!-- Social Login Buttons -->
                            <div class="space-y-3">
                                <button class="btn btn-secondary w-full">
                                    <i class="fab fa-google mr-2"></i>
                                    <?php echo $lang->get('continue_with_google'); ?>
                                </button>
                                
                                <button class="btn btn-secondary w-full">
                                    <i class="fab fa-apple mr-2"></i>
                                    <?php echo $lang->get('continue_with_apple'); ?>
                                </button>
                            </div>

                            <!-- Sign Up Link -->
                            <div class="text-center mt-6">
                                <p class="text-gray-600">
                                    <?php echo $lang->get('dont_have_account'); ?> 
                                    <a href="signup.php" class="text-primary-600 hover:text-primary-700 font-medium">
                                        <?php echo $lang->get('sign_up'); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Home -->
                    <div class="text-center mt-6">
                        <a href="index.php" class="text-gray-600 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <?php echo $lang->get('back_to_home'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordToggle.classList.remove('fa-eye');
            passwordToggle.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordToggle.classList.remove('fa-eye-slash');
            passwordToggle.classList.add('fa-eye');
        }
    }

    // Auto-focus on username field
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('username').focus();
    });
    </script>
</body>
</html>