<?php
require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Permission.php';

class PermissionModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllPermissions() {
        $this->db->query("SELECT permissions.* , r.id as roleId, r.roleName as roleName FROM permissions Left Join role_permissions rp ON permissions.id = rp.permissionId
         Left Join roles r ON rp.roleId = r.id");
        $data = $this->db->resultSet();
        
        $permissions = [];
        foreach ($data as $row) {      
             
            $permission = new Permission($row['id'], $row['permissionName'], $row['description']);
            $role = new Role($row['roleId'], $row['roleName'], '', 0);
            
            $permission->setRole($role);

           
            $permissions[] = $permission;
        }
       
        return $permissions;
    }

    public function getPermissionById($id) {
        $this->db->query("SELECT * FROM permissions WHERE id = ?", [$id]);
        $row = $this->db->single();
        if ($row) {
            return new Permission($row['id'], $row['permissionName'], $row['description']);
        }
        
        return null;
    }

    public function createPermission($permissionName, $description) {
        $this->db->query("INSERT INTO permissions (permissionName, description) VALUES (?, ?)", [$permissionName, $description]);
        return $this->db->lastInsertId();
    }

    public function updatePermission($id, $permissionName, $description) {
        $this->db->query("UPDATE permissions SET permissionName = ?, description = ? WHERE id = ?", [$permissionName, $description, $id]);
    }

    public function deletePermission($id) {
        $this->db->query("DELETE FROM permissions WHERE id = ?", [$id]);
        
    }

    public function assignPermissionToRole($permissionId, $roleId) {
        // Check if the permission is already assigned to the role
        $this->db->query("SELECT COUNT(*) as count FROM role_permissions WHERE permissionId = ? AND roleId = ?", [$permissionId, $roleId]);
        $result = $this->db->single();
        if ($result['count'] == 0) {
            $this->db->query("INSERT INTO role_permissions (permissionId, roleId) VALUES (?, ?)", [$permissionId, $roleId]);
        }
    }

    public function rolePermissionExists($permissionId) {
        $this->db->query("SELECT COUNT(*) as count FROM role_permissions WHERE permissionId = ?", [$permissionId]);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    public function updateRolePermission($permissionId, $roleId) {
        $this->db->query("UPDATE role_permissions SET roleId = ? WHERE permissionId = ?", [$roleId, $permissionId]);
    }

    public function removePermissionFromRole($permissionId, $roleId) {
        $this->db->query("DELETE FROM role_permissions WHERE roleId = ? AND permissionId = ?", [$roleId, $permissionId]);
    }

    public function getRoleIdByPermissionId($permissionId) {
        $this->db->query("SELECT roleId FROM role_permissions WHERE permissionId = ?", [$permissionId]);
        $row = $this->db->single();
        return $row ? $row['roleId'] : null;
    }
}
?>
