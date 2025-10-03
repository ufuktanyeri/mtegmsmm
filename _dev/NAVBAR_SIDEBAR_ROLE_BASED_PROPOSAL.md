# Role-Based Navbar & Sidebar System - Comprehensive Analysis & Proposal

**Project:** MTEGM SMM Portal
**Analysis Date:** October 3, 2025
**Status:** Detailed Analysis Complete

---

## Executive Summary

After analyzing the current navbar/sidebar implementation, I've identified **key architectural issues** and propose a **modern, role-based navigation system** that properly leverages the existing MVC structure with improved security, maintainability, and user experience.

---

## Current System Analysis

### ✅ What Works Well

1. **Permission-Based Rendering**
   - Uses `hasPermission()` function consistently
   - SuperAdmin bypass logic implemented
   - Permission checks at component level

2. **Multiple Layout Options**
   - `navbar.php` - Horizontal navbar for all users
   - `sidebar_menu.php` - Vertical sidebar for admin panel
   - `navbar_with_sidebar.php` - Combined layout
   - Flexible layout switching via `UnifiedViewService`

3. **Bootstrap 5.3 Integration**
   - Modern responsive design
   - Dark mode support
   - Clean utility-based styling

### ❌ Critical Issues Found

#### 1. **Permission Logic Duplication**
**Location:** All navbar/sidebar components

```php
// PROBLEM: Same fallback defined in 3 different files
if (!function_exists('hasPermission')) {
    function hasPermission($permissionName, $operation = 'select') {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }
}
```

**Impact:**
- Code duplication (3 locations)
- Inconsistent fallback behavior
- Hard to maintain and test
- Violates DRY principle

#### 2. **Hard-Coded Permission Checks**
**Location:** `navbar.php` lines 179-271

```php
<?php if ($isSuperAdmin || hasPermission('aims.manage') || hasPermission('objectives.manage') || ...): ?>
```

**Problems:**
- Menu structure embedded in view
- No centralized menu configuration
- Adding new menu items requires editing PHP template
- Cannot dynamically configure menus per role

#### 3. **Inconsistent Role Checking**
**Location:** Multiple files

```php
// Method 1: Direct session check
$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';

// Method 2: role_name check (missing in some files)
$_SESSION['role_name'] // Sometimes used, sometimes not

// Method 3: Permission-based
hasPermission('permission.name')
```

**Impact:**
- Fragile role detection
- Case sensitivity issues (SuperAdmin vs superadmin)
- No unified role checking service

#### 4. **Missing Role-Specific Menu Items**

**Current Menu Structure Analysis:**

| Menu Section | Permissions Required | Missing Roles |
|--------------|---------------------|---------------|
| **Stratejik** | aims.manage, objectives.manage, indicators.manage, actions.manage | ✅ Good |
| **İçerik** | news.manage, documentstrategies.manage, regulations.manage | ✅ Good |
| **Sistem** | users.manage, coves.manage, fields.manage, logs.manage | ❌ Missing Coordinator/User views |
| **Görev Yönetimi** | ❌ COMPLETELY MISSING | ❌ No task menu |
| **Raporlar** | ❌ COMPLETELY MISSING | ❌ No reports |
| **Profil/Ayarlar** | ✅ In user dropdown | ⚠️ Limited |

#### 5. **No Service Layer for Navigation**

**Current Architecture:**
```
View Component (navbar.php)
    ├─> Direct $_SESSION access
    ├─> Direct hasPermission() calls
    └─> Hard-coded menu arrays
```

**Should Be:**
```
Controller
    └─> NavigationService
            ├─> RoleService (role detection)
            ├─> PermissionService (permission checks)
            └─> MenuBuilder (dynamic menu generation)
                    └─> Returns role-specific menu array
```

---

## Proposed Solution: Modern Role-Based Navigation System

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                   Navigation System                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  BaseController                                              │
│       ├─> $this->navigationService->getMenuForRole()        │
│       └─> Passes menu data to view                          │
│                                                              │
│  NavigationService (NEW)                                     │
│       ├─> getMenuForRole($user)                             │
│       ├─> filterMenuByPermissions($menu, $permissions)      │
│       └─> buildBreadcrumbs($currentUrl)                     │
│                                                              │
│  RoleService (NEW)                                           │
│       ├─> getCurrentUserRole()                              │
│       ├─> isSuperAdmin()                                    │
│       ├─> isAdmin()                                         │
│       ├─> isCoordinator()                                   │
│       └─> canAccessModule($moduleName)                      │
│                                                              │
│  MenuConfig (NEW - Config File)                             │
│       └─> Complete menu structure with roles/permissions    │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## Implementation Plan

