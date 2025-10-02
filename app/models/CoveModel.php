<?php

require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Cove.php';
require_once APP_PATH . 'models/FieldModel.php';

class CoveModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCoves() {
        $this->db->query("SELECT * FROM coves");
        $data = $this->db->resultSet();
        $coves = [];
        foreach ($data as $row) {
            $coves[] = new Cove($row['id'], $row['name'], $row['city'], $row['district'], $row['address']);
        }
        return $coves;
    }

    public function getCoveById($id) {
        $this->db->query("SELECT * FROM coves WHERE id = ?", [$id]);
        $data = $this->db->single();
        if ($data) {
            $cove = new Cove($data['id'], $data['name'], $data['city'], $data['district'], $data['address']);
            
            // Fields bilgisini de ekleyelim
            $fieldModel = new FieldModel();
            $cove->setFields($fieldModel->getFieldsByCoveId($data['id']));
            
            return $cove;
        }
        return null;
    }

    public function createCove($name, $city, $district, $address) {
        $this->db->query("INSERT INTO coves (name, city, district, address) VALUES (?, ?, ?, ?)", [$name, $city, $district, $address]);
        return $this->db->lastInsertId();
    }

    public function updateCove($id, $name, $city, $district, $address) {
        $this->db->query("UPDATE coves SET name = ?, city = ?, district = ?, address = ? WHERE id = ?", [$name, $city, $district, $address, $id]);
    }

    public function deleteCove($id) {
        $this->db->query("DELETE FROM coves WHERE id = ?", [$id]);
    }

    public function getFieldsByCoveId($coveId) {
        $this->db->query("SELECT fieldId FROM cove_fields WHERE coveId = ?", [$coveId]);
        $fieldData = $this->db->resultSet();
        return array_column($fieldData, 'fieldId');
    }

    public function updateCoveFields($coveId, $fieldIds) {
        // Delete existing relationships
        $this->db->query("DELETE FROM cove_fields WHERE coveId = ?", [$coveId]);

        // Insert new relationships
        if (!empty($fieldIds)) {
            foreach ($fieldIds as $fieldId) {
                $this->db->query("INSERT INTO cove_fields (coveId, fieldId) VALUES (?, ?)", [$coveId, $fieldId]);
            }
        }
    }

    public function getCoveByUserId($userId) {
        $this->db->query("SELECT c.id, c.name, c.city, c.district, c.address FROM coves c
                              JOIN cove_users uc ON c.id = uc.coveId
                              WHERE uc.userId = ?", [$userId]);
      
        $coveData = $this->db->single();
        $cove = null;
        $fieldModel = new FieldModel();

        if ($coveData) {
            $cove = new Cove(
                $coveData['id'],
                $coveData['name'],
                $coveData['city'],
                $coveData['district'],
                $coveData['address']
            );
            $cove->setFields($fieldModel->getFieldsByCoveId($coveData['id']));
        }
        
        return $cove;
    }

    // Kullanıcı sayısını getir
    public function getCoveUserCount($coveId) {
        $this->db->query("SELECT COUNT(*) as count FROM cove_users WHERE coveId = ?", [$coveId]);
        $result = $this->db->single();
        return $result ? $result['count'] : 0;
    }

    // Merkeze kullanıcı ata
    public function assignUserToCove($userId, $coveId) {
        // Önce mevcut atamaları kontrol et
        $this->db->query("SELECT * FROM cove_users WHERE userId = ?", [$userId]);
        $existing = $this->db->single();
        
        if ($existing) {
            // Güncelle
            $this->db->query("UPDATE cove_users SET coveId = ? WHERE userId = ?", [$coveId, $userId]);
        } else {
            // Yeni ekleme
            $this->db->query("INSERT INTO cove_users (userId, coveId) VALUES (?, ?)", [$userId, $coveId]);
        }
    }

    // Kullanıcıyı merkezden kaldır
    public function removeUserFromCove($userId) {
        $this->db->query("DELETE FROM cove_users WHERE userId = ?", [$userId]);
    }

    // Merkez bazında kullanıcıları getir
    public function getUsersByCoveId($coveId) {
        $this->db->query("SELECT u.id, u.username, u.email FROM users u
                          JOIN cove_users cu ON u.id = cu.userId
                          WHERE cu.coveId = ?", [$coveId]);
        return $this->db->resultSet();
    }

    // Merkez istatistikleri için
    public function getCoveStats($coveId) {
        $stats = [];
        
        // Kullanıcı sayısı
        $stats['user_count'] = $this->getCoveUserCount($coveId);
        
        // Amaç sayısı
        $this->db->query("SELECT COUNT(*) as count FROM aims WHERE coveId = ?", [$coveId]);
        $result = $this->db->single();
        $stats['aim_count'] = $result ? $result['count'] : 0;
        
        // Hedef sayısı
        $this->db->query("SELECT COUNT(*) as count FROM objectives o
                          JOIN aims a ON o.aimId = a.id 
                          WHERE a.coveId = ?", [$coveId]);
        $result = $this->db->single();
        $stats['objective_count'] = $result ? $result['count'] : 0;
        
        // Faaliyet sayısı
        $this->db->query("SELECT COUNT(*) as count FROM actions ac
                          JOIN objectives o ON ac.objectiveId = o.id
                          JOIN aims a ON o.aimId = a.id 
                          WHERE a.coveId = ?", [$coveId]);
        $result = $this->db->single();
        $stats['action_count'] = $result ? $result['count'] : 0;
        
        return $stats;
    }

    // Aktif merkez sayısı
    public function getActiveCoveCount() {
        $this->db->query("SELECT COUNT(DISTINCT c.id) as count FROM coves c
                          JOIN cove_users cu ON c.id = cu.coveId");
        $result = $this->db->single();
        return $result ? $result['count'] : 0;
    }

    // Boş merkezler (kullanıcısı olmayan)
    public function getEmptyCoves() {
        $this->db->query("SELECT c.* FROM coves c
                          LEFT JOIN cove_users cu ON c.id = cu.coveId
                          WHERE cu.coveId IS NULL");
        $data = $this->db->resultSet();
        $coves = [];
        foreach ($data as $row) {
            $coves[] = new Cove($row['id'], $row['name'], $row['city'], $row['district'], $row['address']);
        }
        return $coves;
    }

    // Merkez arama
    public function searchCoves($searchTerm) {
        $searchTerm = '%' . $searchTerm . '%';
        $this->db->query("SELECT * FROM coves WHERE name LIKE ? OR city LIKE ? OR district LIKE ?", 
                         [$searchTerm, $searchTerm, $searchTerm]);
        $data = $this->db->resultSet();
        $coves = [];
        foreach ($data as $row) {
            $coves[] = new Cove($row['id'], $row['name'], $row['city'], $row['district'], $row['address']);
        }
        return $coves;
    }

    // Şehir bazında merkezler
    public function getCovesByCity($city) {
        $this->db->query("SELECT * FROM coves WHERE city = ? ORDER BY district, name", [$city]);
        $data = $this->db->resultSet();
        $coves = [];
        foreach ($data as $row) {
            $coves[] = new Cove($row['id'], $row['name'], $row['city'], $row['district'], $row['address']);
        }
        return $coves;
    }

    // Tüm şehirleri getir
    public function getAllCities() {
        $this->db->query("SELECT DISTINCT city FROM coves WHERE city IS NOT NULL ORDER BY city");
        $data = $this->db->resultSet();
        return array_column($data, 'city');
    }

    // Şehir bazında ilçeleri getir
    public function getDistrictsByCity($city) {
        $this->db->query("SELECT DISTINCT district FROM coves WHERE city = ? AND district IS NOT NULL ORDER BY district", [$city]);
        $data = $this->db->resultSet();
        return array_column($data, 'district');
    }
}
?>
