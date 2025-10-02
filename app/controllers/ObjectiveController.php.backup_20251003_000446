<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Controllers\ObjectiveController.php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/ObjectiveModel.php';
require_once __DIR__ . '/../models/AimModel.php';
require_once __DIR__ . '/../models/CoveModel.php';
require_once __DIR__ . '/../entities/Objective.php';
require_once __DIR__ . '/../entities/Aim.php';

class ObjectiveController extends BaseController
{
    private $model;
    private $aimModel;
    private $coveModel;

    public function __construct()
    {
        $this->model = new ObjectiveModel();
        $this->aimModel = new AimModel();
        $this->coveModel = new CoveModel();
    }

    protected function checkControllerPermission($permission = null)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/login');
            exit();
        }

        if ($permission === 'users.manage') {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                header('Location: index.php?url=objective/index');
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

    public function index($params = [])
    {
        $this->checkControllerPermission();

        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);

        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }
        // Opsiyonel aim filtresi (objective/list yerine kullanılır)
        $filterAimId = 0;
        if (isset($_GET['aimid'])) {
            $filterAimId = (int)$_GET['aimid'];
        }
        if (isset($params['aimid'])) {
            $filterAimId = (int)$params['aimid'];
        }

        if ($filterAimId > 0) {
            $objectives = $this->model->getObjectivesByAimIdAndCoveId($filterAimId, $coveId);
        } else {
            $objectives = $this->model->getObjectivesByCoveId($coveId);
        }

        $aims = $this->aimModel->getAimsByCoveId($coveId);
        foreach ($objectives as $objective) {
            if ($objective instanceof Objective) {
                $objective->setAims($aims);
            }
        }

        $currentAim = null;
        if ($filterAimId > 0) {
            foreach ($aims as $a) {
                // Aim bir object olduğu için sadece object notation kullan
                if ($a instanceof Aim) {
                    if ((int)$a->getId() === $filterAimId) {
                        $currentAim = $a;
                        break;
                    }
                }
            }
        }

        $this->render('objective/index', [
            'objectives' => $objectives,
            'aims' => $aims,
            'coveId' => $coveId,
            'currentAimId' => $filterAimId,
            'currentAim' => $currentAim,
            'filtered' => $filterAimId > 0
        ]);
    }

    public function create($params = [])
    {
        $this->checkSuperadminPermission();

        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);

        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'objectiveTitle' => htmlspecialchars($_POST['objectiveTitle']),
                'objectiveDesc' => htmlspecialchars($_POST['objectiveDesc']),
                'aimId' => (int)htmlspecialchars($_POST['aimId']),
                'coveId' => $coveId
            ];

            $this->model->createObjective($data);
            header('Location: index.php?url=objective/index');
            exit();
        }

        $aims = $this->aimModel->getAimsByCoveId($coveId);

        $this->render('objective/create', [
            'aims' => $aims,
            'coveId' => $coveId
        ]);
    }

    public function edit($params)
    {
        $this->checkSuperadminPermission();

        $id = (int)htmlspecialchars($params['id']);
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);

        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        $objective = $this->model->getObjectiveByIdAndCoveId($id, $coveId);
        if (!$objective) {
            header('Location: index.php?url=home/error&error=Hedef bulunamadı');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'objectiveTitle' => htmlspecialchars($_POST['objectiveTitle']),
                'objectiveDesc' => htmlspecialchars($_POST['objectiveDesc']),
                'aimId' => (int)htmlspecialchars($_POST['aimId']),
                'coveId' => $coveId
            ];

            $this->model->updateObjective($id, $data);
            header('Location: index.php?url=objective/index');
            exit();
        }

        $aims = $this->aimModel->getAimsByCoveId($coveId);

        $this->render('objective/edit', [
            'objective' => $objective,
            'aims' => $aims,
            'coveId' => $coveId
        ]);
    }

    public function delete($params)
    {
        $this->checkSuperadminPermission();

        $id = (int)htmlspecialchars($params['id']);
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);

        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        $this->model->deleteObjective($id, $coveId);
        header('Location: index.php?url=objective/index');
        exit();
    }

    public function getObjectivesByAimId()
    {
        $this->checkControllerPermission();

        $aimId = isset($_GET['aimId']) ? (int)$_GET['aimId'] : 0;
        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);

        if (!$coveId || !$aimId) {
            echo json_encode([]);
            return;
        }

        header('Content-Type: application/json');
        $objectives = $this->model->getObjectivesByAimIdAndCoveId($aimId, $coveId);

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

    // ✅ 'list' route alias (objective/list?aimid=XX) - 'list' anahtar sözcük olduğu için Router fallback kullanır
    public function listObjectives($params = [])
    {
        $this->checkControllerPermission();

        $aimId = isset($_GET['aimid']) ? (int)$_GET['aimid'] : (isset($params['aimid']) ? (int)$params['aimid'] : 0);
        if ($aimId <= 0) {
            header('Location: index.php?url=objective/index');
            exit();
        }

        $coveId = $this->model->getCoveIdByUserId($_SESSION['user_id']);
        if (!$coveId) {
            header('Location: index.php?url=home/error&error=Yetkisiz işlem');
            exit();
        }

        $objectives = $this->model->getObjectivesByAimIdAndCoveId($aimId, $coveId);
        $aims = $this->aimModel->getAimsByCoveId($coveId);
        $currentAim = null;
        foreach ($aims as $a) {
            // Aim bir object olduğu için sadece object notation kullan
            if ($a instanceof Aim) {
                if ((int)$a->getId() === $aimId) {
                    $currentAim = $a;
                    break;
                }
            }
        }

        // Aims referansını objectives'e ekle (index ile uyumlu davranış)
        foreach ($objectives as $objective) {
            if ($objective instanceof Objective) {
                $objective->setAims($aims);
            }
        }

        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Stratejik Yönetim', 'url' => ''],
            ['title' => 'Hedefler', 'url' => '']
        ];

        // Add other data to $this->data
        $this->data['objectives'] = $objectives;
        $this->data['aims'] = $aims;
        $this->data['currentAimId'] = $aimId;
        $this->data['currentAim'] = $currentAim;
        $this->data['coveId'] = $coveId;
        $this->data['filtered'] = true;

        // Ayrı view yoksa index view yeniden kullanılır
        $this->render('objective/index', $this->data);
    }

    // Route: objective/list -> bu method çağrılacak
    public function list($params = [])
    {
        $this->listObjectives($params);
    }
}
