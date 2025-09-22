<?php
/**
 * SecurityLogger Class
 * 
 * Handles security event logging to the security_logs table
 * Provides methods for logging authentication events and security violations
 */

require_once __DIR__ . '/Database.php';

class SecurityLogger
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Log a security event
     * 
     * @param int|null $userId User ID (null for failed logins with invalid username)
     * @param string|null $username Username (for failed login attempts)
     * @param string $actionType Type of action (login, logout, failed_login, etc.)
     * @param string|null $ipAddress IP address
     * @param string|null $userAgent User agent string
     * @param string|null $sessionId PHP session ID
     * @param array|null $details Additional details as associative array
     * @param bool $success Whether the action was successful
     * @return bool Success status
     */
    public function logSecurityEvent(
        ?int $userId,
        ?string $username,
        string $actionType,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $sessionId = null,
        ?array $details = null,
        bool $success = false
    ): bool {
        try {
            // Auto-detect IP and User Agent if not provided
            if ($ipAddress === null) {
                $ipAddress = $this->getClientIP();
            }
            
            if ($userAgent === null) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            }
            
            if ($sessionId === null) {
                $sessionId = session_id() ?: null;
            }
            
            // Convert details to JSON
            $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null;
            
            $this->db->query(
                "INSERT INTO security_logs (user_id, username, action_type, ip_address, user_agent, session_id, details, success, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $userId,
                    $username,
                    $actionType,
                    $ipAddress,
                    $userAgent,
                    $sessionId,
                    $detailsJson,
                    $success ? 1 : 0
                ]
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("SecurityLogger Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log successful login
     */
    public function logSuccessfulLogin(int $userId, string $username, ?array $details = null): bool
    {
        return $this->logSecurityEvent(
            $userId,
            $username,
            'login',
            null,
            null,
            null,
            $details,
            true
        );
    }
    
    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $username, ?array $details = null): bool
    {
        return $this->logSecurityEvent(
            null,
            $username,
            'failed_login',
            null,
            null,
            null,
            $details,
            false
        );
    }
    
    /**
     * Log successful logout
     */
    public function logLogout(int $userId, string $username): bool
    {
        return $this->logSecurityEvent(
            $userId,
            $username,
            'logout',
            null,
            null,
            null,
            null,
            true
        );
    }
    
    /**
     * Log password change
     */
    public function logPasswordChange(int $userId, string $username, bool $forced = false): bool
    {
        $details = ['forced' => $forced];
        return $this->logSecurityEvent(
            $userId,
            $username,
            'password_change',
            null,
            null,
            null,
            $details,
            true
        );
    }
    
    /**
     * Log account lockout
     */
    public function logAccountLocked(int $userId, string $username, int $attemptCount): bool
    {
        $details = [
            'failed_attempts' => $attemptCount,
            'lockout_reason' => 'max_failed_attempts_exceeded'
        ];
        
        return $this->logSecurityEvent(
            $userId,
            $username,
            'account_locked',
            null,
            null,
            null,
            $details,
            true
        );
    }
    
    /**
     * Log account unlock
     */
    public function logAccountUnlocked(int $userId, string $username, string $unlockReason = 'timeout'): bool
    {
        $details = ['unlock_reason' => $unlockReason];
        
        return $this->logSecurityEvent(
            $userId,
            $username,
            'account_unlocked',
            null,
            null,
            null,
            $details,
            true
        );
    }
    
    /**
     * Log permission denied
     */
    public function logPermissionDenied(?int $userId, string $resource, string $action): bool
    {
        $details = [
            'resource' => $resource,
            'attempted_action' => $action,
            'requested_url' => $_SERVER['REQUEST_URI'] ?? null
        ];
        
        $username = null;
        if ($userId && isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }
        
        return $this->logSecurityEvent(
            $userId,
            $username,
            'permission_denied',
            null,
            null,
            null,
            $details,
            false
        );
    }
    
    /**
     * Get recent failed login attempts for a username
     */
    public function getRecentFailedAttempts(string $username, int $minutes = 60): int
    {
        try {
            $this->db->query(
                "SELECT COUNT(*) as count FROM security_logs 
                 WHERE username = ? AND action_type = 'failed_login' 
                 AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
                [$username, $minutes]
            );
            
            $result = $this->db->single();
            return (int)($result['count'] ?? 0);
            
        } catch (Exception $e) {
            error_log("SecurityLogger Error getting failed attempts: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent failed login attempts from an IP
     */
    public function getRecentFailedAttemptsFromIP(string $ipAddress, int $minutes = 60): int
    {
        try {
            $this->db->query(
                "SELECT COUNT(*) as count FROM security_logs 
                 WHERE ip_address = ? AND action_type = 'failed_login' 
                 AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
                [$ipAddress, $minutes]
            );
            
            $result = $this->db->single();
            return (int)($result['count'] ?? 0);
            
        } catch (Exception $e) {
            error_log("SecurityLogger Error getting IP failed attempts: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Clean old security logs based on retention policy
     */
    public function cleanOldLogs(int $retentionDays = 365): int
    {
        try {
            $this->db->query(
                "DELETE FROM security_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
                [$retentionDays]
            );
            
            return $this->db->rowCount();
            
        } catch (Exception $e) {
            error_log("SecurityLogger Error cleaning old logs: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get client IP address (handles proxies and load balancers)
     */
    private function getClientIP(): string
    {
        // Check for various headers that might contain the real IP
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback to REMOTE_ADDR even if it's a private IP
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
?>