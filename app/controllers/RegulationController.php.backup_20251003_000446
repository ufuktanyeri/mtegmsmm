<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/RegulationModel.php';
require_once __DIR__ . '/../validators/RegulationValidator.php';
require_once __DIR__ . '/../entities/Permission.php';

class RegulationController extends BaseController {
    protected function checkControllerPermission() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === 'regulations.manage') {
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

    private function checkIsAdmin() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === 'users.manage') {
                    return true;
                    break;
                }
            }
        }
        return false;     
    }

    public function index($params) {
        $this->checkControllerPermission();
        $model = new RegulationModel();
        $isAdmin = $this->checkIsAdmin();
        $userId = $_SESSION['user_id'];
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);
        $regulations = $isAdmin ? $model->getAllRegulations() : $model->getRegulationsByCoveId($userCoveId);
        $error = isset($params['error']) ? $params['error'] : "";

        // Breadcrumb data
        $this->data["breadcrumb"] = [
            ["title" => "İçerik Yönetimi", "url" => ""],
            ["title" => "Mevzuat", "url" => ""]
        ];

        // Add other data to $this->data
        $this->data["title"] = "Regülasyonlar";
        $this->data["regulations"] = $regulations;
        $this->data["error"] = $error;
        $this->data["userCoveId"] = $userCoveId;
        $this->data["isAdmin"] = $isAdmin;

        $this->render("regulation/index", $this->data);
    }

    public function create() {
        $this->checkControllerPermission();
        $model = new RegulationModel();
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('regulation/create', ['title' => 'Regülasyon Ekle', 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            $validator = new RegulationValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('regulation/create', ['title' => 'Regülasyon Ekle', 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            $regulationDesc = filter_input(INPUT_POST, 'regulationDesc', FILTER_SANITIZE_STRING);
            $regulationSource = filter_input(INPUT_POST, 'regulationSource', FILTER_SANITIZE_STRING);
            $regulationSourceNo = filter_input(INPUT_POST, 'regulationSourceNo', FILTER_SANITIZE_STRING);
            $ownerCoveId =  $this->checkIsAdmin()? 0 : $userCoveId;

            if ($regulationDesc && $regulationSource && $regulationSourceNo) {
                $model = new RegulationModel();
                $model->createRegulation($regulationDesc, $regulationSource, $regulationSourceNo, $ownerCoveId);
                header('Location: index.php?url=regulation/index');
                exit();
            } else {
                $error = 'Tüm alanlar gerekli';
                $this->render('regulation/create', ['title' => 'Regülasyon Ekle', 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('regulation/create', ['title' => 'Regülasyon Ekle', 'csrfToken' => $csrfToken]);
        }
    }

    public function edit($params) {
        $this->checkControllerPermission();
        $model = new RegulationModel();
        $id = htmlspecialchars($params['id']);
        $userId = $_SESSION['user_id'];
        $isAdmin = $this->checkIsAdmin();
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);
        $regulation = $isAdmin ? $model->getRegulationById($id) : $model->getRegulationByIdByCove($id, $userCoveId);

        if (!$regulation) {
            header('Location: index.php?url=regulation/index&error=Yetkisiz işlem denediniz!');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('regulation/edit', ['title' => 'Regülasyon Düzenle', 'regulation' => $regulation, 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            if ($isAdmin || ($regulation && $regulation->getOwnerCoveId() == $userCoveId)) {
                $validator = new RegulationValidator($_POST);
                $errors = $validator->validateForm();
                if (!empty($errors)) {
                    $this->render('regulation/edit', ['title' => 'Regülasyon Düzenle', 'regulation' => $regulation, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                    return;
                }

                $regulationDesc = filter_input(INPUT_POST, 'regulationDesc', FILTER_SANITIZE_STRING);
                $regulationSource = filter_input(INPUT_POST, 'regulationSource', FILTER_SANITIZE_STRING);
                $regulationSourceNo = filter_input(INPUT_POST, 'regulationSourceNo', FILTER_SANITIZE_STRING);
                $ownerCoveId =  $this->checkIsAdmin()? 0 : $userCoveId;

                if ($regulationDesc && $regulationSource && $regulationSourceNo) {
                    $model->updateRegulation($id, $regulationDesc, $regulationSource, $regulationSourceNo, $ownerCoveId);
                    header('Location: index.php?url=regulation/index');
                    exit();
                } else {
                    $error = 'Tüm alanlar gerekli';
                    $this->render('regulation/edit', ['title' => 'Regülasyon Düzenle', 'regulation' => $regulation, 'csrfToken' => $csrfToken, 'error' => $error]);
                }
            } else {
                header('Location: index.php?url=regulation/index&error=1');
                exit();
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('regulation/edit', ['title' => 'Regülasyon Düzenle', 'regulation' => $regulation, 'csrfToken' => $csrfToken]);
        }
    }

    public function delete($params) {
        $this->checkControllerPermission();
        $model = new RegulationModel();
        $id = htmlspecialchars($params['id']);
        $regulation = $model->getRegulationById($id);
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);

        if ($regulation && $regulation->getOwnerCoveId() == $userCoveId || $this->checkIsAdmin()) {
            $model->deleteRegulation($id);
            header('Location: index.php?url=regulation/index');
        } else {
            
            header('Location: index.php?url=regulation/index&error=Yetkiniz yok');
        }
        exit();
    }
}
?>
