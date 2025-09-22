<?php
// app/Helpers/Security.php

class Security 
{
    /**
     * Generate CSRF Token
     */
    public static function generateCSRFToken(): string 
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF Token
     */
    public static function verifyCSRFToken(string $token): bool 
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitizeInput($input): string 
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 10485760): array 
    {
        $errors = [];
        
        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = 'Invalid upload parameter';
            return $errors;
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file sent';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File too large';
                break;
            default:
                $errors[] = 'Unknown upload error';
                break;
        }
        
        if (!empty($errors)) {
            return $errors;
        }
        
        // File size validation
        if ($file['size'] > $maxSize) {
            $errors[] = 'File exceeds maximum size limit';
        }
        
        // File type validation
        if (!empty($allowedTypes)) {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);
            
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedTypes) || 
                !in_array($mimeType, self::getAllowedMimeTypes($allowedTypes))) {
                $errors[] = 'Invalid file type';
            }
        }
        
        return $errors;
    }
    
    /**
     * Get allowed MIME types for file extensions
     */
    private static function getAllowedMimeTypes(array $extensions): array 
    {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain'
        ];
        
        $allowedMimes = [];
        foreach ($extensions as $ext) {
            if (isset($mimeMap[$ext])) {
                $allowedMimes[] = $mimeMap[$ext];
            }
        }
        
        return $allowedMimes;
    }
    
    /**
     * Generate secure random filename
     */
    public static function generateSecureFilename(string $originalName): string 
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sanitize base name
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $baseName = substr($baseName, 0, 50); // Limit length
        
        return date('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(8)) . '_' . $baseName . '.' . $extension;
    }
    
    /**
     * Secure headers for production
     */
    public static function setSecurityHeaders(): void 
    {
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Security Policy - reCAPTCHA için Google domain'leri eklendi
        header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com https://www.google.com https://www.gstatic.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self' https://www.google.com; frame-src https://www.google.com;");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Hide server information
        header_remove('X-Powered-By');
        
        // HTTPS redirect (only if HTTPS available)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
    
    /**
     * Rate limiting (simple implementation)
     */
    public static function checkRateLimit(string $identifier, int $limit = 100, int $window = 3600): bool 
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $key = 'rate_limit_' . $identifier;
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'reset' => $now + $window];
            return true;
        }
        
        $data = $_SESSION[$key];
        
        if ($now > $data['reset']) {
            $_SESSION[$key] = ['count' => 1, 'reset' => $now + $window];
            return true;
        }
        
        if ($data['count'] >= $limit) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Password strength validation
     */
    public static function validatePasswordStrength(string $password): array 
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
}