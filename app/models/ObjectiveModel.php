<?php

require_once 'BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../entities/Objective.php';

class ObjectiveModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDb() {
        return $this->db;
    }

    public function getAllObjectives() {
        $this->db->query("SELECT * FROM objectives");
        $data = $this->db->resultSet();
        $objectives = [];
        foreach ($data as $row) {
            $objectives[] = new Objective($row); // array-based constructor
        }
        return $objectives;
    }

    public function getObjectiveByIdByCove($id, $coveId) {
        $this->db->query("SELECT * FROM objectives WHERE id = ? AND coveId = ?", [$id, $coveId]);
        $row = $this->db->single();
        if ($row) {
            return new Objective($row);
        }
        return null;
    }

    public function getCoveIdByUserId($userId) {
        $this->db->query("SELECT coveId FROM cove_users WHERE userId = ?", [$userId]);
        $row = $this->db->single();
        return $row ? $row['coveId'] : null;
    }

    // Eski imza ile uyumlu + Controller'da array gönderimini destekle
    public function createObjective($objectiveTitle, $objectiveDesc = null, $coveId = null, $aimId = null, $objectiveResult = null) {
        if (is_array($objectiveTitle)) { // array formu
            $data = $objectiveTitle;
            $objectiveTitle   = $data['objectiveTitle'] ?? '';
            $objectiveDesc    = $data['objectiveDesc'] ?? null;
            $aimId            = $data['aimId'] ?? null;
            $coveId           = $data['coveId'] ?? null;
            $objectiveResult  = $data['objectiveResult'] ?? null;
        }
        $this->db->query(
            "INSERT INTO objectives (objectiveTitle, objectiveDesc, coveId, createdAt, aimId, objectiveResult) VALUES (?, ?, ?, NOW(), ?, ?)",
            [$objectiveTitle, $objectiveDesc, $coveId, $aimId, $objectiveResult]
        );
        return $this->db->lastInsertId();
    }

    public function updateObjective($id, $objectiveTitle, $objectiveDesc = null, $coveId = null, $objectiveResult = null) {
        if (is_array($objectiveTitle)) { // array formu
            $data = $objectiveTitle;
            $objectiveTitle   = $data['objectiveTitle'] ?? '';
            $objectiveDesc    = $data['objectiveDesc'] ?? null;
            $coveId           = $data['coveId'] ?? null;
            $objectiveResult  = $data['objectiveResult'] ?? null;
        }
        $this->db->query(
            "UPDATE objectives SET objectiveTitle = ?, objectiveDesc = ?, coveId = ?, objectiveResult = ? WHERE id = ?",
            [$objectiveTitle, $objectiveDesc, $coveId, $objectiveResult, $id]
        );
        return true;
    }

    public function deleteObjective($id) {
        $this->db->query("DELETE FROM objectives WHERE id = ?", [$id]);
    }

    public function getAimsByObjectiveId($objectiveId) {
        $this->db->query("SELECT aims.id, aims.aimTitle, aims.coveId FROM aims
        JOIN aims_objectives ao ON aims.id = ao.aimId
        WHERE ao.objectiveId = ?", [$objectiveId]);
        return $this->db->resultSet();
    }

    public function getObjectivesByCoveId($coveId) {
        $this->db->query("SELECT * FROM objectives WHERE coveId = ?", [$coveId]);
        $data = $this->db->resultSet();
        $objectives = [];
        foreach ($data as $row) {
            $objectives[] = new Objective($row);
        }
        return $objectives;
    }

    public function getObjectivesByAimId($aimId, $coveId) {
        $this->db->query("SELECT o.* FROM objectives o                         
                          WHERE o.aimId = ? AND o.coveId = ?", [$aimId, $coveId]);
        $data = $this->db->resultSet();
        $objectives = [];
        foreach ($data as $row) {
            $objectives[] = new Objective($row);
        }
        return $objectives;
    }

    // Controller'da kullanılan fakat mevcut olmayan metodlar için alias'lar
    public function getObjectiveByIdAndCoveId($id, $coveId) {
        return $this->getObjectiveByIdByCove($id, $coveId);
    }

    public function getObjectivesByAimIdAndCoveId($aimId, $coveId) {
        return $this->getObjectivesByAimId($aimId, $coveId);
    }
}
?>
