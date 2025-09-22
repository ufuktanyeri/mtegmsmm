<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Controllers\ActionController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/ActionModel.php';
require_once __DIR__ . '/../models/CoveModel.php';
require_once __DIR__ . '/../models/ObjectiveModel.php';
require_once __DIR__ . '/../models/AimModel.php';
require_once __DIR__ . '/../validators/ActionValidator.php';
require_once __DIR__ . '/../entities/Permission.php';
require_once __DIR__ . '/../models/DetailedLogModel.php';

class ActionController extends BaseController {
    private $model;
    private $objectiveModel;
    private $coveModel;
    private $aimModel;

    public function __construct() {
        $this->model = new ActionModel();
        $this->objectiveModel = new ObjectiveModel();
        $this->coveModel = new CoveModel();
        $this->aimModel = new AimModel();
    }

    protected function render(string $view, array $data = [], array $options = []): void {
        extract($data);
        include __DIR__ . "/../views/{$view}.php";
    }

    protected function checkControllerPermission($permission = null) {
        // Session başlatılmış mı kontrol et
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Login kontrol
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=auth/login');
            exit();
        }
        
        // Admin yetkisi gerekiyorsa
        if ($permission === 'users.manage') {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                header('Location: index.php?url=action/calendar');
                exit();
            }
        }
    }

    public function index($params) {
        $this->checkControllerPermission();
        $aimId = isset($params['aimid']) ? (int)htmlspecialchars($params['aimid']) : null;
        $objectiveId = isset($params['objectiveId']) ? (int)htmlspecialchars($params['objectiveId']) : null;
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        $actions = $this->model->getActionsByCoveIdByAimId($aimId, $coveId, $objectiveId);
        $objectives = $this->objectiveModel->getObjectivesByAimIdAndCoveId($aimId, $coveId);
        $aim = $this->aimModel->getAimByIdAndCoveId($aimId, $coveId);
        
        $this->render('action/index', [
            'actions' => $actions,
            'aimid' => $aimId,
            'aimId' => $aimId,
            'objectiveId' => $objectiveId,
            'objectives' => $objectives,
            'aim' => $aim,
            'error' => ''
        ]);
    }

    public function create($params) {
        $this->checkControllerPermission();
        $aimId = (int)htmlspecialchars($params['aimid']);
        $objectiveId = isset($params['objectiveId']) ? (int)htmlspecialchars($params['objectiveId']) : null;
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'actionTitle' => htmlspecialchars($_POST['actionTitle']),
                'actionDesc' => htmlspecialchars($_POST['actionDesc']),
                'actionResponsible' => htmlspecialchars($_POST['actionResponsible']),
                'actionStatus' => (int)htmlspecialchars($_POST['actionStatus']),
                'dateStart' => htmlspecialchars($_POST['dateStart']),
                'dateEnd' => htmlspecialchars($_POST['dateEnd']),
                'periodic' => htmlspecialchars($_POST['periodic']),
                'periodType' => htmlspecialchars($_POST['periodType']),
                'periodTime' => htmlspecialchars($_POST['periodTime']),
                'objectiveId' => (int)htmlspecialchars($_POST['objectiveId']),
                'coveId' => $coveId
            ];
            
            $this->model->createAction($data);
            header('Location: index.php?url=action/index&aimid=' . $aimId . ($objectiveId ? '&objectiveId=' . $objectiveId : ''));
            exit();
        }
        
        $objectives = $this->objectiveModel->getObjectivesByAimIdAndCoveId($aimId, $coveId);
        $aim = $this->aimModel->getAimByIdAndCoveId($aimId, $coveId);
        
        $this->render('action/create', [
            'aimId' => $aimId,
            'objectiveId' => $objectiveId,
            'objectives' => $objectives,
            'aim' => $aim
        ]);
    }

    public function edit($params) {
        $this->checkControllerPermission();
        $id = (int)htmlspecialchars($params['id']);
        $objectiveId = isset($params['objectiveId']) ? (int)htmlspecialchars($params['objectiveId']) : null;
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        $action = $this->model->getActionByIdAndCoveId($id, $coveId);
        if (!$action) {
            header('Location: index.php?url=home/error&error=Faaliyet bulunamadı');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'actionTitle' => htmlspecialchars($_POST['actionTitle']),
                'actionDesc' => htmlspecialchars($_POST['actionDesc']),
                'actionResponsible' => htmlspecialchars($_POST['actionResponsible']),
                'actionStatus' => (int)htmlspecialchars($_POST['actionStatus']),
                'dateStart' => htmlspecialchars($_POST['dateStart']),
                'dateEnd' => htmlspecialchars($_POST['dateEnd']),
                'periodic' => htmlspecialchars($_POST['periodic']),
                'periodType' => htmlspecialchars($_POST['periodType']),
                'periodTime' => htmlspecialchars($_POST['periodTime']),
                'objectiveId' => (int)htmlspecialchars($_POST['objectiveId']),
                'coveId' => $coveId
            ];
            
            $this->model->updateAction($id, $data);
            
            if ($objectiveId) {
                $objective = $this->objectiveModel->getObjectiveByIdAndCoveId($objectiveId, $coveId);
                $aimId = $objective ? $objective->aimId : 1;
                header('Location: index.php?url=action/index&aimid=' . $aimId . '&objectiveId=' . $objectiveId);
            } else {
                header('Location: index.php?url=action/calendar');
            }
            exit();
        }
        
    $objective = $this->objectiveModel->getObjectiveByIdAndCoveId($action->objectiveId, $coveId);
        $aimId = $objective ? $objective->aimId : 1;
        $objectives = $this->objectiveModel->getObjectivesByAimIdAndCoveId($aimId, $coveId);
        $aim = $this->aimModel->getAimByIdAndCoveId($aimId, $coveId);
        
        $this->render('action/edit', [
            'action' => $action,
            'aimId' => $aimId,
            'objectiveId' => $objectiveId,
            'objectives' => $objectives,
            'aim' => $aim
        ]);
    }

    public function delete($params) {
        $this->checkControllerPermission();
        $id = (int)htmlspecialchars($params['id']);
        $aimId = isset($params['aimid']) ? (int)htmlspecialchars($params['aimid']) : null;
        $objectiveId = isset($params['objectiveId']) ? (int)htmlspecialchars($params['objectiveId']) : null;
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        $this->model->deleteAction($id, $coveId);
        
        if ($aimId) {
            header('Location: index.php?url=action/index&aimid=' . $aimId . ($objectiveId ? '&objectiveId=' . $objectiveId : ''));
        } else {
            header('Location: index.php?url=action/calendar');
        }
        exit();
    }

    public function calendar() {
        $this->checkControllerPermission();
        
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $actions = $this->model->getAllActionsForCalendar();
            $overdueCount = $this->model->getAllOverdueTasksCount();
            $urgentCount = $this->model->getAllUrgentTasksCount();
            $upcomingCount = $this->model->getAllUpcomingTasksCount(7);
            $completedCount = $this->model->getAllCompletedTasksCount();
            
            $this->render('action/calendar', [
                'actions' => $actions,
                'overdueCount' => $overdueCount,
                'urgentCount' => $urgentCount,
                'upcomingCount' => $upcomingCount,
                'completedCount' => $completedCount,
                'isAdmin' => true
            ]);
            return;
        }
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        $actions = $this->model->getActionsForCalendar($coveId);
        $overdueCount = $this->model->getOverdueTasksCount($coveId);
        $urgentCount = $this->model->getUrgentTasksCount($coveId);
        $upcomingCount = $this->model->getUpcomingTasksCount($coveId, 7);
        $completedCount = $this->model->getCompletedTasksCount($coveId);
        
        $this->render('action/calendar', [
            'actions' => $actions,
            'overdueCount' => $overdueCount,
            'urgentCount' => $urgentCount,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
            'isAdmin' => false
        ]);
    }

    public function calendarAdmin() {
        $this->checkControllerPermission();
        // Eski kontrol sadece $_SESSION['user_role']==='admin' idi ve bazı ortamlarda user_role set edilmediği için
        // yetkili kullanıcılar (users.manage izni olan) calendar() sayfasına yönleniyordu. İzin tabanlı doğrulama ekleniyor.
        $hasUsersManage = false;
        if (isset($_SESSION['permissions'])) {
            $permList = array_map('unserialize', $_SESSION['permissions']);
            foreach($permList as $permObj) {
                if ($permObj instanceof Permission && $permObj->getPermissionName() === 'users.manage') { $hasUsersManage = true; break; }
            }
        }
        $isAdminRole = isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === 'admin';
        if (!$hasUsersManage && !$isAdminRole) {
            header('Location: index.php?url=action/calendar');
            exit();
        }
        
        $selectedCoveId = 0;
        $actions = [];
        
        if (isset($_GET['coveId'])) {
            $selectedCoveId = (int)htmlspecialchars($_GET['coveId']);
        }
        
        if ($selectedCoveId > 0) {
            $actions = $this->model->getActionsForCalendarWithCoveInfo($selectedCoveId);
            $overdueCount = $this->model->getOverdueTasksCount($selectedCoveId);
            $urgentCount = $this->model->getUrgentTasksCount($selectedCoveId);
            $upcomingCount = $this->model->getUpcomingTasksCount($selectedCoveId, 7);
            $completedCount = $this->model->getCompletedTasksCount($selectedCoveId);
        } else {
            $actions = $this->model->getAllActionsForCalendar();
            $overdueCount = $this->model->getAllOverdueTasksCount();
            $urgentCount = $this->model->getAllUrgentTasksCount();
            $upcomingCount = $this->model->getAllUpcomingTasksCount(7);
            $completedCount = $this->model->getAllCompletedTasksCount();
            // DEBUG: toplu sayılar beklenenden 0 ise merkez bazında ham toplama
            if(($overdueCount+$urgentCount+$upcomingCount+$completedCount) === 0) {
                if(!isset($this->coveModel)) { require_once __DIR__.'/../models/CoveModel.php'; $this->coveModel = new \CoveModel(); }
                $debugCoves = $this->coveModel->getAllCoves();
                $perCenter = [];
                foreach($debugCoves as $cv) {
                    $cid = is_object($cv)?($cv->getId()):($cv['id']??0);
                    if(!$cid) continue;
                    $perCenter[$cid] = [
                        'overdue'=>$this->model->getOverdueTasksCount($cid),
                        'urgent'=>$this->model->getUrgentTasksCount($cid),
                        'upcoming'=>$this->model->getUpcomingTasksCount($cid,7),
                        'completed'=>$this->model->getCompletedTasksCount($cid)
                    ];
                }
                // Tarayıcı konsolu için header ekle
                header('X-Debug-AdminCalendar: '.substr(json_encode($perCenter),0,950));
            }
        }
        
        $coves = $this->coveModel->getAllCoves();
        
    $this->render('action/calendarAdmin', [
            'actions' => $actions,
            'coves' => $coves,
            'selectedCoveId' => $selectedCoveId,
            'overdueCount' => $overdueCount,
            'urgentCount' => $urgentCount,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount
        ]);
    }

    public function taskList($params = []) {
        $this->checkControllerPermission();
        
        $type = isset($params['type']) ? htmlspecialchars($params['type']) : (isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'all');
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
    $actions = $this->model->getTasksByFilter($type, $coveId);

    // Global (tüm merkezler) istatistik istenirse aşağıdaki satırların başına yorum kaldırılabilir
    // $completedCount = $this->model->getAllCompletedTasksCount();
    // $overdueCount   = $this->model->getAllOverdueTasksCount();
    // $urgentCount    = $this->model->getAllUrgentTasksCount();
    // $upcomingCount  = $this->model->getAllUpcomingTasksCount(7);

    // Kullanıcı: sadece kendi merkezine ait sayılar
    $completedCount = $this->model->getCompletedTasksCount($coveId);
    $overdueCount   = $this->model->getOverdueTasksCount($coveId);
    $urgentCount    = $this->model->getUrgentTasksCount($coveId);
    $upcomingCount  = $this->model->getUpcomingTasksCount($coveId, 7);
    $allCount       = count($this->model->getTasksByFilter('all', $coveId));
        
        $title = '';
        switch($type) {
            case 'overdue':
                $title = 'Geciken Görevler';
                break;
            case 'urgent':
                $title = 'Acil Görevler (3 gün)';
                break;
            case 'upcoming':
                $title = 'Yaklaşan Görevler (7 gün)';
                break;
            case 'completed':
                $title = 'Tamamlanan Görevler';
                break;
            default:
                $title = 'Tüm Görevler';
        }
        
        $this->render('action/taskList', [
            'actions' => $actions,
            'title' => $title,
            'type' => $type,
            'completedCount' => $completedCount,
            'overdueCount' => $overdueCount,
            'urgentCount' => $urgentCount,
            'upcomingCount' => $upcomingCount,
            'allCount' => $allCount
        ]);
    }

    public function adminTaskList($params = []) {
        $this->checkControllerPermission();
        // Eski kontrol sadece admin role idi; calendarAdmin ile uyumlu hale getir (users.manage izni veya admin rolü)
        $hasUsersManage = false; $isAdminRole = isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === 'admin';
        if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) {
            foreach(array_map('unserialize', $_SESSION['permissions']) as $permObj) {
                if ($permObj instanceof Permission && $permObj->getPermissionName() === 'users.manage') { $hasUsersManage = true; break; }
            }
        }
        if (!$isAdminRole && !$hasUsersManage) {
            $type = isset($params['filter']) ? $params['filter'] : (isset($_GET['filter']) ? $_GET['filter'] : 'all');
            header('Location: index.php?url=action/taskList&type=' . urlencode($type));
            exit();
        }
        
        $filter = isset($params['filter']) ? htmlspecialchars($params['filter']) : (isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : 'all');
        $coveId = isset($_GET['coveId']) ? (int)htmlspecialchars($_GET['coveId']) : 0;
        
        $actions = $this->model->getAdminTasksByFilter($filter, $coveId > 0 ? $coveId : null);
        
        if ($coveId > 0) {
            $allCount = count($this->model->getAdminTasksByFilter('all', $coveId));
            $overdueCount = $this->model->getOverdueTasksCount($coveId);
            $urgentCount = $this->model->getUrgentTasksCount($coveId);
            $upcomingCount = $this->model->getUpcomingTasksCount($coveId, 7);
            $completedCount = $this->model->getCompletedTasksCount($coveId);
        } else {
            $allCount = count($this->model->getAdminTasksByFilter('all'));
            $overdueCount = $this->model->getAllOverdueTasksCount();
            $urgentCount = $this->model->getAllUrgentTasksCount();
            $upcomingCount = $this->model->getAllUpcomingTasksCount(7);
            $completedCount = $this->model->getAllCompletedTasksCount();
        }
        
        $title = '';
        switch($filter) {
            case 'overdue':
                $title = 'Geciken Görevler';
                break;
            case 'urgent':
                $title = 'Acil Görevler (3 gün)';
                break;
            case 'upcoming':
                $title = 'Yaklaşan Görevler (7 gün)';
                break;
            case 'completed':
                $title = 'Tamamlanan Görevler';
                break;
            default:
                $title = 'Tüm Görevler';
        }
        
        $coves = $this->coveModel->getAllCoves();
        
        $this->render('action/adminTaskList', [
            'actions' => $actions,
            'title' => $title,
            'filter' => $filter,
            'coves' => $coves,
            'selectedCoveId' => $coveId,
            'allCount' => $allCount,
            'overdueCount' => $overdueCount,
            'urgentCount' => $urgentCount,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount
        ]);
    }

    public function getActionsByCoveId() {
        $this->checkControllerPermission();
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            echo json_encode(['error' => 'Yetkisiz işlem']);
            return;
        }
        
        header('Content-Type: application/json');
        $actions = $this->model->getActionsByCoveIdJson($coveId);
        echo json_encode($actions);
    }

    public function getStatistics() {
        $this->checkControllerPermission();
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Yetkisiz işlem']);
            return;
        }
        
        $coveId = isset($_GET['coveId']) ? (int)$_GET['coveId'] : null;
        $statistics = $this->model->getTasksStatistics($coveId);
        
        header('Content-Type: application/json');
        echo json_encode($statistics);
    }

    public function updateStatus() {
        $this->checkControllerPermission();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $actionId = (int)($_POST['actionId'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        
        if (!$coveId) {
            echo json_encode(['success' => false, 'message' => 'Yetkisiz işlem']);
            return;
        }
        
        $action = $this->model->getActionByIdAndCoveId($actionId, $coveId);
        if (!$action) {
            echo json_encode(['success' => false, 'message' => 'Görev bulunamadı']);
            return;
        }
        
        $data = [
            'actionTitle' => $action->actionTitle,
            'actionDesc' => $action->actionDesc,
            'actionResponsible' => $action->actionResponsible,
            'actionStatus' => $status,
            'dateStart' => $action->dateStart,
            'dateEnd' => $action->dateEnd,
            'periodic' => $action->periodic,
            'periodType' => $action->periodType,
            'periodTime' => $action->periodTime,
            'objectiveId' => $action->objectiveId,
            'coveId' => $coveId
        ];
        
        $result = $this->model->updateAction($actionId, $data);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Görev durumu güncellendi' : 'Güncelleme başarısız'
        ]);
    }

    /**
     * ✅ UPLOAD SİSTEMİ - İYİLEŞTİRİLMİŞ
     */
    public function upload($params = [])
    {
        $this->checkControllerPermission();
        
        $userId = $_SESSION['user_id'];
        $userCoveId = $this->model->getCoveIdByUserId($userId);
        
        if (!$userCoveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        
        $success = '';
        $error = '';
        
        // POST işlemi varsa dosya yükleme
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->handleFileUpload($userCoveId);
            
            // AJAX isteği ise JSON döndür
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => $result['success'],
                        'fileName' => $result['fileName'] ?? ''
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => $result['error']
                    ]);
                }
                exit();
            }
            
            // Normal form submit
            $success = $result['success'];
            $error = $result['error'];
        }
        
        // View'i render et
        $this->render('action/upload', [
            'title' => 'Dosya Yükleme',
            'userCoveId' => $userCoveId,
            'success' => $success,
            'error' => $error
        ]);
    }
    
    /**
     * ✅ Dosya yükleme işlemi - İyileştirilmiş
     */
    private function handleFileUpload($userCoveId)
    {
        try {
            // CSRF token kontrolü
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    throw new Exception('Geçersiz CSRF token');
                }
            }
            
            // Dosya kontrolü
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                // Detaylı hata mesajları
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'Dosya boyutu çok büyük (php.ini sınırı)',
                    UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu form sınırını aşıyor',
                    UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
                    UPLOAD_ERR_NO_FILE => 'Dosya seçilmedi',
                    UPLOAD_ERR_NO_TMP_DIR => 'Geçici dizin bulunamadı',
                    UPLOAD_ERR_CANT_WRITE => 'Disk yazma hatası',
                    UPLOAD_ERR_EXTENSION => 'PHP eklentisi dosya yüklemeyi durdurdu'
                ];
                
                $errorCode = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
                $errorMessage = isset($errorMessages[$errorCode]) 
                    ? $errorMessages[$errorCode] 
                    : 'Bilinmeyen dosya yükleme hatası';
                    
                throw new Exception($errorMessage);
            }
            
            $uploadedFile = $_FILES['file'];
            $originalFileName = $uploadedFile['name'];
            $fileSize = $uploadedFile['size'];
            $fileTmpName = $uploadedFile['tmp_name'];
            $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            
            // ✅ DÜZELTME: Turkish characters için güvenli dosya adı
            $safeFileName = $this->createSafeFileName($originalFileName);
            
            // Dosya uzantısı kontrolü
            $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('İzin verilen dosya türleri: ' . implode(', ', $allowedExtensions));
            }
            
            // Dosya boyutu kontrolü (10MB)
            $maxFileSize = 10 * 1024 * 1024; // 10MB
            if ($fileSize > $maxFileSize) {
                throw new Exception('Dosya boyutu 10MB\'dan küçük olmalıdır');
            }
            
            // ✅ HEDEF (geri alındı): wwwroot/uploads/tasks klasörü
            $wwwrootDir = realpath(__DIR__ . '/../../wwwroot');
            if (!$wwwrootDir || !is_dir($wwwrootDir)) {
                throw new Exception('wwwroot bulunamadı: ' . __DIR__ . '/../../wwwroot');
            }

            $tasksDir = $wwwrootDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'tasks';

            if (!$this->createUploadDirectory($tasksDir)) {
                $diagnostic = $this->uploadDirDiagnostic($tasksDir, $tasksDir);
                throw new Exception('Upload dizini oluşturulamadı veya yazılamıyor: ' . $tasksDir . ' | ' . $diagnostic);
            }

            $uploadDir = $tasksDir; // Dosyalar uploads/tasks içine kaydedilecek

            // Debug için log (isterseniz yorum satırına alabilirsiniz)
            if (function_exists('error_log')) {
                @error_log('[UPLOAD] hedef dizin: ' . $uploadDir);
            }
            
            // ✅ DÜZELTME: Benzersiz ve güvenli dosya adı
            $uniqueFileName = date('Y-m-d_H-i-s') . '_' . uniqid() . '_' . $safeFileName;
            $targetFile = $uploadDir . DIRECTORY_SEPARATOR . $uniqueFileName;
            
            // ✅ DÜZELTME: Dosyayı taşı - hata kontrolü ile
            if (!move_uploaded_file($fileTmpName, $targetFile)) {
                throw new Exception('Dosya yükleme sırasında hata oluştu. Dizin izinlerini kontrol edin.');
            }
            
            // ✅ DÜZELTME: Dosya izinlerini ayarla
            chmod($targetFile, 0644);
            
            // Veritabanına kaydet
            $this->saveFileToDatabase($originalFileName, $uniqueFileName, $fileSize, $userCoveId);
            
            return [
                'success' => 'Dosya "' . htmlspecialchars($originalFileName) . '" başarıyla yüklendi.',
                'error' => '',
                'fileName' => $originalFileName
            ];
            
        } catch (Exception $e) {
            return [
                'success' => '',
                'error' => $e->getMessage(),
                'fileName' => ''
            ];
        }
    }
    
    /**
     * ✅ Turkish characters için güvenli dosya adı oluştur
     */
    private function createSafeFileName($fileName)
    {
        // Turkish characters mapping
        $turkishChars = [
            'ç' => 'c', 'Ç' => 'C',
            'ğ' => 'g', 'Ğ' => 'G', 
            'ı' => 'i', 'I' => 'I',
            'İ' => 'I', 'i' => 'i',
            'ö' => 'o', 'Ö' => 'O',
            'ş' => 's', 'Ş' => 'S',
            'ü' => 'u', 'Ü' => 'U'
        ];
        
        // Turkish karakterleri değiştir
        $safeFileName = strtr($fileName, $turkishChars);
        
        // Özel karakterleri temizle
        $safeFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $safeFileName);
        
        // Birden fazla alt çizgiyi tek yap
        $safeFileName = preg_replace('/_+/', '_', $safeFileName);
        
        // Baş ve sondaki alt çizgileri kaldır
        $safeFileName = trim($safeFileName, '_');
        
        return $safeFileName;
    }
    
    /**
     * ✅ Upload dizinini oluştur ve izinleri ayarla
     */
    private function createUploadDirectory($uploadDir)
    {
        try {
            $isWindows = strtoupper(substr(PHP_OS,0,3)) === 'WIN';

            $uploadsDir = dirname($uploadDir);
            if (!is_dir($uploadsDir)) {
                if (!@mkdir($uploadsDir, 0755, true)) {
                    return false;
                }
            }
            if (!is_dir($uploadDir)) {
                if (!@mkdir($uploadDir, 0755, true)) {
                    return false;
                }
            }

            // İzin hatalarını sessiz yut (paylaşımlı hosting'te başarısız olabilir)
            @chmod($uploadsDir, 0755);
            @chmod($uploadDir, 0755);

            // Windows IIS ortamında .htaccess gereksiz; yalnızca Apache + yazılabilir ise oluştur
            if (!$isWindows) {
                $htaccessFile = $uploadsDir . DIRECTORY_SEPARATOR . '.htaccess';
                if (!file_exists($htaccessFile) && is_writable($uploadsDir)) {
                    $htaccessContent = "# Upload security\nOptions -Indexes\nOptions -ExecCGI\n<Files ~ \"\\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|sh|cgi)$\">\n    deny from all\n</Files>\n";
                    @file_put_contents($htaccessFile, $htaccessContent);
                    @chmod($htaccessFile, 0644);
                }
            }

            return is_dir($uploadDir) && is_writable($uploadDir);
        } catch (Exception $e) {
            error_log('Upload directory creation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Upload dizini teşhis bilgisi
     */
    private function uploadDirDiagnostic($baseDir, $targetDir)
    {
        $parts = [];
        $check = function($path) {
            return '[dir='.(is_dir($path)?'1':'0').',w='.(is_writable($path)?'1':'0').',exists='.(file_exists($path)?'1':'0').']';
        };
        $parts[] = 'base(' . $baseDir . ') ' . $check($baseDir);
        $parts[] = 'target(' . $targetDir . ') ' . $check($targetDir);
        $parent = dirname($targetDir);
        $parts[] = 'parent(' . $parent . ') ' . $check($parent);
        return implode(' | ', $parts);
    }
    
    /**
     * ✅ Dosya bilgilerini veritabanına kaydet - İyileştirilmiş
     */
    private function saveFileToDatabase($originalName, $savedName, $fileSize, $coveId)
    {
        try {
            require_once __DIR__ . '/../../config/config.php';
            
            // PDO kullan (daha güvenli)
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            // uploads tablosu yoksa oluştur
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS uploads (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    original_name VARCHAR(255) NOT NULL,
                    saved_name VARCHAR(255) NOT NULL,
                    file_size INT NOT NULL,
                    cove_id INT NOT NULL,
                    uploaded_by INT NOT NULL,
                    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_cove_id (cove_id),
                    INDEX idx_uploaded_by (uploaded_by),
                    INDEX idx_upload_date (upload_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $pdo->exec($createTableSQL);
            
            // Dosya bilgilerini kaydet
            $stmt = $pdo->prepare("
                INSERT INTO uploads 
                (original_name, saved_name, file_size, cove_id, uploaded_by, upload_date) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $originalName,
                $savedName,
                $fileSize,
                $coveId,
                $_SESSION['user_id']
            ]);
            
            return true;
            
        } catch (Exception $e) {
            // Veritabanı hatası loglansın ama upload işlemini durdurmasın
            error_log("File database save error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ Dosya boyutunu okunabilir formata çevir
     */
    private function formatFileSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * ✅ Yüklenen dosyaları listele
     */
    public function listUploads($params = [])
    {
        $this->checkControllerPermission();
        
        try {
            require_once __DIR__ . '/../../config/config.php';
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            $userId = $_SESSION['user_id'];
            $userCoveId = $this->model->getCoveIdByUserId($userId);
            
            // uploads tablosunda cove_id veya coveId kolonlarını tespit et
            $columnName = 'cove_id';
            try {
                $colCheck = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='uploads' AND COLUMN_NAME=? LIMIT 1");
                $colCheck->execute([$columnName]);
                if (!$colCheck->fetch()) {
                    $altCheck = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='uploads' AND COLUMN_NAME='coveId' LIMIT 1");
                    $altCheck->execute();
                    if ($altCheck->fetch()) {
                        $columnName = 'coveId';
                    } else {
                        $columnName = null; // Kolon yoksa daha sonra bellekte filtrele
                    }
                }
            } catch (Exception $ignore) {
                // Sessiz devam
            }

            if ($columnName) {
                $sql = "SELECT up.*, us.username AS userName FROM uploads up LEFT JOIN users us ON up.uploaded_by = us.id WHERE up.".$columnName." = ? ORDER BY up.upload_date DESC LIMIT 50";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userCoveId]);
            } else {
                $sql = "SELECT up.*, us.username AS userName FROM uploads up LEFT JOIN users us ON up.uploaded_by = us.id ORDER BY up.upload_date DESC LIMIT 200";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
            $files = $stmt->fetchAll();
            if (!$columnName) {
                $files = array_values(array_filter($files, function($f) use ($userCoveId) {
                    if (isset($f['cove_id'])) return $f['cove_id'] == $userCoveId;
                    if (isset($f['coveId'])) return $f['coveId'] == $userCoveId;
                    return true; // kolon yok; limitli sonuçlar
                }));
            }
            // Dosya boyutlarını formatla
            foreach ($files as &$file) {
                $file['formatted_size'] = $this->formatFileSize($file['file_size']);
                $file['upload_date_formatted'] = date('d.m.Y H:i', strtotime($file['upload_date']));
            }
            
            $this->render('action/listUploads', [
                'title' => 'Yüklenen Dosyalar',
                'files' => $files
            ]);
            
        } catch (Exception $e) {
            header('Location: index.php?url=action/upload&error=' . urlencode($e->getMessage()));
        }
    }
    
    /**
     * ✅ Dosya indirme - Güvenli
     */
    public function download($params = [])
    {
        $this->checkControllerPermission();
        
        if (!isset($params['file'])) {
            header('Location: index.php?url=action/listUploads&error=' . urlencode('Dosya belirtilmedi'));
            exit();
        }
        
        $fileName = basename($params['file']); // Güvenlik için
        $uploadBaseDir = realpath(__DIR__ . '/../../wwwroot');
        if (!$uploadBaseDir) {
            $uploadBaseDir = realpath(__DIR__ . '/../..');
        }
        // Birincil dizin (geri dönüş): uploads/tasks
        $primaryDir = $uploadBaseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'tasks';
        $filePath = $primaryDir . DIRECTORY_SEPARATOR . $fileName;

        // Geri uyumluluk: önceki sürümde "distribution of tasks" kullanılmış olabilir
        if (!file_exists($filePath)) {
            $legacyDir = $uploadBaseDir . DIRECTORY_SEPARATOR . 'distribution of tasks';
            $legacyPath = $legacyDir . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($legacyPath)) {
                $filePath = $legacyPath;
            }
        }
        
        // Dosya var mı?
        if (!file_exists($filePath)) {
            header('Location: index.php?url=action/listUploads&error=' . urlencode('Dosya bulunamadı'));
            exit();
        }
        
        // Kullanıcının dosyaya erişim hakkı var mı?
        $userCoveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        if (!$this->checkFileAccess($fileName, $userCoveId)) {
            header('Location: index.php?url=action/listUploads&error=' . urlencode('Bu dosyaya erişim yetkiniz yok'));
            exit();
        }
        
        // Dosya bilgilerini al
    // Dinamik kolon (cove_id / coveId) tespiti ve yoksa filtreyi sonradan uygulama
        $fileSize = filesize($filePath);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // MIME type belirleme
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain'
        ];
        
        $mimeType = isset($mimeTypes[$fileExtension]) ? $mimeTypes[$fileExtension] : 'application/octet-stream';
        
        // Orijinal dosya adını al
        $originalName = $this->getOriginalFileName($fileName);
        
        // Headers ayarla
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $originalName . '"');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        // Dosyayı çıktıla
        readfile($filePath);
        exit();
    }
    
    /**
     * ✅ Dosyaya erişim kontrolü
     */
    private function checkFileAccess($fileName, $userCoveId)
    {
        try {
            require_once __DIR__ . '/../../config/config.php';
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM uploads 
                WHERE saved_name = ? AND cove_id = ?
            ");
            
            $stmt->execute([$fileName, $userCoveId]);
            return $stmt->fetchColumn() > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * ✅ Orijinal dosya adını getir
     */
    private function getOriginalFileName($savedName)
    {
        try {
            require_once __DIR__ . '/../../config/config.php';
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $stmt = $pdo->prepare("
                SELECT original_name 
                FROM uploads 
                WHERE saved_name = ?
            ");
            
            $stmt->execute([$savedName]);
            $result = $stmt->fetchColumn();
            
            return $result ?: $savedName;
            
        } catch (Exception $e) {
            return $savedName;
        }
    }
}
?>