<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/DetailedLogModel.php';
require_once __DIR__ . '/../entities/DetailedLog.php';
require_once __DIR__ . '/../entities/Permission.php';

class DetailedlogController extends BaseController {
    protected function checkControllerPermission() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
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
        $detailedLogModel = new DetailedLogModel();
        $detailedLogs = $detailedLogModel->getAllDetailedLogs();

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Sistem', 'url' => ''],
            ['title' => 'Detaylı Loglar', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Detailed Logs';
        $this->data['detailedLogs'] = $detailedLogs;

        $this->render('detailedlog/index', $this->data);
    }
}
?>