### Phase 1: Create Service Layer (Week 1)

#### 1.1 Create RoleService

**File:** `app/services/RoleService.php`

```php
<?php
namespace App\Services;

class RoleService
{
    // Role constants
    const ROLE_SUPERADMIN = 'SuperAdmin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_COORDINATOR = 'Coordinator';
    const ROLE_USER = 'User';

    /**
     * Get current user's role
     */
    public static function getCurrentRole(): string
    {
        if (!isset($_SESSION['role_name'])) {
            return self::ROLE_USER;
        }

        // Normalize role name
        $roleName = $_SESSION['role_name'];

        // Map variations to constants
        $roleMap = [
            'superadmin' => self::ROLE_SUPERADMIN,
            'admin' => self::ROLE_ADMIN,
            'coordinator' => self::ROLE_COORDINATOR,
            'user' => self::ROLE_USER
        ];

        $lowerRole = strtolower($roleName);
        return $roleMap[$lowerRole] ?? self::ROLE_USER;
    }

    /**
     * Check if current user is SuperAdmin
     */
    public static function isSuperAdmin(): bool
    {
        return self::getCurrentRole() === self::ROLE_SUPERADMIN;
    }

    /**
     * Check if current user is Admin or higher
     */
    public static function isAdmin(): bool
    {
        $role = self::getCurrentRole();
        return in_array($role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN]);
    }

    /**
     * Check if current user is Coordinator or higher
     */
    public static function isCoordinator(): bool
    {
        $role = self::getCurrentRole();
        return in_array($role, [
            self::ROLE_SUPERADMIN,
            self::ROLE_ADMIN,
            self::ROLE_COORDINATOR
        ]);
    }

    /**
     * Get role hierarchy level (higher = more permissions)
     */
    public static function getRoleLevel(string $role = null): int
    {
        $role = $role ?? self::getCurrentRole();

        $levels = [
            self::ROLE_USER => 1,
            self::ROLE_COORDINATOR => 2,
            self::ROLE_ADMIN => 3,
            self::ROLE_SUPERADMIN => 4
        ];

        return $levels[$role] ?? 0;
    }

    /**
     * Check if user can access a module
     */
    public static function canAccessModule(string $module): bool
    {
        // SuperAdmin can access everything
        if (self::isSuperAdmin()) {
            return true;
        }

        // Define module access by role
        $moduleAccess = [
            'tasks' => [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_COORDINATOR, self::ROLE_USER],
            'strategic' => [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_COORDINATOR],
            'content' => [self::ROLE_SUPERADMIN, self::ROLE_ADMIN],
            'system' => [self::ROLE_SUPERADMIN, self::ROLE_ADMIN],
            'reports' => [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_COORDINATOR]
        ];

        $allowedRoles = $moduleAccess[$module] ?? [];
        return in_array(self::getCurrentRole(), $allowedRoles);
    }

    /**
     * Get user's cove ID (for multi-tenant)
     */
    public static function getUserCoveId(): ?int
    {
        return $_SESSION['cove_id'] ?? null;
    }

    /**
     * Get user display name
     */
    public static function getUserDisplayName(): string
    {
        return $_SESSION['realname'] ?? $_SESSION['username'] ?? 'Kullanıcı';
    }

    /**
     * Get user initials for avatar
     */
    public static function getUserInitials(): string
    {
        $name = self::getUserDisplayName();
        $parts = explode(' ', $name);

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }
}
```

#### 1.2 Create NavigationService

**File:** `app/services/NavigationService.php`

