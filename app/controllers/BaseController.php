<?php
// app/Controllers/BaseController.php
require_once __DIR__ . '/../services/UnifiedViewService.php';
require_once __DIR__ . '/../../includes/SessionManager.php';

use App\Services\UnifiedViewService;

class BaseController
{
    protected function render(string $view, array $data = [], array $options = []): void
    {
        try {
            // UnifiedViewService kullanarak render et
            UnifiedViewService::render($view, $data, $options);
        } catch (Exception $e) {
            error_log("View Render Error: " . $e->getMessage());
            $this->handleError("Sayfa yüklenirken bir hata oluştu.");
        }
    }

    protected function handleError(string $message, int $code = 500, Exception $exception = null): never
    {
        error_log("Controller Error: " . $message);

        // Send to Sentry with additional context
        if (class_exists('\Sentry\SentrySdk')) {
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($message, $code, $exception) {
                // Add controller context
                $scope->setTag('error_source', 'controller');
                $scope->setTag('http_code', (string)$code);
                $scope->setLevel(\Sentry\Severity::error());
                
                // Add user and organization context
                $this->addSentryUserContext($scope);
                
                // Add request context
                $scope->setContext('request', [
                    'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                    'uri' => $_SERVER['REQUEST_URI'] ?? '/',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                    'is_ajax' => $this->isAjax()
                ]);

                // Capture the exception or create a new one
                if ($exception) {
                    \Sentry\captureException($exception);
                } else {
                    \Sentry\captureMessage($message, \Sentry\Severity::error());
                }
            });
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            http_response_code($code);
            echo json_encode([
                'error' => $message,
                'code' => $code,
                'timestamp' => date('c')
            ]);
        } else {
            $_SESSION['error'] = $message;
            header('Location: ' . BASE_URL . 'home/error');
        }
        exit;
    }

    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function checkAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('user/login');
        }
        
        // Validate session with database
        $sessionManager = new SessionManager();
        if (!$sessionManager->validateSession()) {
            // Session is invalid, force logout
            session_destroy();
            $this->redirect('user/login');
        }
    }

    /**
     * Check if user has permission for an operation
     * @param string $permissionName Permission to check
     * @param string $operation Operation type (select, insert, update, delete)
     * @throws Exception if permission denied
     */
    protected function checkPermission(string $permissionName, string $operation = 'select'): void
    {
        require_once __DIR__ . '/../../includes/PermissionHelper.php';
        
        if (!hasPermission($permissionName, $operation)) {
            $this->handleError('Bu işlem için yetkiniz bulunmamaktadır.', 403);
        }
    }

    /**
     * Check if user has permission for a specific cove
     * @param string $permissionName Permission to check
     * @param int|null $coveId Cove ID to check
     * @param string $operation Operation type
     * @throws Exception if permission denied
     */
    protected function checkCovePermission(string $permissionName, ?int $coveId, string $operation = 'select'): void
    {
        require_once __DIR__ . '/../../includes/PermissionHelper.php';
        
        if (!hasPermissionForCove($permissionName, $coveId, $operation)) {
            $this->handleError('Bu merkez için yetkiniz bulunmamaktadır.', 403);
        }
    }

    /**
     * Get user's cove ID from session
     * @return int|null
     */
    protected function getUserCoveId(): ?int
    {
        return $_SESSION['cove_id'] ?? null;
    }

    /**
     * Check if current user is superadmin
     * @return bool
     */
    protected function isSuperAdmin(): bool
    {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }

    /**
     * Check if current user is coordinator
     * @return bool
     */
    protected function isCoordinator(): bool
    {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'coordinator';
    }

    /**
     * Generate a new CSRF token
     */
    protected function generateCSRFToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRFToken(?string $token): bool
    {
        // Check if token exists
        if (!isset($_SESSION['csrf_token']) || !$token) {
            return false;
        }

        // Check token expiration (30 minutes)
        if (!isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > 1800) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        // Verify token matches
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get current CSRF token or generate new one
     */
    protected function getCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) ||
            (time() - $_SESSION['csrf_token_time']) > 1800) {
            return $this->generateCSRFToken();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Add user and organization context to Sentry scope
     */
    protected function addSentryUserContext(\Sentry\State\Scope $scope): void
    {
        if (isset($_SESSION['user_id'])) {
            $scope->setUser([
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? 'unknown',
                'email' => $_SESSION['user_email'] ?? null,
                'realname' => $_SESSION['user_realname'] ?? null
            ]);
        }

        if (isset($_SESSION['cove_id'])) {
            $scope->setTag('cove_id', (string)$_SESSION['cove_id']);
            $scope->setTag('cove_name', $_SESSION['cove_name'] ?? 'unknown');
        }

        if (isset($_SESSION['role_name'])) {
            $scope->setTag('user_role', $_SESSION['role_name']);
        }
    }

    /**
     * Log and track performance events to Sentry
     */
    protected function trackPerformance(string $operationName, callable $callback, array $context = [])
    {
        if (!class_exists('\Sentry\SentrySdk')) {
            return $callback();
        }

        $startTime = microtime(true);

        try {
            $result = $callback();
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            // Log successful operation with performance data
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($operationName, $duration, $context) {
                $scope->setTag('operation', $operationName);
                $scope->setTag('performance_tracked', 'true');
                
                foreach ($context as $key => $value) {
                    $scope->setTag($key, (string)$value);
                }
                
                $this->addSentryUserContext($scope);
                
                $scope->setContext('performance', [
                    'operation' => $operationName,
                    'duration_ms' => round($duration, 2),
                    'status' => 'success'
                ]);
                
                \Sentry\captureMessage("Operation completed: {$operationName}", \Sentry\Severity::info());
            });
            
            return $result;
        } catch (Exception $e) {
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000;
            
            // Log failed operation with performance data
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($operationName, $duration, $context, $e) {
                $scope->setTag('operation', $operationName);
                $scope->setTag('performance_tracked', 'true');
                
                foreach ($context as $key => $value) {
                    $scope->setTag($key, (string)$value);
                }
                
                $this->addSentryUserContext($scope);
                
                $scope->setContext('performance', [
                    'operation' => $operationName,
                    'duration_ms' => round($duration, 2),
                    'status' => 'error',
                    'error_message' => $e->getMessage()
                ]);
                
                \Sentry\captureException($e);
            });
            
            throw $e;
        }
    }
}
