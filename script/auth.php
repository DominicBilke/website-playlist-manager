<?php
/**
 * Authentication System
 * Handles user authentication, session management, and security
 */

class Auth {
    private $pdo;
    private $lang;
    
    public function __construct($pdo, $lang) {
        $this->pdo = $pdo;
        $this->lang = $lang;
        $this->initSession();
    }
    
    /**
     * Initialize secure session
     */
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
        }
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            // Check if user exists and is active
            $stmt = $this->pdo->prepare("
                SELECT id, login, password, email, team, office, role, status, login_counter 
                FROM users 
                WHERE login = ? AND status = 'active'
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => $this->lang->get('invalid_credentials')];
            }
            
            // Check for account lockout
            if ($user['login_counter'] >= 5) {
                return ['success' => false, 'message' => $this->lang->get('account_locked')];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                // Increment login counter
                $this->incrementLoginCounter($user['id']);
                return ['success' => false, 'message' => $this->lang->get('invalid_credentials')];
            }
            
            // Reset login counter on successful login
            $this->resetLoginCounter($user['id']);
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Create session
            $this->createSession($user);
            
            // Log successful login
            $this->logAdminAction($user['id'], 'user_login', 'user', $user['id'], 'Successful login');
            
            return ['success' => true, 'user' => $user];
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => $this->lang->get('login_error')];
        }
    }
    
    /**
     * Register new user
     */
    public function register($data) {
        try {
            // Check if registration is enabled
            if (!$this->isRegistrationEnabled()) {
                return ['success' => false, 'message' => $this->lang->get('registration_disabled')];
            }
            
            // Validate input
            $validation = $this->validateRegistrationData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Check if username already exists
            if ($this->userExists($data['login'])) {
                return ['success' => false, 'message' => $this->lang->get('username_taken')];
            }
            
            // Check if email already exists
            if (!empty($data['email']) && $this->emailExists($data['email'])) {
                return ['success' => false, 'message' => $this->lang->get('email_taken')];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (login, password, email, team, office, role, status, days, daytime_from, daytime_to) 
                VALUES (?, ?, ?, ?, ?, 'user', 'active', '1,2,3,4,5', '09:00:00', '17:00:00')
            ");
            
            $stmt->execute([
                $data['login'],
                $hashedPassword,
                $data['email'] ?? null,
                $data['team'],
                $data['office']
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Create user settings
            $this->createUserSettings($userId);
            
            // Log registration
            $this->logAdminAction($userId, 'user_registration', 'user', $userId, 'New user registration');
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => $this->lang->get('signup_error')];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Log logout
            $this->logAdminAction($_SESSION['user_id'], 'user_logout', 'user', $_SESSION['user_id'], 'User logout');
            
            // Remove session from database
            $this->removeSession(session_id());
        }
        
        // Destroy session
        session_destroy();
        
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['session_id']);
    }
    
    /**
     * Check if user has admin role
     */
    public function isAdmin() {
        return $this->isLoggedIn() && in_array($_SESSION['role'], ['admin', 'super_admin']);
    }
    
    /**
     * Check if user has super admin role
     */
    public function isSuperAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'super_admin';
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Require admin role
     */
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: account.php?error=' . urlencode($this->lang->get('access_denied')));
            exit;
        }
    }
    
    /**
     * Require super admin role
     */
    public function requireSuperAdmin() {
        $this->requireAuth();
        if (!$this->isSuperAdmin()) {
            header('Location: account.php?error=' . urlencode($this->lang->get('access_denied')));
            exit;
        }
    }
    
    /**
     * Create user session
     */
    private function createSession($user) {
        // Generate session ID
        $sessionId = bin2hex(random_bytes(32));
        
        // Store session in database
        $stmt = $this->pdo->prepare("
            INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'],
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['team'] = $user['team'];
        $_SESSION['office'] = $user['office'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['session_id'] = $sessionId;
    }
    
    /**
     * Remove session from database
     */
    private function removeSession($sessionId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE session_id = ?");
            $stmt->execute([$sessionId]);
        } catch (PDOException $e) {
            error_log("Error removing session: " . $e->getMessage());
        }
    }
    
    /**
     * Increment login counter
     */
    private function incrementLoginCounter($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET login_counter = login_counter + 1 WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error incrementing login counter: " . $e->getMessage());
        }
    }
    
    /**
     * Reset login counter
     */
    private function resetLoginCounter($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET login_counter = 0 WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error resetting login counter: " . $e->getMessage());
        }
    }
    
    /**
     * Update last login
     */
    private function updateLastLogin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }
    
    /**
     * Validate registration data
     */
    private function validateRegistrationData($data) {
        // Username validation
        if (empty($data['login']) || strlen($data['login']) < 3) {
            return ['valid' => false, 'message' => $this->lang->get('username_too_short')];
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['login'])) {
            return ['valid' => false, 'message' => $this->lang->get('username_invalid')];
        }
        
        // Password validation
        if (empty($data['password']) || strlen($data['password']) < 6) {
            return ['valid' => false, 'message' => $this->lang->get('password_too_short')];
        }
        
        // Email validation (optional)
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => $this->lang->get('invalid_email_format')];
        }
        
        // Team validation
        if (empty($data['team']) || $data['team'] <= 0) {
            return ['valid' => false, 'message' => $this->lang->get('team_number_required')];
        }
        
        // Office validation
        if (empty($data['office'])) {
            return ['valid' => false, 'message' => $this->lang->get('office_required')];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Check if user exists
     */
    private function userExists($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE login = ?");
            $stmt->execute([$username]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if registration is enabled
     */
    private function isRegistrationEnabled() {
        try {
            $stmt = $this->pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'registration_enabled'");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result && $result['setting_value'] == '1';
        } catch (PDOException $e) {
            return true; // Default to enabled
        }
    }
    
    /**
     * Create user settings
     */
    private function createUserSettings($userId) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_settings (user_id) VALUES (?)
            ");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error creating user settings: " . $e->getMessage());
        }
    }
    
    /**
     * Log admin action
     */
    public function logAdminAction($adminId, $action, $targetType, $targetId, $details) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_audit_log (admin_id, action, target_type, target_id, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $adminId,
                $action,
                $targetType,
                $targetId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error logging admin action: " . $e->getMessage());
        }
    }
    
    /**
     * Clean expired sessions
     */
    public function cleanExpiredSessions() {
        try {
            $timeout = $this->getSessionTimeout();
            $stmt = $this->pdo->prepare("
                DELETE FROM user_sessions 
                WHERE last_activity < DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$timeout]);
        } catch (PDOException $e) {
            error_log("Error cleaning expired sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Get session timeout
     */
    private function getSessionTimeout() {
        try {
            $stmt = $this->pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'session_timeout'");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result['setting_value'] : 3600; // Default 1 hour
        } catch (PDOException $e) {
            return 3600;
        }
    }
}
?> 