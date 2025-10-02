<?php
require_once APP_PATH . 'models/BaseModel.php';

class FaqModel extends BaseModel {
    public function getActiveAll(){
        $stmt = $this->pdo->query("SELECT id, question, answer FROM faqs WHERE is_active=1 ORDER BY id ASC");
        return $stmt->fetchAll();
    }
    public function getActiveLimited($limit=10){
        $stmt = $this->pdo->prepare("SELECT id, question, answer FROM faqs WHERE is_active=1 ORDER BY id ASC LIMIT ?");
        $stmt->bindValue(1,(int)$limit,PDO::PARAM_INT); $stmt->execute();
        return $stmt->fetchAll();
    }
    public function search($q,$limit=5){
        $like = '%'.$q.'%';
        $stmt = $this->pdo->prepare("SELECT id, question, answer FROM faqs WHERE is_active=1 AND (question LIKE ? OR answer LIKE ?) ORDER BY id ASC LIMIT ?");
        $stmt->bindValue(1,$like); $stmt->bindValue(2,$like); $stmt->bindValue(3,(int)$limit,PDO::PARAM_INT); $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getById($id){
        $stmt=$this->pdo->prepare("SELECT * FROM faqs WHERE id=?"); $stmt->execute([$id]); return $stmt->fetch();
    }
    public function create($question,$answer,$userId){
        $stmt=$this->pdo->prepare("INSERT INTO faqs(question,answer,created_by) VALUES(?,?,?)");
        $stmt->execute([$question,$answer,$userId]);
        return $this->pdo->lastInsertId();
    }
    public function update($id,$question,$answer,$active,$userId){
        $stmt=$this->pdo->prepare("UPDATE faqs SET question=?, answer=?, is_active=?, updated_by=? WHERE id=?");
        return $stmt->execute([$question,$answer,(int)$active,$userId,$id]);
    }
    public function delete($id){
        $stmt=$this->pdo->prepare("DELETE FROM faqs WHERE id=?"); return $stmt->execute([$id]);
    }
}
