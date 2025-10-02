<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/DocumentStrategyModel.php';
require_once APP_PATH . 'validators/DocumentStrategyValidator.php';
require_once APP_PATH . 'entities/Permission.php';

class DocumentStrategyController extends BaseController {
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
        $model = new DocumentStrategyModel();
        $strategies = $model->getAllDocumentStrategies();

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'İçerik Yönetimi', 'url' => ''],
            ['title' => 'Strateji Belgeleri', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Document Strategies';
        $this->data['strategies'] = $strategies;

        $this->render('documentStrategy/index', $this->data);
    }

    public function create() {
        $this->checkControllerPermission();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new DocumentStrategyValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('documentStrategy/create', ['title' => 'Create Document Strategy', 'errors' => $errors]);
                return;
            }

            $strategyDesc = filter_input(INPUT_POST, 'strategyDesc', FILTER_SANITIZE_STRING);
            $strategyNo = filter_input(INPUT_POST, 'strategyNo', FILTER_SANITIZE_STRING);

            if ($strategyDesc && $strategyNo) {
                $model = new DocumentStrategyModel();
                $model->createDocumentStrategy($strategyDesc, $strategyNo);
                header('Location: index.php?url=documentStrategy/index');
                exit();
            } else {
                $error = 'All fields are required';
                $this->render('documentStrategy/create', ['title' => 'Create Document Strategy', 'error' => $error]);
            }
        } else {
            $this->render('documentStrategy/create', ['title' => 'Create Document Strategy']);
        }
    }

    public function edit($params) {
        $this->checkControllerPermission();
        $model = new DocumentStrategyModel();
        $id = $params['id'];
        $strategy = $model->getDocumentStrategyById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new DocumentStrategyValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('documentStrategy/edit', ['title' => 'Edit Document Strategy', 'strategy' => $strategy, 'errors' => $errors]);
                return;
            }

            $strategyDesc = filter_input(INPUT_POST, 'strategyDesc', FILTER_SANITIZE_STRING);
            $strategyNo = filter_input(INPUT_POST, 'strategyNo', FILTER_SANITIZE_STRING);

            if ($strategyDesc && $strategyNo) {
                $model->updateDocumentStrategy($id, $strategyDesc, $strategyNo);
                header('Location: index.php?url=documentStrategy/index');
                exit();
            } else {
                $error = 'All fields are required';
                $this->render('documentStrategy/edit', ['title' => 'Edit Document Strategy', 'strategy' => $strategy, 'error' => $error]);
            }
        } else {
            $this->render('documentStrategy/edit', ['title' => 'Edit Document Strategy', 'strategy' => $strategy]);
        }
    }

    public function delete($params) {
        $this->checkControllerPermission();
        $model = new DocumentStrategyModel();
        $id = $params['id'];
        $model->deleteDocumentStrategy($id);
        header('Location: index.php?url=documentStrategy/index');
        exit();
    }
}
?>
