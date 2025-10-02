<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/FieldModel.php';
require_once APP_PATH . 'entities/Field.php';
require_once APP_PATH . 'validators/FieldValidator.php';
require_once APP_PATH . 'entities/Permission.php';

class FieldController extends BaseController {
    protected function checkControllerPermission() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === 'coves.manage') {
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
        $fieldModel = new FieldModel();
        $fields = $fieldModel->getAllFields();

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Sistem Yönetimi', 'url' => ''],
            ['title' => 'SMM Alanları', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Fields';
        $this->data['fields'] = $fields;

        $this->render('field/index', $this->data);
    }

    public function create() {
        $this->checkControllerPermission();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('field/create', ['title' => 'Alan Ekle', 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            $validator = new FieldValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('field/create', ['title' => 'Alan Ekle', 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

            if ($name) {
                $fieldModel = new FieldModel();
                $fieldModel->createField($name);
                header('Location: index.php?url=field/index');
                exit();
            } else {
                $error = 'İsim gerekli';
                $this->render('field/create', ['title' => 'Alan Ekle', 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('field/create', ['title' => 'Alan Ekle', 'csrfToken' => $csrfToken]);
        }
    }

    public function edit($params) {
        $this->checkControllerPermission();
        $fieldModel = new FieldModel();
        $id= htmlspecialchars($params['id']);
        $field = $fieldModel->getFieldById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('field/edit', ['title' => 'Alan Düzenle', 'field' => $field, 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            $validator = new FieldValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('field/edit', ['title' => 'Alan Düzenle', 'field' => $field, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

            if ($name) {
                $fieldModel->updateField($id, $name);
                header('Location: index.php?url=field/index');
                exit();
            } else {
                $error = 'İsim gerekli';
                $this->render('field/edit', ['title' => 'Alan Düzenle', 'field' => $field, 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('field/edit', ['title' => 'Alan Düzenle', 'field' => $field, 'csrfToken' => $csrfToken]);
        }
    }

    public function delete($params) {
        $this->checkControllerPermission();
        $id= htmlspecialchars($params['id']);
        $fieldModel = new FieldModel();
        $fieldModel->deleteField($id);
        header('Location: index.php?url=field/index');
        exit();
    }
}
?>
