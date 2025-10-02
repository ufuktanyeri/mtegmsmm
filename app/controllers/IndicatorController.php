<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Controllers\IndicatorController.php

require_once 'BaseController.php';
require_once APP_PATH . 'models/IndicatorModel.php';
require_once APP_PATH . 'models/CoveModel.php';
require_once APP_PATH . 'models/AimModel.php';
require_once APP_PATH . 'models/ObjectiveModel.php';
require_once APP_PATH . 'models/FieldModel.php';
require_once APP_PATH . 'models/IndicatorTypeModel.php';
require_once APP_PATH . 'entities/Indicator.php';

class IndicatorController extends BaseController
{
    private $model;
    private $coveModel;
    private $aimModel;
    private $objectiveModel;
    private $fieldModel;
    private $indicatorTypeModel;

    public function __construct()
    {
        $this->model = new IndicatorModel();
        $this->coveModel = new CoveModel();
        $this->aimModel = new AimModel();
        $this->objectiveModel = new ObjectiveModel();
        $this->fieldModel = new FieldModel();
        $this->indicatorTypeModel = new IndicatorTypeModel();
    }

    protected function checkControllerPermission($permission = null)
    {
        // Session başlatılmış mı kontrol et
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Login kontrol
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/login');
            exit();
        }

        // Admin yetkisi gerekiyorsa
        if ($permission === 'users.manage') {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                header('Location: index.php?url=indicator/index');
                exit();
            }
        }
    }

    protected function checkSuperadminPermission()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/login');
            exit();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
            header('Location: index.php?url=home/error&error=Bu işlem için yetkiniz bulunmamaktadır');
            exit();
        }
    }

    /**
     * ✅ Index sayfası - Tüm göstergeler
     */
    public function index($params = [])
    {
        $this->checkControllerPermission();

        // ✅ DÜZELTME: Yetki kontrolünü kaldır veya düzelt
        $userId = $_SESSION['user_id'];
        // Opsiyonel aim parametresi (eski sekmeli yapı ile uyum için)
        $aimIdParam = null;
        if (isset($params['aimid'])) {
            $aimIdParam = (int)$params['aimid'];
        } elseif (isset($_GET['aimid'])) {
            $aimIdParam = (int)$_GET['aimid'];
        }

        // Superadmin ve Admin kontrolü
        $isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        
        if ($isSuperAdmin || $isAdmin) {
            // Superadmin veya Admin ise tüm indicators'ı ve amaç/hedef ilişkileri için tüm objective'leri göster
            $indicators  = $this->model->getAllIndicators();
            $coves       = $this->coveModel->getAllCoves();
            $objectives  = method_exists($this->objectiveModel, 'getAllObjectives') ? $this->objectiveModel->getAllObjectives() : [];

            // Breadcrumb data
            $this->data['breadcrumb'] = [
                ['title' => 'Stratejik Yönetim', 'url' => ''],
                ['title' => 'Performans Göstergeleri', 'url' => '']
            ];

            // Add other data to $this->data
            $this->data['indicators'] = $indicators;
            $this->data['coves'] = $coves;
            $this->data['objectives'] = $objectives;
            $this->data['isAdmin'] = true;
            $this->data['isSuperAdmin'] = $isSuperAdmin;
            $this->data['aimid'] = $aimIdParam;
            $this->data['title'] = 'Performans Göstergeleri' . ($isSuperAdmin ? ' (SuperAdmin)' : ' (Admin)');
            $this->data['error'] = '';

            $this->render('indicator/index', $this->data);
            return;
        }

        // Normal kullanıcı için kendi merkezi
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId) {
            // ✅ DÜZELTME: Yetki hatası yerine boş liste göster
            // Breadcrumb data
            $this->data['breadcrumb'] = [
                ['title' => 'Stratejik Yönetim', 'url' => ''],
                ['title' => 'Performans Göstergeleri', 'url' => '']
            ];

            // Add other data to $this->data
            $this->data['indicators'] = [];
            $this->data['coves'] = [];
            $this->data['objectives'] = [];
            $this->data['isAdmin'] = false;
            $this->data['aimid'] = $aimIdParam;
            $this->data['title'] = 'Performans Göstergeleri';
            $this->data['error'] = 'Henüz bir merkeze atanmamışsınız. Lütfen yöneticinizle iletişime geçin.';

            $this->render('indicator/index', $this->data);
            return;
        }

        $indicators = $this->model->getIndicatorsByCoveId($coveId);
        $objectives = method_exists($this->objectiveModel, 'getObjectivesByCoveId') ? $this->objectiveModel->getObjectivesByCoveId($coveId) : [];

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Stratejik Yönetim', 'url' => ''],
            ['title' => 'Performans Göstergeleri', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['indicators'] = $indicators;
        $this->data['coveId'] = $coveId;
        $this->data['objectives'] = $objectives;
        $this->data['isAdmin'] = false;
        $this->data['aimid'] = $aimIdParam;
        $this->data['title'] = 'Performans Göstergeleri';

        $this->render('indicator/index', $this->data);
    }

    /**
     * ✅ Yeni gösterge ekleme sayfası
     */
    public function create($params = [])
    {
        $this->checkControllerPermission();
        
        // Sadece superadmin create/edit/delete işlemleri yapabilir
        $isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
        if (!$isSuperAdmin) {
            header('Location: index.php?url=home/error&error=Bu işlem için yetkiniz bulunmamaktadır');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);
        // Opsiyonel aim id
        $aimIdParam = null;
        if (isset($params['aimid'])) {
            $aimIdParam = (int)$params['aimid'];
        } elseif (isset($_GET['aimid'])) {
            $aimIdParam = (int)$_GET['aimid'];
        }

        // Superadmin için coveId kontrolünü kaldır - tüm merkezlerle çalışabilir
        if (!$isSuperAdmin && !$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        // CSRF token hazırlama
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        $csrfToken = $_SESSION['csrf_token'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF doğrulama
            $postedToken = $_POST['csrf_token'] ?? '';
            if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
                $this->render('indicator/create', [
                    'objectives' => $this->objectiveModel->getObjectivesByCoveId($coveId),
                    'fields' => $this->fieldModel->getFieldsByCoveId($coveId),
                    'indicatorTypes' => $this->indicatorTypeModel->getAllIndicatorTypes(),
                    'coveId' => $coveId,
                    'aimid' => $aimIdParam,
                    'csrfToken' => $csrfToken,
                    'errors' => ['Geçersiz güvenlik anahtarı (CSRF). Lütfen formu tekrar gönderin.'],
                    'title' => 'Yeni Performans Göstergesi'
                ]);
                return;
            }
            $data = [
                'indicatorTitle' => htmlspecialchars($_POST['indicatorTitle'] ?? ''),
                'indicatorDesc' => htmlspecialchars($_POST['indicatorDesc'] ?? ''),
                // Form alan isimleri target/completed -> model targetValue/currentValue
                'targetValue' => (float)($_POST['target'] ?? 0),
                'currentValue' => (float)($_POST['completed'] ?? 0),
                'indicatorStatus' => (int)($_POST['indicatorStatus'] ?? 0),
                'unit' => htmlspecialchars($_POST['unit'] ?? ''),
                'measurementPeriod' => htmlspecialchars($_POST['measurementPeriod'] ?? ''),
                'objectiveId' => (int)($_POST['objectiveId'] ?? 0),
                'indicatorTypeId' => (int)($_POST['indicatorTypeId'] ?? 0),
                'fieldId' => (int)($_POST['fieldId'] ?? 0),
                'coveId' => $coveId
            ];

            $this->model->createIndicator($data);
            header('Location: index.php?url=indicator/index');
            exit();
        }

        // Superadmin için tüm objectives ve fields'ları getir, normal kullanıcı için sadece kendi merkezininkiler
        if ($isSuperAdmin) {
            $objectives = method_exists($this->objectiveModel, 'getAllObjectives') ? $this->objectiveModel->getAllObjectives() : [];
            $fields = method_exists($this->fieldModel, 'getAllFields') ? $this->fieldModel->getAllFields() : $this->fieldModel->getFieldsByCoveId($coveId);
            $coves = $this->coveModel->getAllCoves();
        } else {
            $objectives = $this->objectiveModel->getObjectivesByCoveId($coveId);
            $fields = $this->fieldModel->getFieldsByCoveId($coveId);
            $coves = [];
        }
        $indicatorTypes = $this->indicatorTypeModel->getAllIndicatorTypes();

        $this->render('indicator/create', [
            'objectives' => $objectives,
            'fields' => $fields,
            'indicatorTypes' => $indicatorTypes,
            'coves' => $coves,
            'coveId' => $coveId,
            'aimid' => $aimIdParam,
            'csrfToken' => $csrfToken,
            'isSuperAdmin' => $isSuperAdmin,
            'title' => 'Yeni Performans Göstergesi'
        ]);
    }

    /**
     * ✅ Gösterge düzenleme sayfası
     */
    public function edit($params)
    {
        $this->checkControllerPermission();
        
        // Sadece superadmin edit işlemi yapabilir
        $isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
        if (!$isSuperAdmin) {
            header('Location: index.php?url=home/error&error=Bu işlem için yetkiniz bulunmamaktadır');
            exit();
        }

        $id = (int)htmlspecialchars($params['id']);
        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);
        $aimIdParam = null;
        if (isset($params['aimid'])) {
            $aimIdParam = (int)$params['aimid'];
        } elseif (isset($_GET['aimid'])) {
            $aimIdParam = (int)$_GET['aimid'];
        }

        // Superadmin tüm göstergeleri düzenleyebilir
        if ($isSuperAdmin) {
            $indicator = $this->model->getIndicatorById($id);
        } else {
            if (!$coveId) {
                header('Location: index.php?url=home/error&error=Yetkisiz işlem');
                exit();
            }
            $indicator = $this->model->getIndicatorByIdAndCoveId($id, $coveId);
        }
        
        if (!$indicator) {
            header('Location: index.php?url=home/error&error=Gösterge bulunamadı');
            exit();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        $csrfToken = $_SESSION['csrf_token'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postedToken = $_POST['csrf_token'] ?? '';
            if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
                $objectives = $this->objectiveModel->getObjectivesByCoveId($coveId);
                $fields = $this->fieldModel->getFieldsByCoveId($coveId);
                $indicatorTypes = $this->indicatorTypeModel->getAllIndicatorTypes();
                $this->render('indicator/edit', [
                    'indicator' => $indicator,
                    'objectives' => $objectives,
                    'fields' => $fields,
                    'indicatorTypes' => $indicatorTypes,
                    'coveId' => $coveId,
                    'aimid' => $aimIdParam,
                    'csrfToken' => $csrfToken,
                    'errors' => ['Geçersiz güvenlik anahtarı (CSRF). Lütfen tekrar deneyin.'],
                    'title' => 'Performans Göstergesi Düzenle'
                ]);
                return;
            }
            $data = [
                'indicatorTitle' => htmlspecialchars($_POST['indicatorTitle'] ?? ''),
                'indicatorDesc' => htmlspecialchars($_POST['indicatorDesc'] ?? ''),
                'targetValue' => (float)($_POST['target'] ?? 0),
                'currentValue' => (float)($_POST['completed'] ?? 0),
                'indicatorStatus' => (int)($_POST['indicatorStatus'] ?? 0),
                'unit' => htmlspecialchars($_POST['unit'] ?? ''),
                'measurementPeriod' => htmlspecialchars($_POST['measurementPeriod'] ?? ''),
                'objectiveId' => (int)($_POST['objectiveId'] ?? 0),
                'indicatorTypeId' => (int)($_POST['indicatorTypeId'] ?? 0),
                'fieldId' => (int)($_POST['fieldId'] ?? 0),
                'coveId' => $coveId
            ];

            $this->model->updateIndicator($id, $data);
            header('Location: index.php?url=indicator/index');
            exit();
        }

        // Superadmin için tüm objectives ve fields'ları getir
        if ($isSuperAdmin) {
            $objectives = method_exists($this->objectiveModel, 'getAllObjectives') ? $this->objectiveModel->getAllObjectives() : [];
            $fields = method_exists($this->fieldModel, 'getAllFields') ? $this->fieldModel->getAllFields() : $this->fieldModel->getFieldsByCoveId($coveId);
            $coves = $this->coveModel->getAllCoves();
        } else {
            $objectives = $this->objectiveModel->getObjectivesByCoveId($coveId);
            $fields = $this->fieldModel->getFieldsByCoveId($coveId);
            $coves = [];
        }
        $indicatorTypes = $this->indicatorTypeModel->getAllIndicatorTypes();

        $this->render('indicator/edit', [
            'indicator' => $indicator,
            'objectives' => $objectives,
            'fields' => $fields,
            'indicatorTypes' => $indicatorTypes,
            'coves' => $coves,
            'coveId' => $coveId,
            'aimid' => $aimIdParam,
            'csrfToken' => $csrfToken,
            'isSuperAdmin' => $isSuperAdmin,
            'title' => 'Performans Göstergesi Düzenle'
        ]);
    }

    /**
     * ✅ Gösterge silme
     */
    public function delete($params)
    {
        $this->checkControllerPermission();
        
        // Sadece superadmin delete işlemi yapabilir
        $isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
        if (!$isSuperAdmin) {
            header('Location: index.php?url=home/error&error=Bu işlem için yetkiniz bulunmamaktadır');
            exit();
        }

        $id = (int)htmlspecialchars($params['id']);
        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        // Superadmin tüm göstergeleri silebilir
        if ($isSuperAdmin) {
            $this->model->deleteIndicator($id);
        } else {
            if (!$coveId) {
                header('Location: index.php?url=home/error&error=Yetkisiz işlem');
                exit();
            }
            $this->model->deleteIndicator($id, $coveId);
        }
        
        header('Location: index.php?url=indicator/index');
        exit();
    }

    /**
     * ✅ Gösterge değeri güncelleme (AJAX)
     */
    public function updateValue()
    {
        $this->checkControllerPermission();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $indicatorId = (int)($_POST['indicatorId'] ?? 0);
        $currentValue = (float)($_POST['currentValue'] ?? 0);

        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId) {
            echo json_encode(['success' => false, 'message' => 'Yetkisiz işlem']);
            return;
        }

        $indicator = $this->model->getIndicatorByIdAndCoveId($indicatorId, $coveId);
        if (!$indicator) {
            echo json_encode(['success' => false, 'message' => 'Gösterge bulunamadı']);
            return;
        }

        $result = $this->model->updateIndicatorValue($indicatorId, $currentValue);

        header('Content-Type: application/json');
        $targetVal = method_exists($indicator, 'getTarget') ? (float)$indicator->getTarget() : 0;
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Gösterge değeri güncellendi' : 'Güncelleme başarısız',
            'newPercentage' => $targetVal > 0 ? round(($currentValue / $targetVal) * 100, 2) : 0
        ]);
    }

    /**
     * ✅ Gösterge istatistikleri (AJAX)
     */
    public function getStatistics()
    {
        $this->checkControllerPermission();

        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId) {
            echo json_encode(['error' => 'Yetkisiz işlem']);
            return;
        }

        $statistics = $this->model->getIndicatorStatistics($coveId);

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }

    /**
     * ✅ Admin dashboard için tüm merkez istatistikleri
     */
    public function adminStatistics()
    {
        $this->checkControllerPermission();

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'Yetkisiz işlem']);
            return;
        }

        $coveId = isset($_GET['coveId']) ? (int)$_GET['coveId'] : null;
        // Basit admin istatistik üretimi
        $indicatorsAll = $this->model->getAllIndicators();
        $filtered = [];
        foreach ($indicatorsAll as $ind) {
            if ($coveId && method_exists($ind, 'getCoveId') && $ind->getCoveId() != $coveId) continue;
            $filtered[] = $ind;
        }
        $total = count($filtered);
        $completed = 0;
        $avg = 0;
        $acc = 0;
        $last = null;
        foreach ($filtered as $ind) {
            $t = method_exists($ind, 'getTarget') ? (float)$ind->getTarget() : 0;
            $c = method_exists($ind, 'getCompleted') ? (float)$ind->getCompleted() : 0;
            if ($t > 0 && $c >= $t) $completed++;
            if ($t > 0) $acc += min(100, $c / $t * 100);
            if (method_exists($ind, 'getCreatedAt')) {
                $ts = $ind->getCreatedAt();
                $last = $last ? max($last, $ts) : $ts;
            }
        }
        $avg = $total ? round($acc / $total, 2) : 0;
        header('Content-Type: application/json');
        echo json_encode([
            'total' => $total,
            'completed' => $completed,
            'averagePerformance' => $avg,
            'lastUpdateDate' => $last
        ]);
    }

    /**
     * ✅ Rapor sayfası
     */
    public function report($params = [])
    {
        $this->checkControllerPermission();

        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        $period = isset($_GET['period']) ? htmlspecialchars($_GET['period']) : 'monthly';
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

        // Rapor için mevcut göstergeleri al ve dönem filtrelemesi uygula (createdAt yılı/ayına göre)
        $indicatorsAll = $this->model->getIndicatorsByCoveId($coveId);
        $indicators = [];
        foreach ($indicatorsAll as $ind) {
            if (!method_exists($ind, 'getCreatedAt')) continue;
            $createdAt = $ind->getCreatedAt(); // formatlı geliyor d.m.Y H:i -> parse
            $ts = \DateTime::createFromFormat('d.m.Y H:i', $createdAt);
            if (!$ts) continue;
            $y = (int)$ts->format('Y');
            $m = (int)$ts->format('n');
            if ($y != $year) continue;
            if ($period === 'monthly' && $m != $month) continue;
            $indicators[] = $ind;
        }
        $statistics = $this->model->getIndicatorStatistics($coveId);

        $this->render('indicator/report', [
            'indicators' => $indicators,
            'statistics' => $statistics,
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'title' => 'Performans Raporu'
        ]);
    }

    /**
     * ✅ Admin raporu (indicator/adminReport)
     */
    public function adminreport($params = [])
    {
        $this->checkControllerPermission('users.manage');
        // Sadece admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?url=indicator/index');
            exit();
        }

        // Gösterge türleri (eğer IndicatorTypeModel varsa gelecekte entegre edilebilir). Şimdilik boş/dummy.
        $indicatorTypes = [];
        $selectedTypeId = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedTypeId = isset($_POST['indicatorTypeId']) ? (int)$_POST['indicatorTypeId'] : null;
        }

        // Admin tüm göstergeleri çeker
        $indicators = $this->model->getAllIndicators();

        // Toplamlar (örnek alan isimleri mevcut Indicator entity alanlarına uyumlu varsayılıyor)
        $totals = [
            'target' => 0,
            'completed' => 0
        ];
        foreach ($indicators as $ind) {
            if (method_exists($ind, 'getTarget')) {
                $totals['target'] += (float)$ind->getTarget();
            }
            if (method_exists($ind, 'getCompleted')) {
                $totals['completed'] += (float)$ind->getCompleted();
            }
        }

        $this->render('indicator/adminReport', [
            'title' => 'Admin Raporu',
            'indicatorTypes' => $indicatorTypes,
            'selectedTypeId' => $selectedTypeId,
            'indicators' => $indicators,
            'totals' => $totals
        ]);
    }

    /**
     * ✅ Kullanıcının merkez ID'sini getir - İyileştirilmiş
     */
    private function getCoveIdByUserId($userId)
    {
        try {
            // Model'deki method varsa kullan
            if (method_exists($this->model, 'getCoveIdByUserId')) {
                return $this->model->getCoveIdByUserId($userId);
            }

            // Yoksa direkt DB'den çek
            require_once dirname(__DIR__) . '/config/config.php';

            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->prepare("SELECT coveId FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetchColumn();

            return $result ?: null;
        } catch (Exception $e) {
            error_log("getCoveIdByUserId error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Objectivelar AJAX ile getir
     */
    public function getObjectivesByAimId()
    {
        $this->checkControllerPermission();

        $aimId = isset($_GET['aimId']) ? (int)$_GET['aimId'] : 0;
        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId || !$aimId) {
            echo json_encode([]);
            return;
        }

        header('Content-Type: application/json');
        $objectives = $this->objectiveModel->getObjectivesByAimIdAndCoveId($aimId, $coveId);

        $result = [];
        foreach ($objectives as $objective) {
            $result[] = [
                'id' => $objective->id,
                'objectiveTitle' => $objective->objectiveTitle,
                'objectiveDesc' => $objective->objectiveDesc
            ];
        }

        echo json_encode($result);
    }

    /**
     * ✅ Dashboard widget için özet veriler
     */
    public function getDashboardSummary()
    {
        $this->checkControllerPermission();

        $userId = $_SESSION['user_id'];
        $coveId = $this->getCoveIdByUserId($userId);

        if (!$coveId) {
            echo json_encode(['error' => 'Yetkisiz işlem']);
            return;
        }

        // Model'de özel sayaç metodları yok; mevcut göstergeler üzerinden hesapla
        $indicators = $this->model->getIndicatorsByCoveId($coveId);
        $total = count($indicators);
        $completed = 0;
        $perfSum = 0;
        $lastUpdate = null;
        foreach ($indicators as $ind) {
            $t = method_exists($ind, 'getTarget') ? (float)$ind->getTarget() : 0;
            $c = method_exists($ind, 'getCompleted') ? (float)$ind->getCompleted() : 0;
            if ($c >= $t && $t > 0) {
                $completed++;
            }
            if ($t > 0) {
                $perfSum += min(100, ($c / $t) * 100);
            }
            if (method_exists($ind, 'getCreatedAt')) {
                $lastUpdate = $lastUpdate ? max($lastUpdate, $ind->getCreatedAt()) : $ind->getCreatedAt();
            }
        }
        $summary = [
            'totalIndicators' => $total,
            'completedIndicators' => $completed,
            'averagePerformance' => $total ? round($perfSum / $total, 2) : 0,
            'lastUpdateDate' => $lastUpdate
        ];

        header('Content-Type: application/json');
        echo json_encode($summary);
    }
}
