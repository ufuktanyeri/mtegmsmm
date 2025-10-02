<?php

require_once __DIR__.'/BaseModel.php';
require_once __DIR__ . '/../../includes/Database.php'; // Class for database operations
require_once __DIR__ . '/../entities/User.php'; // Include the User entity
require_once __DIR__ . '/../entities/Role.php'; // Include the Role entity
require_once __DIR__ . '/../entities/Permission.php'; // Include the Permission entity
require_once __DIR__ . '/../entities/Cove.php'; // Include the Cove entity

class UserModel extends BaseModel {
    private $db;
    private $themeColumnChecked = false;
    private $hasThemeColumn = false;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDb() {
        return $this->db;
    }

    private function ensureThemeColumnInfo() {
        if ($this->themeColumnChecked) return;
        // INFORMATION_SCHEMA üzerinden kontrol
        try {
            $this->db->query("SELECT COUNT(*) as c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='ui_theme'");
            $row = $this->db->single();
            $this->hasThemeColumn = isset($row['c']) && (int)$row['c'] > 0;
        } catch (Throwable $t) { $this->hasThemeColumn = false; }
        $this->themeColumnChecked = true;
    }

    private function addThemeColumnIfMissing() {
        $this->ensureThemeColumnInfo();
        if ($this->hasThemeColumn) return true;
        try {
            $this->db->query("ALTER TABLE users ADD COLUMN ui_theme VARCHAR(20) NULL DEFAULT 'light'");
            $this->hasThemeColumn = true;
            return true;
        } catch (Throwable $t) { return false; }
    }

    public function getUserTheme($userId) {
        $this->ensureThemeColumnInfo();
        if (!$this->hasThemeColumn) return null;
        try {
            $this->db->query("SELECT ui_theme FROM users WHERE id = ?", [$userId]);
            $row = $this->db->single();
            return $row && !empty($row['ui_theme']) ? $row['ui_theme'] : null;
        } catch (Throwable $t) { return null; }
    }

    public function setUserTheme($userId, $theme) {
        if (!$this->addThemeColumnIfMissing()) return false;
        try {
            $this->db->query("UPDATE users SET ui_theme = ? WHERE id = ?", [$theme, $userId]);
            return true;
        } catch (Throwable $t) { return false; }
    }

    public function clearUserTheme($userId) {
        if (!$this->addThemeColumnIfMissing()) return false;
        try {
            $this->db->query("UPDATE users SET ui_theme = NULL WHERE id = ?", [$userId]);
            return true;
        } catch (Throwable $t) { return false; }
    }

    public function createUser($realname, $username, $hashedPassword, $email) {
        $this->db->query("INSERT INTO users (realname, username, password, email, createdAt) VALUES (?, ?, ?, ?, NOW())", [$realname, $username, $hashedPassword, $email]);
        return $this->db->lastInsertId();
    }

    public function assignRole($userId, $roleName) {
        $this->db->query("INSERT INTO user_roles (userId, roleId) VALUES (?, (SELECT id FROM roles WHERE roleName = ?))", [$userId, $roleName]);
        return $this->db->lastInsertId();
    }

    public function assignRoleByRoleId($userId, $roleId) {
        $this->db->query("INSERT INTO user_roles (userId, roleId) VALUES (?, ?)", [$userId, $roleId]);
        return $this->db->lastInsertId();
    }

    public function assignCove($userId, $coveId) {
        $this->db->query("INSERT INTO cove_users (userId, coveId) VALUES (?, ?)", [$userId, $coveId]);
        return $this->db->lastInsertId();
    }

