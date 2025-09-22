<?php

require_once 'BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../entities/Regulation.php';

class RegulationModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllRegulations() {
        $this->db->query("SELECT rg.id, rg.regulationDesc, rg.regulationSource, rg.regulationSourceNo, rg.ownerCoveId, coves.name as ownerCoveName FROM regulations as rg Left Join coves on rg.ownerCoveId = coves.id");
        $data = $this->db->resultSet();
        $regulations = [];
        foreach ($data as $row) {
            $regulations[] = new Regulation($row['id'], $row['regulationDesc'], $row['regulationSource'], $row['regulationSourceNo'], $row['ownerCoveId'], $row['ownerCoveName']);
        }
       
        return $regulations;
    }
 

    public function getRegulationsByCoveId($coveId) {
        $this->db->query("SELECT * FROM regulations WHERE ownerCoveId = 0 OR ownerCoveId = ?", [$coveId]);
        $data = $this->db->resultSet();
        $regulations = [];
        foreach ($data as $row) {
            $regulations[] = new Regulation($row['id'], $row['regulationDesc'], $row['regulationSource'], $row['regulationSourceNo'], $row['ownerCoveId']);
        }
        return $regulations;
    }

    public function getCoveIdByUserId($userId) {
        $this->db->query("SELECT coveId FROM cove_users WHERE userId = ?", [$userId]);
        $row = $this->db->single();
        return $row ? $row['coveId'] : null;
    }

    public function getRegulationById($id) {
        $this->db->query("SELECT * FROM regulations WHERE id = ?", [$id]);
        $row = $this->db->single();
        if ($row) {
            return new Regulation($row['id'], $row['regulationDesc'], $row['regulationSource'], $row['regulationSourceNo'], $row['ownerCoveId']);
        }
        return null;
    }

    public function getRegulationByIdByCove($id, $coveId) {
        $this->db->query("SELECT * FROM regulations WHERE id = ? AND ownerCoveId = ?", [$id, $coveId]);
        $row = $this->db->single();
        if ($row) {
            return new Regulation($row['id'], $row['regulationDesc'], $row['regulationSource'], $row['regulationSourceNo'], $row['ownerCoveId']);
        }
        return null;
    }

    public function createRegulation($regulationDesc, $regulationSource, $regulationSourceNo, $coveId) {
        $this->db->query("INSERT INTO regulations (regulationDesc, regulationSource, regulationSourceNo, ownerCoveId) VALUES (?, ?, ?, ?)", [$regulationDesc, $regulationSource, $regulationSourceNo, $coveId]);
        return $this->db->lastInsertId();
    }

    public function updateRegulation($id, $regulationDesc, $regulationSource, $regulationSourceNo, $coveId) {
        $this->db->query("UPDATE regulations SET regulationDesc = ?, regulationSource = ?, regulationSourceNo = ?, ownerCoveId = ? WHERE id = ?", [$regulationDesc, $regulationSource, $regulationSourceNo, $coveId, $id]);
    }

    public function deleteRegulation($id) {
        $this->db->query("DELETE FROM regulations WHERE id = ?", [$id]);
    }
}
?>
