<?php
/**
 * Menu Helper - Role-based Dynamic Menu System
 * MTEGM SMM Portal
 */

class MenuHelper {

    /**
     * Get menu items based on user role
     */
    public static function getMenuItems($roleId = null) {
        if (!$roleId) {
            $roleId = $_SESSION['role_id'] ?? 4; // Default to user role
        }

        $allMenuItems = [
            // Dashboard - Everyone
            [
                'id' => 'dashboard',
                'title' => 'Ana Sayfa',
                'icon' => 'bi-speedometer2',
                'url' => '/home',
                'roles' => [1, 2, 3, 4], // All roles
                'parent' => null
            ],

            // Strategic Management Section
            [
                'id' => 'strategic',
                'title' => 'Stratejik Yönetim',
                'type' => 'heading',
                'roles' => [1, 2, 3, 4]
            ],
            [
                'id' => 'objectives',
                'title' => 'Amaçlar',
                'icon' => 'bi-bullseye',
                'url' => '/objective',
                'roles' => [1, 2, 3, 4],
                'parent' => 'strategic'
            ],
            [
                'id' => 'actions',
                'title' => 'Faaliyetler',
                'icon' => 'bi-list-task',
                'url' => '/action',
                'roles' => [1, 2, 3, 4],
                'parent' => 'strategic'
            ],
            [
                'id' => 'indicators',
                'title' => 'Göstergeler',
                'icon' => 'bi-graph-up',
                'url' => '/indicator',
                'roles' => [1, 2, 3],
                'parent' => 'strategic'
            ],
            [
                'id' => 'aims',
                'title' => 'Hedefler',
                'icon' => 'bi-target',
                'url' => '/aim',
                'roles' => [1, 2, 3],
                'parent' => 'strategic'
            ],

            // Management Section
            [
                'id' => 'management',
                'title' => 'Yönetim',
                'type' => 'heading',
                'roles' => [1, 2] // Admin and coordinator only
            ],
            [
                'id' => 'users',
                'title' => 'Kullanıcılar',
                'icon' => 'bi-people',
                'url' => '/user',
                'roles' => [1, 2],
                'parent' => 'management',
                'badge' => self::getUserCount() // Dynamic badge
            ],
            [
                'id' => 'coves',
                'title' => 'Birimler',
                'icon' => 'bi-building',
                'url' => '/cove',
                'roles' => [1, 2],
                'parent' => 'management'
            ],
            [
                'id' => 'fields',
                'title' => 'Alanlar',
                'icon' => 'bi-tags',
                'url' => '/field',
                'roles' => [1, 2, 3],
                'parent' => 'management'
            ],
            [
                'id' => 'roles',
                'title' => 'Roller ve Yetkiler',
                'icon' => 'bi-shield-lock',
                'url' => '/role',
                'roles' => [1], // Superadmin only
                'parent' => 'management'
            ],

            // Reports Section
            [
                'id' => 'reports_section',
                'title' => 'Raporlar',
                'type' => 'heading',
                'roles' => [1, 2, 3]
            ],
            [
                'id' => 'reports',
                'title' => 'Raporlar',
                'icon' => 'bi-file-earmark-bar-graph',
                'url' => '/report',
                'roles' => [1, 2, 3],
                'parent' => 'reports_section'
            ],
            [
                'id' => 'logs',
                'title' => 'İşlem Kayıtları',
                'icon' => 'bi-clock-history',
                'url' => '/log',
                'roles' => [1, 2],
                'parent' => 'reports_section'
            ],
            [
                'id' => 'statistics',
                'title' => 'İstatistikler',
                'icon' => 'bi-bar-chart-line',
                'url' => '/statistics',
                'roles' => [1, 2, 3],
                'parent' => 'reports_section'
            ],

            // Content Section
            [
                'id' => 'content',
                'title' => 'İçerik Yönetimi',
                'type' => 'heading',
                'roles' => [1, 2]
            ],
            [
                'id' => 'news',
                'title' => 'Haberler',
                'icon' => 'bi-newspaper',
                'url' => '/news',
                'roles' => [1, 2],
                'parent' => 'content'
            ],
            [
                'id' => 'documents',
                'title' => 'Dokümanlar',
                'icon' => 'bi-file-earmark-text',
                'url' => '/document',
                'roles' => [1, 2, 3],
                'parent' => 'content'
            ],

            // System Section
            [
                'id' => 'system',
                'title' => 'Sistem',
                'type' => 'heading',
                'roles' => [1] // Superadmin only
            ],
            [
                'id' => 'settings',
                'title' => 'Ayarlar',
                'icon' => 'bi-gear',
                'url' => '/settings',
                'roles' => [1],
                'parent' => 'system'
            ],
            [
                'id' => 'backup',
                'title' => 'Yedekleme',
                'icon' => 'bi-cloud-download',
                'url' => '/backup',
                'roles' => [1],
                'parent' => 'system'
            ],
            [
                'id' => 'health',
                'title' => 'Sistem Sağlığı',
                'icon' => 'bi-heart-pulse',
                'url' => '/health',
                'roles' => [1],
                'parent' => 'system'
            ]
        ];

        // Filter menu items based on user role
        return array_filter($allMenuItems, function($item) use ($roleId) {
            return in_array($roleId, $item['roles']);
        });
    }

