<?php

require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Role.php';
require_once APP_PATH . 'entities/Permission.php';

class RoleModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllRoles() {
        $this->db->query("SELECT r.*, p.roleName AS parentRoleName 
                          FROM roles r 
                          LEFT JOIN roles p ON r.parentRoleId = p.id");
        $data = $this->db->resultSet();
        $roles = [];
        foreach ($data as $row) {
            $role = new Role($row['id'], $row['roleName'], $row['description'], $row['parentRoleId']);
            if ($row['parentRoleId']) {
                $parentRole = new Role($row['parentRoleId'], $row['parentRoleName'], '', null);
                $role->setParentRole($parentRole);
            }
            $roles[] = $role;
        }
        return $roles;
    }

    public function getRoleById($id) {
        $this->db->query("SELECT * FROM roles WHERE id = ?", [$id]);
        $data = $this->db->single();
        if ($data) {
            $role = new Role($data['id'], $data['roleName'], $data['description'], $data['parentRoleId']);
            $role->setPermissions($this->getPermissionsByRoleId($data['id']));
            return $role;
        }
        return null;
    }

    public function createRole($roleName, $description, $parentRoleId = null) {
        $this->db->query("INSERT INTO roles (roleName, description, parentRoleId) VALUES (?, ?, ?)", [$roleName, $description, $parentRoleId]);
        return $this->db->lastInsertId();
    }

    public function updateRole($roleId, $roleName, $description, $parentRoleId = null) {
        $this->db->query("UPDATE roles SET roleName = ?, description = ?, parentRoleId = ? WHERE id = ?", [$roleName, $description, $parentRoleId, $roleId]);
    }

    public function deleteRole($id) {
        $this->db->query("DELETE FROM roles WHERE id = ?", [$id]);
    }

    public function roleExists($roleId) {
        $this->db->query("SELECT COUNT(*) as count FROM roles WHERE id = ?", [$roleId]);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    private function getPermissionsByRoleId($roleId) {
        $this->db->query("SELECT p.* FROM permissions p INNER JOIN role_permissions rp ON p.id = rp.permissionId WHERE rp.roleId = ?", [$roleId]);
        $permissionData = $this->db->resultSet();
        $permissions = [];
        foreach ($permissionData as $data) {
            $permissions[] = new Permission($data['id'], $data['permissionName'], $data['description']);
        }
        return $permissions;
    }
}
?>
