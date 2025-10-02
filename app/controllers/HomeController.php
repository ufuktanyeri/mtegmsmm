<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/UserModel.php';
require_once APP_PATH . 'entities/Permission.php';
require_once APP_PATH . 'entities/Role.php';
require_once APP_PATH . 'entities/User.php';

class HomeController extends BaseController {
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/main');
            exit();
        }

        // Tema belirleme: session -> kullanıcı kaydı -> sistem algısı
        if (!isset($_SESSION['ui_theme'])) {
            require_once APP_PATH . 'models/UserModel.php';
            $um = new UserModel();
            $stored = $um->getUserTheme($_SESSION['user_id']);
            if ($stored) {
                $_SESSION['ui_theme'] = $stored;
            } else {
                // Basit otomatik algı (client JS ile override edilecek)
                $_SESSION['ui_theme'] = 'light';
            }
        }

        // Get permissions from session
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];

        $hasUsersManage = false;
        $hasAimsManage = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === 'users.manage') {
                    $hasUsersManage = true;
                    break;
                }
                if ($permission->getPermissionName() === 'aims.manage') {
                    $hasAimsManage = true;
                    break;
                }
            }
        }

        if ($hasAimsManage||$hasUsersManage) {
            // Breadcrumb data
            $this->data['breadcrumb'] = [['title' => 'Dashboard', 'url' => '']];

            $this->render('home/index', ['permissions' => $permissions,"hasAimsManage"=>$hasAimsManage, 'hasUsersManage'=>$hasUsersManage]);
        } else {
            header('Location: index.php?url=home/error');
            exit();
        }

        // Render the index page
       // $this->render('home/index', ['title' => 'Dashboard']);
    }

    public function error() {
        $this->render('home/error', ['title' => 'Error']);
    }

    public function smmnetwork() {
        // SMM network page can be viewed by anyone
        // Breadcrumb data
        $this->data['breadcrumb'] = [['title' => 'SMM Haritası', 'url' => '']];

        $this->render('home/smmnetwork', ['title' => 'SMM Haritası']);
    }
}
?>
