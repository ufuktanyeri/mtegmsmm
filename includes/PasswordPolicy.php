<?php
/**
 * PasswordPolicy Class
 * 
 * Handles password policy validation, history checking, and expiration management
 * Uses system_config table for configurable security policies
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/SecurityLogger.php';

class PasswordPolicy
{
    private $db;
    private $securityLogger;
    
    // Default password policies (overridden by system_config)
    private const DEFAULT_MIN_LENGTH = 8;
    private const DEFAULT_REQUIRE_UPPERCASE = true;
    private const DEFAULT_REQUIRE_LOWERCASE = true;
    private const DEFAULT_REQUIRE_NUMBERS = true;
    private const DEFAULT_REQUIRE_SPECIAL = false;
    private const DEFAULT_HISTORY_COUNT = 5;
    private const DEFAULT_EXPIRY_DAYS = 90;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->securityLogger = new SecurityLogger();
    }
    
    /**
     * Validate password against all policy rules
     */
    public function validatePassword(string $password, ?int $userId = null): array
    {
        $errors = [];
        
        // Length validation
        $minLength = $this->getConfig('password_min_length', self::DEFAULT_MIN_LENGTH);
        if (strlen($password) < $minLength) {
            $errors[] = "Şifre en az {$minLength} karakter olmalıdır.";
        }
        
        // Character requirements
        if ($this->getConfig('password_require_uppercase', self::DEFAULT_REQUIRE_UPPERCASE)) {
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Şifre en az bir büyük harf içermelidir.";
            }
        }
        
        if ($this->getConfig('password_require_lowercase', self::DEFAULT_REQUIRE_LOWERCASE)) {
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = "Şifre en az bir küçük harf içermelidir.";
            }
        }
        
        if ($this->getConfig('password_require_numbers', self::DEFAULT_REQUIRE_NUMBERS)) {
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Şifre en az bir rakam içermelidir.";
            }
        }
        
        if ($this->getConfig('password_require_special', self::DEFAULT_REQUIRE_SPECIAL)) {
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $errors[] = "Şifre en az bir özel karakter içermelidir.";
            }
        }
        
        // Password history validation (if user ID provided)
        if ($userId && !$this->checkPasswordHistory($password, $userId)) {
            $historyCount = $this->getConfig('password_history_count', self::DEFAULT_HISTORY_COUNT);
            $errors[] = "Bu şifre son {$historyCount} şifrenizden biri olduğu için kullanılamaz.";
        }
        
        // Common password patterns (basic check)
        if ($this->isCommonPassword($password)) {
            $errors[] = "Bu şifre çok yaygın kullanılan bir şifredir. Daha güvenli bir şifre seçin.";
        }
        
        return $errors;
    }
    
    /**
     * Check password against history
     */
    public function checkPasswordHistory(string $password, int $userId): bool
    {
        try {
            $historyCount = $this->getConfig('password_history_count', self::DEFAULT_HISTORY_COUNT);
            
            // Get recent password hashes for user
            $this->db->query(
                "SELECT password_hash FROM password_history 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC 
                 LIMIT ?",
                [$userId, $historyCount]
            );
            
            $historicalPasswords = $this->db->resultSet();
            
            // Check against each historical password
            foreach ($historicalPasswords as $row) {
                if (password_verify($password, $row['password_hash'])) {
                    return false; // Password found in history
                }
            }
            
            // Also check current password from users table
            $this->db->query(
                "SELECT password FROM users WHERE id = ?",
                [$userId]
            );
            
            $currentUser = $this->db->single();
            if ($currentUser && password_verify($password, $currentUser['password'])) {
                return false; // Same as current password
            }
            
            return true; // Password not in history
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error checking history: " . $e->getMessage());
            return true; // Allow on error to prevent lockout
        }
    }
    
    /**
     * Add password to history
     */
    public function addToPasswordHistory(int $userId, string $passwordHash): bool
    {
        try {
            // Add new password to history
            $this->db->query(
                "INSERT INTO password_history (user_id, password_hash, created_at) 
                 VALUES (?, ?, NOW())",
                [$userId, $passwordHash]
            );
            
            // Clean up old entries beyond the configured limit
            $historyCount = $this->getConfig('password_history_count', self::DEFAULT_HISTORY_COUNT);
            
            $this->db->query(
                "DELETE FROM password_history 
                 WHERE user_id = ? 
                 AND id NOT IN (
                     SELECT id FROM (
                         SELECT id FROM password_history 
                         WHERE user_id = ? 
                         ORDER BY created_at DESC 
                         LIMIT ?
                     ) tmp
                 )",
                [$userId, $userId, $historyCount]
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error adding to history: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if password needs to be changed due to expiration
     */
    public function isPasswordExpired(int $userId): bool
    {
        try {
            $expiryDays = $this->getConfig('password_expiry_days', self::DEFAULT_EXPIRY_DAYS);
            
            if ($expiryDays <= 0) {
                return false; // Password expiration disabled
            }
            
            $this->db->query(
                "SELECT password_changed_at, force_password_change FROM users WHERE id = ?",
                [$userId]
            );
            
            $user = $this->db->single();
            
            if (!$user) {
                return false;
            }
            
            // Check force password change flag
            if ($user['force_password_change']) {
                return true;
            }
            
            // Check expiration based on last change date
            if ($user['password_changed_at']) {
                $passwordAge = time() - strtotime($user['password_changed_at']);
                $expiryTime = $expiryDays * 24 * 60 * 60; // Convert days to seconds
                
                return $passwordAge >= $expiryTime;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error checking expiration: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get days until password expires
     */
    public function getDaysUntilExpiry(int $userId): ?int
    {
        try {
            $expiryDays = $this->getConfig('password_expiry_days', self::DEFAULT_EXPIRY_DAYS);
            
            if ($expiryDays <= 0) {
                return null; // No expiration
            }
            
            $this->db->query(
                "SELECT password_changed_at, force_password_change FROM users WHERE id = ?",
                [$userId]
            );
            
            $user = $this->db->single();
            
            if (!$user || $user['force_password_change']) {
                return 0; // Immediate expiration
            }
            
            if ($user['password_changed_at']) {
                $passwordAge = time() - strtotime($user['password_changed_at']);
                $expiryTime = $expiryDays * 24 * 60 * 60;
                $remainingTime = $expiryTime - $passwordAge;
                
                return max(0, ceil($remainingTime / (24 * 60 * 60)));
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error getting expiry days: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Force password change for user
     */
    public function forcePasswordChange(int $userId, string $reason = 'admin_initiated'): bool
    {
        try {
            $this->db->query(
                "UPDATE users SET force_password_change = 1 WHERE id = ?",
                [$userId]
            );
            
            // Log the action
            $this->db->query("SELECT username FROM users WHERE id = ?", [$userId]);
            $user = $this->db->single();
            
            if ($user) {
                $this->securityLogger->logSecurityEvent(
                    $userId,
                    $user['username'],
                    'password_change',
                    null,
                    null,
                    null,
                    ['type' => 'forced', 'reason' => $reason],
                    true
                );
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error forcing password change: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update password with policy compliance
     */
    public function updatePassword(int $userId, string $newPassword, ?string $oldPassword = null): array
    {
        $result = ['success' => false, 'errors' => []];
        
        try {
            // Validate new password
            $validationErrors = $this->validatePassword($newPassword, $userId);
            if (!empty($validationErrors)) {
                $result['errors'] = $validationErrors;
                return $result;
            }
            
            // Hash the new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Get current password hash for history
            $this->db->query("SELECT password FROM users WHERE id = ?", [$userId]);
            $currentUser = $this->db->single();
            
            if (!$currentUser) {
                $result['errors'][] = "Kullanıcı bulunamadı.";
                return $result;
            }
            
            // Verify old password if provided
            if ($oldPassword !== null) {
                if (!password_verify($oldPassword, $currentUser['password'])) {
                    $result['errors'][] = "Mevcut şifre yanlış.";
                    return $result;
                }
            }
            
            // Add current password to history before updating
            $this->addToPasswordHistory($userId, $currentUser['password']);
            
            // Update user password
            $this->db->query(
                "UPDATE users SET 
                 password = ?, 
                 password_changed_at = NOW(), 
                 force_password_change = 0 
                 WHERE id = ?",
                [$newPasswordHash, $userId]
            );
            
            // Log password change
            $this->db->query("SELECT username FROM users WHERE id = ?", [$userId]);
            $user = $this->db->single();
            
            if ($user) {
                $this->securityLogger->logPasswordChange($userId, $user['username'], false);
            }
            
            $result['success'] = true;
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error updating password: " . $e->getMessage());
            $result['errors'][] = "Şifre güncellenirken bir hata oluştu.";
        }
        
        return $result;
    }
    
    /**
     * Generate password strength score
     */
    public function getPasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];
        
        // Length scoring
        $length = strlen($password);
        if ($length >= 8) $score += 20;
        if ($length >= 12) $score += 10;
        if ($length >= 16) $score += 10;
        
        // Character diversity
        if (preg_match('/[a-z]/', $password)) {
            $score += 10;
        } else {
            $feedback[] = "Küçük harf ekleyin";
        }
        
        if (preg_match('/[A-Z]/', $password)) {
            $score += 10;
        } else {
            $feedback[] = "Büyük harf ekleyin";
        }
        
        if (preg_match('/[0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = "Rakam ekleyin";
        }
        
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = "Özel karakter ekleyin";
        }
        
        // Pattern checks
        if (!preg_match('/(.)\1{2,}/', $password)) { // No repeated characters
            $score += 10;
        }
        
        if (!preg_match('/123|abc|qwe/i', $password)) { // No common sequences
            $score += 10;
        }
        
        // Determine strength level
        if ($score >= 80) {
            $level = 'Çok Güçlü';
            $class = 'very-strong';
        } elseif ($score >= 60) {
            $level = 'Güçlü';
            $class = 'strong';
        } elseif ($score >= 40) {
            $level = 'Orta';
            $class = 'medium';
        } elseif ($score >= 20) {
            $level = 'Zayıf';
            $class = 'weak';
        } else {
            $level = 'Çok Zayıf';
            $class = 'very-weak';
        }
        
        return [
            'score' => min(100, $score),
            'level' => $level,
            'class' => $class,
            'feedback' => $feedback
        ];
    }
    
    /**
     * Check if password is commonly used
     */
    private function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            '123456', 'password', '123456789', '12345678', '12345',
            'qwerty', 'abc123', 'password123', 'admin', 'letmein',
            'welcome', 'monkey', '1234567890', 'iloveyou', 'princess',
            'şifre', 'parola', '123456789', 'qwertyuiop'
        ];
        
        return in_array(strtolower($password), array_map('strtolower', $commonPasswords));
    }
    
    /**
     * Get configuration value from system_config
     */
    private function getConfig(string $key, $default)
    {
        try {
            $this->db->query(
                "SELECT config_value, config_type FROM system_config WHERE config_key = ?",
                [$key]
            );
            
            $result = $this->db->single();
            
            if (!$result) {
                return $default;
            }
            
            $value = $result['config_value'];
            $type = $result['config_type'];
            
            switch ($type) {
                case 'integer':
                    return (int)$value;
                case 'boolean':
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                case 'json':
                    return json_decode($value, true);
                default:
                    return $value;
            }
            
        } catch (Exception $e) {
            error_log("PasswordPolicy Error getting config: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Get all password policy settings for display
     */
    public function getPolicySettings(): array
    {
        return [
            'min_length' => $this->getConfig('password_min_length', self::DEFAULT_MIN_LENGTH),
            'require_uppercase' => $this->getConfig('password_require_uppercase', self::DEFAULT_REQUIRE_UPPERCASE),
            'require_lowercase' => $this->getConfig('password_require_lowercase', self::DEFAULT_REQUIRE_LOWERCASE),
            'require_numbers' => $this->getConfig('password_require_numbers', self::DEFAULT_REQUIRE_NUMBERS),
            'require_special' => $this->getConfig('password_require_special', self::DEFAULT_REQUIRE_SPECIAL),
            'history_count' => $this->getConfig('password_history_count', self::DEFAULT_HISTORY_COUNT),
            'expiry_days' => $this->getConfig('password_expiry_days', self::DEFAULT_EXPIRY_DAYS)
        ];
    }
}
?>