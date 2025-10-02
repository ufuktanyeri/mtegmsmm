<?php

require_once 'BaseModel.php';
require_once 'ObjectiveModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Aim.php';

class AimModel extends BaseModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getAllAims()
    {
        $this->db->query("SELECT * FROM aims");
        $rows = $this->db->resultSet();
        $aims = [];
        foreach ($rows as $row) {
            $aim = new Aim($row); // associative array constructor
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aims[] = $aim;
        }
        return $aims;
    }

    public function getAllAimsWithObjAndReg()
    {
        $this->db->query("SELECT * FROM aims");
        $rows = $this->db->resultSet();
        $aims = [];
        foreach ($rows as $row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aim->setObjectives($this->getObjectivesByAimId($row['id']));
            $aims[] = $aim;
        }
        return $aims;
    }

    public function getAimsByCoveId($coveId)
    {
        $this->db->query("SELECT * FROM aims WHERE coveId = ?", [$coveId]);
        $rows = $this->db->resultSet();
        $aims = [];
        foreach ($rows as $row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aims[] = $aim;
        }
        return $aims;
    }

    public function getAimsByCoveWithObjAndReg($coveId)
    {
        $this->db->query("SELECT * FROM aims WHERE coveId = ?", [$coveId]);
        $rows = $this->db->resultSet();
        $aims = [];
        foreach ($rows as $row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aim->setObjectives($this->getObjectivesByAimId($row['id']));
            $aims[] = $aim;
        }
        return $aims;
    }

    public function getAimsForReport($coveId)
    {
        // * kullanımı aimResult dahil tüm alanları getirir
        $this->db->query("SELECT * FROM aims WHERE coveId = ?", [$coveId]);
        $rows = $this->db->resultSet();
        $aims = [];
        $objectiveModel = new ObjectiveModel();
        foreach ($rows as $row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aim->setObjectives($objectiveModel->getObjectivesByAimId($row['id'], $coveId)); // Objective artık result alanını içeriyor
            $aims[] = $aim;
        }
        return $aims;
    }

    public function getAimByIdByCoveId($id, $coveId)
    {
        $this->db->query("SELECT * FROM aims WHERE id = ? and coveId = ?", [$id, $coveId]);
        $row = $this->db->single();
        if ($row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            return $aim;
        }
        return null;
    }

    // Controller'da kullanılan fakat bulunmayan alias
    public function getAimByIdAndCoveId($id, $coveId)
    {
        return $this->getAimByIdByCoveId($id, $coveId);
    }

    /*
    public function getAimByIdByCoveIdWithReg($id, $coveId) {
        $this->db->query("SELECT * FROM aims WHERE id = ? and coveId = ?", [$id, $coveId]);
        $row = $this->db->single();
        if ($row) {
            $aim = new Aim($row['id'], $row['aimTitle'], $row['aimDesc'], $row['coveId'], $row['createdAt'], $row['aimResult']);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            return $aim;
        }
        return null;
    }*/

    /*
    public function getAimByIdByCoveWithObjAndReg($id, $coveId) {
        $this->db->query("SELECT * FROM aims WHERE id = ? and coveId = ?", [$id, $coveId]);
        $row = $this->db->single();
        if ($row) {
            $aim = new Aim($row['id'], $row['aimTitle'], $row['aimDesc'], $row['coveId'], $row['createdAt'], $row['aimResult']);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            $aim->setObjectives($this->getObjectivesByAimId($row['id']));
            
            return $aim;
        }
        return null;
    }
        */

    public function getAimById($id)
    {
        $this->db->query("SELECT * FROM aims WHERE id = ?", [$id]);
        $row = $this->db->single();
        if ($row) {
            $aim = new Aim($row);
            $aim->setRegulations($this->getRegulationsByAimId($row['id']));
            return $aim;
        }
        return null;
    }

    public function createAim($aimTitle, $aimDesc, $coveId, $aimResult = null)
    {
        $this->db->query("INSERT INTO aims (aimTitle, aimDesc, coveId, aimResult, createdAt) VALUES (?, ?, ?, ?, NOW())", [$aimTitle, $aimDesc, $coveId, $aimResult]);
        return $this->db->lastInsertId();
    }

    public function updateAim($id, $aimTitle, $aimDesc, $coveId, $aimResult = null)
    {
        $this->db->query("UPDATE aims SET aimTitle = ?, aimDesc = ?, coveId = ?, aimResult = ? WHERE id = ?", [$aimTitle, $aimDesc, $coveId, $aimResult, $id]);
    }

    public function deleteAim($id)
    {
        $this->db->query("DELETE FROM aims WHERE id = ?", [$id]);
    }

    public function getCoveIdByUserId($userId)
    {
        $this->db->query("SELECT coveId FROM cove_users WHERE userId = ?", [$userId]);
        $row = $this->db->single();
        return $row ? $row['coveId'] : null;
    }

    public function getAllRegulations()
    {
        $this->db->query("SELECT * FROM regulations");
        return $this->db->resultSet();
    }

    public function assignRegulationsToAim($aimId, $regulationIds)
    {
        $this->db->query("DELETE FROM aim_regulations WHERE aimId = ?", [$aimId]);
        foreach ($regulationIds as $regulationId) {
            $this->db->query("INSERT INTO aim_regulations (aimId, regulationId, createdAt) VALUES (?, ?, NOW())", [$aimId, $regulationId]);
        }
    }

    public function getRegulationsByAimId($aimId)
    {
        $this->db->query("SELECT r.id, r.regulationDesc, r.regulationSource, r.regulationSourceNo FROM regulations r
                          JOIN aim_regulations ar ON r.id = ar.regulationId
                          WHERE ar.aimId = ?", [$aimId]);
        return $this->db->resultSet();
    }

    public function getObjectivesByAimId($aimId)
    {
        // Tabloda aims_objectives bağı gerekliyse join, değilse doğrudan objectives'tan çek
        $this->db->query("SELECT ob.* FROM objectives ob
                        JOIN aims_objectives ao ON ob.id = ao.objectiveId 
                        WHERE ao.aimId = ?", [$aimId]);
        $rows = $this->db->resultSet();
        // Tam veri gerekli (objectiveDesc, objectiveResult) rapor için
        return $rows;
    }

    public function getObjectivesForReport($aimId)
    {
        $this->db->query("SELECT ob.id, ob.objectiveTitle, ob.coveId FROM objectives ob
                        JOIN aims_objectives ao ON ob.id = ao.objectiveId 
                        WHERE ao.aimId = ?", [$aimId]);
        return $this->db->resultSet();
    }
}
