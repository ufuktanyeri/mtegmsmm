<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Controllers\AimController.php

use Dompdf\Dompdf;
use Dompdf\Options;

require_once 'BaseController.php';
require_once __DIR__ . '/../models/AimModel.php';
require_once __DIR__ . '/../models/CoveModel.php';
require_once __DIR__ . '/../models/IndicatorModel.php';
require_once __DIR__ . '/../models/ActionModel.php';
require_once __DIR__ . '/../models/RegulationModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../validators/AimValidator.php';
require_once __DIR__ . '/../entities/Permission.php';
require_once __DIR__ . '/../models/DetailedLogModel.php';

// DomPDF ve PHPWord için autoloader
require_once __DIR__ . '/../lib/dompdf/vendor/autoload.php';
require_once __DIR__ . '/../lib/PHPWord/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;

class AimController extends BaseController
{
    // Yetki kontrolü
    protected function checkControllerPermission($perm = 'aims.manage')
    {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === $perm) {
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

    // Ortak veri hazırlama metodu  
    private function prepareReportData($coveId = null)
    {
        $model = new AimModel();
        $coveModel = new CoveModel();

        if ($coveId) {
            // Admin raporu - belirli cove
            $cove = $coveModel->getCoveById($coveId);
            $aims = $model->getAimsForReport($coveId);
        } else {
            // Kullanıcı raporu - kendi cove'i
            $userId = $_SESSION['user_id'];
            $cove = $coveModel->getCoveByUserId($userId);
            $aims = $model->getAimsForReport($cove->getId());
        }

        return ['cove' => $cove, 'aims' => $aims];
    }

    // Ortak HTML oluşturma metodu
    private function generateReportHtml($cove, $aims)
    {
        $cityDistrict = $cove->getCityDistrict();
        $coveName = $cove->getName();

        $html = '<!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <title>' . $cityDistrict . ' - ' . $coveName . ' - Yıllık Faaliyet Raporu</title>
            <style>
                @page { 
                    margin: 15mm; 
                    size: A4 portrait; 
                }
                body { 
                    font-family: DejaVu Sans, sans-serif; 
                    font-size: 12px; 
                    margin: 0;
                    padding: 0;
                    line-height: 1.3;
                }
                h1 { 
                    text-align: center; 
                    font-size: 12px; 
                    margin: 0 0 6px 0;
                    line-height: 1.4;
                }
                h2 { 
                    background-color: #f2f2f2; 
                    padding: 6px; 
                    font-size: 11px; 
                    text-align: justify;
                    margin: 0 0 4px 0;
                    page-break-inside: avoid;
                    page-break-after: avoid;
                }                
                h3 { 
                    text-align: justify;
                    font-size: 10px;
                    font-weight: bold;
                    margin: 0 0 4px 0;
                    page-break-inside: avoid;
                    page-break-after: avoid;
                }
                h4 {
                    font-size: 9px;
                    font-weight: bold;
                    margin: 0 0 4px 0;
                    color: #555;
                    page-break-inside: avoid;
                    page-break-after: avoid;
                }
                p{
                    padding: 6px;
                    font-size: 10px;
                    text-align: justify;
                    margin: 0 0 4px 0;
                }
                .section { 
                    margin-bottom: 10px; 
                    text-align: justify; 
                }
                .sub-section { 
                    margin-left: 15px; 
                    font-size: 9px; 
                }
                .sub-section p { 
                    margin: 2px 0; 
                    text-align: justify; 
                }

                table, th, td {
                    border-collapse: collapse;
                    border: 1px solid #ddd;
                }

                th + th, td + td {
                    border-left: 0;
                }

                tr + tr td {
                    border-top: 0;
                }

                table { 
                    width: 100%; 
                    max-width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 5px; 
                    font-size: 9px; 
                    table-layout: fixed;
                    page-break-inside: auto;
                }
                
                thead {
                    display: table-header-group;
                    page-break-inside: avoid;
                }
                
                tbody tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 4px; 
                    text-align: left; 
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    vertical-align: top;
                }
                th { 
                    background-color: #f4f4f4; 
                    font-size: 9px;
                    page-break-inside: avoid;
                }
                
                .faaliyet {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 5px;
                    font-size: 8px;
                    table-layout: fixed;
                    page-break-inside: auto;
                }
                .faaliyet th:nth-child(1), .faaliyet td:nth-child(1) { width: 3%; }
                .faaliyet th:nth-child(2), .faaliyet td:nth-child(2) { width: 13%; }
                .faaliyet th:nth-child(3), .faaliyet td:nth-child(3) { width: 39%; }
                .faaliyet th:nth-child(5), .faaliyet td:nth-child(5) { width: 9%; text-align: center; }
                .faaliyet th:nth-child(6), .faaliyet td:nth-child(6) { width: 9%; text-align: center; }
                .faaliyet th:nth-child(7), .faaliyet td:nth-child(7) { width: 9%; text-align: center; }
                .faaliyet th:nth-child(8), .faaliyet td:nth-child(8) { width: 9%; text-align: center; }

                .gosterge {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 5px;
                    font-size: 8px;
                    table-layout: fixed;
                    page-break-inside: auto;
                }
                .gosterge th:nth-child(1), .gosterge td:nth-child(1) { width: 3%; }
                .gosterge th:nth-child(2), .gosterge td:nth-child(2) { width: 25%; }
                .gosterge th:nth-child(3), .gosterge td:nth-child(3) { width: 42%; }
                .gosterge th:nth-child(4), .gosterge td:nth-child(4) { width: 10%; text-align: center; }
                .gosterge th:nth-child(5), .gosterge td:nth-child(5) { width: 10%; text-align: center; }
                .gosterge th:nth-child(6), .gosterge td:nth-child(6) { width: 10%; text-align: center; }

                .page-break-before { 
                    page-break-before: always; 
                }
                .keep-together { 
                    page-break-inside: avoid; 
                }
                
                .long-text {
                    word-break: break-word;
                    hyphens: auto;
                    line-height: 1.2;
                    max-height: none;
                }
            </style>
        </head>
        <body>
            <h1>' . $cityDistrict . ' - ' . $coveName . '</h1>
            <h1>Yıllık Faaliyet Raporu</h1>';

        $aimnumber = 1;
        foreach ($aims as $a) {
            $html .= '<h2>Amaç ' . $aimnumber . ': ' . htmlspecialchars($a->getAimTitle()) . '</h2>';
            $html .= '<div class="section">';
            $html .= '<p><strong>Açıklama:</strong> ' . htmlspecialchars($a->getAimDesc()) . '</p>';
            $html .= '<p><strong>Sonuç:</strong> ' . htmlspecialchars($a->getAimResult()) . '</p>';

            $objectivenumber = 1;
            foreach ($a->getObjectives() as $o) {
                $html .= '<div class="sub-section">';
                $html .= '<h3>Hedef ' . $objectivenumber . ': ' . htmlspecialchars($o->getObjectiveTitle()) . '</h3>';
                $html .= '<p><strong>Açıklama:</strong> ' . htmlspecialchars($o->getObjectiveDesc()) . '</p>';
                $html .= '<p><strong>Sonuç:</strong> ' . htmlspecialchars($o->getObjectiveResult()) . '</p>';

                // Faaliyetler Tablosu - SİMGELERLE
                $actionModel = new ActionModel();
                $actions = $actionModel->getActionsByCoveIdByAimId($a->getId(), $cove->getId(), $o->getId());

                if (!empty($actions)) {
                    $html .= '<h4>Faaliyetler:</h4>';
                    $html .= '<table class="faaliyet">
                          <thead>
                            <tr>
                                <th>#</th>
                                <th>Faaliyet Adı</th>
                                <th>Açıklama</th>
                                <th>Sorumlu</th>
                                <th>Durum</th>
                                <th>Başlangıç</th>
                                <th>Bitiş</th>
                                <th>Tekrar</th>
                            </tr>
                          </thead>
                          <tbody>';

                    $actionnumber = 1;
                    foreach ($actions as $action) {
                        // ✅ DÜZELTME: Object/Array hybrid desteği
                        if (is_object($action)) {
                            // Object access
                            $periodic = $action->getPeriodic() == 1 ? "Haftalık" : "-";
                            $actionStatus = $action->getActionStatus();
                            $actionTitle = $action->getActionTitle();
                            $actionDesc = $action->getActionDesc();
                            $actionResponsible = $action->getActionResponsible();
                            $dateStart = $action->getDateStart();
                            $dateEnd = $action->getDateEnd();
                        } else {
                            // Array access
                            $periodic = (($action['periodic'] ?? 0) == 1) ? "Haftalık" : "-";
                            $actionStatus = $action['actionStatus'] ?? 0;
                            $actionTitle = $action['actionTitle'] ?? '';
                            $actionDesc = $action['actionDesc'] ?? '';
                            $actionResponsible = $action['actionResponsible'] ?? '';
                            $dateStart = $action['dateStart'] ?? date('Y-m-d');
                            $dateEnd = $action['dateEnd'] ?? date('Y-m-d');
                        }

                        // SİMGE LOGİĞİ - Faaliyetler için
                        if ($actionStatus) {
                            $statusIcon = '<span style="color: #28a745; font-size: 9px; font-weight: bold;">●</span>';
                        } else {
                            $statusIcon = '<span style="color: #ffc107; font-size: 9px; font-weight: bold;">●</span>';
                        }

                        $html .= '<tr>
                            <td>' . $actionnumber . '</td>
                            <td class="long-text">' . htmlspecialchars($actionTitle) . '</td>
                            <td class="long-text">' . htmlspecialchars($actionDesc) . '</td>
                            <td class="long-text" style="text-align: center;">' . htmlspecialchars($actionResponsible) . '</td>
                            <td style="text-align: center;">' . $statusIcon . '</td>
                            <td style="text-align: center;">' . date('d.m.Y', strtotime($dateStart)) . '</td>
                            <td style="text-align: center;">' . date('d.m.Y', strtotime($dateEnd)) . '</td>
                            <td style="text-align: center;">' . $periodic . '</td>
                        </tr>';
                        $actionnumber++;
                    }
                    $html .= '</tbody></table>';
                }

                // Göstergeler Tablosu - SİMGELERLE
                $indicatorModel = new IndicatorModel();
                $indicators = $indicatorModel->getIndicatorsByCoveId($a->getId(), $cove->getId(), $o->getId());

                if (!empty($indicators)) {
                    $html .= '<h4>Göstergeler:</h4>';
                    $html .= '<table class="gosterge">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gösterge</th>
                                <th>Açıklama</th>
                                <th>Hedef</th>
                                <th>Mevcut</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>';

                    $indicatornumber = 1;
                    foreach ($indicators as $indicator) {
                        // ✅ DÜZELTME: Object/Array hybrid desteği
                        if (is_object($indicator)) {
                            // Object access
                            $target = (int)$indicator->getTarget();
                            $completed = (int)$indicator->getCompleted();
                            $indicatorTitle = $indicator->getIndicatorTitle();
                            $indicatorDesc = $indicator->getIndicatorDesc();
                        } else {
                            // Array access
                            $target = (int)($indicator['target'] ?? 0);
                            $completed = (int)($indicator['completed'] ?? 0);
                            $indicatorTitle = $indicator['indicatorTitle'] ?? '';
                            $indicatorDesc = $indicator['indicatorDesc'] ?? '';
                        }

                        // SİMGE LOGİĞİ - Göstergeler için
                        $difference = $target - $completed;

                        if ($difference <= 0) {
                            $statusIcon = '<span style="color: #28a745; font-size: 9px; font-weight: bold;">●</span>';
                        } else {
                            $statusIcon = '<span style="color: #ffc107; font-size: 9px; font-weight: bold;">●</span>';
                        }

                        $html .= '<tr>
                            <td>' . $indicatornumber . '</td>
                            <td class="long-text">' . htmlspecialchars($indicatorTitle) . '</td>
                            <td class="long-text">' . htmlspecialchars($indicatorDesc) . '</td>
                            <td style="text-align: center;">' . htmlspecialchars((string)$target) . '</td>
                            <td style="text-align: center;">' . htmlspecialchars((string)$completed) . '</td>
                            <td style="text-align: center;">' . $statusIcon . '</td>
                        </tr>';
                        $indicatornumber++;
                    }
                    $html .= '</tbody></table>';
                }

                $html .= '</div>'; // .sub-section
                $objectivenumber++;
            }

            $html .= '</div>'; // .section
            $aimnumber++;
        }

        $html .= '</body></html>';
        return $html;
    }