    public function getUserByUsername($username) {
        $this->db->query("SELECT id, realname, username, email, password, createdAt, profile_photo FROM users WHERE username = ? LIMIT 1", [$username]);
        $userData = $this->db->single();
        if ($userData) {
            $user = new User(
                $userData['id'],
                $userData['realname'],
                $userData['username'],
                $userData['password'],
                $userData['email'],
                $userData['createdAt'],
                $userData['profile_photo']
            );

            // Fetch roles
            $roles = $this->getRolesByUserId($user->getId());
            $user->setRole($roles);

            // Fetch permissions for each role
            $permissions = [];
            foreach ($roles as $role) {
                $rolePermissions = $this->getPermissionsByRoleId($role->getId());
                $permissions = array_merge($permissions, $rolePermissions);
            }
            //$user->setPermissions($permissions);

            // Fetch cove information
            $this->db->query("SELECT c.id, c.name, c.city, c.address FROM coves c
                              JOIN cove_users uc ON c.id = uc.coveId
                              WHERE uc.userId = ?", [$user->getId()]);
            $coveData = $this->db->single();
            if ($coveData) {
                $cove = new Cove(
                    $coveData['id'],
                    $coveData['name'],
                    $coveData['city'],
                    $coveData['address']
                );
                $user->setCove($cove);
            }

            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $this->db->query("SELECT id, realname, username, email, password, createdAt, profile_photo FROM users WHERE id = ? LIMIT 1", [$id]);
        $userData = $this->db->single();
        if ($userData) {
            $user = new User(
                $userData['id'],
                $userData['realname'],
                $userData['username'],
                $userData['password'],
                $userData['email'],
                $userData['createdAt'],
                $userData['profile_photo']
            );

            // Fetch roles
            $roles = $this->getRolesByUserId($user->getId());
            $user->setRole($roles);

            // Fetch permissions for each role
            $permissions = [];
            foreach ($roles as $role) {
                $rolePermissions = $this->getPermissionsByRoleId($role->getId());
                $permissions = array_merge($permissions, $rolePermissions);
            }
            //$user->setPermissions($permissions);

            // Fetch cove information
            $cove = $this->getCoveByUserId($user->getId());
            if ($cove)
                $user->setCove($cove);
            //$user->setCove($cove);

            return $user;
        }
        return false;
    }

    public function updateUserWithoutPassword($id, $realname, $username, $email) {
        $this->db->query("UPDATE users SET realname = ?, username = ?, email = ? WHERE id = ?", [$realname, $username, $email, $id]);
    }

    public function updateUser($id, $realname, $username, $hashedPassword, $email) {
        $this->db->query("UPDATE users SET realname = ?, username = ?, password = ?, email = ? WHERE id = ?", [$realname, $username, $hashedPassword, $email, $id]);
    }

    public function updateUserWithPhoto($id, $realname, $username, $hashedPassword, $email, $profilePhoto) {
        $this->db->query("UPDATE users SET realname = ?, username = ?, password = ?, email = ?, profile_photo = ? WHERE id = ?", [$realname, $username, $hashedPassword, $email, $profilePhoto, $id]);
    }

    public function updateUserWithoutPasswordButWithPhoto($id, $realname, $username, $email, $profilePhoto) {
        $this->db->query("UPDATE users SET realname = ?, username = ?, email = ?, profile_photo = ? WHERE id = ?", [$realname, $username, $email, $profilePhoto, $id]);
    }

    public function updateProfilePhoto($id, $profilePhoto) {
        $this->db->query("UPDATE users SET profile_photo = ? WHERE id = ?", [$profilePhoto, $id]);
    }

    public function updateUserRole($userId, $roleId) {
        $this->db->query("UPDATE user_roles SET roleId = ? WHERE userId = ?", [$roleId, $userId]);
    }

    public function updateUserCove($userId, $coveId) {
        $this->db->query("UPDATE cove_users SET coveId = ? WHERE userId = ?", [$coveId, $userId]);
    }

    private function getRolesByUserId($userId) {
        $this->db->query("SELECT r.id, r.roleName, r.description, r.parentRoleId FROM roles r
                          JOIN user_roles ur ON r.id = ur.roleId
                          WHERE ur.userId = ?", [$userId]);
        $roleData = $this->db->single();
        $role=0;
      if ($roleData) {
            $role = new Role(
                $roleData['id'],
                $roleData['roleName'],
                $roleData['description'],
                $roleData['parentRoleId']
            );

            // Fetch permissions for each role, including parent roles
            $permissions = $this->getPermissionsByRoleId($role->getId());
            $role->setPermissions($permissions);
      
        }
        return $role;
    }

    private function getCoveByUserId($userId) {
        $this->db->query("SELECT c.id, c.name, c.city, c.address FROM coves c
                              JOIN cove_users uc ON c.id = uc.coveId
                              WHERE uc.userId = ?", [$userId]);
      
      $coveData = $this->db->single();
      $cove=0;
      if ($coveData) {
          $cove = new Cove(
              $coveData['id'],
              $coveData['name'],
              $coveData['city'],
              $coveData['address']
          );
      
        }
        return $cove;
    }
    private function getPermissionsByRoleId($roleId) {
        $permissions = [];
        $this->fetchPermissionsRecursive($roleId, $permissions);
        return $permissions;
    }

    private function fetchPermissionsRecursive($roleId, &$permissions) {
        $this->db->query("SELECT p.id, p.permissionName, p.description FROM permissions p
                          JOIN role_permissions rp ON p.id = rp.permissionId
                          WHERE rp.roleId = ?", [$roleId]);
        $permissionsData = $this->db->resultSet();
        foreach ($permissionsData as $permissionData) {
            $permissions[] = new Permission(
                $permissionData['id'],
                $permissionData['permissionName'],
                $permissionData['description']
            );
        }

        // Fetch parent role and its permissions recursively
        $this->db->query("SELECT parentRoleId FROM roles WHERE id = ?", [$roleId]);
        $parentRoleData = $this->db->single();
        if ($parentRoleData && $parentRoleData['parentRoleId']) {
            $this->fetchPermissionsRecursive($parentRoleData['parentRoleId'], $permissions);
        }
    }

    public function usernameExists($username, $excludeUserId = null) {
        $query = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $params = [$username];
        if ($excludeUserId) {
            $query .= " AND id != ?";
            $params[] = $excludeUserId;
        }
        $this->db->query($query, $params);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    public function emailExists($email, $excludeUserId = null) {
        $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeUserId) {
            $query .= " AND id != ?";
            $params[] = $excludeUserId;
        }
        $this->db->query($query, $params);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    public function getAllUsers() {
        $this->db->query("SELECT u.*, r.roleName, ur.roleId as roleId, cu.coveId as coveId, c.name as coveName FROM users u 
                          LEFT JOIN user_roles ur ON u.id = ur.userId 
                          LEFT JOIN roles r ON ur.roleId = r.id 
                          LEFT JOIN cove_users cu ON u.id = cu.userId 
                          LEFT JOIN coves c ON cu.coveId = c.id");
        $userData = $this->db->resultSet();
        $users = [];
        foreach ($userData as $data) {
            $user = new User($data['id'], $data['realname'], $data['username'], $data['password'], $data['email'], $data['createdAt']);
            $role = new Role($data['roleId'], $data['roleName'], '', null);
            $cove = new Cove($data['coveId'], $data['coveName'], '', '');
            $user->setRole($role);
            $user->setCove($cove);
            $users[] = $user;
        }
        return $users;
    }

    public function deleteUser($id) {
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
        $this->db->query("DELETE FROM user_roles WHERE userId = ?", [$id]);
        $this->db->query("DELETE FROM cove_users WHERE userId = ?", [$id]);
    }

    public function userRoleExists($userId) {
        $this->db->query("SELECT COUNT(*) as count FROM user_roles WHERE userId = ?", [$userId]);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    public function userCoveExists($userId) {
        $this->db->query("SELECT COUNT(*) as count FROM cove_users WHERE userId = ?", [$userId]);
        $result = $this->db->single();
        return $result['count'] > 0;
    }
}
?>
