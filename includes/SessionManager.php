<?php
/**
 * SessionManager Class
 * 
 * Manages user sessions using the user_sessions table
 * Provides session tracking, concurrent session management, and security features
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/SecurityLogger.php';

class SessionManager
{
    private $db;
    private $securityLogger;
    
    // Default session policies
    private const DEFAULT_SESSION_TIMEOUT = 120; // minutes
    private const DEFAULT_MAX_CONCURRENT_SESSIONS = 3;
    private const DEFAULT_SESSION_REGENERATE_INTERVAL = 30; // minutes
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->securityLogger = new SecurityLogger();
    }
    
    /**
     * Create a new session record
     */
    public function createSession(int $userId, ?int $coveId = null): bool
    {
        try {
            $sessionToken = session_id();
            $ipAddress = $this->getClientIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $timeout = $this->getSecurityConfig('session_timeout', self::DEFAULT_SESSION_TIMEOUT);
            $expiresAt = date('Y-m-d H:i:s', time() + ($timeout * 60));
            
            // Clean up any existing expired sessions for this user
            $this->cleanupExpiredSessions($userId);
            
            // Check for maximum concurrent sessions
            $maxSessions = $this->getSecurityConfig('max_concurrent_sessions', self::DEFAULT_MAX_CONCURRENT_SESSIONS);
            $this->enforceMaxSessions($userId, $maxSessions);
            
            // Create new session record
            $this->db->query(
                "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, cove_id, expires_at, is_active, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 1, NOW())",
                [$userId, $sessionToken, $ipAddress, $userAgent, $coveId, $expiresAt]
            );
            
            // Update session regeneration time
            $_SESSION['session_created'] = time();
            $_SESSION['session_regenerated'] = time();
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error creating session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update session activity
     */
    public function updateSessionActivity(): bool
    {
        try {
            $sessionToken = session_id();
            
            if (!$sessionToken) {
                return false;
            }
            
            // Update last activity time
            $this->db->query(
                "UPDATE user_sessions SET last_activity = NOW() 
                 WHERE session_token = ? AND is_active = 1",
                [$sessionToken]
            );
            
            // Check if session should be regenerated
            $this->checkSessionRegeneration();
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error updating activity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate session
     */
    public function validateSession(): bool
    {
        try {
            $sessionToken = session_id();
            
            if (!$sessionToken) {
                return false;
            }
            
            // Check if session exists and is valid
            $this->db->query(
                "SELECT user_id, expires_at, is_active FROM user_sessions 
                 WHERE session_token = ? AND is_active = 1",
                [$sessionToken]
            );
            
            $session = $this->db->single();
            
            if (!$session) {
                return false;
            }
            
            // Check if session has expired
            if (strtotime($session['expires_at']) <= time()) {
                $this->destroySession($sessionToken);
                return false;
            }
            
            // Update activity
            $this->updateSessionActivity();
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error validating session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Destroy session
     */
    public function destroySession(?string $sessionToken = null): bool
    {
        try {
            if ($sessionToken === null) {
                $sessionToken = session_id();
            }
            
            if (!$sessionToken) {
                return false;
            }
            
            // Mark session as inactive
            $this->db->query(
                "UPDATE user_sessions SET is_active = 0 WHERE session_token = ?",
                [$sessionToken]
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error destroying session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Destroy all sessions for a user
     */
    public function destroyAllUserSessions(int $userId, ?string $exceptSessionToken = null): bool
    {
        try {
            if ($exceptSessionToken) {
                $this->db->query(
                    "UPDATE user_sessions SET is_active = 0 
                     WHERE user_id = ? AND session_token != ? AND is_active = 1",
                    [$userId, $exceptSessionToken]
                );
            } else {
                $this->db->query(
                    "UPDATE user_sessions SET is_active = 0 
                     WHERE user_id = ? AND is_active = 1",
                    [$userId]
                );
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error destroying user sessions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get active sessions for a user
     */
    public function getUserActiveSessions(int $userId): array
    {
        try {
            $this->db->query(
                "SELECT session_token, ip_address, user_agent, last_activity, created_at, expires_at 
                 FROM user_sessions 
                 WHERE user_id = ? AND is_active = 1 AND expires_at > NOW() 
                 ORDER BY last_activity DESC",
                [$userId]
            );
            
            return $this->db->resultSet();
            
        } catch (Exception $e) {
            error_log("SessionManager Error getting user sessions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get session information
     */
    public function getSessionInfo(?string $sessionToken = null): ?array
    {
        try {
            if ($sessionToken === null) {
                $sessionToken = session_id();
            }
            
            if (!$sessionToken) {
                return null;
            }
            
            $this->db->query(
                "SELECT us.*, u.username, u.realname, c.name as cove_name 
                 FROM user_sessions us 
                 JOIN users u ON us.user_id = u.id 
                 LEFT JOIN coves c ON us.cove_id = c.id 
                 WHERE us.session_token = ?",
                [$sessionToken]
            );
            
            return $this->db->single();
            
        } catch (Exception $e) {
            error_log("SessionManager Error getting session info: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions(?int $userId = null): int
    {
        try {
            if ($userId) {
                $this->db->query(
                    "UPDATE user_sessions SET is_active = 0 
                     WHERE user_id = ? AND (expires_at <= NOW() OR is_active = 0)",
                    [$userId]
                );
            } else {
                $this->db->query(
                    "UPDATE user_sessions SET is_active = 0 
                     WHERE expires_at <= NOW() OR is_active = 0"
                );
            }
            
            return $this->db->rowCount();
            
        } catch (Exception $e) {
            error_log("SessionManager Error cleaning expired sessions: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Enforce maximum concurrent sessions
     */
    private function enforceMaxSessions(int $userId, int $maxSessions): void
    {
        try {
            // Get current active session count
            $this->db->query(
                "SELECT COUNT(*) as count FROM user_sessions 
                 WHERE user_id = ? AND is_active = 1 AND expires_at > NOW()",
                [$userId]
            );
            
            $currentCount = (int)$this->db->single()['count'];
            
            if ($currentCount >= $maxSessions) {
                // Deactivate oldest sessions
                $sessionsToRemove = $currentCount - $maxSessions + 1;
                
                $this->db->query(
                    "UPDATE user_sessions SET is_active = 0 
                     WHERE user_id = ? AND is_active = 1 
                     ORDER BY last_activity ASC 
                     LIMIT ?",
                    [$userId, $sessionsToRemove]
                );
            }
            
        } catch (Exception $e) {
            error_log("SessionManager Error enforcing max sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Check if session should be regenerated
     */
    private function checkSessionRegeneration(): void
    {
        $regenerateInterval = $this->getSecurityConfig('session_regenerate_interval', self::DEFAULT_SESSION_REGENERATE_INTERVAL);
        $lastRegenerated = $_SESSION['session_regenerated'] ?? 0;
        
        if ((time() - $lastRegenerated) > ($regenerateInterval * 60)) {
            $this->regenerateSession();
        }
    }
    
    /**
     * Regenerate session ID
     */
    public function regenerateSession(): bool
    {
        try {
            $oldSessionToken = session_id();
            
            // Regenerate PHP session ID
            session_regenerate_id(true);
            $newSessionToken = session_id();
            
            // Update database record
            $this->db->query(
                "UPDATE user_sessions SET session_token = ? WHERE session_token = ?",
                [$newSessionToken, $oldSessionToken]
            );
            
            $_SESSION['session_regenerated'] = time();
            
            return true;
            
        } catch (Exception $e) {
            error_log("SessionManager Error regenerating session: " . $e->getMessage());
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
            error_log("SessionManager Error getting config: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Get session statistics for admin dashboard
     */
    public function getSessionStatistics(): array
    {
        try {
            // Active sessions count
            $this->db->query(
                "SELECT COUNT(*) as count FROM user_sessions 
                 WHERE is_active = 1 AND expires_at > NOW()"
            );
            $activeSessions = (int)$this->db->single()['count'];
            
            // Unique active users
            $this->db->query(
                "SELECT COUNT(DISTINCT user_id) as count FROM user_sessions 
                 WHERE is_active = 1 AND expires_at > NOW()"
            );
            $activeUsers = (int)$this->db->single()['count'];
            
            // Sessions created today
            $this->db->query(
                "SELECT COUNT(*) as count FROM user_sessions 
                 WHERE DATE(created_at) = CURDATE()"
            );
            $sessionsToday = (int)$this->db->single()['count'];
            
            return [
                'active_sessions' => $activeSessions,
                'active_users' => $activeUsers,
                'sessions_created_today' => $sessionsToday,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            error_log("SessionManager Error getting statistics: " . $e->getMessage());
            return [
                'active_sessions' => 0,
                'active_users' => 0,
                'sessions_created_today' => 0,
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Unable to fetch session statistics'
            ];
        }
    }
}
?>