    public function getCoveReport($params)
    {
        $this->checkControllerPermission();
        $data = $this->prepareReportData();
        $html = $this->generateReportHtml($data['cove'], $data['aims']);

        /** @var \Dompdf\Options $options */
        $options = new \Dompdf\Options();
        $options->set('chroot', realpath(''));
        $options->set('isHtml5ParserEnabled', true);

        /** @var \Dompdf\Dompdf $dompdf */
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = $data['cove']->getCityDistrict() . ' - ' . $data['cove']->getName() . ' - Yillik_Faaliyet_Raporu.pdf';

        if (ob_get_length()) {
            ob_clean();
        }

        $dompdf->stream($filename, ["Attachment" => true]);
        exit();
    }

    public function getCoveReportWord($params)
    {
        $this->checkControllerPermission();
        $data = $this->prepareReportData();
        $html = $this->generateReportHtml($data['cove'], $data['aims']);

        $filename = $data['cove']->getCityDistrict() . ' - ' . $data['cove']->getName() . "_Yillik_Faaliyet_Raporu.doc";

        if (ob_get_length()) ob_clean();
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=" . $filename);
        echo $html;
    }

    public function getCoveAdminReport($params)
    {
        $this->checkControllerPermission("users.manage");
        $coveid = htmlspecialchars($params['id']);
        $data = $this->prepareReportData($coveid);
        $html = $this->generateReportHtml($data['cove'], $data['aims']);

        /** @var \Dompdf\Options $options */
        $options = new \Dompdf\Options();
        $options->set('chroot', realpath(''));
        $options->set('isHtml5ParserEnabled', true);

        /** @var \Dompdf\Dompdf $dompdf */
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = $data['cove']->getCityDistrict() . ' - ' . $data['cove']->getName() . ' - Yillik_Faaliyet_Raporu.pdf';

        if (ob_get_length()) {
            ob_clean();
        }

        $dompdf->stream($filename, ["Attachment" => true]);
        exit();
    }

