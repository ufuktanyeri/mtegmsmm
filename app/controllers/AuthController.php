<?php
/**
 * Authentication Controller
 * MTEGM SMM Portal
 */

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/helpers/SessionHelper.php';
require_once dirname(__DIR__) . '/helpers/PermissionHelper.php';

class AuthController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display login page
     */
    public function login() {
        // If already logged in, redirect to home
        if (SessionHelper::isLoggedIn()) {
            $this->redirect('/home');
            return;
        }

        $data = [
            'title' => 'Giriş Yap',
            'error' => $_SESSION['login_error'] ?? null
        ];

        unset($_SESSION['login_error']);

        $this->view('auth/login', $data);
    }

    /**
     * Process login request
     */
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/login');
            return;
        }

        // CSRF token validation
        if (!$this->validateCSRFToken()) {
            $_SESSION['login_error'] = 'Güvenlik hatası. Lütfen tekrar deneyin.';
            $this->redirect('/auth/login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Kullanıcı adı ve şifre gereklidir.';
            $this->redirect('/auth/login');
            return;
        }

        try {
            // Get user from database
            $stmt = $this->db->prepare("
                SELECT u.*, r.roleName
                FROM users u
                LEFT JOIN roles r ON u.roleId = r.id
                WHERE u.username = ? AND u.is_active = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                $_SESSION['login_error'] = 'Kullanıcı adı veya şifre hatalı.';
                $this->logSecurityEvent('failed_login', $username);
                $this->redirect('/auth/login');
                return;
            }

            // Check if account is locked
            if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                $_SESSION['login_error'] = 'Hesabınız kilitli. Lütfen daha sonra tekrar deneyin.';
                $this->logSecurityEvent('login_locked', $username);
                $this->redirect('/auth/login');
                return;
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                // Increment failed login count
                $this->incrementFailedLogin($user['id']);
                $_SESSION['login_error'] = 'Kullanıcı adı veya şifre hatalı.';
                $this->logSecurityEvent('failed_login', $username);
                $this->redirect('/auth/login');
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
            $this->redirect('/home');

        } catch (Exception $e) {
            $this->handleError($e, 'Login error');
            $_SESSION['login_error'] = 'Sistem hatası. Lütfen daha sonra tekrar deneyin.';
            $this->redirect('/auth/login');
        }
    }

    /**
     * Process logout
     */
    public function logout() {
        $this->logSecurityEvent('logout', SessionHelper::get('username'));

        SessionHelper::destroy();
        session_start();
        $_SESSION['logout_message'] = 'Başarıyla çıkış yaptınız.';

        $this->redirect('/auth/login');
    }

    /**
     * Display change password page
     */
    public function changePassword() {
        if (!SessionHelper::isLoggedIn() && !isset($_SESSION['temp_user_id'])) {
            $this->redirect('/auth/login');
            return;
        }

        $data = [
            'title' => 'Şifre Değiştir',
            'forced' => isset($_SESSION['temp_user_id'])
        ];

        $this->view('auth/change_password', $data);
    }

    /**
     * Process password change
     */
    public function doChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/changePassword');
            return;
        }

        $userId = SessionHelper::get('user_id') ?? $_SESSION['temp_user_id'] ?? null;
        if (!$userId) {
            $this->redirect('/auth/login');
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
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Mevcut şifre hatalı.';
                $this->redirect('/auth/changePassword');
                return;
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE users
                SET password = ?,
                    password_changed_at = NOW(),
                    force_password_change = 0,
                    password_expires_at = DATE_ADD(NOW(), INTERVAL 90 DAY)
                WHERE id = ?
            ");
            $stmt->execute([$hashedPassword, $userId]);

            // If forced change, complete login
            if (isset($_SESSION['temp_user_id'])) {
                $stmt = $this->db->prepare("SELECT u.*, r.roleName FROM users u LEFT JOIN roles r ON u.roleId = r.id WHERE u.id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

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
        $stmt = $this->db->prepare("
            UPDATE users
            SET last_login = NOW(),
                failed_login_count = 0,
                account_locked_until = NULL
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);

        // Set session variables
        SessionHelper::set('user_id', $user['id']);
        SessionHelper::set('username', $user['username']);
        SessionHelper::set('name', $user['name'] . ' ' . $user['surname']);
        SessionHelper::set('email', $user['email']);
        SessionHelper::set('role_id', $user['roleId']);
        SessionHelper::set('role_name', $user['roleName']);
        SessionHelper::set('cove_id', $user['coveId']);
        SessionHelper::set('logged_in', true);
        SessionHelper::set('login_time', time());

        // Load user permissions
        PermissionHelper::loadUserPermissions($user['roleId']);

        // Log successful login
        $this->logSecurityEvent('login', $user['username'], true);
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedLogin($userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users
                SET failed_login_count = failed_login_count + 1,
                    account_locked_until = IF(failed_login_count >= 4, DATE_ADD(NOW(), INTERVAL 30 MINUTE), NULL)
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Failed login increment error: " . $e->getMessage());
        }
    }

    /**
     * Log security events
     */
    private function logSecurityEvent($action, $username, $success = false) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO security_logs (user_id, action_type, ip_address, user_agent, success, created_at)
                SELECT id, ?, ?, ?, ?, NOW() FROM users WHERE username = ? LIMIT 1
            ");
            $stmt->execute([
                $action,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                $success ? 1 : 0,
                $username
            ]);
        } catch (Exception $e) {
            error_log("Security log error: " . $e->getMessage());
        }
    }
}