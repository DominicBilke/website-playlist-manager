<?php
require 'script/inc_start.php';
require 'script/languages.php';
require 'script/language_utils.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Playlist Manager</title>
    <meta name="description" content="Create your Playlist Manager account">
    
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .animate-fade-in { animation: fadeIn 0.6s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .form-input:focus { box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .password-strength { transition: all 0.3s ease; }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-plus text-3xl text-purple-600"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Konto erstellen' : 'Create Account'; ?></h2>
            <p class="text-purple-100"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Werden Sie Teil der Playlist Manager Community' : 'Join the Playlist Manager community'; ?></p>
        </div>

        <!-- Signup Form -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <form class="space-y-6" method="GET" action="script/signup.php" id="signupForm">
                <!-- Username Field -->
                <div>
                    <label for="login" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Benutzername' : 'Username'; ?>
                    </label>
                    <input 
                        id="login" 
                        name="login" 
                        type="text" 
                        required 
                        class="form-input appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                        placeholder="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Wählen Sie einen Benutzernamen' : 'Choose a username'; ?>"
                        minlength="3"
                    >
                    <p class="mt-1 text-xs text-purple-200"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Benutzername muss mindestens 3 Zeichen lang sein' : 'Username must be at least 3 characters long'; ?></p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i><?php echo $lang->getCurrentLanguage() === 'de' ? 'Passwort' : 'Password'; ?>
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="form-input appearance-none relative block w-full px-3 py-3 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="<?php echo $lang->getCurrentLanguage() === 'de' ? 'Erstellen Sie ein sicheres Passwort' : 'Create a strong password'; ?>"
                            minlength="6"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle"
                            onclick="togglePassword()"
                        >
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="flex space-x-1">
                            <div class="flex-1 h-2 bg-gray-300 rounded-full overflow-hidden">
                                <div id="strength-bar" class="h-full bg-red-500 transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                        <p id="strength-text" class="mt-1 text-xs text-purple-200"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Passwort-Stärke:' : 'Password strength:'; ?> <span class="font-medium"><?php echo $lang->getCurrentLanguage() === 'de' ? 'Schwach' : 'Weak'; ?></span></p>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirm Password
                    </label>
                    <input 
                        id="password_confirm" 
                        name="password_confirm" 
                        type="password" 
                        required 
                        class="form-input appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                        placeholder="Confirm your password"
                    >
                    <p id="password-match" class="mt-1 text-xs text-red-300 hidden">Passwords do not match</p>
                </div>

                <!-- Team Number -->
                <div>
                    <label for="team" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-users mr-2"></i>Team Number
                    </label>
                    <input 
                        id="team" 
                        name="team" 
                        type="number" 
                        required 
                        class="form-input appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                        placeholder="Enter your team number"
                        min="1"
                    >
                </div>

                <!-- Office -->
                <div>
                    <label for="office" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-building mr-2"></i>Office
                    </label>
                    <select 
                        id="office" 
                        name="office" 
                        required 
                        class="form-input appearance-none relative block w-full px-3 py-3 border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                    >
                        <option value="">Select your office</option>
                        <option value="Berlin">Berlin</option>
                        <option value="Hamburg">Hamburg</option>
                        <option value="Munich">Munich</option>
                        <option value="Cologne">Cologne</option>
                        <option value="Frankfurt">Frankfurt</option>
                        <option value="Stuttgart">Stuttgart</option>
                        <option value="Düsseldorf">Düsseldorf</option>
                        <option value="Leipzig">Leipzig</option>
                        <option value="Dortmund">Dortmund</option>
                        <option value="Essen">Essen</option>
                    </select>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required 
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-white">
                            I agree to the 
                            <a href="#" class="text-purple-200 hover:text-white underline">Terms of Service</a> 
                            and 
                            <a href="datenschutz.php" class="text-purple-200 hover:text-white underline">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        id="submit-btn"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-purple-300 group-hover:text-purple-200"></i>
                        </span>
                        Create Account
                    </button>
                </div>
            </form>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-white">
                Already have an account? 
                <a href="login.php" class="font-medium text-purple-200 hover:text-white transition-colors">
                    Sign in here
                </a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="index.php" class="text-purple-200 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-blue-600 to-purple-800"></div>
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <!-- Animated background elements -->
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-white opacity-10 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-16 h-16 bg-white opacity-10 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Modern JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.3.11/dist/alpine.min.js" defer></script>
    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            const strengthLabel = strengthText.querySelector('span');

            switch (strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.className = 'h-full bg-red-500 transition-all duration-300';
                    strengthLabel.textContent = 'Very Weak';
                    strengthLabel.className = 'font-medium text-red-300';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.className = 'h-full bg-orange-500 transition-all duration-300';
                    strengthLabel.textContent = 'Weak';
                    strengthLabel.className = 'font-medium text-orange-300';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.className = 'h-full bg-yellow-500 transition-all duration-300';
                    strengthLabel.textContent = 'Fair';
                    strengthLabel.className = 'font-medium text-yellow-300';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.className = 'h-full bg-blue-500 transition-all duration-300';
                    strengthLabel.textContent = 'Good';
                    strengthLabel.className = 'font-medium text-blue-300';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'h-full bg-green-500 transition-all duration-300';
                    strengthLabel.textContent = 'Strong';
                    strengthLabel.className = 'font-medium text-green-300';
                    break;
            }
        }

        // Password confirmation checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            const matchIndicator = document.getElementById('password-match');
            const submitBtn = document.getElementById('submit-btn');

            if (confirmPassword && password !== confirmPassword) {
                matchIndicator.classList.remove('hidden');
                submitBtn.disabled = true;
            } else {
                matchIndicator.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }

        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirm');
            const form = document.getElementById('signupForm');

            // Password strength checking
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });

            // Password confirmation checking
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            // Form submission enhancement
            form.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';
                submitBtn.disabled = true;
                
                // Re-enable after a delay (in case of error)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        });

        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
            } else {
                passwordInput.type = 'password';
                toggleBtn.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
            }
        }

        // Real-time validation
        document.getElementById('login').addEventListener('input', function() {
            const username = this.value;
            const submitBtn = document.getElementById('submit-btn');
            
            if (username.length < 3) {
                this.classList.add('border-red-500');
                submitBtn.disabled = true;
            } else {
                this.classList.remove('border-red-500');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>