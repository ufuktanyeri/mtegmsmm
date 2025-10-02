<?php
require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Gallery.php';

class GalleryModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createGalleryItem($newsId, $userId, $path) {
        $this->db->query("INSERT INTO gallery (news_id, user_id, path) VALUES (?, ?, ?)", 
                         [$newsId, $userId, $path]);
        return $this->db->lastInsertId();
    }

    public function getGalleryByNewsId($newsId) {
        $this->db->query("SELECT * FROM gallery WHERE news_id = ?", [$newsId]);
        $galleryData = $this->db->resultSet();
        $gallery = [];
        foreach ($galleryData as $row) {
            $gallery[] = new Gallery(
                $row['id'],
                $row['news_id'],
                $row['user_id'],
                $row['path']
            );
        }
        return $gallery;
    }

    public function getGalleryByUserAndNewsId($userId, $newsId) {
       // $this->db->query("SELECT * FROM gallery WHERE user_id = ? AND news_id = ?", 
         //                [$userId, $newsId]);
        $this->db->query("SELECT * FROM gallery WHERE news_id = ?", 
                         [$newsId]);
        $galleryData = $this->db->resultSet();
        $gallery = [];
        foreach ($galleryData as $row) {
            $gallery[] = new Gallery(
                $row['id'],
                $row['news_id'],
                $row['user_id'],
                $row['path']
            );
        }
        return $gallery;
    }

    public function getGalleryItemById($id, $userId) {
        //$this->db->query("SELECT * FROM gallery WHERE id = ? AND user_id = ?", 
        //                 [$id, $userId]);
        $this->db->query("SELECT * FROM gallery WHERE id = ?", 
                         [$id]);
        $row = $this->db->single();
        if ($row) {
            return new Gallery(
                $row['id'],
                $row['news_id'],
                $row['user_id'],
                $row['path']
            );
        }
        return null;
    }

    public function updateGalleryItem($id, $userId, $path) {
        $this->db->query("UPDATE gallery SET path = ? WHERE id = ? AND user_id = ?", 
                         [$path, $id, $userId]);
        return $this->db->rowCount();
    }

    public function deleteGalleryItem($id, $userId) {
      //  $this->db->query("DELETE FROM gallery WHERE id = ? AND user_id = ?", [$id, $userId]);
        $this->db->query("DELETE FROM gallery WHERE id = ?", [$id]);
        return $this->db->rowCount();
    }

    public function deleteGalleryByNewsId($newsId, $userId) {
       // $this->db->query("DELETE FROM gallery WHERE news_id = ? AND user_id = ?", 
       //                  [$newsId, $userId]);
        $this->db->query("DELETE FROM gallery WHERE news_id = ?", 
                         [$newsId]);
        return $this->db->rowCount();
    }

    public function getGalleryPath($id) {
        $this->db->query("SELECT path FROM gallery WHERE id = ?", [$id]);
        $result = $this->db->single();
        return $result ? $result['path'] : null;
    }
}
?>