```php
<?php
namespace App\Services;

require_once __DIR__ . '/RoleService.php';
require_once __DIR__ . '/../config/MenuConfig.php';

class NavigationService
{
    /**
     * Get menu items for current user's role
     */
    public static function getMenuForCurrentUser(): array
    {
        $role = RoleService::getCurrentRole();
        $menuConfig = \MenuConfig::getMenuItems();

        // Filter menu based on role and permissions
        return self::filterMenuItems($menuConfig, $role);
    }

    /**
     * Filter menu items based on role and permissions
     */
    private static function filterMenuItems(array $items, string $role): array
    {
        $filtered = [];

        foreach ($items as $item) {
            // Check if user has access to this item
            if (!self::userCanAccessItem($item, $role)) {
                continue;
            }

            // If item has children, filter them too
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = self::filterMenuItems($item['children'], $role);

                // Skip parent if no children remain
                if (empty($item['children'])) {
                    continue;
                }
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    /**
     * Check if user can access a menu item
     */
    private static function userCanAccessItem(array $item, string $role): bool
    {
        // No restrictions - everyone can access
        if (!isset($item['roles']) && !isset($item['permission'])) {
            return true;
        }

        // SuperAdmin can access everything
        if ($role === RoleService::ROLE_SUPERADMIN) {
            return true;
        }

        // Check role-based access
        if (isset($item['roles'])) {
            if (!in_array($role, $item['roles'])) {
                return false;
            }
        }

        // Check permission-based access
        if (isset($item['permission'])) {
            if (!hasPermission($item['permission'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build breadcrumbs from current URL
     */
    public static function buildBreadcrumbs(string $currentUrl): array
    {
        $breadcrumbs = [
            ['name' => 'Ana Sayfa', 'url' => 'home/index']
        ];

        // Parse URL and build breadcrumb trail
        $parts = explode('/', trim($currentUrl, '/'));

        if (count($parts) > 0 && $parts[0] !== 'home') {
            // Add module breadcrumb
            $moduleName = self::getModuleDisplayName($parts[0]);
            $breadcrumbs[] = [
                'name' => $moduleName,
                'url' => $parts[0] . '/index'
            ];

            // Add action breadcrumb if present
            if (count($parts) > 1 && $parts[1] !== 'index') {
                $actionName = self::getActionDisplayName($parts[1]);
                $breadcrumbs[] = [
                    'name' => $actionName,
                    'url' => null // Current page (no link)
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Get user-friendly module name
     */
    private static function getModuleDisplayName(string $module): string
    {
        $names = [
            'aim' => 'Amaçlar',
            'objective' => 'Hedefler',
            'indicator' => 'Göstergeler',
            'action' => 'Eylemler',
            'task' => 'Görevler',
            'news' => 'Haberler',
            'documentstrategy' => 'Belgeler',
            'regulation' => 'Mevzuat',
            'user' => 'Kullanıcılar',
            'cove' => 'SMM Merkezleri',
            'field' => 'SMM Alanları',
            'log' => 'Sistem Logları',
            'report' => 'Raporlar'
        ];

        return $names[$module] ?? ucfirst($module);
    }

    /**
     * Get user-friendly action name
     */
    private static function getActionDisplayName(string $action): string
    {
        $names = [
            'create' => 'Yeni Oluştur',
            'edit' => 'Düzenle',
            'view' => 'Görüntüle',
            'delete' => 'Sil',
            'manage' => 'Yönet',
            'calendar' => 'Takvim',
            'roles' => 'Roller',
            'editprofile' => 'Profil Düzenle'
        ];

        return $names[$action] ?? ucfirst($action);
    }

    /**
     * Get notification count for current user
     */
    public static function getNotificationCount(): int
    {
        // TODO: Implement actual notification counting
        // For now, return 0
        return 0;
    }

    /**
     * Get pending task count for current user
     */
    public static function getPendingTaskCount(): int
    {
        // TODO: Implement actual task counting from TaskAssignmentModel
        // For now, return 0
        return 0;
    }
}
```

#### 1.3 Create MenuConfig

**File:** `app/config/MenuConfig.php`

