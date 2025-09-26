<?php
/**
 * NavbarService - Gelişmiş Navbar Yönetim Sistemi
 *
 * Dinamik badge'ler, contextual menüler, widget desteği ve
 * mobil optimizasyon özellikleri sağlar
 *
 * @author Claude AI Assistant
 * @version 2.0.0
 */

namespace App\Services;

class NavbarService
{
    /**
     * Badge önbelleği (performans için)
     */
    private static $badgeCache = [];

    /**
     * Widget listesi
     */
    private static $widgets = [];

    /**
     * Dinamik badge sayılarını getir
     *
     * @return array Badge verileri
     */
    public static function getDynamicBadges(): array
    {
        // Cache varsa kullan
        if (!empty(self::$badgeCache)) {
            return self::$badgeCache;
        }

        $badges = [
            'notifications' => 0,
            'tasks' => 0,
            'messages' => 0,
            'pending_approvals' => 0,
            'alerts' => 0
        ];

        // Oturum açmış kullanıcı için badge'leri hesapla
        if (isset($_SESSION['user_id'])) {
            try {
                require_once dirname(__DIR__) . '/config/config.php';
                $db = getDbConnection();

                // Bekleyen görevler
                $stmt = $db->prepare("
                    SELECT COUNT(*) as count
                    FROM actions
                    WHERE action_responsible = :user_id
                    AND action_status = 0
                    AND date_end >= CURDATE()
                ");
                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $badges['tasks'] = $result['count'] ?? 0;

                // Okunmamış bildirimler (notifications tablosu varsa)
                $stmt = $db->prepare("
                    SELECT COUNT(*) as count
                    FROM notifications
                    WHERE user_id = :user_id
                    AND is_read = 0
                ");
                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $badges['notifications'] = $result['count'] ?? 0;

                // Admin için onay bekleyen işlemler
                if (self::isAdmin()) {
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM users
                        WHERE status = 0
                    ");
                    $stmt->execute();
                    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $badges['pending_approvals'] = $result['count'] ?? 0;
                }

                // Son 24 saatteki sistem uyarıları (superadmin için)
                if (self::isSuperAdmin()) {
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM logs
                        WHERE log_type = 'error'
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ");
                    $stmt->execute();
                    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $badges['alerts'] = $result['count'] ?? 0;
                }

            } catch (\Exception $e) {
                error_log("NavbarService badge error: " . $e->getMessage());
            }
        }

        // Cache'e kaydet (5 dakika)
        self::$badgeCache = $badges;

        return $badges;
    }

    /**
     * Kullanıcı rolüne göre contextual menü öğelerini getir
     *
     * @return array Menü öğeleri
     */
    public static function getContextualMenuItems(): array
    {
        $items = [];

        if (!isset($_SESSION['user_id'])) {
            return self::getPublicMenuItems();
        }

        // Temel menü öğeleri
        $items = [
            'dashboard' => [
                'title' => 'Yönetim Paneli',
                'url' => 'home/index',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => null, // Herkes erişebilir
                'badge' => null
            ]
        ];

        // Rol bazlı menüler
        $userRole = strtolower($_SESSION['role'] ?? 'user');

        // Kullanıcı son aktivitesine göre hızlı erişim menüsü
        $items['quick_access'] = self::getQuickAccessMenu();

        // Rol bazlı özel menüler
        switch ($userRole) {
            case 'superadmin':
                $items = array_merge($items, self::getSuperAdminMenuItems());
                break;
            case 'coordinator':
                $items = array_merge($items, self::getCoordinatorMenuItems());
                break;
            case 'admin':
                $items = array_merge($items, self::getAdminMenuItems());
                break;
            default:
                $items = array_merge($items, self::getUserMenuItems());
                break;
        }

        // Badge'leri ekle
        $badges = self::getDynamicBadges();
        foreach ($items as &$item) {
            if (isset($item['badge_key']) && isset($badges[$item['badge_key']])) {
                $item['badge'] = $badges[$item['badge_key']];
            }
        }

        return $items;
    }

    /**
     * Hızlı erişim menüsü (son kullanılan özellikler)
     */
    private static function getQuickAccessMenu(): array
    {
        return [
            'title' => 'Hızlı Erişim',
            'type' => 'dropdown',
            'icon' => 'fas fa-bolt',
            'items' => [
                [
                    'title' => 'Yeni Faaliyet',
                    'url' => 'action/create',
                    'icon' => 'fas fa-plus-circle text-success'
                ],
                [
                    'title' => 'Görev Takvimi',
                    'url' => 'action/calendar',
                    'icon' => 'fas fa-calendar-alt text-primary'
                ],
                [
                    'title' => 'Raporlar',
                    'url' => 'indicator/adminReport',
                    'icon' => 'fas fa-chart-line text-info'
                ]
            ]
        ];
    }

    /**
     * SuperAdmin menü öğeleri
     */
    private static function getSuperAdminMenuItems(): array
    {
        return [
            'system_management' => [
                'title' => 'Sistem Yönetimi',
                'type' => 'dropdown',
                'icon' => 'fas fa-server',
                'badge_key' => 'alerts',
                'items' => [
                    [
                        'title' => 'Sistem Durumu',
                        'url' => 'system/status',
                        'icon' => 'fas fa-heartbeat text-danger'
                    ],
                    [
                        'title' => 'Veritabanı Yedekleme',
                        'url' => 'system/backup',
                        'icon' => 'fas fa-database text-primary'
                    ],
                    [
                        'title' => 'Güvenlik Logları',
                        'url' => 'log/security',
                        'icon' => 'fas fa-shield-alt text-warning'
                    ],
                    [
                        'title' => 'API Anahtarları',
                        'url' => 'system/api-keys',
                        'icon' => 'fas fa-key text-success'
                    ]
                ]
            ]
        ];
    }

    /**
     * Coordinator menü öğeleri
     */
    private static function getCoordinatorMenuItems(): array
    {
        return [
            'coordination' => [
                'title' => 'Koordinasyon',
                'type' => 'dropdown',
                'icon' => 'fas fa-project-diagram',
                'badge_key' => 'tasks',
                'items' => [
                    [
                        'title' => 'Ekip Yönetimi',
                        'url' => 'team/manage',
                        'icon' => 'fas fa-users text-primary'
                    ],
                    [
                        'title' => 'Görev Dağıtımı',
                        'url' => 'action/taskList',
                        'icon' => 'fas fa-tasks text-warning',
                        'badge_key' => 'tasks'
                    ],
                    [
                        'title' => 'İlerleme Raporu',
                        'url' => 'reports/progress',
                        'icon' => 'fas fa-chart-pie text-success'
                    ]
                ]
            ]
        ];
    }

    /**
     * Admin menü öğeleri
     */
    private static function getAdminMenuItems(): array
    {
        return [
            'administration' => [
                'title' => 'Yönetim',
                'type' => 'dropdown',
                'icon' => 'fas fa-cog',
                'badge_key' => 'pending_approvals',
                'items' => [
                    [
                        'title' => 'Kullanıcı Onayları',
                        'url' => 'user/approvals',
                        'icon' => 'fas fa-user-check text-warning',
                        'badge_key' => 'pending_approvals'
                    ],
                    [
                        'title' => 'İçerik Yönetimi',
                        'url' => 'content/manage',
                        'icon' => 'fas fa-file-alt text-info'
                    ]
                ]
            ]
        ];
    }

    /**
     * Standart kullanıcı menü öğeleri
     */
    private static function getUserMenuItems(): array
    {
        return [
            'my_work' => [
                'title' => 'Çalışmalarım',
                'type' => 'dropdown',
                'icon' => 'fas fa-briefcase',
                'badge_key' => 'tasks',
                'items' => [
                    [
                        'title' => 'Görevlerim',
                        'url' => 'action/myTasks',
                        'icon' => 'fas fa-clipboard-list text-primary',
                        'badge_key' => 'tasks'
                    ],
                    [
                        'title' => 'Takvimim',
                        'url' => 'action/calendar',
                        'icon' => 'fas fa-calendar text-success'
                    ]
                ]
            ]
        ];
    }

    /**
     * Public (giriş yapmamış) kullanıcılar için menü
     */
    private static function getPublicMenuItems(): array
    {
        return [
            'home' => [
                'title' => 'Ana Sayfa',
                'url' => 'user/main',
                'icon' => 'fas fa-home'
            ],
            'news' => [
                'title' => 'Haberler',
                'url' => 'user/haberlist',
                'icon' => 'fas fa-newspaper'
            ],
            'network' => [
                'title' => 'SMM Haritası',
                'url' => 'home/smmnetwork',
                'icon' => 'fas fa-map-marked-alt'
            ]
        ];
    }

    /**
     * Widget ekle
     *
     * @param string $id Widget ID
     * @param array $config Widget konfigürasyonu
     */
    public static function registerWidget(string $id, array $config): void
    {
        self::$widgets[$id] = $config;
    }

    /**
     * Navbar widget'larını getir
     */
    public static function getWidgets(): array
    {
        $activeWidgets = [];

        // Varsayılan widget'lar
        if (isset($_SESSION['user_id'])) {
            // Bildirim widget'ı
            $activeWidgets['notifications'] = [
                'type' => 'notification-bell',
                'badge' => self::getDynamicBadges()['notifications'] ?? 0,
                'dropdown_content' => self::getNotificationList()
            ];

            // Arama widget'ı
            $activeWidgets['search'] = [
                'type' => 'search-box',
                'placeholder' => 'Ara...',
                'shortcut' => 'Ctrl+K'
            ];

            // Dil seçici (gelecekte çok dilli destek için)
            $activeWidgets['language'] = [
                'type' => 'language-selector',
                'current' => 'tr',
                'options' => ['tr' => 'Türkçe', 'en' => 'English']
            ];
        }

        // Kayıtlı widget'ları ekle
        foreach (self::$widgets as $id => $widget) {
            if (self::canShowWidget($widget)) {
                $activeWidgets[$id] = $widget;
            }
        }

        return $activeWidgets;
    }

    /**
     * Widget gösterilmeli mi kontrolü
     */
    private static function canShowWidget(array $widget): bool
    {
        if (isset($widget['permission'])) {
            require_once dirname(__DIR__) . '/../includes/PermissionHelper.php';
            return hasPermission($widget['permission']);
        }

        if (isset($widget['role'])) {
            return strtolower($_SESSION['role'] ?? '') === $widget['role'];
        }

        return true;
    }

    /**
     * Bildirim listesi getir (örnek)
     */
    private static function getNotificationList(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Yeni görev atandı',
                'message' => '2024 Stratejik Plan görevi size atandı',
                'time' => '5 dakika önce',
                'icon' => 'fas fa-tasks text-primary',
                'url' => 'action/view/123'
            ],
            [
                'id' => 2,
                'title' => 'Sistem güncellemesi',
                'message' => 'Portal v2.0 güncellemesi tamamlandı',
                'time' => '1 saat önce',
                'icon' => 'fas fa-info-circle text-info',
                'url' => 'system/updates'
            ]
        ];
    }

