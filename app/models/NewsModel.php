<?php
require_once 'BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../entities/News.php';

class NewsModel extends BaseModel
{
    private $db;

    public function __construct()
    {
        // Singleton pattern kullan
        $this->db = Database::getInstance();
    }

    public function createNews($title, $details, $content, $frontpage_image, $userId, $state, $headline, $orderNo)
    {
        $createdDate = date('Y-m-d H:i:s');
        $this->db->query(
            "INSERT INTO news (title, details, content, frontpage_image, created_date, user_id, state, headline, order_no) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$title, $details, $content, $frontpage_image, $createdDate, $userId, $state, $headline, $orderNo]
        );
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function getNewsByUserId($userId)
    {
        $this->db->query("SELECT * FROM news WHERE user_id = ? ORDER BY created_date DESC", [$userId]);
        $newsData = $this->db->resultSet();
        $news = [];
        foreach ($newsData as $row) {
            $news[] = new News(
                $row['id'],
                $row['title'],
                $row['details'],
                $row['frontpage_image'],
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return $news;
    }

    public function getNewsAll()
    {
        $this->db->query("SELECT * FROM news ORDER BY created_date DESC");
        $newsData = $this->db->resultSet();
        $news = [];
        foreach ($newsData as $row) {
            $news[] = new News(
                $row['id'],
                $row['title'],
                $row['details'],
                "",
                "",
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return $news;
    }

    public function getNewsById($id)
    {
        $this->db->query("SELECT * FROM news WHERE id = ?", [$id]);
        $row = $this->db->single();
        if ($row) {
            return new News(
                $row['id'],
                $row['title'],
                $row['details'],
                $row['content'],
                $row['frontpage_image'],
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return null;
    }

    public function getNewsPublicById($id)
    {
        $this->db->query("SELECT * FROM news WHERE id = ? AND state = ?", [$id, 1]);
        $row = $this->db->single();
        if ($row) {
            return new News(
                $row['id'],
                $row['title'],
                $row['details'],
                $row['content'],
                $row['frontpage_image'],
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return null;
    }

    public function updateNews($id, $title, $details, $content, $frontpage_image, $userId, $state, $headline, $orderNo)
    {
        $this->db->query(
            "UPDATE news 
                         SET title = ?, details = ?, content = ?, frontpage_image = ?, state = ?, 
                             headline = ?, order_no = ? 
                         WHERE id = ?",
            [$title, $details, $content, $frontpage_image, $state, $headline, $orderNo, $id]
        );
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function deleteNews($id, $userId)
    {
        $this->db->query("DELETE FROM news WHERE id = ?", [$id]);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getHeadlineNews()
    {
        $this->db->query("SELECT n.*, u.username 
                         FROM news n 
                         LEFT JOIN users u ON n.user_id = u.id 
                         WHERE n.headline = 1 AND n.state = 1 
                         ORDER BY n.created_date DESC");
        return $this->db->resultSet();
    }

    public function updateNewsState($id, $userId, $state)
    {
        $this->db->query("UPDATE news SET state = ? WHERE id = ?", [$state, $id]);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateNewsOrderNo($id, $orderNo, $userId)
    {
        $this->db->query("UPDATE news SET order_no = ? WHERE id = ?", [$orderNo, $id]);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getHeadlineNewsOrderedLimited($limit = 5)
    {
        $limit = intval($limit);
        $this->db->query("SELECT * FROM news WHERE headline = 1 AND state = 1 ORDER BY order_no ASC LIMIT $limit");
        $newsData = $this->db->resultSet();
        $news = [];
        foreach ($newsData as $row) {
            $news[] = new News(
                $row['id'],
                $row['title'],
                $row['details'],
                $row['content'],
                $row['frontpage_image'],
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return $news;
    }

    public function countAllActiveNews()
    {
        $this->db->query("SELECT COUNT(*) as total FROM news WHERE state = 1");
        $row = $this->db->single();
        return $row ? intval($row['total']) : 0;
    }

    public function getAllActiveNewsOrderedPaged($limit, $offset)
    {
        $limit = intval($limit);
        $offset = intval($offset);
        $this->db->query("SELECT * FROM news WHERE state = 1 ORDER BY order_no ASC, created_date DESC LIMIT $limit OFFSET $offset");
        $newsData = $this->db->resultSet();
        $news = [];
        foreach ($newsData as $row) {
            $news[] = new News(
                $row['id'],
                $row['title'],
                $row['details'],
                $row['content'],
                $row['frontpage_image'],
                $row['created_date'],
                $row['user_id'],
                $row['state'],
                $row['headline'],
                $row['order_no']
            );
        }
        return $news;
    }
}
