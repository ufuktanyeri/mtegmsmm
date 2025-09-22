<?php
require_once __DIR__ . '/BaseModel.php';

class SupportMessageModel extends BaseModel {
    public function addMessage($sessionId, $sender, $message){
        $stmt = $this->pdo->prepare("INSERT INTO support_messages (session_id, sender, message) VALUES (?,?,?)");
        $stmt->execute([$sessionId, $sender, $message]);
        return $this->pdo->lastInsertId();
    }
    public function getMessagesSince($sessionId, $sinceId = 0){
        $stmt = $this->pdo->prepare("SELECT * FROM support_messages WHERE session_id=? AND id>? ORDER BY id ASC");
        $stmt->execute([$sessionId, $sinceId]);
        return $stmt->fetchAll();
    }
    public function getLastId($sessionId){
        $stmt = $this->pdo->prepare("SELECT id FROM support_messages WHERE session_id=? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId]);
        $row = $stmt->fetch();
        return $row? (int)$row['id'] : 0;
    }
}
