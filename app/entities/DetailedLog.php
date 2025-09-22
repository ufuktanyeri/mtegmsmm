<?php

class DetailedLog {
    private $id;
    private $userId;
    private $logType;
    private $entityType;
    private $entityTitle;
    private $dateTime;
    private $ipAddress;
    private $username;

    public function __construct($id, $userId, $logType, $entityType, $entityTitle, $dateTime, $ipAddress, $username) {
        $this->id = $id;
        $this->userId = $userId;
        $this->logType = $logType;
        $this->entityType = $entityType;
        $this->entityTitle = $entityTitle;
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

    public function getLogType() {
        return $this->logType;
    }

    public function getEntityType() {
        return $this->entityType;
    }

    public function getEntityTitle() {
        return $this->entityTitle;
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