    /**
     * Generate breadcrumb for current page
     */
    public static function getBreadcrumb($currentPath = null) {
        if (!$currentPath) {
            $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
        }

        // Parse the path
        $pathParts = explode('/', trim($currentPath, '/'));
        $controller = $pathParts[0] ?? 'home';
        $action = $pathParts[1] ?? 'index';
        $id = $pathParts[2] ?? null;

        $breadcrumb = [
            ['title' => 'Ana Sayfa', 'url' => '/home', 'icon' => 'bi-house']
        ];

        // Map controllers to breadcrumb items
        $controllerMap = [
            'objective' => ['title' => 'Amaçlar', 'icon' => 'bi-bullseye'],
            'action' => ['title' => 'Faaliyetler', 'icon' => 'bi-list-task'],
            'indicator' => ['title' => 'Göstergeler', 'icon' => 'bi-graph-up'],
            'user' => ['title' => 'Kullanıcılar', 'icon' => 'bi-people'],
            'cove' => ['title' => 'Birimler', 'icon' => 'bi-building'],
            'field' => ['title' => 'Alanlar', 'icon' => 'bi-tags'],
            'report' => ['title' => 'Raporlar', 'icon' => 'bi-file-earmark-bar-graph'],
            'log' => ['title' => 'İşlem Kayıtları', 'icon' => 'bi-clock-history'],
            'news' => ['title' => 'Haberler', 'icon' => 'bi-newspaper'],
            'settings' => ['title' => 'Ayarlar', 'icon' => 'bi-gear']
        ];

        if ($controller != 'home' && isset($controllerMap[$controller])) {
            $breadcrumb[] = [
                'title' => $controllerMap[$controller]['title'],
                'icon' => $controllerMap[$controller]['icon'],
                'url' => '/' . $controller
            ];

            // Add action breadcrumb
            $actionMap = [
                'create' => 'Yeni Ekle',
                'edit' => 'Düzenle',
                'view' => 'Görüntüle',
                'delete' => 'Sil',
                'report' => 'Rapor',
                'export' => 'Dışa Aktar'
            ];

            if ($action != 'index' && isset($actionMap[$action])) {
                $breadcrumb[] = [
                    'title' => $actionMap[$action],
                    'url' => null // Current page, no link
                ];
            }
        }

        return $breadcrumb;
    }

    /**
     * Check if user has access to menu item
     */
    public static function hasAccess($menuId, $roleId = null) {
        if (!$roleId) {
            $roleId = $_SESSION['role_id'] ?? 4;
        }

        $menuItems = self::getMenuItems($roleId);
        foreach ($menuItems as $item) {
            if ($item['id'] == $menuId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get role name
     */
    public static function getRoleName($roleId) {
        $roles = [
            1 => 'Süper Yönetici',
            2 => 'Yönetici',
            3 => 'Koordinatör',
            4 => 'Kullanıcı'
        ];
        return $roles[$roleId] ?? 'Misafir';
    }

    /**
     * Get user count for badge (example)
     */
    private static function getUserCount() {
        // This would normally come from database
        return null; // Return null to hide badge, or number to show
    }

    /**
     * Generate menu HTML
     */
    public static function renderMenu($activeMenu = '', $roleId = null) {
        $menuItems = self::getMenuItems($roleId);
        $html = '<ul class="nav flex-column">';

        $currentHeading = null;
        foreach ($menuItems as $item) {
            // Section heading
            if (isset($item['type']) && $item['type'] == 'heading') {
                if ($currentHeading !== null) {
                    // Close previous section
                }
                $html .= '<h6 class="sidebar-heading mt-3"><span>' . $item['title'] . '</span></h6>';
                $currentHeading = $item['id'];
                continue;
            }

            // Skip if it's a child of different heading
            if (isset($item['parent']) && $item['parent'] != $currentHeading && $item['parent'] !== null) {
                continue;
            }

            // Regular menu item
            $isActive = ($activeMenu == $item['id']) ? 'active' : '';
            $badge = isset($item['badge']) && $item['badge'] ?
                     '<span class="badge bg-danger rounded-pill ms-auto">' . $item['badge'] . '</span>' : '';

            $html .= '<li class="nav-item">';
            $html .= '<a class="nav-link ' . $isActive . '" href="' . $item['url'] . '">';
            $html .= '<i class="bi ' . $item['icon'] . ' me-2"></i>';
            $html .= $item['title'];
            $html .= $badge;
            $html .= '</a>';
            $html .= '</li>';
        }

        $html .= '</ul>';
        return $html;
    }

    /**
     * Render breadcrumb HTML
     */
    public static function renderBreadcrumb($currentPath = null) {
        $breadcrumb = self::getBreadcrumb($currentPath);

        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';

        foreach ($breadcrumb as $index => $item) {
            $isLast = ($index == count($breadcrumb) - 1);
            $icon = isset($item['icon']) ? '<i class="bi ' . $item['icon'] . ' me-1"></i>' : '';

            if ($isLast || !isset($item['url'])) {
                // Current page - no link
                $html .= '<li class="breadcrumb-item active" aria-current="page">';
                $html .= $icon . $item['title'];
                $html .= '</li>';
            } else {
                // Link to page
                $html .= '<li class="breadcrumb-item">';
                $html .= '<a href="' . $item['url'] . '">' . $icon . $item['title'] . '</a>';
                $html .= '</li>';
            }
        }

        $html .= '</ol>';
        $html .= '</nav>';
        return $html;
    }
}