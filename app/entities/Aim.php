<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Entities\Aim.php

class Aim {
    public $id;                    
    public $aimTitle;              
    public $aimDesc;               
    public $aimResult;             // ✅ EKLENDİ: Raporlarda kullanılan sonuç alanı
    public $coveId;                
    public $createdAt;             
    public $updatedAt;             
    
    // ✅ İlişkili veriler için
    public $coveName;
    public $objectiveCount;
    public $actionCount;
    
    // ✅ İlişkili objeler
    private $objectives = [];
    private $actions = [];
    private $regulations = [];  // ✅ EKLENDİ: Regulations için

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->aimTitle = $data['aimTitle'] ?? '';
            $this->aimDesc = $data['aimDesc'] ?? '';
            $this->aimResult = $data['aimResult'] ?? '';
            $this->coveId = $data['coveId'] ?? null;
            $this->createdAt = $data['createdAt'] ?? null;
            $this->updatedAt = $data['updatedAt'] ?? null;
            
            // İlişkili veriler
            $this->coveName = $data['coveName'] ?? '';
            $this->objectiveCount = $data['objectiveCount'] ?? 0;
            $this->actionCount = $data['actionCount'] ?? 0;
        }
    }

    /**
     * ✅ GETTER METHODS
     */
    
    public function getId() {
        return $this->id;
    }
    
    public function getAimTitle() {
        return $this->aimTitle;
    }
    
    public function getAimDesc() {
        return $this->aimDesc;
    }

    public function getAimResult() {
        return $this->aimResult;
    }
    
    public function getCoveId() {
        return $this->coveId;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    public function getCoveName() {
        return $this->coveName;
    }

    /**
     * ✅ SETTER METHODS
     */
    
    public function setAimTitle($title) {
        $this->aimTitle = $title;
    }
    
    public function setAimDesc($desc) {
        $this->aimDesc = $desc;
    }

    public function setAimResult($result) {
        $this->aimResult = $result;
    }
    
    public function setCoveId($coveId) {
        $this->coveId = $coveId;
    }

    /**
     * ✅ Objectives Methods
     */
    public function setObjectives($objectives) {
        $this->objectives = $objectives;
    }

    public function getObjectives() {
        return $this->objectives;
    }

    /**
     * ✅ Actions Methods
     */
    public function setActions($actions) {
        $this->actions = $actions;
    }

    public function getActions() {
        return $this->actions;
    }

    /**
     * ✅ REGULATIONS METHODS - EKSİK OLAN METHODLAR
     */
    
    /**
     * ✅ Regulations listesini set et - EKSİK METHOD
     */
    public function setRegulations($regulations) {
        $this->regulations = $regulations;
    }

    /**
     * ✅ Regulations listesini getir
     */
    public function getRegulations() {
        return $this->regulations;
    }

    /**
     * ✅ Regulation ekle
     */
    public function addRegulation($regulation) {
        $this->regulations[] = $regulation;
    }

    /**
     * ✅ Regulation kaldır
     */
    public function removeRegulation($regulationId) {
        $this->regulations = array_filter($this->regulations, function($reg) use ($regulationId) {
            return (isset($reg->id) ? $reg->id : $reg['id']) != $regulationId;
        });
    }

    /**
     * ✅ Belirli regulation'ı getir
     */
    public function getRegulationById($regulationId) {
        foreach ($this->regulations as $regulation) {
            $regId = isset($regulation->id) ? $regulation->id : $regulation['id'];
            if ($regId == $regulationId) {
                return $regulation;
            }
        }
        return null;
    }

    /**
     * ✅ Regulation sayısını getir
     */
    public function getRegulationCount() {
        return count($this->regulations);
    }

    /**
     * ✅ Aktif regulation'ları getir
     */
    public function getActiveRegulations() {
        return array_filter($this->regulations, function($reg) {
            if (is_object($reg) && method_exists($reg, 'isActive')) {
                return $reg->isActive();
            }
            return isset($reg['status']) && $reg['status'] == 1;
        });
    }

    /**
     * ✅ EXISTING METHODS - DEĞİŞMEDİ
     */
    
    public function belongsToCove($coveId) {
        return $this->coveId == $coveId;
    }

    public function isActive() {
        return !empty($this->id) && !empty($this->aimTitle);
    }

    public function getObjectiveCount() {
        if (isset($this->objectiveCount)) {
            return $this->objectiveCount;
        }
        return count($this->objectives);
    }

    public function getActionCount() {
        if (isset($this->actionCount)) {
            return $this->actionCount;
        }
        return count($this->actions);
    }

    public function getShortDescription($limit = 100) {
        if (strlen($this->aimDesc) <= $limit) {
            return $this->aimDesc;
        }
        return substr($this->aimDesc, 0, $limit) . '...';
    }

    public function getFormattedCreatedAt($format = 'd.m.Y H:i') {
        if ($this->createdAt) {
            return date($format, strtotime($this->createdAt));
        }
        return '';
    }

    public function getFormattedUpdatedAt($format = 'd.m.Y H:i') {
        if ($this->updatedAt) {
            return date($format, strtotime($this->updatedAt));
        }
        return '';
    }

    public function isCompleted() {
        if (empty($this->objectives)) {
            return false;
        }
        
        foreach ($this->objectives as $objective) {
            if (is_object($objective) && method_exists($objective, 'isCompleted')) {
                if (!$objective->isCompleted()) {
                    return false;
                }
            }
        }
        
        return true;
    }

    public function getProgressPercentage() {
        if (empty($this->objectives)) {
            return 0;
        }
        
        $totalProgress = 0;
        $validObjectives = 0;
        
        foreach ($this->objectives as $objective) {
            if (is_object($objective) && method_exists($objective, 'getProgressPercentage')) {
                $totalProgress += $objective->getProgressPercentage();
                $validObjectives++;
            }
        }
        
        return $validObjectives > 0 ? round($totalProgress / $validObjectives, 2) : 0;
    }

    public function getSafeTitle() {
        return htmlspecialchars($this->aimTitle);
    }

    public function getSafeDescription() {
        return htmlspecialchars($this->aimDesc);
    }

    public function getStatusClass() {
        $percentage = $this->getProgressPercentage();
        
        if ($percentage >= 100) return 'success';
        if ($percentage >= 75) return 'warning';
        if ($percentage >= 50) return 'info';
        return 'danger';
    }

    public function getStatusText() {
        $percentage = $this->getProgressPercentage();
        
        if ($percentage >= 100) return 'Tamamlandı';
        if ($percentage >= 75) return 'İyi gidiyor';
        if ($percentage >= 50) return 'Orta seviye';
        if ($percentage > 0) return 'Başlangıç seviyesi';
        return 'Henüz başlanmadı';
    }

    /**
     * ✅ Array'e çevir - GÜNCELLEME
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'aimTitle' => $this->aimTitle,
            'aimDesc' => $this->aimDesc,
            'aimResult' => $this->aimResult,
            'coveId' => $this->coveId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'coveName' => $this->coveName,
            'objectiveCount' => $this->getObjectiveCount(),
            'actionCount' => $this->getActionCount(),
            'regulationCount' => $this->getRegulationCount(),  // ✅ EKLENDİ
            'isActive' => $this->isActive(),
            'progressPercentage' => $this->getProgressPercentage(),
            'statusClass' => $this->getStatusClass(),
            'statusText' => $this->getStatusText()
        ];
    }

    public function toJson() {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function __toString() {
        return $this->aimTitle ?? 'Unnamed Aim';
    }

    /**
     * ✅ Magic getter method - GÜNCELLEME
     */
    public function __get($property) {
        $getter = 'get' . ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
        // Direct property access için
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        
        return null;
    }

    public function __isset($property) {
        $getter = 'get' . ucfirst($property);
        return method_exists($this, $getter) || property_exists($this, $property);
    }

    public function isValid() {
        return !empty($this->aimTitle) && 
               !empty($this->coveId);
    }

    public function getValidationErrors() {
        $errors = [];
        
        if (empty($this->aimTitle)) {
            $errors[] = 'Amaç başlığı gereklidir';
        }
        
        if (empty($this->coveId)) {
            $errors[] = 'Merkez bilgisi gereklidir';
        }
        
        if (strlen($this->aimTitle) > 255) {
            $errors[] = 'Amaç başlığı 255 karakterden uzun olamaz';
        }
        
        return $errors;
    }

    /**
     * ✅ OBJECTIVE METHODS
     */
    public function addObjective($objective) {
        $this->objectives[] = $objective;
    }

    public function removeObjective($objectiveId) {
        $this->objectives = array_filter($this->objectives, function($obj) use ($objectiveId) {
            $objId = is_object($obj) ? $obj->id : $obj['id'];
            return $objId != $objectiveId;
        });
    }

    public function getObjectiveById($objectiveId) {
        foreach ($this->objectives as $objective) {
            $objId = is_object($objective) ? $objective->id : $objective['id'];
            if ($objId == $objectiveId) {
                return $objective;
            }
        }
        return null;
    }

    public function getActiveObjectives() {
        return array_filter($this->objectives, function($obj) {
            if (is_object($obj) && method_exists($obj, 'isActive')) {
                return $obj->isActive();
            }
            return isset($obj['status']) && $obj['status'] == 1;
        });
    }

    public function getCompletedObjectives() {
        return array_filter($this->objectives, function($obj) {
            if (is_object($obj) && method_exists($obj, 'isCompleted')) {
                return $obj->isCompleted();
            }
            return isset($obj['completed']) && $obj['completed'] == 1;
        });
    }

    /**
     * ✅ BULK OPERATIONS
     */
    
    /**
     * ✅ Tüm ilişkili verileri set et
     */
    public function setRelatedData($objectives = [], $actions = [], $regulations = []) {
        $this->setObjectives($objectives);
        $this->setActions($actions);
        $this->setRegulations($regulations);
    }

    /**
     * ✅ Tüm ilişkili verileri getir
     */
    public function getRelatedData() {
        return [
            'objectives' => $this->getObjectives(),
            'actions' => $this->getActions(),
            'regulations' => $this->getRegulations()
        ];
    }

    /**
     * ✅ İstatistikleri getir
     */
    public function getStatistics() {
        return [
            'totalObjectives' => $this->getObjectiveCount(),
            'activeObjectives' => count($this->getActiveObjectives()),
            'completedObjectives' => count($this->getCompletedObjectives()),
            'totalActions' => $this->getActionCount(),
            'totalRegulations' => $this->getRegulationCount(),
            'activeRegulations' => count($this->getActiveRegulations()),
            'progressPercentage' => $this->getProgressPercentage(),
            'statusText' => $this->getStatusText()
        ];
    }
}
?>