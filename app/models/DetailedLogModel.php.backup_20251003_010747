<?php

require_once 'BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../entities/DetailedLog.php';

class DetailedLogModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createDetailedLog($userId, $logType, $entityType, $entityTitle, $ipAddress) {
        $this->db->query("INSERT INTO detailedlogs (userId, logType, entityType, entityTitle, dateTime, ipAddress) VALUES (?, ?, ?, ?, NOW(), ?)", [
            $userId, $logType, $entityType, $entityTitle, $ipAddress
        ]);
        return $this->db->lastInsertId();
    }


    public function getAllDetailedLogs() {
        $this->db->query("SELECT detailedlogs.*, users.username FROM detailedlogs JOIN users ON detailedlogs.userId = users.id ORDER BY detailedlogs.dateTime DESC");
        $data = $this->db->resultSet();
        $detailedLogs = [];
        foreach ($data as $row) {
            $detailedLogs[] = new DetailedLog($row['id'], $row['userId'], $row['logType'], $row['entityType'], $row['entityTitle'], $row['dateTime'], $row['ipAddress'], $row['username']);
        }
        return $detailedLogs;
    }

    public function deleteDetailedLog($id) {
        $this->db->query("DELETE FROM detailedlogs WHERE id = ?", [$id]);
    }
}
?>
