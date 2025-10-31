<?php
/**
 * Authentication Controller
 * Handles user login, logout, and authentication
 */

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Display login form or process login
     */
    public function login() {
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['last_activity'] = time();
                    
                    // Redirect to dashboard
                    header('Location: /dashboard');
                    exit;
                } else {
                    $error = ERROR_MESSAGES['login_failed'];
                }
            }
        }
        
        // Display login form
        include 'views/auth/login.php';
    }
    
    /**
     * Process logout
     */
    public function logout() {
        // Destroy session
        session_destroy();
        
        // Redirect to login
        header('Location: /login');
        exit;
    }
    
    /**
     * Display registration form or process registration
     */
    public function register() {
        // Only allow registration if user is admin
        if (!$this->isLoggedIn() || !$this->hasPermission('manage_users')) {
            header('Location: /login');
            exit;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => trim($_POST['username'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'last_name' => trim($_POST['last_name'] ?? ''),
                    'role' => $_POST['role'] ?? 'standard'
                ];
                
                // Validate password
                if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                    throw new Exception("Password must be at least " . PASSWORD_MIN_LENGTH . " characters long");
                }
                
                // Validate email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email address");
                }
                
                $userId = $this->userModel->create($data);
                $success = "User created successfully";
                
                // Clear form data
                $_POST = [];
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        include 'views/auth/register.php';
    }
    
    /**
     * Display password reset form or process reset
     */
    public function resetPassword() {
        $error = '';
        $success = '';
        $step = $_GET['step'] ?? 'request';
        $token = $_GET['token'] ?? '';
        
        if ($step === 'request') {
            // Request password reset
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = trim($_POST['email'] ?? '');
                
                if (empty($email)) {
                    $error = 'Please enter your email address';
                } else {
                    $resetToken = $this->userModel->generatePasswordResetToken($email);
                    
                    if ($resetToken) {
                        // In a real application, you would send an email here
                        // For now, we'll just show the reset link
                        $resetLink = APP_URL . "/reset-password?step=reset&token=" . $resetToken;
                        $success = "Password reset link: <a href='{$resetLink}'>Click here to reset password</a>";
                    } else {
                        $error = 'Email address not found';
                    }
                }
            }
            
            include 'views/auth/reset-request.php';
            
        } elseif ($step === 'reset') {
            // Reset password with token
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPassword = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($newPassword) || empty($confirmPassword)) {
                    $error = 'Please enter and confirm your new password';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Passwords do not match';
                } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                    $error = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
                } else {
                    if ($this->userModel->resetPassword($token, $newPassword)) {
                        $success = 'Password reset successfully. You can now login with your new password.';
                    } else {
                        $error = 'Invalid or expired reset token';
                    }
                }
            }
            
            include 'views/auth/reset-password.php';
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name']
        ];
    }
    
    /**
     * Check if current user has permission
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->userModel->hasPermission($_SESSION['user_id'], $permission);
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require specific permission
     */
    public function requirePermission($permission) {
        $this->requireAuth();
        
        if (!$this->hasPermission($permission)) {
            http_response_code(403);
            echo "Access denied. Insufficient permissions.";
            exit;
        }
    }
}

// Helper functions for use in views and other controllers
function isLoggedIn() {
    $auth = new AuthController();
    return $auth->isLoggedIn();
}

function getCurrentUser() {
    $auth = new AuthController();
    return $auth->getCurrentUser();
}

function hasPermission($permission) {
    $auth = new AuthController();
    return $auth->hasPermission($permission);
}

function requireAuth() {
    $auth = new AuthController();
    $auth->requireAuth();
}

function requirePermission($permission) {
    $auth = new AuthController();
    $auth->requirePermission($permission);
}
?>
