<?php

require_once 'BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../entities/Log.php';

class LogModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createLog($userId, $ipAddress) {
        $this->db->query("INSERT INTO logs (userId,  dateTime, ipAddress) VALUES (?, NOW(), ?)", [
            $userId,  $ipAddress
        ]);
        return $this->db->lastInsertId();
    }


    public function getAllLogs() {
        $this->db->query("SELECT logs.*, users.username FROM logs JOIN users ON logs.userId = users.id ORDER BY logs.dateTime DESC");
        $data = $this->db->resultSet();
        $logs = [];
        foreach ($data as $row) {
            $logs[] = new Log($row['id'], $row['userId'], $row['dateTime'], $row['ipAddress'], $row['username']);
        }
        return $logs;
    }

    public function deleteLog($id) {
        $this->db->query("DELETE FROM logs WHERE id = ?", [$id]);
    }
}
?>