```php
<?php
/**
 * Central Menu Configuration
 * Defines all menu items with roles and permissions
 */

use App\Services\RoleService;

class MenuConfig
{
    /**
     * Get complete menu structure
     */
    public static function getMenuItems(): array
    {
        return [
            // Dashboard (All users)
            [
                'id' => 'dashboard',
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'url' => 'home/index',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN,
                    RoleService::ROLE_COORDINATOR,
                    RoleService::ROLE_USER
                ]
            ],

            // Task Management (NEW - All authenticated users)
            [
                'id' => 'tasks',
                'title' => 'Görev Yönetimi',
                'icon' => 'fas fa-tasks',
                'type' => 'section',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN,
                    RoleService::ROLE_COORDINATOR,
                    RoleService::ROLE_USER
                ],
                'children' => [
                    [
                        'title' => 'Görevlerim',
                        'icon' => 'fas fa-list-check',
                        'url' => 'task/myTasks',
                        'badge_function' => 'getPendingTaskCount',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN,
                            RoleService::ROLE_COORDINATOR,
                            RoleService::ROLE_USER
                        ]
                    ],
                    [
                        'title' => 'Görev Tanımları',
                        'icon' => 'fas fa-file-alt',
                        'url' => 'task/definitions',
                        'permission' => 'tasks.define',
                        'roles' => [RoleService::ROLE_SUPERADMIN]
                    ],
                    [
                        'title' => 'Görev Ata',
                        'icon' => 'fas fa-user-plus',
                        'url' => 'task/assign',
                        'permission' => 'tasks.assign',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN
                        ]
                    ],
                    [
                        'title' => 'Tüm Görevler',
                        'icon' => 'fas fa-list',
                        'url' => 'task/index',
                        'permission' => 'tasks.view_all',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN
                        ]
                    ],
                    [
                        'title' => 'Görev Raporları',
                        'icon' => 'fas fa-chart-bar',
                        'url' => 'task/reports',
                        'permission' => 'tasks.report',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN
                        ]
                    ]
                ]
            ],

            // Strategic Management
            [
                'id' => 'strategic',
                'title' => 'Stratejik Yönetim',
                'icon' => 'fas fa-bullseye',
                'type' => 'section',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN,
                    RoleService::ROLE_COORDINATOR
                ],
                'children' => [
                    [
                        'title' => 'Amaçlar',
                        'icon' => 'fas fa-bullseye',
                        'url' => 'aim/index',
                        'permission' => 'aims.manage',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN
                        ]
                    ],
                    [
                        'title' => 'Hedefler',
                        'icon' => 'fas fa-flag',
                        'url' => 'objective/index',
                        'permission' => 'objectives.manage',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN
                        ]
                    ],
                    [
                        'title' => 'Göstergeler',
                        'icon' => 'fas fa-chart-line',
                        'url' => 'indicator/index',
                        'permission' => 'indicators.manage',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN,
                            RoleService::ROLE_COORDINATOR
                        ]
                    ],
                    [
                        'title' => 'Eylemler',
                        'icon' => 'fas fa-tasks',
                        'url' => 'action/index',
                        'permission' => 'actions.manage',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN,
                            RoleService::ROLE_COORDINATOR
                        ]
                    ],
                    [
                        'title' => 'Takvim',
                        'icon' => 'fas fa-calendar',
                        'url' => 'action/calendar',
                        'permission' => 'actions.manage',
                        'roles' => [
                            RoleService::ROLE_SUPERADMIN,
                            RoleService::ROLE_ADMIN,
                            RoleService::ROLE_COORDINATOR
                        ]
                    ]
                ]
            ],

            // Content Management
            [
                'id' => 'content',
                'title' => 'İçerik Yönetimi',
                'icon' => 'fas fa-file-alt',
                'type' => 'section',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN
                ],
                'children' => [
                    [
                        'title' => 'Haberler',
                        'icon' => 'fas fa-newspaper',
                        'url' => 'news/index',
                        'permission' => 'news.manage'
                    ],
                    [
                        'title' => 'Belgeler',
                        'icon' => 'fas fa-file-pdf',
                        'url' => 'documentstrategy/index',
                        'permission' => 'documentstrategies.manage'
                    ],
                    [
                        'title' => 'Mevzuat',
                        'icon' => 'fas fa-book',
                        'url' => 'regulation/index',
                        'permission' => 'regulations.manage'
                    ]
                ]
            ],

            // Reports (NEW - For Admin/SuperAdmin/Coordinator)
            [
                'id' => 'reports',
                'title' => 'Raporlar',
                'icon' => 'fas fa-chart-pie',
                'type' => 'section',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN,
                    RoleService::ROLE_COORDINATOR
                ],
                'children' => [
                    [
                        'title' => 'Görev Raporları',
                        'icon' => 'fas fa-tasks',
                        'url' => 'report/tasks',
                        'permission' => 'tasks.report'
                    ],
                    [
                        'title' => 'Performans Raporları',
                        'icon' => 'fas fa-chart-bar',
                        'url' => 'report/performance',
                        'permission' => 'reports.view'
                    ],
                    [
                        'title' => 'Aktivite Logları',
                        'icon' => 'fas fa-history',
                        'url' => 'report/activity',
                        'permission' => 'logs.view'
                    ]
                ]
            ],

            // System Management
            [
                'id' => 'system',
                'title' => 'Sistem Yönetimi',
                'icon' => 'fas fa-cogs',
                'type' => 'section',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN
                ],
                'children' => [
                    [
                        'title' => 'Kullanıcılar',
                        'icon' => 'fas fa-users',
                        'url' => 'user/manage',
                        'permission' => 'users.manage'
                    ],
                    [
                        'title' => 'Roller',
                        'icon' => 'fas fa-shield-alt',
                        'url' => 'user/roles',
                        'permission' => 'users.manage'
                    ],
                    [
                        'title' => 'SMM Merkezleri',
                        'icon' => 'fas fa-building',
                        'url' => 'cove/index',
                        'permission' => 'coves.manage'
                    ],
                    [
                        'title' => 'SMM Alanları',
                        'icon' => 'fas fa-th',
                        'url' => 'field/index',
                        'permission' => 'fields.manage'
                    ],
                    [
                        'title' => 'Sistem Logları',
                        'icon' => 'fas fa-history',
                        'url' => 'log/index',
                        'permission' => 'logs.manage',
                        'roles' => [RoleService::ROLE_SUPERADMIN]
                    ]
                ]
            ],

            // Help (All users)
            [
                'id' => 'help',
                'title' => 'Yardım',
                'icon' => 'fas fa-question-circle',
                'url' => 'help/index',
                'roles' => [
                    RoleService::ROLE_SUPERADMIN,
                    RoleService::ROLE_ADMIN,
                    RoleService::ROLE_COORDINATOR,
                    RoleService::ROLE_USER
                ]
            ]
        ];
    }

    /**
     * Get user menu items (profile dropdown)
     */
    public static function getUserMenuItems(): array
    {
        return [
            [
                'title' => 'Profilim',
                'icon' => 'fas fa-user',
                'url' => 'user/editprofile'
            ],
            [
                'title' => 'Ayarlar',
                'icon' => 'fas fa-cog',
                'url' => 'user/settings'
            ],
            [
                'type' => 'divider'
            ],
            [
                'title' => 'Çıkış Yap',
                'icon' => 'fas fa-sign-out-alt',
                'url' => 'user/logout',
                'class' => 'text-danger'
            ]
        ];
    }
}
```