    public function getCoveAdminReportWord($params)
    {
        $this->checkControllerPermission("users.manage");
        $coveid = htmlspecialchars($params['id']);
        $data = $this->prepareReportData($coveid);
        $html = $this->generateReportHtml($data['cove'], $data['aims']);

        $filename = $data['cove']->getCityDistrict() . ' - ' . $data['cove']->getName() . "_Yillik_Faaliyet_Raporu.doc";

        if (ob_get_length()) ob_clean();
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=" . $filename);
        echo $html;
    }

    // Amaçlar listesi
    public function index($params)
    {
        $this->checkControllerPermission();
        $model = new AimModel();
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['user_id']);

        // Superadmin (roleId = 5) tüm cove'ların amaçlarını görebilir
        if ($user && $user->getRole() && $user->getRole()->getId() == 5) {
            $aims = $model->getAllAimsWithObjAndReg();
        } else {
            $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);
            $aims = $model->getAimsByCoveWithObjAndReg($userCoveId);
        }

        $error = isset($params['error']) ? $params['error'] : "";
        $this->render('aim/index', ['title' => 'Amaçlar', 'aims' => $aims, 'error' => $error]);
    }

    // Amaç oluşturma
    public function create()
    {
        $this->checkControllerPermission();
        $model = new AimModel();
        $userId = $_SESSION['user_id'];
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);
        $regulationModel = new RegulationModel();
        $regulations = $regulationModel->getRegulationsByCoveId($userCoveId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('aim/create', ['title' => 'Amaç Ekle', 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            $validator = new AimValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('aim/create', ['title' => 'Amaç Ekle', 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            $aimTitle = $_POST['aimTitle'];
            $aimDesc = $_POST['aimDesc'];
            $aimResult = $_POST['aimResult'];
            $coveId = $model->getCoveIdByUserId($_SESSION['user_id']);
            $regulationIds = isset($_POST['regulations']) ? $_POST['regulations'] : [];

            if ($aimTitle && $aimDesc && $coveId) {
                $aimId = $model->createAim($aimTitle, $aimDesc, $coveId, $aimResult);
                $model->assignRegulationsToAim($aimId, $regulationIds);
                header('Location: index.php?url=aim/index');
                exit();
            } else {
                $error = 'Tüm alanlar gerekli';
                $this->render('aim/create', ['title' => 'Amaç Ekle', 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'error' => $error]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('aim/create', ['title' => 'Amaç Ekle', 'regulations' => $regulations, 'csrfToken' => $csrfToken]);
        }
    }

    // Amaç düzenleme
    public function edit($params)
    {
        $this->checkControllerPermission();
        $model = new AimModel();
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['user_id']);

        // Sadece superadmin düzenleme yapabilir
        if (!$user || !$user->getRole() || $user->getRole()->getId() != 5) {
            header('Location: index.php?url=aim/index&error="Düzenleme yetkiniz yok!"');
            exit();
        }

        $id = htmlspecialchars($params['id']);
        $aim = $model->getAimById($id);
        if (!$aim) {
            header('Location: index.php?url=aim/index&error="Düzenleme yetkiniz yok!"');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $regulationModel = new RegulationModel();
        // Superadmin tüm mevzuatları görebilir, cove sınırlaması yok
        $regulations = $regulationModel->getAllRegulations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('aim/edit', ['title' => 'Amaç Düzenle', 'aim' => $aim, 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'error' => $error]);
                return;
            }

            if ($aim) {
                $validator = new AimValidator($_POST);
                $errors = $validator->validateForm();
                if (!empty($errors)) {
                    $this->render('aim/edit', ['title' => 'Amaç Düzenle', 'aim' => $aim, 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'errors' => $errors]);
                    return;
                }

                $aimTitle = $_POST['aimTitle'];
                $aimDesc = $_POST['aimDesc'];
                $aimResult = $_POST['aimResult'];
                $regulationIds = isset($_POST['regulations']) ? $_POST['regulations'] : [];

                if ($aimTitle && $aimDesc) {
                    // Superadmin için cove sınırlaması kaldırıldı
                    $model->updateAim($id, $aimTitle, $aimDesc, $aim->getCoveId(), $aimResult);
                    $model->assignRegulationsToAim($id, $regulationIds);

                    // Log the aim edit event
                    $detailedLogModel = new DetailedLogModel();
                    $detailedLogModel->createDetailedLog($_SESSION['user_id'], 'aimedit', 'Amaç', $aimTitle, $_SERVER['REMOTE_ADDR']);

                    header('Location: index.php?url=aim/index');
                    exit();
                } else {
                    $error = 'Tüm alanlar gerekli';
                    $this->render('aim/edit', ['title' => 'Amaç Düzenle', 'aim' => $aim, 'regulations' => $regulations, 'csrfToken' => $csrfToken, 'error' => $error]);
                }
            } else {
                header('Location: index.php?url=aim/index&error=1');
                exit();
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $this->render('aim/edit', ['title' => 'Amaç Düzenle', 'aim' => $aim, 'regulations' => $regulations, 'csrfToken' => $csrfToken]);
        }
    }

    // Amaç silme
    public function delete($params)
    {
        $this->checkControllerPermission();
        $model = new AimModel();
        $id = htmlspecialchars($params['id']);
        $aim = $model->getAimById($id);
        $userCoveId = $model->getCoveIdByUserId($_SESSION['user_id']);

        if ($aim && $aim->getCoveId() == $userCoveId) {
            $model->deleteAim($id);

            // Log the aim delete event
            $detailedLogModel = new DetailedLogModel();
            $detailedLogModel->createDetailedLog($_SESSION['user_id'], 'aimdelete', 'Amaç', $aim->getAimTitle(), $_SERVER['REMOTE_ADDR']);

            header('Location: index.php?url=aim/index');
        } else {
            header('Location: index.php?url=aim/index&error="Silme yetkiniz yok!');
        }
        exit();
    }

    // Amaç mevzuatları getirme
    public function getRegulations($params)
    {
        $this->checkControllerPermission();
        $aimId = $params['aimId'] ? htmlspecialchars($params['aimId']) : 0;

        $model = new AimModel();
        $regulations = $model->getRegulationsByAimId($aimId);
        header('Content-Type: application/json');
        echo json_encode($regulations);
    }
}
