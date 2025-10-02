<?php

require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/DocumentStrategy.php';

class DocumentStrategyModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllDocumentStrategies() {
        $this->db->query("SELECT * FROM DocumentStrategies");
        $data = $this->db->resultSet();
        $strategies = [];
        foreach ($data as $row) {
            $strategies[] = new DocumentStrategy($row['id'], $row['strategyDesc'], $row['strategyNo']);
        }
        return $strategies;
    }

    public function getDocumentStrategyById($id) {
        $this->db->query("SELECT * FROM DocumentStrategies WHERE id = ?", [$id]);
        $row = $this->db->single();
        if ($row) {
            return new DocumentStrategy($row['id'], $row['strategyDesc'], $row['strategyNo']);
        }
        return null;
    }

    public function createDocumentStrategy($strategyDesc, $strategyNo) {
        $this->db->query("INSERT INTO DocumentStrategies (strategyDesc, strategyNo) VALUES (?, ?)", [$strategyDesc, $strategyNo]);
        return $this->db->lastInsertId();
    }

    public function updateDocumentStrategy($id, $strategyDesc, $strategyNo) {
        $this->db->query("UPDATE DocumentStrategies SET strategyDesc = ?, strategyNo = ? WHERE id = ?", [$strategyDesc, $strategyNo, $id]);
    }

    public function deleteDocumentStrategy($id) {
        $this->db->query("DELETE FROM DocumentStrategies WHERE id = ?", [$id]);
    }
}
?>
