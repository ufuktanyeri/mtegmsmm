<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Entities\Objective.php

class Objective {
    public $id;
    public $objectiveTitle;
    public $objectiveDesc;
    public $objectiveResult; // ✅ EKLENDİ: Raporlarda kullanılan sonuç alanı
    public $aimId;
    public $coveId;
    public $createdAt;
    public $updatedAt;
    
    // ✅ İlişkili veriler için
    public $aimTitle;
    public $coveName;
    public $actionCount;
    
    // ✅ İlişkili objeler
    private $aims = [];
    private $actions = [];

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->objectiveTitle = $data['objectiveTitle'] ?? '';
            $this->objectiveDesc = $data['objectiveDesc'] ?? '';
            $this->objectiveResult = $data['objectiveResult'] ?? '';
            $this->aimId = $data['aimId'] ?? null;
            $this->coveId = $data['coveId'] ?? null;
            $this->createdAt = $data['createdAt'] ?? null;
            $this->updatedAt = $data['updatedAt'] ?? null;
            
            // İlişkili veriler
            $this->aimTitle = $data['aimTitle'] ?? '';
            $this->coveName = $data['coveName'] ?? '';
            $this->actionCount = $data['actionCount'] ?? 0;
        }
    }

    /**
     * ✅ GETTER METHODS - EKSİK OLAN METHODLAR
     */
    
    /**
     * ✅ ID getter
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * ✅ Objective Title getter - EKSİK OLAN METHOD
     */
    public function getObjectiveTitle() {
        return $this->objectiveTitle;
    }
    
    /**
     * ✅ Objective Description getter
     */
    public function getObjectiveDesc() {
        return $this->objectiveDesc;
    }

    public function getObjectiveResult() {
        return $this->objectiveResult;
    }
    
    /**
     * ✅ Aim ID getter
     */
    public function getAimId() {
        return $this->aimId;
    }
    
    /**
     * ✅ Cove ID getter
     */
    public function getCoveId() {
        return $this->coveId;
    }
    
    /**
     * ✅ Created At getter
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    /**
     * ✅ Updated At getter
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    /**
     * ✅ Aim Title getter
     */
    public function getAimTitle() {
        return $this->aimTitle;
    }
    
    /**
     * ✅ Cove Name getter
     */
    public function getCoveName() {
        return $this->coveName;
    }

    /**
     * ✅ SETTER METHODS
     */
    
    /**
     * ✅ Objective Title setter
     */
    public function setObjectiveTitle($title) {
        $this->objectiveTitle = $title;
    }
    
    /**
     * ✅ Objective Description setter
     */
    public function setObjectiveDesc($desc) {
        $this->objectiveDesc = $desc;
    }

    public function setObjectiveResult($result) {
        $this->objectiveResult = $result;
    }
    
    /**
     * ✅ Aim ID setter
     */
    public function setAimId($aimId) {
        $this->aimId = $aimId;
    }
    
    /**
     * ✅ Cove ID setter
     */
    public function setCoveId($coveId) {
        $this->coveId = $coveId;
    }

    /**
     * ✅ Aims listesini set et
     */
    public function setAims($aims) {
        $this->aims = $aims;
    }

    /**
     * ✅ Aims listesini getir
     */
    public function getAims() {
        return $this->aims;
    }

    /**
     * ✅ Actions listesini set et
     */
    public function setActions($actions) {
        $this->actions = $actions;
    }

    /**
     * ✅ Actions listesini getir
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * ✅ Belirli bir aim'e ait mi kontrol et
     */
    public function belongsToAim($aimId) {
        return $this->aimId == $aimId;
    }

    /**
     * ✅ Belirli bir cove'a ait mi kontrol et
     */
    public function belongsToCove($coveId) {
        return $this->coveId == $coveId;
    }

    /**
     * ✅ Objective'in aktif olup olmadığını kontrol et
     */
    public function isActive() {
        return !empty($this->id) && !empty($this->objectiveTitle);
    }

    /**
     * ✅ Objective'in action sayısını getir
     */
    public function getActionCount() {
        if (isset($this->actionCount)) {
            return $this->actionCount;
        }
        return count($this->actions);
    }

    /**
     * ✅ Kısa açıklama getir (karakter sınırı ile)
     */
    public function getShortDescription($limit = 100) {
        if (strlen($this->objectiveDesc) <= $limit) {
            return $this->objectiveDesc;
        }
        return substr($this->objectiveDesc, 0, $limit) . '...';
    }

    /**
     * ✅ Formatted created date
     */
    public function getFormattedCreatedAt($format = 'd.m.Y H:i') {
        if ($this->createdAt) {
            return date($format, strtotime($this->createdAt));
        }
        return '';
    }

    /**
     * ✅ Formatted updated date
     */
    public function getFormattedUpdatedAt($format = 'd.m.Y H:i') {
        if ($this->updatedAt) {
            return date($format, strtotime($this->updatedAt));
        }
        return '';
    }

    /**
     * ✅ Objective'in tamamlanma durumunu kontrol et
     */
    public function isCompleted() {
        // Bu logic action'lara bağlı olarak geliştirilebilir
        return false;
    }

    /**
     * ✅ Progress yüzdesi hesapla
     */
    public function getProgressPercentage() {
        if (empty($this->actions)) {
            return 0;
        }
        
        $completedActions = 0;
        foreach ($this->actions as $action) {
            if (isset($action->actionStatus) && $action->actionStatus == 1) {
                $completedActions++;
            }
        }
        
        return count($this->actions) > 0 ? round(($completedActions / count($this->actions)) * 100, 2) : 0;
    }

    /**
     * ✅ HTML-safe title getir
     */
    public function getSafeTitle() {
        return htmlspecialchars($this->objectiveTitle);
    }

    /**
     * ✅ HTML-safe description getir
     */
    public function getSafeDescription() {
        return htmlspecialchars($this->objectiveDesc);
    }

    /**
     * ✅ Array'e çevir
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'objectiveTitle' => $this->objectiveTitle,
            'objectiveDesc' => $this->objectiveDesc,
            'objectiveResult' => $this->objectiveResult,
            'aimId' => $this->aimId,
            'coveId' => $this->coveId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'aimTitle' => $this->aimTitle,
            'coveName' => $this->coveName,
            'actionCount' => $this->getActionCount(),
            'isActive' => $this->isActive(),
            'progressPercentage' => $this->getProgressPercentage()
        ];
    }

    /**
     * ✅ JSON'a çevir
     */
    public function toJson() {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * ✅ String representation
     */
    public function __toString() {
        return $this->objectiveTitle ?? 'Unnamed Objective';
    }

    /**
     * ✅ Magic getter method - property yoksa getter methodunu çağır
     */
    public function __get($property) {
        $getter = 'get' . ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        return null;
    }

    /**
     * ✅ Magic isset method
     */
    public function __isset($property) {
        $getter = 'get' . ucfirst($property);
        return method_exists($this, $getter) || property_exists($this, $property);
    }

    /**
     * ✅ Validation - Objective valid mi kontrol et
     */
    public function isValid() {
        return !empty($this->objectiveTitle) && 
               !empty($this->aimId) && 
               !empty($this->coveId);
    }

    /**
     * ✅ Validation errors listesi
     */
    public function getValidationErrors() {
        $errors = [];
        
        if (empty($this->objectiveTitle)) {
            $errors[] = 'Hedef başlığı gereklidir';
        }
        
        if (empty($this->aimId)) {
            $errors[] = 'Amaç seçimi gereklidir';
        }
        
        if (empty($this->coveId)) {
            $errors[] = 'Merkez bilgisi gereklidir';
        }
        
        if (strlen($this->objectiveTitle) > 255) {
            $errors[] = 'Hedef başlığı 255 karakterden uzun olamaz';
        }
        
        return $errors;
    }
}
?>