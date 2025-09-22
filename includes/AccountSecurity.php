<?php
/**
 * AccountSecurity Class
 * 
 * Handles account security features like lockouts, failed login tracking,
 * and account status management using the enhanced users table
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/SecurityLogger.php';

class AccountSecurity
{
    private $db;
    private $securityLogger;
    
    // Default security policies (can be overridden by system_config)
    private const DEFAULT_MAX_FAILED_ATTEMPTS = 5;
    private const DEFAULT_LOCKOUT_DURATION = 30; // minutes
    private const DEFAULT_LOCKOUT_RESET_TIME = 60; // minutes
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->securityLogger = new SecurityLogger();
    }
    
    /**
     * Check if account is locked
     */
    public function isAccountLocked(int $userId): bool
    {
        try {
            $this->db->query(
                "SELECT account_locked_until FROM users WHERE id = ? AND is_active = 1",
                [$userId]
            );
            
            $result = $this->db->single();
            
            if (!$result || !$result['account_locked_until']) {
                return false;
            }
            
            // Check if lockout has expired
            $lockoutTime = strtotime($result['account_locked_until']);
            if ($lockoutTime <= time()) {
                // Lockout has expired, unlock the account
                $this->unlockAccount($userId);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error checking lock status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get account lockout information
     */
    public function getAccountLockInfo(int $userId): ?array
    {
        try {
            $this->db->query(
                "SELECT failed_login_count, account_locked_until, last_login 
                 FROM users WHERE id = ?",
                [$userId]
            );
            
            $result = $this->db->single();
            
            if (!$result) {
                return null;
            }
            
            return [
                'failed_attempts' => (int)$result['failed_login_count'],
                'locked_until' => $result['account_locked_until'],
                'last_login' => $result['last_login'],
                'is_locked' => $this->isAccountLocked($userId)
            ];
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error getting lock info: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Record failed login attempt
     */
    public function recordFailedLogin(string $username): bool
    {
        try {
            // Get user info
            $this->db->query(
                "SELECT id, failed_login_count FROM users WHERE username = ?",
                [$username]
            );
            
            $user = $this->db->single();
            
            if (!$user) {
                // Log failed attempt for non-existent user
                $this->securityLogger->logFailedLogin($username, ['reason' => 'invalid_username']);
                return false;
            }
            
            $userId = $user['id'];
            $currentFailedCount = (int)$user['failed_login_count'];
            $newFailedCount = $currentFailedCount + 1;
            
            // Get security policies
            $maxAttempts = $this->getSecurityConfig('max_failed_login_attempts', self::DEFAULT_MAX_FAILED_ATTEMPTS);
            $lockoutDuration = $this->getSecurityConfig('account_lockout_duration', self::DEFAULT_LOCKOUT_DURATION);
            
            // Update failed login count
            $this->db->query(
                "UPDATE users SET failed_login_count = ? WHERE id = ?",
                [$newFailedCount, $userId]
            );
            
            // Check if account should be locked
            if ($newFailedCount >= $maxAttempts) {
                $this->lockAccount($userId, $lockoutDuration);
                
                // Log account lockout
                $this->securityLogger->logAccountLocked($userId, $username, $newFailedCount);
                
                // Log the failed attempt that caused the lockout
                $this->securityLogger->logFailedLogin($username, [
                    'reason' => 'invalid_credentials',
                    'failed_attempt_count' => $newFailedCount,
                    'result' => 'account_locked'
                ]);
            } else {
                // Log regular failed attempt
                $this->securityLogger->logFailedLogin($username, [
                    'reason' => 'invalid_credentials',
                    'failed_attempt_count' => $newFailedCount,
                    'attempts_remaining' => $maxAttempts - $newFailedCount
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error recording failed login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record successful login (reset failed attempts)
     */
    public function recordSuccessfulLogin(int $userId): bool
    {
        try {
            // Check if account was previously locked
            $wasLocked = $this->isAccountLocked($userId);
            
            // Reset failed login count and update last login
            $this->db->query(
                "UPDATE users SET 
                 failed_login_count = 0, 
                 account_locked_until = NULL, 
                 last_login = NOW() 
                 WHERE id = ?",
                [$userId]
            );
            
            // If account was locked and is now being unlocked, log it
            if ($wasLocked) {
                $this->db->query("SELECT username FROM users WHERE id = ?", [$userId]);
                $user = $this->db->single();
                if ($user) {
                    $this->securityLogger->logAccountUnlocked($userId, $user['username'], 'successful_login');
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error recording successful login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lock an account for specified duration
     */
    public function lockAccount(int $userId, int $durationMinutes): bool
    {
        try {
            $lockUntil = date('Y-m-d H:i:s', time() + ($durationMinutes * 60));
            
            $this->db->query(
                "UPDATE users SET account_locked_until = ? WHERE id = ?",
                [$lockUntil, $userId]
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error locking account: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unlock an account
     */
    public function unlockAccount(int $userId, string $reason = 'manual'): bool
    {
        try {
            // Get username for logging
            $this->db->query("SELECT username FROM users WHERE id = ?", [$userId]);
            $user = $this->db->single();
            
            // Unlock the account
            $this->db->query(
                "UPDATE users SET 
                 account_locked_until = NULL, 
                 failed_login_count = 0 
                 WHERE id = ?",
                [$userId]
            );
            
            // Log the unlock
            if ($user) {
                $this->securityLogger->logAccountUnlocked($userId, $user['username'], $reason);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error unlocking account: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if account is active
     */
    public function isAccountActive(int $userId): bool
    {
        try {
            $this->db->query(
                "SELECT is_active FROM users WHERE id = ?",
                [$userId]
            );
            
            $result = $this->db->single();
            return $result && (bool)$result['is_active'];
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error checking account status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activate/deactivate account
     */
    public function setAccountStatus(int $userId, bool $active): bool
    {
        try {
            $this->db->query(
                "UPDATE users SET is_active = ? WHERE id = ?",
                [$active ? 1 : 0, $userId]
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error setting account status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get security configuration value
     */
    private function getSecurityConfig(string $key, $default)
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
            
            // Convert based on type
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
            error_log("AccountSecurity Error getting config: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Get account security summary for admin dashboard
     */
    public function getSecuritySummary(): array
    {
        try {
            // Get locked accounts count
            $this->db->query(
                "SELECT COUNT(*) as count FROM users 
                 WHERE account_locked_until > NOW() AND is_active = 1"
            );
            $lockedAccounts = (int)$this->db->single()['count'];
            
            // Get recent failed attempts (last 24 hours)
            $this->db->query(
                "SELECT COUNT(*) as count FROM security_logs 
                 WHERE action_type = 'failed_login' 
                 AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"
            );
            $recentFailedAttempts = (int)$this->db->single()['count'];
            
            // Get accounts with high failed attempt counts
            $this->db->query(
                "SELECT COUNT(*) as count FROM users 
                 WHERE failed_login_count >= 3 AND account_locked_until IS NULL"
            );
            $accountsAtRisk = (int)$this->db->single()['count'];
            
            return [
                'locked_accounts' => $lockedAccounts,
                'recent_failed_attempts_24h' => $recentFailedAttempts,
                'accounts_at_risk' => $accountsAtRisk,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            error_log("AccountSecurity Error getting summary: " . $e->getMessage());
            return [
                'locked_accounts' => 0,
                'recent_failed_attempts_24h' => 0,
                'accounts_at_risk' => 0,
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Unable to fetch security summary'
            ];
        }
    }
}
?>