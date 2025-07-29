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

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'login' => trim($_POST['login'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'team' => trim($_POST['team'] ?? ''),
        'office' => trim($_POST['office'] ?? '')
    ];
    
    $result = $auth->register($data);
    
    if ($result['success']) {
        $success_message = $lang->get('signup_successful');
    } else {
        $error_message = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('sign_up'); ?> - Playlist Manager</title>
    
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
                <div class="w-full max-w-lg">
                    <!-- Signup Card -->
                    <div class="card">
                        <div class="card-header text-center">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                <?php echo $lang->get('create_account'); ?>
                            </h1>
                            <p class="text-gray-600">
                                <?php echo $lang->get('join_playlist_manager'); ?>
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

                            <!-- Signup Form -->
                            <form method="POST" action="signup.php" class="space-y-4">
                                <div class="form-group">
                                    <label for="login" class="form-label">
                                        <?php echo $lang->get('username'); ?> *
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="login" 
                                            name="login" 
                                            class="form-input pl-10" 
                                            placeholder="<?php echo $lang->get('enter_username'); ?>"
                                            value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                                            required
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <?php echo $lang->get('email'); ?>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            class="form-input pl-10" 
                                            placeholder="<?php echo $lang->get('enter_your_email'); ?>"
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <?php echo $lang->get('password'); ?> *
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            id="password" 
                                            name="password" 
                                            class="form-input pl-10 pr-10" 
                                            placeholder="<?php echo $lang->get('enter_password'); ?>"
                                            required
                                            minlength="8"
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <button 
                                            type="button" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                            onclick="togglePassword('password')"
                                        >
                                            <i id="password-toggle" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2" id="password-strength"></div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <?php echo $lang->get('confirm_password'); ?> *
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            id="confirm_password" 
                                            name="confirm_password" 
                                            class="form-input pl-10 pr-10" 
                                            placeholder="<?php echo $lang->get('confirm_password'); ?>"
                                            required
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <button 
                                            type="button" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                            onclick="togglePassword('confirm_password')"
                                        >
                                            <i id="confirm-password-toggle" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                        </button>
                                    </div>
                                    <div class="password-match mt-2" id="password-match"></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label for="team" class="form-label">
                                            <?php echo $lang->get('team'); ?> *
                                        </label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                id="team" 
                                                name="team" 
                                                class="form-input pl-10" 
                                                placeholder="<?php echo $lang->get('enter_team'); ?>"
                                                value="<?php echo htmlspecialchars($_POST['team'] ?? ''); ?>"
                                                required
                                            >
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-users text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="office" class="form-label">
                                            <?php echo $lang->get('office'); ?> *
                                        </label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                id="office" 
                                                name="office" 
                                                class="form-input pl-10" 
                                                placeholder="<?php echo $lang->get('enter_office'); ?>"
                                                value="<?php echo htmlspecialchars($_POST['office'] ?? ''); ?>"
                                                required
                                            >
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-building text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-full" id="submit-btn" disabled>
                                    <i class="fas fa-user-plus mr-2"></i>
                                    <?php echo $lang->get('create_account'); ?>
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

                            <!-- Social Signup Buttons -->
                            <div class="space-y-3">
                                <?php
                                // Initialize OAuth manager
                                require_once 'script/OAuthManager.php';
                                $oauthManager = new OAuthManager($GLOBALS['pdo'], $lang);
                                
                                // Check if OAuth providers are enabled
                                $googleEnabled = true; // You can make this configurable
                                $appleEnabled = true;  // You can make this configurable
                                ?>
                                
                                <?php if ($googleEnabled): ?>
                                    <a href="oauth_initiate.php?provider=google" class="btn btn-secondary w-full">
                                        <i class="fab fa-google mr-2"></i>
                                        <?php echo $lang->get('continue_with_google'); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($appleEnabled): ?>
                                    <a href="oauth_initiate.php?provider=apple" class="btn btn-secondary w-full">
                                        <i class="fab fa-apple mr-2"></i>
                                        <?php echo $lang->get('continue_with_apple'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Sign In Link -->
                            <div class="text-center mt-6">
                                <p class="text-gray-600">
                                    <?php echo $lang->get('already_have_account'); ?> 
                                    <a href="login.php" class="text-primary-600 hover:text-primary-700 font-medium">
                                        <?php echo $lang->get('sign_in'); ?>
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
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const passwordToggle = document.getElementById(fieldId === 'password' ? 'password-toggle' : 'confirm-password-toggle');
        
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

    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) strength++;
        else feedback.push('At least 8 characters');

        if (/[a-z]/.test(password)) strength++;
        else feedback.push('Lowercase letter');

        if (/[A-Z]/.test(password)) strength++;
        else feedback.push('Uppercase letter');

        if (/[0-9]/.test(password)) strength++;
        else feedback.push('Number');

        if (/[^A-Za-z0-9]/.test(password)) strength++;
        else feedback.push('Special character');

        return { strength, feedback };
    }

    function updatePasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthDiv = document.getElementById('password-strength');
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchDiv = document.getElementById('password-match');
        const submitBtn = document.getElementById('submit-btn');

        if (password) {
            const { strength, feedback } = checkPasswordStrength(password);
            const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const strengthColor = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-blue-600', 'text-green-600'];

            strengthDiv.innerHTML = `
                <div class="text-sm">
                    <span class="${strengthColor[strength - 1]} font-medium">${strengthText[strength - 1]}</span>
                    ${feedback.length > 0 ? '<br><span class="text-gray-500 text-xs">Missing: ' + feedback.join(', ') + '</span>' : ''}
                </div>
            `;
        } else {
            strengthDiv.innerHTML = '';
        }

        // Check password match
        if (confirmPassword) {
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-green-600 text-sm"><i class="fas fa-check mr-1"></i>Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span class="text-red-600 text-sm"><i class="fas fa-times mr-1"></i>Passwords do not match</span>';
            }
        } else {
            matchDiv.innerHTML = '';
        }

        // Enable/disable submit button
        const { strength } = checkPasswordStrength(password);
        const passwordsMatch = password === confirmPassword;
        const hasRequiredFields = document.getElementById('login').value && 
                                 document.getElementById('team').value && 
                                 document.getElementById('office').value;

        submitBtn.disabled = !(strength >= 3 && passwordsMatch && hasRequiredFields);
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const fields = ['login', 'email', 'password', 'confirm_password', 'team', 'office'];
        fields.forEach(field => {
            document.getElementById(field).addEventListener('input', updatePasswordStrength);
        });
    });
    </script>
</body>
</html>