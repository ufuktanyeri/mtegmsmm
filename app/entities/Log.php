<?php

class Log {
    private $id;
    private $userId;
    private $dateTime;
    private $ipAddress;
    private $username;

    public function __construct($id, $userId, $dateTime, $ipAddress, $username) {
        $this->id = $id;
        $this->userId = $userId;
        $this->dateTime = $dateTime;
        $this->ipAddress = $ipAddress;
        $this->username = $username;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDateTime() {
        return $this->dateTime;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

    public function getUsername() {
        return $this->username;
    }
}
?>
