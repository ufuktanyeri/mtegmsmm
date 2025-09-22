<?php

class Permission {
    private $id;
    private $permissionName;
    private $description;

    private $role;


    public function __construct($id, $permissionName, $description) {
        $this->id = $id;
        $this->permissionName = $permissionName;
        $this->description = $description;
        $this->role;
    }

    public function getId() {
        return $this->id;
    }

    public function getPermissionName() {
        return $this->permissionName;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole(Role $role) {
        $this->role = $role;
    }
}
?>
