<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/LogModel.php';
require_once __DIR__ . '/../entities/Log.php';
require_once __DIR__ . '/../entities/Permission.php';

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
        $this->render('log/index', ['title' => 'Logs', 'logs' => $logs]);
    }
   
}
?>
