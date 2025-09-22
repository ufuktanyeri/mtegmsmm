<?php

class Role {
    private $id;
    private $roleName;
    private $description;
    private $parentRoleId;
    private $permissions;
    private $parentRole;

    public function __construct($id, $roleName, $description, $parentRoleId) {
        $this->id = $id;
        $this->roleName = $roleName;
        $this->description = $description;
        $this->parentRoleId = $parentRoleId;
        $this->permissions = [];
    }

    public function getId() {
        return $this->id;
    }

    public function getRoleName() {
        return $this->roleName;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getParentRoleId() {
        return $this->parentRoleId;
    }


    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($permissions) {
        $this->permissions = $permissions;
    }

    public function getParentRole() {
        return $this->parentRole;
    }

    public function setParentRole($parentRole) {
        $this->parentRole = $parentRole;
    }
}
?>
