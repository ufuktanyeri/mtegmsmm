<?php
/**
 * Authentication Controller
 * MTEGM SMM Portal
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../includes/SessionManager.php';
require_once __DIR__ . '/../../includes/PermissionHelper.php';

class AuthController extends BaseController {

    private $sessionManager;
    private $db;

    public function __construct() {
        // BaseController doesn't have __construct, so no parent call needed
        $this->sessionManager = new SessionManager();

        // Initialize database connection
        require_once __DIR__ . '/../../includes/Database.php';
        $this->db = Database::getInstance();
    }

    /**
     * Display login page - redirects to user/login
     */
    public function login() {
        // Redirect to the actual login page in UserController
        header('Location: ' . BASE_URL . 'index.php?url=user/login');
        exit();
    }

    /**
     * Process login request
     */
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=user/login');
            return;
        }

        // CSRF token validation
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCSRFToken($token)) {
            $_SESSION['login_error'] = 'Güvenlik hatası. Lütfen tekrar deneyin.';
            $this->redirect('index.php?url=user/login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Kullanıcı adı ve şifre gereklidir.';
            $this->redirect('index.php?url=user/login');
            return;
        }

        try {
            // Get user from database
            $this->db->query("
                SELECT u.*, r.roleName
                FROM users u
                LEFT JOIN roles r ON u.roleId = r.id
                WHERE u.username = ? AND u.is_active = 1
            ", [$username]);
            $user = $this->db->single();

            if (!$user) {
                $_SESSION['login_error'] = 'Kullanıcı adı veya şifre hatalı.';
                $this->logSecurityEvent('failed_login', $username);
                $this->redirect('index.php?url=user/login');
                return;
            }

            // Check if account is locked
            if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                $_SESSION['login_error'] = 'Hesabınız kilitli. Lütfen daha sonra tekrar deneyin.';
                $this->logSecurityEvent('login_locked', $username);
                $this->redirect('index.php?url=user/login');
                return;
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                // Increment failed login count
                $this->incrementFailedLogin($user['id']);
                $_SESSION['login_error'] = 'Kullanıcı adı veya şifre hatalı.';
                $this->logSecurityEvent('failed_login', $username);
                $this->redirect('index.php?url=user/login');
                return;
            }

            // Check if password needs to be changed
            if ($user['force_password_change']) {
                $_SESSION['temp_user_id'] = $user['id'];
                $this->redirect('/auth/changePassword');
                return;
            }

            // Login successful
            $this->loginUser($user);
            $this->redirect('index.php?url=home');

        } catch (Exception $e) {
            $this->handleError($e, 'Login error');
            $_SESSION['login_error'] = 'Sistem hatası. Lütfen daha sonra tekrar deneyin.';
            $this->redirect('index.php?url=user/login');
        }
    }

    /**
     * Process logout
     */
    public function logout() {
        $this->logSecurityEvent('logout', $_SESSION['username'] ?? '');

        session_destroy();
        session_start();
        $_SESSION['logout_message'] = 'Başarıyla çıkış yaptınız.';

        $this->redirect('index.php?url=user/login');
    }

    /**
     * Display change password page
     */
    public function changePassword() {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['temp_user_id'])) {
            $this->redirect('index.php?url=user/login');
            return;
        }

        $data = [
            'title' => 'Şifre Değiştir',
            'forced' => isset($_SESSION['temp_user_id'])
        ];

        $this->render('auth/change_password', $data);
    }

    /**
     * Process password change
     */
    public function doChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/changePassword');
            return;
        }

        $userId = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
        if (!$userId) {
            $this->redirect('index.php?url=user/login');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate passwords
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Yeni şifreler eşleşmiyor.';
            $this->redirect('/auth/changePassword');
            return;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'Şifre en az 8 karakter olmalıdır.';
            $this->redirect('/auth/changePassword');
            return;
        }

        try {
            // Verify current password
            $this->db->query("SELECT password FROM users WHERE id = ?", [$userId]);
            $user = $this->db->single();

            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Mevcut şifre hatalı.';
                $this->redirect('/auth/changePassword');
                return;
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->query("
                UPDATE users
                SET password = ?,
                    password_changed_at = NOW(),
                    force_password_change = 0,
                    password_expires_at = DATE_ADD(NOW(), INTERVAL 90 DAY)
                WHERE id = ?
            ", [$hashedPassword, $userId]);
            $this->db->execute();

            // If forced change, complete login
            if (isset($_SESSION['temp_user_id'])) {
                $this->db->query("SELECT u.*, r.roleName FROM users u LEFT JOIN roles r ON u.roleId = r.id WHERE u.id = ?", [$userId]);
                $user = $this->db->single();

                unset($_SESSION['temp_user_id']);
                $this->loginUser($user);
                $_SESSION['success'] = 'Şifreniz başarıyla değiştirildi.';
                $this->redirect('/home');
            } else {
                $_SESSION['success'] = 'Şifreniz başarıyla değiştirildi.';
                $this->redirect('/home');
            }

        } catch (Exception $e) {
            $this->handleError($e, 'Password change error');
            $_SESSION['error'] = 'Şifre değiştirme hatası.';
            $this->redirect('/auth/changePassword');
        }
    }

    /**
     * Login user and set session
     */
    private function loginUser($user) {
        // Update last login
        $this->db->query("
            UPDATE users
            SET last_login = NOW(),
                failed_login_count = 0,
                account_locked_until = NULL
            WHERE id = ?
        ", [$user['id']]);
        $this->db->execute();

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'] . ' ' . $user['surname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['roleId'];
        $_SESSION['role_name'] = $user['roleName'];
        $_SESSION['cove_id'] = $user['coveId'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Load user permissions
        // Load user permissions
        if (class_exists('PermissionHelper')) {
            PermissionHelper::loadUserPermissions($user['roleId']);
        }

        // Log successful login
        $this->logSecurityEvent('login', $user['username'], true);
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedLogin($userId) {
        try {
            $this->db->query("
                UPDATE users
                SET failed_login_count = failed_login_count + 1,
                    account_locked_until = IF(failed_login_count >= 4, DATE_ADD(NOW(), INTERVAL 30 MINUTE), NULL)
                WHERE id = ?
            ", [$userId]);
            $this->db->execute();
        } catch (Exception $e) {
            error_log("Failed login increment error: " . $e->getMessage());
        }
    }

    /**
     * Log security events
     */
    private function logSecurityEvent($action, $username, $success = false) {
        try {
            $this->db->query("
                INSERT INTO security_logs (user_id, action_type, ip_address, user_agent, success, created_at)
                SELECT id, ?, ?, ?, ?, NOW() FROM users WHERE username = ? LIMIT 1
            ", [
                $action,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                $success ? 1 : 0,
                $username
            ]);
            $this->db->execute();
        } catch (Exception $e) {
            error_log("Security log error: " . $e->getMessage());
        }
    }
}