    /**
     * Mobil menü için optimize edilmiş veri
     */
    public static function getMobileMenuData(): array
    {
        $menu = self::getContextualMenuItems();

        // Mobil için sadeleştir
        $mobileMenu = [];
        foreach ($menu as $key => $item) {
            if ($item['type'] ?? '' === 'dropdown') {
                // Dropdown'ları düzleştir
                foreach ($item['items'] ?? [] as $subItem) {
                    $mobileMenu[] = [
                        'title' => $subItem['title'],
                        'url' => $subItem['url'],
                        'icon' => $subItem['icon'] ?? 'fas fa-circle',
                        'badge' => $subItem['badge'] ?? null,
                        'group' => $item['title']
                    ];
                }
            } else {
                $mobileMenu[] = $item;
            }
        }

        return $mobileMenu;
    }

    /**
     * Helper metodlar
     */
    private static function isAdmin(): bool
    {
        $role = strtolower($_SESSION['role'] ?? '');
        return in_array($role, ['admin', 'superadmin', 'coordinator']);
    }

    private static function isSuperAdmin(): bool
    {
        return strtolower($_SESSION['role'] ?? '') === 'superadmin';
    }

    /**
     * Badge cache'i temizle
     */
    public static function clearBadgeCache(): void
    {
        self::$badgeCache = [];
    }
}