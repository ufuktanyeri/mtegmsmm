<?php
require_once __DIR__ . '/BaseController.php';

class HelpController extends BaseController {
    public function index($params = []) {
        $this->render('help/index', [
            'title' => 'Yardım ve Destek',
            'page_title' => 'Yardım Merkezi'
        ]);
    }

    /** Dinamik + fallback FAQ veri kaynağı */
    private function getFaqData() {
        // DB'den yüklemeyi dene
        try {
            require_once __DIR__.'/../models/FaqModel.php';
            $faqModel = new FaqModel();
            $rows = $faqModel->getActiveAll();
            if ($rows) {
                return array_map(function($r){ return ['q'=>$r['question'],'a'=>$r['answer']]; }, $rows);
            }
        } catch (Throwable $e) { /* fallback'e düş */ }
        // Fallback statik
        return [
            ['q'=>'Dosya yüklerken hata alıyorum','a'=>'Dosya boyutu 10MB altında ve izinli uzantılarda olduğundan emin olun (pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif, txt). Klasör yazma izni yoksa sistem yöneticisine bildiriniz.'],
            ['q'=>'Faaliyet durumu güncellenmiyor','a'=>'Oturum süreniz dolmuş olabilir. Sayfayı yenileyip tekrar deneyin. Gerekli alanların doldurulduğundan emin olun.'],
            ['q'=>'Hedef filtrelemesi çalışmıyor','a'=>'Tarayıcı önbelleğini temizleyip yeniden deneyin. Sorun devam ederse yöneticiye bildiriniz.'],
            ['q'=>'Görevlerim takvimde görünmüyor','a'=>'İlgili merkeze ait olup olmadığını ve tarih aralığının doğru girildiğini doğrulayın. Periyodik görevlerde haftalık gün ayarı kontrol edilmelidir.'],
            ['q'=>'Periyodik görev nasıl eklenir','a'=>'Faaliyet formunda periyodik alanını işaretleyip, periyot türünü (haftalık) ve gün (1-7) seçerek kaydedebilirsiniz.'],
            ['q'=>'Görev tamamlandı olarak işaretlenmiyor','a'=>'İzinleriniz yetersiz olabilir veya sayfa eski sürümle önbellekten geliyor olabilir. Çıkış yapıp tekrar girin.'],
        ];
    }

    /**
     * /index.php?url=help/faqSearch&query=... endpoint
     * Basit anahtar kelime araması, Top 3 sonuç döner.
     */
    public function faqSearch($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        if ($query === '') {
            echo json_encode(['results' => []]);
            return;
        }
        // Önce DB araması (LIKE basit)
        try {
            require_once __DIR__.'/../models/FaqModel.php';
            $faqModel = new FaqModel();
            $dbResults = $faqModel->search($query, 5);
            if ($dbResults) {
                echo json_encode(['query'=>$query,'results'=>array_map(function($r){return ['q'=>$r['question'],'a'=>$r['answer']];}, $dbResults)]);
                return;
            }
        } catch (Throwable $e) { /* fallback kullan */ }
        // Fallback statik set üzerinde arama
        $faq = $this->getFaqData();
        $needle = mb_strtolower($query,'UTF-8');
        $matches = [];
        foreach($faq as $row){
            $hay = mb_strtolower($row['q'].' '.$row['a'],'UTF-8');
            $pos = mb_strpos($hay,$needle);
            if($pos!==false){ $matches[]=['score'=>$pos,'q'=>$row['q'],'a'=>$row['a']]; }
        }
        usort($matches,function($a,$b){return $a['score']<=>$b['score'];});
        $out = array_slice(array_map(function($m){return ['q'=>$m['q'],'a'=>$m['a']];},$matches),0,5);
        echo json_encode(['query'=>$query,'results'=>$out]);
    }

    /** Başlangıç öneri listesi için aktif SSS */
    public function faqList($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        try {
            require_once __DIR__.'/../models/FaqModel.php';
            $faqModel = new FaqModel();
            $rows = $faqModel->getActiveLimited(12);
            echo json_encode(['success'=>true,'items'=>array_map(function($r){return ['q'=>$r['question'],'a'=>$r['answer']];},$rows)]);
            return;
        } catch (Throwable $e) {
            echo json_encode(['success'=>false,'items'=>$this->getFaqData()]);
        }
    }

