<?php

require_once 'BaseController.php';
require_once APP_PATH . 'models/CoveModel.php';
require_once APP_PATH . 'models/FieldModel.php';
require_once APP_PATH . 'entities/Cove.php';
require_once APP_PATH . 'validators/CoveValidator.php';
require_once APP_PATH . 'entities/Permission.php';
require_once APP_PATH . 'models/DetailedLogModel.php'; // Include DetailedLogModel

class CoveController extends BaseController {
    /** @var CoveModel */
    private $coveModel;
    /** @var FieldModel */
    private $fieldModel;
    /** @var DetailedLogModel */
    private $detailedLogModel;

    private const PERMISSION_MANAGE = 'coves.manage';

    public function __construct() {
        $this->coveModel = new CoveModel();
        $this->fieldModel = new FieldModel();
        $this->detailedLogModel = new DetailedLogModel();
    }

    /**
     * Basit izin kontrolü. Gerekirse BaseController'a taşınabilir.
     */
    protected function checkControllerPermission() {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission && $permission->getPermissionName() === self::PERMISSION_MANAGE) {
                return; // izin var
            }
        }
        header('Location: index.php?url=home/error');
        exit();
    }



    /**
     * Listeleme.
     */
    public function index($params = null) {
        $this->checkControllerPermission();
        $coves = $this->coveModel->getAllCoves();

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Sistem Yönetimi', 'url' => ''],
            ['title' => 'SMM Merkezleri', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['title'] = 'Coves';
        $this->data['coves'] = $coves;

        $this->render('cove/index', $this->data);
    }

    /**
     * admin menüsündeki cove/manage linkini karşılar. index'e yönlendirir.
     */
    public function manage($params = null) {
        return $this->index($params);
    }

    public function create() {
        $this->checkControllerPermission();
        $fields = $this->fieldModel->getAllFields();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($csrfToken)) {
                $error = 'Geçersiz CSRF token';
                $this->render('cove/create', ['title' => 'SMM Ekle', 'fields' => $fields, 'csrfToken' => $this->generateCsrfToken(), 'error' => $error]);
                return;
            }

            $validator = new CoveValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('cove/create', ['title' => 'SMM Ekle', 'fields' => $fields, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            require_once INCLUDES_PATH . 'Sanitizer.php';
            $name = Sanitizer::text('name', 150);
            $city = Sanitizer::text('city', 100);
            $district = Sanitizer::text('district', 100);
            $address = Sanitizer::text('address', 255, true);
            $selectedFields = isset($_POST['fields']) ? $_POST['fields'] : [];

            if ($name && $city && $district && $address) {
                $coveId = $this->coveModel->createCove($name, $city, $district, $address);

                // Update Cove_Fields relationship
                $this->coveModel->updateCoveFields($coveId, $selectedFields);

                header('Location: index.php?url=cove/index');
                return;
            } else {
                $error = 'Tüm alanlar gerekli';
                $this->render('cove/create', ['title' => 'SMM Ekle', 'fields' => $fields, 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            $this->render('cove/create', ['title' => 'SMM Ekle', 'fields' => $fields, 'csrfToken' => $this->generateCsrfToken()]);
        }
    }

    public function edit($params) {
        $this->checkControllerPermission();
        $id = htmlspecialchars($params['id']);
        $cove = $this->coveModel->getCoveById($id);
        if (!$cove) {
            header('Location: index.php?url=cove/index&error=Kayit+bulunamadi');
            return;
        }
        $fields = $this->fieldModel->getAllFields();
        $selectedFields = $this->coveModel->getFieldsByCoveId($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($csrfToken)) {
                $error = 'Geçersiz CSRF token';
                $this->render('cove/edit', ['title' => 'SMM Güncelle', 'cove' => $cove, 'fields' => $fields, 'selectedFields' => $selectedFields, 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            $validator = new CoveValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('cove/edit', ['title' => 'SMM Güncelle', 'cove' => $cove, 'fields' => $fields, 'selectedFields' => $selectedFields, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            require_once INCLUDES_PATH . 'Sanitizer.php';
            $name = Sanitizer::text('name', 150);
            $city = Sanitizer::text('city', 100);
            $district = Sanitizer::text('district', 100);
            $address = Sanitizer::text('address', 255, true);
            $selectedFields = isset($_POST['fields']) ? $_POST['fields'] : [];

            if ($name && $city && $district && $address) {
                $this->coveModel->updateCove($id, $name, $city, $district, $address);

                // Update Cove_Fields relationship
                $this->coveModel->updateCoveFields($id, $selectedFields);

                // Log
                $this->detailedLogModel->createDetailedLog($_SESSION['user_id'], 'coveedit', 'SMM', $name, $_SERVER['REMOTE_ADDR']);

                header('Location: index.php?url=cove/index');
                return;
            } else {
                $error = 'Tüm alanlar gerekli';
                $this->render('cove/edit', ['title' => 'SMM Güncelle', 'cove' => $cove, 'fields' => $fields, 'selectedFields' => $selectedFields, 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            $this->render('cove/edit', ['title' => 'SMM Güncelle', 'cove' => $cove, 'fields' => $fields, 'selectedFields' => $selectedFields, 'csrfToken' => $this->generateCsrfToken()]);
        }
    }

    public function delete($params) {
        $this->checkControllerPermission();
        $id = htmlspecialchars($params['id']);
        $cove = $this->coveModel->getCoveById($id);
        if ($cove) {
            $this->coveModel->deleteCove($id);
            $name = method_exists($cove, 'getCoveName') ? $cove->getCoveName() : 'SMM';
            $this->detailedLogModel->createDetailedLog($_SESSION['user_id'], 'covedelete', 'SMM', $name, $_SERVER['REMOTE_ADDR']);
        }
        header('Location: index.php?url=cove/index');
    }
}
?>

