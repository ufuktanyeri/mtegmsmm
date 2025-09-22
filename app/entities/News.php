<?php

class News {
    private $id;
    private $title;
    private $details;
    private $content;
    private $frontpage_image;
    private $created_date;
    private $user_id;
    private $state;
    private $headline;
    private $order_no;

    public function __construct($id = null, $title = null, $details = null,$content = null, $frontpage_image = null,
                              $created_date = null, $user_id = null, $state = 0,
                              $headline = 0, $order_no = 0) {
        $this->id = $id;
        $this->title = $title;
        $this->details = $details;
        $this->content = $content;
        $this->frontpage_image = $frontpage_image;
        $this->created_date = $created_date;
        $this->user_id = $user_id;
        $this->state = $state;
        $this->headline = $headline;
        $this->order_no = $order_no;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDetails() {
        return $this->details;
    }
    public function getContent() {
        return $this->content;
    }

    public function getFrontpageImage() {
        // BASE_URL'i kullan (production ve development için otomatik uyum)
        $baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '/mtegmsmm/wwwroot/';

        // Eğer görüntü yoksa veya boşsa placeholder döndür
        if (empty($this->frontpage_image)) {
            return $baseUrl . 'uploads/news/placeholder.svg';
        }

        // Placeholder için özel kontrol
        if ($this->frontpage_image === 'placeholder.svg') {
            return $baseUrl . 'uploads/news/placeholder.svg';
        }

        // Eğer tam URL ise direkt döndür
        if (filter_var($this->frontpage_image, FILTER_VALIDATE_URL)) {
            return $this->frontpage_image;
        }

        // Eğer / ile başlıyorsa direkt döndür (absolute path)
        if (strpos($this->frontpage_image, '/') === 0) {
            return $this->frontpage_image;
        }

        // Eğer zaten uploads/ ile başlıyorsa
        if (strpos($this->frontpage_image, 'uploads/') === 0) {
            return $baseUrl . $this->frontpage_image;
        }

        // Diğer durumlarda tam path oluştur (sadece dosya adı varsa)
        return $baseUrl . 'uploads/news/' . $this->frontpage_image;
    }

    public function getCreatedDate() {
      return date('d.m.Y H:i', strtotime($this->created_date));
    //return $this->created_date;
  }

    public function getUserId() {
        return $this->user_id;
    }

    public function getState() {
        return $this->state;
    }

    public function getHeadline() {
        return $this->headline;
    }

    public function getOrderNo() {
        return $this->order_no;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDetails($details) {
        $this->details = $details;
    }
    public function setContent($content) {
        $this->content = $content;
    }

    public function setFrontpageImage($frontpage_image) {
        $this->frontpage_image = $frontpage_image;
    }

    public function setCreatedDate($created_date) {
        $this->created_date = $created_date;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setHeadline($headline) {
        $this->headline = $headline;
    }

    public function setOrderNo($order_no) {
        $this->order_no = $order_no;
    }
}
?>
