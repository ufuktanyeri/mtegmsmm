<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/LogModel.php';
require_once APP_PATH . 'entities/Log.php';
require_once APP_PATH . 'entities/Permission.php';

class LogController extends BaseController {
    protected function checkControllerPermission() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
       // var_dump($permissions);
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {               
                if ($permission->getPermissionName() === 'users.manage') {
                    $hasPermission = true;
                    break;
                }
            }
        }
      
        if (!$hasPermission) {
            header('Location: index.php?url=home/error');
            exit();
        }       
    }

    public function index() {
        $this->checkControllerPermission();
        $logModel = new LogModel();
        $logs = $logModel->getAllLogs();

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Sistem', 'url' => ''],
            ['title' => 'Sistem Logları', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Logs';
        $this->data['logs'] = $logs;

        $this->render('log/index', $this->data);
    }
   
}
?>
