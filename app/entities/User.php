<?php

require_once __DIR__ . '/Cove.php';
require_once __DIR__ . '/Role.php';
require_once __DIR__ . '/Permission.php';

class User {
    private $id;
    private $realname;
    private $username;
    private $password;
    private $email;
    private $createdAt;
    private $profilePhoto;
    private $role;
    private $permissions;
    private $cove;

    public function __construct($id, $realname, $username, $password, $email, $createdAt, $profilePhoto = null) {
        $this->id = $id;
        $this->username = $username;
        $this->realname = $realname;
        $this->password = $password;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->profilePhoto = $profilePhoto;
        $this->permissions = [];
        $this->role;
        $this->cove;
    }

    public function getId() {
        return $this->id;
    }

    public function getRealname() {
        return $this->realname;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole(Role $role) {
        $this->role = $role;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions(Permission $permissions) {
        $this->permissions = $permissions;
    }

    public function getCove() {
        return $this->cove;
    }

    public function setCove(Cove $cove) {
        $this->cove = $cove;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    
    public function getProfilePhoto() {
        return $this->profilePhoto;
    }
    
    public function setProfilePhoto($profilePhoto) {
        $this->profilePhoto = $profilePhoto;
    }
    
    public function setRealname($realname) {
        $this->realname = $realname;
    }
}
?>
