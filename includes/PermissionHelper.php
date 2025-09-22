<?php
/**
 * Permission Helper Functions
 * 
 * This file provides helper functions for checking user permissions
 * across the application views.
 */

/**
 * Check if current user is superadmin
 * @return bool
 */
function isSuperAdmin() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
}

/**
 * Check if user has permission for a specific operation
 * 
 * @param string $permissionName The permission to check
 * @param string $operation The operation type: 'select', 'insert', 'update', 'delete'
 * @return bool
 */
function hasPermission($permissionName, $operation = 'select') {
    // Superadmin has all permissions
    if (isSuperAdmin()) {
        return true;
    }
    
    // For other roles, only allow SELECT operations
    if ($operation !== 'select') {
        return false;
    }
    
    // Check if user has specific permission for SELECT
    if (isset($_SESSION['permissions'])) {
        $permissions = $_SESSION['permissions'];
        
        // Handle serialized permissions
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                // If permission is serialized, unserialize it
                if (is_string($permission)) {
                    $permission = @unserialize($permission);
                }
                
                // Check permission name
                if (is_object($permission) && method_exists($permission, 'getPermissionName')) {
                    if ($permission->getPermissionName() === $permissionName) {
                        return true;
                    }
                } elseif (is_array($permission) && isset($permission['permission_name'])) {
                    if ($permission['permission_name'] === $permissionName) {
                        return true;
                    }
                }
            }
        }
    }
    
    return false;
}

/**
 * Check if user can create records (INSERT operation)
 * @param string $permissionName
 * @return bool
 */
function canCreate($permissionName) {
    return hasPermission($permissionName, 'insert');
}

/**
 * Check if user can read records (SELECT operation)
 * @param string $permissionName
 * @return bool
 */
function canRead($permissionName) {
    return hasPermission($permissionName, 'select');
}

/**
 * Check if user can update records (UPDATE operation)
 * @param string $permissionName
 * @return bool
 */
function canUpdate($permissionName) {
    return hasPermission($permissionName, 'update');
}

/**
 * Check if user can delete records (DELETE operation)
 * @param string $permissionName
 * @return bool
 */
function canDelete($permissionName) {
    return hasPermission($permissionName, 'delete');
}

/**
 * Check if user has any CRUD permission
 * @param string $permissionName
 * @return bool
 */
function hasAnyPermission($permissionName) {
    return canRead($permissionName) || canCreate($permissionName) || 
           canUpdate($permissionName) || canDelete($permissionName);
}

/**
 * Check if user has permission for a specific cove (SMM center)
 * Coordinators can only manage their own cove
 * 
 * @param string $permissionName The permission to check
 * @param int|null $coveId The cove ID to check against
 * @param string $operation The operation type: 'select', 'insert', 'update', 'delete'
 * @return bool
 */
function hasPermissionForCove($permissionName, $coveId = null, $operation = 'select') {
    // Superadmin can access all coves
    if (isSuperAdmin()) {
        return true;
    }
    
    // Check if user has the base permission
    if (!hasPermission($permissionName, $operation)) {
        return false;
    }
    
    // For coordinators, check if they're accessing their own cove
    if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'coordinator') {
        // If no cove specified, allow (will be filtered later)
        if ($coveId === null) {
            return true;
        }
        
        // Check if the cove matches user's assigned cove
        if (isset($_SESSION['cove_id']) && $_SESSION['cove_id'] != $coveId) {
            return false; // Coordinator trying to access different cove
        }
    }
    
    return true;
}

/**
 * Get user's cove ID (SMM center ID)
 * @return int|null
 */
function getUserCoveId() {
    return $_SESSION['cove_id'] ?? null;
}

/**
 * Check if user is a coordinator
 * @return bool
 */
function isCoordinator() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'coordinator';
}

/**
 * Check if user is an SMM user
 * @return bool
 */
function isSmmUser() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'smmuser';
}