### Phase 2: Update Components (Week 2)

#### 2.1 Update navbar.php to use Services

**File:** `app/views/components/navbar_v2.php` (New file)

```php
<?php
/**
 * Modern Role-Based Navbar
 * Uses NavigationService for dynamic menu generation
 */

use App\Services\RoleService;
use App\Services\NavigationService;

// Get current URL and user info
$currentUrl = $_GET['url'] ?? '';
$isLoggedIn = isset($_SESSION['username']);

// Get menu items based on role
$menuItems = $isLoggedIn ? NavigationService::getMenuForCurrentUser() : [];
$userMenuItems = \MenuConfig::getUserMenuItems();

// Get user info
$displayName = RoleService::getUserDisplayName();
$roleName = RoleService::getCurrentRole();
$userInitials = RoleService::getUserInitials();

// Get notification/task counts
$notificationCount = NavigationService::getNotificationCount();
$taskCount = NavigationService::getPendingTaskCount();
?>

<!-- Rest of navbar HTML using $menuItems array instead of hard-coded checks -->
```

This implementation provides:
1. **Centralized configuration**
2. **Role-based filtering**
3. **Permission-based access**
4. **Easy maintenance**
5. **Dynamic badge counts**

---

## Benefits of Proposed System

### 1. **Maintainability**
- Single source of truth for menus (`MenuConfig.php`)
- Easy to add/remove/modify menu items
- No code duplication

### 2. **Security**
- Consistent permission checking
- Role-based access control
- Cannot bypass security by URL manipulation

### 3. **Scalability**
- Easy to add new roles
- Simple to add Task Management module
- Can add role-specific features quickly

### 4. **Testing**
- Services are unit-testable
- Mock-friendly architecture
- Clear separation of concerns

### 5. **User Experience**
- Role-appropriate menus
- No cluttered interfaces
- Fast navigation
- Badge notifications

---

## Migration Path

### Option A: Gradual Migration (Recommended)
1. Create service layer (Phase 1)
2. Create `navbar_v2.php` using services
3. Test with SuperAdmin users first
4. Gradually switch layouts to use new navbar
5. Remove old navbar once stable

### Option B: Big Bang Migration
1. Implement all services
2. Update all components at once
3. Deploy and monitor

**Recommendation:** Option A - Less risky, easier to rollback

---

## Summary

**Current State:**
- ❌ Hard-coded menus
- ❌ Duplicated permission logic
- ❌ No service layer
- ❌ Missing task management menu
- ⚠️ Inconsistent role checking

**Proposed State:**
- ✅ Service-driven navigation
- ✅ Centralized menu configuration
- ✅ Role-based filtering
- ✅ Task management integrated
- ✅ Clean, testable architecture

**Estimated Effort:**
- Phase 1 (Services): 2-3 days
- Phase 2 (Components): 2-3 days
- Testing: 1-2 days
- **Total:** 1-1.5 weeks

---

**Next Steps:**
1. Review and approve this proposal
2. Create RoleService.php
3. Create NavigationService.php
4. Create MenuConfig.php
5. Test with multiple user roles

**Document Version:** 1.0
**Status:** Ready for Implementation
