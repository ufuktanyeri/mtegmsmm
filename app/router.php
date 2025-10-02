<?php
// app/Router.php

class Router
{
    private $controller = 'HomeController';
    private $action = 'index';
    private $params = [];

    public function dispatch($url, $customParams = null)
    {
        try {
            // Remove query string from URL
            if (strpos($url, '?') !== false) {
                $url = strstr($url, '?', true);
            }

            $url = trim($url, '/');
            $segments = !empty($url) ? explode('/', $url) : [];

            $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
            $action = !empty($segments[1]) ? $segments[1] : 'index';

            // Parametreleri al
            $params = array_slice($segments, 2);
            if ($customParams) {
                $params = array_merge($params, $customParams);
            }

            $this->controller = $controllerName;
            $this->action = $action;
            $this->params = $params;

            // Use APP_PATH constant instead of __DIR__ for consistency
            $controllerFile = (defined('APP_PATH') ? APP_PATH : __DIR__ . '/') . "controllers/{$controllerName}.php";

            // Controller dosyası var mı?
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller dosyası bulunamadı: {$controllerName}");
            }

            require_once $controllerFile;

            // Controller sınıfı var mı?
            if (!class_exists($controllerName)) {
                throw new Exception("Controller sınıfı bulunamadı: {$controllerName}");
            }
            
            $controller = new $controllerName();

            // Method var mı?
            if (!method_exists($controller, $action)) {
                // Fallback mapping: 'list' -> 'listObjectives'
                if ($action === 'list' && method_exists($controller, 'listObjectives')) {
                    $action = 'listObjectives';
                } else {
                    throw new Exception("Method bulunamadı: {$action} in {$controllerName}");
                }
            }
            
            // Method'u çağır
            call_user_func_array([$controller, $action], [$params]);
        } catch (Exception $e) {
            error_log("Router Error: " . $e->getMessage());
            $this->handleError($e->getMessage());
        }
    }

    private function handleError($message)
    {
        // Error sayfasına yönlendir
        $errorViewPath = (defined('APP_PATH') ? APP_PATH : __DIR__ . '/') . 'views/home/error.php';
        if (file_exists($errorViewPath)) {
            $_SESSION['error_message'] = $message;
            require_once $errorViewPath;
        } else {
            echo "<h1>Hata</h1>";
            echo "<p>{$message}</p>";
            echo "<a href='" . (defined('BASE_URL') ? BASE_URL : '/') . "'>Ana Sayfaya Dön</a>";
        }
    }

    public function debug()
    {
        echo "<h2>Router Debug</h2>";
        echo "<pre>";
        echo "Controller: " . $this->controller . "\n";
        echo "Action: " . $this->action . "\n";
        echo "Params: " . print_r($this->params, true);
        echo "</pre>";
    }
}
