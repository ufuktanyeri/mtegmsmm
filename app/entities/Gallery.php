<?php

class Gallery {
    private $id;
    private $news_id;
    private $user_id;
    private $path;

    public function __construct($id = null, $news_id = null, $user_id = null, $path = null) {
        $this->id = $id;
        $this->news_id = $news_id;
        $this->user_id = $user_id;
        $this->path = $path;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNewsId() {
        return $this->news_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getPath() {
        return $this->path;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNewsId($news_id) {
        $this->news_id = $news_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setPath($path) {
        $this->path = $path;
    }
}
?>
