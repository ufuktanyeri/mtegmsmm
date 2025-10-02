<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Models\IndicatorModel.php

require_once 'BaseModel.php';
require_once APP_PATH . 'entities/Indicator.php';

class IndicatorModel extends BaseModel {
    
    /**
     * ✅ Tüm göstergeleri getir (Admin için)
     */
    public function getAllIndicators() {
    $sql = "SELECT i.*, 
               o.objectiveTitle, 
               c.coveName,
               it.indicatorTitle AS indicatorTypeTitle,
               f.name AS fieldName
        FROM indicators i 
        LEFT JOIN objectives o ON i.objectiveId = o.id 
        LEFT JOIN coves c ON i.coveId = c.id 
        LEFT JOIN indicator_types it ON i.indicatorTypeId = it.id
        LEFT JOIN fields f ON i.fieldId = f.id 
        ORDER BY i.createdAt DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $indicators = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $indicators[] = $this->mapRowToEntity($row);
        }
        
        return $indicators;
    }
    
    /**
     * ✅ Merkeze göre göstergeleri getir
     */
    public function getIndicatorsByCoveId($coveId) {
    $sql = "SELECT i.*, o.objectiveTitle, it.indicatorTitle AS indicatorTypeTitle, f.name AS fieldName
        FROM indicators i 
        LEFT JOIN objectives o ON i.objectiveId = o.id 
        LEFT JOIN indicator_types it ON i.indicatorTypeId = it.id
        LEFT JOIN fields f ON i.fieldId = f.id 
        WHERE i.coveId = ? 
        ORDER BY i.createdAt DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$coveId]);
        
        $indicators = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $indicators[] = $this->mapRowToEntity($row);
        }
        
        return $indicators;
    }
    
    /**
     * ✅ Gösterge oluştur
     */
    public function createIndicator($data) {
    $sql = "INSERT INTO indicators 
        (indicatorTitle, indicatorDesc, targetValue, currentValue, indicatorStatus, unit, measurementPeriod, objectiveId, indicatorTypeId, fieldId, coveId, createdAt) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['indicatorTitle'],
            $data['indicatorDesc'],
            $data['targetValue'],
            $data['currentValue'],
            $data['indicatorStatus'],
            $data['unit'],
            $data['measurementPeriod'],
            $data['objectiveId'],
            $data['indicatorTypeId'],
            $data['fieldId'],
            $data['coveId']
        ]);
    }
    
    /**
     * ✅ Gösterge getir (ID ve Cove kontrolü ile)
     */
    public function getIndicatorByIdAndCoveId($id, $coveId) {
    $sql = "SELECT i.*, o.objectiveTitle, it.indicatorTitle AS indicatorTypeTitle, f.name AS fieldName
        FROM indicators i 
        LEFT JOIN objectives o ON i.objectiveId = o.id 
        LEFT JOIN indicator_types it ON i.indicatorTypeId = it.id
        LEFT JOIN fields f ON i.fieldId = f.id 
        WHERE i.id = ? AND i.coveId = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $coveId]);
        
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $this->mapRowToEntity($row) : null;
    }

    /**
     * ✅ Gösterge getir (sadece ID ile - Superadmin için)
     */
    public function getIndicatorById($id) {
        $sql = "SELECT i.*, o.objectiveTitle, it.indicatorTitle AS indicatorTypeTitle, f.name AS fieldName
            FROM indicators i 
            LEFT JOIN objectives o ON i.objectiveId = o.id 
            LEFT JOIN indicator_types it ON i.indicatorTypeId = it.id
            LEFT JOIN fields f ON i.fieldId = f.id 
            WHERE i.id = ?";
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }
    
    /**
     * ✅ Gösterge güncelle
     */
    public function updateIndicator($id, $data) {
        $sql = "UPDATE indicators 
                SET indicatorTitle = ?, indicatorDesc = ?, targetValue = ?, currentValue = ?, indicatorStatus = ?,
                    unit = ?, measurementPeriod = ?, objectiveId = ?, indicatorTypeId = ?, fieldId = ?, updatedAt = NOW() 
                WHERE id = ? AND coveId = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['indicatorTitle'],
            $data['indicatorDesc'],
            $data['targetValue'],
            $data['currentValue'],
            $data['indicatorStatus'],
            $data['unit'],
            $data['measurementPeriod'],
            $data['objectiveId'],
            $data['indicatorTypeId'],
            $data['fieldId'],
            $id,
            $data['coveId']
        ]);
    }
    
    /**
     * ✅ Gösterge sil (coveId kontrolü ile)
     */
    public function deleteIndicator($id, $coveId = null) {
        if ($coveId !== null) {
            // Normal kullanıcı - cove kontrolü ile
            $sql = "DELETE FROM indicators WHERE id = ? AND coveId = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id, $coveId]);
        } else {
            // Superadmin - tüm göstergeleri silebilir
            $sql = "DELETE FROM indicators WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        }
    }
    
    /**
     * ✅ Gösterge değeri güncelle
     */
    public function updateIndicatorValue($id, $currentValue) {
        $sql = "UPDATE indicators SET currentValue = ?, updatedAt = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$currentValue, $id]);
    }
    
    /**
     * ✅ Gösterge istatistikleri
     */
    public function getIndicatorStatistics($coveId) {
        $sql = "SELECT 
                    COUNT(*) as totalIndicators,
                    SUM(CASE WHEN currentValue >= targetValue THEN 1 ELSE 0 END) as completedIndicators,
                    AVG(CASE WHEN targetValue > 0 THEN (currentValue / targetValue) * 100 ELSE 0 END) as averagePerformance
                FROM indicators 
                WHERE coveId = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$coveId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * ✅ Kullanıcının merkez ID'sini getir
     */
    public function getCoveIdByUserId($userId) {
        $sql = "SELECT coveId FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * ✅ Tablo oluştur (yoksa)
     */
    public function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS indicators (
            id INT AUTO_INCREMENT PRIMARY KEY,
            indicatorTitle VARCHAR(255) NOT NULL,
            indicatorDesc TEXT,
            targetValue DECIMAL(10,2) NOT NULL DEFAULT 0,
            currentValue DECIMAL(10,2) NOT NULL DEFAULT 0,
            indicatorStatus TINYINT(1) NOT NULL DEFAULT 0,
            unit VARCHAR(50),
            measurementPeriod VARCHAR(50),
            objectiveId INT,
            indicatorTypeId INT NULL,
            fieldId INT NULL,
            coveId INT NOT NULL,
            createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
            updatedAt DATETIME NULL,
            INDEX idx_cove_id (coveId),
            INDEX idx_objective_id (objectiveId),
            INDEX idx_indicator_type (indicatorTypeId),
            INDEX idx_field (fieldId),
            FOREIGN KEY (objectiveId) REFERENCES objectives(id) ON DELETE SET NULL,
            FOREIGN KEY (coveId) REFERENCES coves(id) ON DELETE CASCADE,
            FOREIGN KEY (indicatorTypeId) REFERENCES indicator_types(id) ON DELETE SET NULL,
            FOREIGN KEY (fieldId) REFERENCES fields(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->pdo->exec($sql);

        // Ek kolon yoksa ekle (eski tablolar için göç)
        $this->ensureAdditionalColumns();
    }
    
    /**
     * ✅ Constructor'da tablo oluştur
     */
    public function __construct() {
        parent::__construct();
        $this->createTableIfNotExists();
    }

    private function ensureAdditionalColumns() {
        try {
            $columns = [];
            $stmt = $this->pdo->query("SHOW COLUMNS FROM indicators");
            while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) { $columns[] = $c['Field']; }
            $alterSql = [];
            if (!in_array('indicatorStatus',$columns)) {
                $alterSql[] = 'ADD COLUMN indicatorStatus TINYINT(1) NOT NULL DEFAULT 0 AFTER currentValue';
            }
            if (!in_array('indicatorTypeId',$columns)) {
                $alterSql[] = 'ADD COLUMN indicatorTypeId INT NULL AFTER objectiveId';
            }
            if (!in_array('fieldId',$columns)) {
                $alterSql[] = 'ADD COLUMN fieldId INT NULL AFTER indicatorTypeId';
            }
            if ($alterSql) {
                $this->pdo->exec('ALTER TABLE indicators ' . implode(', ', $alterSql));
            }
        } catch (Exception $e) {
            error_log('ensureAdditionalColumns error: '.$e->getMessage());
        }
    }

    /**
     * Satır -> Indicator entity eşleme (eski constructor imzasına uygun)
     */
    private function mapRowToEntity(array $row) {
        return new Indicator(
            $row['id'] ?? null,
            $row['objectiveId'] ?? null,
            $row['indicatorTypeId'] ?? null,
            $row['indicatorTitle'] ?? null,
            $row['indicatorDesc'] ?? null,
            $row['targetValue'] ?? $row['target'] ?? 0,
            $row['currentValue'] ?? $row['completed'] ?? 0,
            $row['indicatorStatus'] ?? 0,
            $row['fieldId'] ?? null,
            $row['coveId'] ?? null,
            $row['createdAt'] ?? date('Y-m-d H:i:s'),
            $row['objectiveTitle'] ?? null,
            $row['indicatorTypeTitle'] ?? null,
            $row['fieldName'] ?? null,
            $row['coveName'] ?? null
        );
    }
}
?>