    /** Canlı destek devri simülasyonu */
    public function handover($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false,'error'=>'login_required']); return; }
        require_once __DIR__.'/../models/SupportSessionModel.php';
        $sessionModel = new SupportSessionModel();
        // Aynı kullanıcı pending açık ise onu ver
        $existingId = $this->findExistingPendingSession($sessionModel, $_SESSION['user_id']);
        if($existingId){
            echo json_encode(['success'=>true,'session_id'=>$existingId,'message'=>'Mevcut bekleyen oturum devam ediyor.']);
            return;
        }
        $subject = isset($_POST['subject']) ? trim($_POST['subject']) : null;
        $id = $sessionModel->createPending($_SESSION['user_id'], $subject ?: null);
        echo json_encode([
            'success'=>true,
            'session_id'=>$id,
            'status'=>'pending',
            'message'=>'Canlı destek isteğiniz alındı. Temsilci bağlanana kadar yazmaya devam edebilirsiniz.'
        ]);
    }

    private function findExistingPendingSession($sessionModel, $userId){
        $list = $sessionModel->listPending();
        foreach($list as $s){ if($s['user_id']==$userId) return $s['id']; }
        return null;
    }

    /** Kullanıcı mesaj gönderir */
    public function sendMessage($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false,'error'=>'login_required']); return; }
        $sessionId = isset($_POST['session_id']) ? (int)$_POST['session_id'] : 0;
        $text = isset($_POST['text']) ? trim($_POST['text']) : '';
        if($sessionId<=0 || $text===''){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        if(strlen($text)>1000) $text = substr($text,0,1000);
        require_once __DIR__.'/../models/SupportSessionModel.php';
        require_once __DIR__.'/../models/SupportMessageModel.php';
        $sessionModel = new SupportSessionModel();
        $msgModel = new SupportMessageModel();
        $session = $sessionModel->getById($sessionId);
        if(!$session || $session['user_id'] != $_SESSION['user_id']) { echo json_encode(['success'=>false,'error'=>'not_found']); return; }
        if($session['status']==='closed'){ echo json_encode(['success'=>false,'error'=>'closed']); return; }
        $msgId = $msgModel->addMessage($sessionId,'user',$text);
        $sessionModel->touchUserMsg($sessionId);
        echo json_encode(['success'=>true,'message_id'=>$msgId]);
    }

    /** Hem user hem agent polling (yetkiye bakarak filtre) */
    public function poll($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        $sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
        $since = isset($_GET['since']) ? (int)$_GET['since'] : 0;
        if($sessionId<=0){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        require_once __DIR__.'/../models/SupportSessionModel.php';
        require_once __DIR__.'/../models/SupportMessageModel.php';
        $sessionModel = new SupportSessionModel();
        $msgModel = new SupportMessageModel();
        $session = $sessionModel->getById($sessionId);
        if(!$session){ echo json_encode(['success'=>false,'error'=>'not_found']); return; }
        $isAgent = $this->isAgent();
        if(!$isAgent){
            if(!isset($_SESSION['user_id']) || $session['user_id'] != $_SESSION['user_id']) { echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        }
        $msgs = $msgModel->getMessagesSince($sessionId,$since);
        echo json_encode(['success'=>true,'session_status'=>$session['status'],'messages'=>$msgs,'last_id'=> $msgModel->getLastId($sessionId)]);
    }

    /** Agent bir session'ı üstlenir */
    public function agentActivate($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->isAgent()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        $sessionId = isset($_POST['session_id']) ? (int)$_POST['session_id'] : 0;
        if($sessionId<=0){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        require_once __DIR__.'/../models/SupportSessionModel.php';
        $sessionModel = new SupportSessionModel();
        $sessionModel->activate($sessionId, $_SESSION['user_id']);
        echo json_encode(['success'=>true]);
    }

    public function agentClose($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->isAgent()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        $sessionId = isset($_POST['session_id']) ? (int)$_POST['session_id'] : 0;
        if($sessionId<=0){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        require_once __DIR__.'/../models/SupportSessionModel.php';
        $sessionModel = new SupportSessionModel();
        $sessionModel->close($sessionId, $_SESSION['user_id']);
        echo json_encode(['success'=>true]);
    }

    public function agentSessions($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->isAgent()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        require_once __DIR__.'/../models/SupportSessionModel.php';
        $sessionModel = new SupportSessionModel();
        echo json_encode([
            'success'=>true,
            'pending'=>$sessionModel->listPending(),
            'active'=>$sessionModel->listActive()
        ]);
    }

    /** Agent panelini render eder */
    public function sessions($params = []) {
        if(!$this->isAgent()) { echo 'Erişim yok'; return; }
        $this->render('help/sessions', [
            'title' => 'Destek Oturumları',
            'page_title' => 'Canlı Destek Oturumları'
        ]);
    }

    /*** ===== FAQ Yönetimi (admin + superadmin) ===== */
    private function canManageFaq(){
        if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','superadmin'])) return true;
        // Ek izin üzerinden de yetkilendirilebilir
        if(isset($_SESSION['permissions'])){
            $perms = array_map('unserialize', $_SESSION['permissions']);
            foreach($perms as $p){ if($p instanceof Permission && $p->getPermissionName()==='support.manage') return true; }
        }
        return false;
    }
    public function faqAdmin($params = []) {
        if(!$this->canManageFaq()) { echo 'Erişim yok'; return; }
        $this->render('help/faq_manage',[ 'title'=>'SSS Yönetimi','page_title'=>'Sık Sorulan Sorular Yönetimi' ]);
    }
    public function faqAdminList($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->canManageFaq()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        require_once __DIR__.'/../models/FaqModel.php';
        $m = new FaqModel();
        echo json_encode(['success'=>true,'items'=>$m->getActiveAll()]);
    }
    public function faqAdminGet($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->canManageFaq()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        $id = isset($_GET['id'])?(int)$_GET['id']:0;
        if($id<=0){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        require_once __DIR__.'/../models/FaqModel.php';
        $m = new FaqModel();
        $row=$m->getById($id);
        if(!$row){ echo json_encode(['success'=>false,'error'=>'not_found']); return; }
        echo json_encode(['success'=>true,'item'=>$row]);
    }
    public function faqAdminSave($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->canManageFaq()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        $id = isset($_POST['id'])?(int)$_POST['id']:0;
        $q = trim($_POST['question'] ?? '');
        $a = trim($_POST['answer'] ?? '');
        $active = isset($_POST['active']) ? (int)$_POST['active'] : 1;
        if($q===''||$a===''){ echo json_encode(['success'=>false,'error'=>'empty']); return; }
        if(strlen($q)>255){ echo json_encode(['success'=>false,'error'=>'question_too_long']); return; }
        require_once __DIR__.'/../models/FaqModel.php';
        $m = new FaqModel();
        if($id>0){
            $ok=$m->update($id,$q,$a,$active,$_SESSION['user_id']??null);
            echo json_encode(['success'=>$ok]);
        } else {
            $newId=$m->create($q,$a,$_SESSION['user_id']??null);
            echo json_encode(['success'=>true,'id'=>$newId]);
        }
    }
    public function faqAdminDelete($params = []) {
        header('Content-Type: application/json; charset=utf-8');
        if(!$this->canManageFaq()){ echo json_encode(['success'=>false,'error'=>'forbidden']); return; }
        $id = isset($_POST['id'])?(int)$_POST['id']:0;
        if($id<=0){ echo json_encode(['success'=>false,'error'=>'invalid']); return; }
        require_once __DIR__.'/../models/FaqModel.php';
        $m = new FaqModel();
        $ok=$m->delete($id);
        echo json_encode(['success'=>$ok]);
    }

    private function isAgent(){
        // Rol bazlı genişletme: superadmin, admin, coordinator yetkili kabul
        if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['superadmin','admin','coordinator'])) {
            return true;
        }
        if(!isset($_SESSION['permissions'])) return false;
        $permissions = array_map('unserialize', $_SESSION['permissions']);
        foreach($permissions as $p){
            if($p instanceof Permission){
                $name = $p->getPermissionName();
                if($name==='users.manage' || $name==='support.manage') return true;
            }
        }
        return false;
    }

    public function about($params = []) {
        $this->render('help/about', [
            'title' => 'Hakkında',
            'page_title' => 'Sistem Hakkında'
        ]);
    }
}
