<?php
require_once __DIR__ . '/BaseModel.php';

class SupportSessionModel extends BaseModel {
    public function createPending($userId, $subject = null){
        $stmt = $this->pdo->prepare("INSERT INTO support_sessions (user_id, subject) VALUES (?, ?)");
        $stmt->execute([$userId, $subject]);
        return $this->pdo->lastInsertId();
    }
    public function getById($id){
        $stmt = $this->pdo->prepare("SELECT * FROM support_sessions WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function activate($id, $agentId){
        $stmt = $this->pdo->prepare("UPDATE support_sessions SET status='active', agent_id=?, started_at=NOW() WHERE id=? AND status='pending'");
        return $stmt->execute([$agentId,$id]);
    }
    public function close($id, $agentId){
        $stmt = $this->pdo->prepare("UPDATE support_sessions SET status='closed', closed_at=NOW() WHERE id=? AND (agent_id=? OR status!='active')");
        return $stmt->execute([$id,$agentId]);
    }
    public function listPending(){
        $stmt = $this->pdo->query("SELECT * FROM support_sessions WHERE status='pending' ORDER BY created_at ASC LIMIT 100");
        return $stmt->fetchAll();
    }
    public function listActive(){
        $stmt = $this->pdo->query("SELECT * FROM support_sessions WHERE status='active' ORDER BY started_at DESC LIMIT 100");
        return $stmt->fetchAll();
    }
    public function touchUserMsg($id){
        $stmt = $this->pdo->prepare("UPDATE support_sessions SET last_user_msg_at=NOW(), updated_at=NOW() WHERE id=?");
        $stmt->execute([$id]);
    }
    public function touchAgentMsg($id){
        $stmt = $this->pdo->prepare("UPDATE support_sessions SET last_agent_msg_at=NOW(), updated_at=NOW() WHERE id=?");
        $stmt->execute([$id]);
    }
}
