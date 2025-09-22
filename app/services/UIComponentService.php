<?php
/**
 * UIComponentService - Tüm UI Komponenlerini Yöneten Merkezi Servis
 *
 * Sidebar, Widget Framework, Modal/Alert, Dashboard Layouts
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

namespace App\Services;

class UIComponentService
{
    /**
     * Dashboard layout türleri
     */
    const LAYOUT_GRID = 'grid';           // Grid based layout
    const LAYOUT_FLUID = 'fluid';         // Full width fluid
    const LAYOUT_BOXED = 'boxed';         // Boxed with margins
    const LAYOUT_COMPACT = 'compact';      // Compact sidebar
    const LAYOUT_HORIZONTAL = 'horizontal'; // Horizontal navigation
    const LAYOUT_DARK = 'dark';            // Dark theme

    /**
     * Widget türleri ve durumları
     */
    private static $widgetTypes = [
        'stat-card' => [
            'template' => 'widgets/stat-card',
            'refresh_interval' => 60000,
            'cache_duration' => 300
        ],
        'chart' => [
            'template' => 'widgets/chart',
            'refresh_interval' => 120000,
            'cache_duration' => 600
        ],
        'activity-feed' => [
            'template' => 'widgets/activity-feed',
            'refresh_interval' => 30000,
            'cache_duration' => 60
        ],
        'calendar' => [
            'template' => 'widgets/calendar',
            'refresh_interval' => 300000,
            'cache_duration' => 900
        ],
        'progress-tracker' => [
            'template' => 'widgets/progress-tracker',
            'refresh_interval' => 60000,
            'cache_duration' => 300
        ],
        'quick-actions' => [
            'template' => 'widgets/quick-actions',
            'refresh_interval' => null,
            'cache_duration' => 3600
        ],
        'weather' => [
            'template' => 'widgets/weather',
            'refresh_interval' => 600000,
            'cache_duration' => 1800
        ],
        'todo-list' => [
            'template' => 'widgets/todo-list',
            'refresh_interval' => 30000,
            'cache_duration' => 60
        ],
        'recent-documents' => [
            'template' => 'widgets/recent-documents',
            'refresh_interval' => 120000,
            'cache_duration' => 300
        ],
        'team-members' => [
            'template' => 'widgets/team-members',
            'refresh_interval' => 300000,
            'cache_duration' => 900
        ]
    ];

    /**
     * Sidebar Konfigürasyonu Oluştur
     *
     * @param array $options Sidebar seçenekleri
     * @return array Sidebar konfigürasyonu
     */
    public static function getSidebarConfig(array $options = []): array
    {
        $userRole = strtolower($_SESSION['role'] ?? 'user');
        $userId = $_SESSION['user_id'] ?? null;

        $config = [
            'style' => $options['style'] ?? 'vertical', // vertical, horizontal, compact
            'theme' => $options['theme'] ?? 'light',    // light, dark, auto
            'position' => $options['position'] ?? 'left', // left, right
            'width' => $options['width'] ?? '260px',
            'collapsed' => $options['collapsed'] ?? false,
            'fixed' => $options['fixed'] ?? true,
            'items' => []
        ];

        // Ana menü öğeleri
        $config['items'] = [
            [
                'id' => 'dashboard',
                'title' => 'Dashboard',
                'icon' => 'fas fa-home',
                'url' => 'home/index',
                'badge' => null,
                'active' => self::isActive('home/index')
            ]
        ];

        // Stratejik Planlama
        if (self::hasAccess('strategic_planning', $userRole)) {
            $config['items'][] = [
                'id' => 'strategic',
                'title' => 'Stratejik Planlama',
                'icon' => 'fas fa-chess',
                'badge' => self::getBadgeCount('strategic'),
                'children' => [
                    ['title' => 'Amaçlar', 'icon' => 'fas fa-bullseye', 'url' => 'aim/index'],
                    ['title' => 'Hedefler', 'icon' => 'fas fa-flag', 'url' => 'objective/index'],
                    ['title' => 'Göstergeler', 'icon' => 'fas fa-chart-line', 'url' => 'indicator/index'],
                    ['title' => 'Faaliyetler', 'icon' => 'fas fa-tasks', 'url' => 'action/index'],
                    ['title' => 'Takvim', 'icon' => 'fas fa-calendar', 'url' => 'action/calendar']
                ]
            ];
        }

        // İçerik Yönetimi
        if (self::hasAccess('content_management', $userRole)) {
            $config['items'][] = [
                'id' => 'content',
                'title' => 'İçerik Yönetimi',
                'icon' => 'fas fa-file-alt',
                'badge' => self::getBadgeCount('content'),
                'children' => [
                    ['title' => 'Haberler', 'icon' => 'fas fa-newspaper', 'url' => 'news/index'],
                    ['title' => 'Belgeler', 'icon' => 'fas fa-file-pdf', 'url' => 'documentstrategy/index'],
                    ['title' => 'Mevzuat', 'icon' => 'fas fa-book', 'url' => 'regulation/index'],
                    ['title' => 'Medya', 'icon' => 'fas fa-photo-video', 'url' => 'media/index']
                ]
            ];
        }

        // Sistem Yönetimi
        if (self::hasAccess('system_management', $userRole)) {
            $config['items'][] = [
                'id' => 'system',
                'title' => 'Sistem',
                'icon' => 'fas fa-cogs',
                'badge' => self::getBadgeCount('system'),
                'children' => [
                    ['title' => 'Kullanıcılar', 'icon' => 'fas fa-users', 'url' => 'user/manage'],
                    ['title' => 'Roller', 'icon' => 'fas fa-shield-alt', 'url' => 'user/roles'],
                    ['title' => 'İzinler', 'icon' => 'fas fa-key', 'url' => 'user/permissions'],
                    ['title' => 'SMM Merkezleri', 'icon' => 'fas fa-building', 'url' => 'cove/index'],
                    ['title' => 'Alanlar', 'icon' => 'fas fa-th', 'url' => 'field/index'],
                    ['title' => 'Loglar', 'icon' => 'fas fa-history', 'url' => 'log/index'],
                    ['title' => 'Ayarlar', 'icon' => 'fas fa-sliders-h', 'url' => 'system/settings']
                ]
            ];
        }

        // Raporlar
        $config['items'][] = [
            'id' => 'reports',
            'title' => 'Raporlar',
            'icon' => 'fas fa-chart-bar',
            'children' => [
                ['title' => 'Performans', 'icon' => 'fas fa-tachometer-alt', 'url' => 'reports/performance'],
                ['title' => 'İstatistikler', 'icon' => 'fas fa-chart-pie', 'url' => 'reports/statistics'],
                ['title' => 'Analizler', 'icon' => 'fas fa-analytics', 'url' => 'reports/analytics']
            ]
        ];

        // Alt bölüm (footer items)
        $config['footer_items'] = [
            ['title' => 'Yardım', 'icon' => 'fas fa-question-circle', 'url' => 'help/index'],
            ['title' => 'Ayarlar', 'icon' => 'fas fa-cog', 'url' => 'user/settings'],
            ['title' => 'Çıkış', 'icon' => 'fas fa-sign-out-alt', 'url' => 'user/logout', 'class' => 'text-danger']
        ];

        return $config;
    }

    /**
     * Widget Framework - Tam özellikli widget sistemi
     *
     * @param string $dashboardId Dashboard ID
     * @return array Widget listesi ve konfigürasyonu
     */
    public static function getDashboardWidgets(string $dashboardId = 'main'): array
    {
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = strtolower($_SESSION['role'] ?? 'user');

        // Kullanıcı widget tercihlerini al (veritabanı veya session'dan)
        $userPreferences = self::getUserWidgetPreferences($userId, $dashboardId);

        // Varsayılan widget layout
        $defaultWidgets = [
            // Row 1 - İstatistik kartları
            [
                'row' => 1,
                'widgets' => [
                    ['type' => 'stat-card', 'id' => 'total-users', 'col' => 3, 'data' => self::getUserStats()],
                    ['type' => 'stat-card', 'id' => 'active-tasks', 'col' => 3, 'data' => self::getTaskStats()],
                    ['type' => 'stat-card', 'id' => 'completion-rate', 'col' => 3, 'data' => self::getCompletionStats()],
                    ['type' => 'stat-card', 'id' => 'notifications', 'col' => 3, 'data' => self::getNotificationStats()]
                ]
            ],
            // Row 2 - Ana içerik
            [
                'row' => 2,
                'widgets' => [
                    ['type' => 'chart', 'id' => 'performance-chart', 'col' => 8, 'data' => self::getPerformanceChart()],
                    ['type' => 'activity-feed', 'id' => 'recent-activity', 'col' => 4, 'data' => self::getActivityFeed()]
                ]
            ],
            // Row 3 - İkincil içerik
            [
                'row' => 3,
                'widgets' => [
                    ['type' => 'calendar', 'id' => 'calendar-widget', 'col' => 4, 'data' => self::getCalendarData()],
                    ['type' => 'todo-list', 'id' => 'todo-widget', 'col' => 4, 'data' => self::getTodoList()],
                    ['type' => 'team-members', 'id' => 'team-widget', 'col' => 4, 'data' => self::getTeamMembers()]
                ]
            ]
        ];

        // Kullanıcı tercihlerini uygula
        if (!empty($userPreferences)) {
            $defaultWidgets = self::mergeWidgetPreferences($defaultWidgets, $userPreferences);
        }

        // Widget türlerine göre konfigürasyonları ekle
        foreach ($defaultWidgets as &$row) {
            foreach ($row['widgets'] as &$widget) {
                if (isset(self::$widgetTypes[$widget['type']])) {
                    $widget['config'] = self::$widgetTypes[$widget['type']];
                    $widget['permissions'] = self::getWidgetPermissions($widget['type'], $userRole);
                }
            }
        }

        return $defaultWidgets;
    }

    /**
     * Modal/Alert Sistemi
     *
     * @param string $type Modal türü (info, success, warning, error, confirm, prompt, custom)
     * @param array $options Modal seçenekleri
     * @return array Modal konfigürasyonu
     */
    public static function createModal(string $type, array $options = []): array
    {
        $modalConfig = [
            'id' => $options['id'] ?? 'modal-' . uniqid(),
            'type' => $type,
            'size' => $options['size'] ?? 'md', // sm, md, lg, xl
            'centered' => $options['centered'] ?? true,
            'backdrop' => $options['backdrop'] ?? true,
            'keyboard' => $options['keyboard'] ?? true,
            'animation' => $options['animation'] ?? 'fade',
            'autohide' => $options['autohide'] ?? false,
            'autohide_delay' => $options['autohide_delay'] ?? 5000
        ];

        // Tip bazlı varsayılan ayarlar
        switch ($type) {
            case 'info':
                $modalConfig['icon'] = 'fas fa-info-circle text-info';
                $modalConfig['title'] = $options['title'] ?? 'Bilgi';
                $modalConfig['buttons'] = [
                    ['text' => 'Tamam', 'class' => 'btn-primary', 'action' => 'close']
                ];
                break;

            case 'success':
                $modalConfig['icon'] = 'fas fa-check-circle text-success';
                $modalConfig['title'] = $options['title'] ?? 'Başarılı';
                $modalConfig['autohide'] = true;
                $modalConfig['autohide_delay'] = 3000;
                break;

            case 'warning':
                $modalConfig['icon'] = 'fas fa-exclamation-triangle text-warning';
                $modalConfig['title'] = $options['title'] ?? 'Uyarı';
                $modalConfig['buttons'] = [
                    ['text' => 'Anladım', 'class' => 'btn-warning', 'action' => 'close']
                ];
                break;

            case 'error':
                $modalConfig['icon'] = 'fas fa-times-circle text-danger';
                $modalConfig['title'] = $options['title'] ?? 'Hata';
                $modalConfig['buttons'] = [
                    ['text' => 'Kapat', 'class' => 'btn-danger', 'action' => 'close']
                ];
                break;

            case 'confirm':
                $modalConfig['icon'] = 'fas fa-question-circle text-primary';
                $modalConfig['title'] = $options['title'] ?? 'Onay';
                $modalConfig['buttons'] = [
                    ['text' => 'İptal', 'class' => 'btn-secondary', 'action' => 'cancel'],
                    ['text' => 'Onayla', 'class' => 'btn-primary', 'action' => 'confirm']
                ];
                break;

            case 'prompt':
                $modalConfig['icon'] = 'fas fa-edit text-primary';
                $modalConfig['title'] = $options['title'] ?? 'Giriş';
                $modalConfig['input'] = [
                    'type' => $options['input_type'] ?? 'text',
                    'placeholder' => $options['placeholder'] ?? '',
                    'default' => $options['default_value'] ?? '',
                    'required' => $options['required'] ?? true
                ];
                $modalConfig['buttons'] = [
                    ['text' => 'İptal', 'class' => 'btn-secondary', 'action' => 'cancel'],
                    ['text' => 'Tamam', 'class' => 'btn-primary', 'action' => 'submit']
                ];
                break;

            case 'custom':
                // Tamamen özelleştirilebilir
                $modalConfig = array_merge($modalConfig, $options);
                break;
        }

        // İçerik ekle
        $modalConfig['content'] = $options['content'] ?? '';

        // Callback fonksiyonları
        $modalConfig['callbacks'] = [
            'onShow' => $options['onShow'] ?? null,
            'onShown' => $options['onShown'] ?? null,
            'onHide' => $options['onHide'] ?? null,
            'onHidden' => $options['onHidden'] ?? null,
            'onConfirm' => $options['onConfirm'] ?? null,
            'onCancel' => $options['onCancel'] ?? null
        ];

        return $modalConfig;
    }

    /**
     * Alert (Toast) Notification Sistemi
     *
     * @param string $message Mesaj
     * @param string $type Alert türü
     * @param array $options Ek seçenekler
     * @return array Alert konfigürasyonu
     */
    public static function createAlert(string $message, string $type = 'info', array $options = []): array
    {
        return [
            'id' => 'alert-' . uniqid(),
            'message' => $message,
            'type' => $type,
            'position' => $options['position'] ?? 'top-right', // top-left, top-center, top-right, bottom-left, bottom-center, bottom-right
            'duration' => $options['duration'] ?? 5000,
            'closable' => $options['closable'] ?? true,
            'progress' => $options['progress'] ?? true,
            'icon' => self::getAlertIcon($type),
            'animation' => $options['animation'] ?? 'slide', // slide, fade, bounce
            'sound' => $options['sound'] ?? false
        ];
    }

    /**
     * Dashboard Layout Seçenekleri
     *
     * @param string $layoutType Layout türü
     * @return array Layout konfigürasyonu
     */
    public static function getDashboardLayout(string $layoutType = self::LAYOUT_GRID): array
    {
        $layouts = [
            self::LAYOUT_GRID => [
                'name' => 'Grid Layout',
                'description' => 'Responsive grid sistem ile esnek yerleşim',
                'container_class' => 'container-fluid px-4',
                'row_class' => 'row g-3',
                'col_classes' => ['col-12', 'col-sm-6', 'col-md-4', 'col-lg-3', 'col-xl-2'],
                'sidebar' => true,
                'sidebar_position' => 'left',
                'navbar' => true,
                'footer' => true
            ],
            self::LAYOUT_FLUID => [
                'name' => 'Fluid Layout',
                'description' => 'Tam genişlik akışkan yerleşim',
                'container_class' => 'container-fluid',
                'row_class' => 'row',
                'sidebar' => true,
                'sidebar_position' => 'left',
                'navbar' => true,
                'footer' => false
            ],
            self::LAYOUT_BOXED => [
                'name' => 'Boxed Layout',
                'description' => 'Merkezi kutulu yerleşim',
                'container_class' => 'container',
                'max_width' => '1200px',
                'row_class' => 'row g-4',
                'sidebar' => true,
                'sidebar_position' => 'left',
                'navbar' => true,
                'footer' => true,
                'background' => true
            ],
            self::LAYOUT_COMPACT => [
                'name' => 'Compact Layout',
                'description' => 'Kompakt sidebar ile daha fazla içerik alanı',
                'container_class' => 'container-fluid',
                'sidebar' => true,
                'sidebar_position' => 'left',
                'sidebar_width' => '60px',
                'sidebar_hover_width' => '260px',
                'navbar' => true,
                'footer' => false
            ],
            self::LAYOUT_HORIZONTAL => [
                'name' => 'Horizontal Layout',
                'description' => 'Üst menü ile yatay yerleşim',
                'container_class' => 'container-fluid',
                'sidebar' => false,
                'navbar' => true,
                'navbar_menu' => true,
                'footer' => true
            ],
            self::LAYOUT_DARK => [
                'name' => 'Dark Layout',
                'description' => 'Karanlık tema ile göz yormayan tasarım',
                'theme' => 'dark',
                'container_class' => 'container-fluid',
                'sidebar' => true,
                'sidebar_theme' => 'dark',
                'navbar' => true,
                'navbar_theme' => 'dark',
                'footer' => true,
                'footer_theme' => 'dark'
            ]
        ];

        return $layouts[$layoutType] ?? $layouts[self::LAYOUT_GRID];
    }

    /**
     * Helper Metodlar
     */

    private static function isActive(string $url): bool
    {
        $currentUrl = $_GET['url'] ?? '';
        return $currentUrl === $url;
    }

    private static function hasAccess(string $section, string $role): bool
    {
        $accessMap = [
            'strategic_planning' => ['superadmin', 'coordinator', 'admin'],
            'content_management' => ['superadmin', 'admin', 'editor'],
            'system_management' => ['superadmin', 'admin']
        ];

        return in_array($role, $accessMap[$section] ?? []);
    }

    private static function getBadgeCount(string $section): ?int
    {
        // TODO: Gerçek veritabanı sorgularıyla değiştirilecek
        $badges = [
            'strategic' => rand(0, 5),
            'content' => rand(0, 3),
            'system' => rand(0, 10)
        ];

        return $badges[$section] ?? null;
    }

    private static function getUserWidgetPreferences($userId, $dashboardId): array
    {
        // TODO: Veritabanından kullanıcı widget tercihlerini al
        return [];
    }

    private static function mergeWidgetPreferences(array $defaults, array $preferences): array
    {
        // Kullanıcı tercihlerini varsayılanlarla birleştir
        return $defaults;
    }

    private static function getWidgetPermissions(string $widgetType, string $role): array
    {
        // Widget bazlı izinler
        return [
            'can_view' => true,
            'can_edit' => in_array($role, ['superadmin', 'admin']),
            'can_delete' => $role === 'superadmin',
            'can_resize' => true,
            'can_move' => true
        ];
    }

    private static function getAlertIcon(string $type): string
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
            'danger' => 'fas fa-exclamation-circle'
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    // Widget veri metodları (örnek)
    private static function getUserStats(): array
    {
        return [
            'title' => 'Toplam Kullanıcı',
            'value' => 1234,
            'change' => '+12%',
            'trend' => 'up',
            'icon' => 'fas fa-users',
            'color' => 'primary'
        ];
    }

    private static function getTaskStats(): array
    {
        return [
            'title' => 'Aktif Görevler',
            'value' => 42,
            'change' => '-3%',
            'trend' => 'down',
            'icon' => 'fas fa-tasks',
            'color' => 'warning'
        ];
    }

    private static function getCompletionStats(): array
    {
        return [
            'title' => 'Tamamlanma Oranı',
            'value' => '78%',
            'change' => '+5%',
            'trend' => 'up',
            'icon' => 'fas fa-chart-pie',
            'color' => 'success'
        ];
    }

    private static function getNotificationStats(): array
    {
        return [
            'title' => 'Bildirimler',
            'value' => 8,
            'change' => 'Yeni',
            'trend' => 'neutral',
            'icon' => 'fas fa-bell',
            'color' => 'info'
        ];
    }

    private static function getPerformanceChart(): array
    {
        return [
            'title' => 'Performans Grafiği',
            'type' => 'line',
            'data' => [
                'labels' => ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz'],
                'datasets' => [
                    [
                        'label' => 'Hedef',
                        'data' => [65, 70, 75, 80, 85, 90]
                    ],
                    [
                        'label' => 'Gerçekleşen',
                        'data' => [60, 68, 72, 78, 82, 88]
                    ]
                ]
            ]
        ];
    }

    private static function getActivityFeed(): array
    {
        return [
            'title' => 'Son Aktiviteler',
            'items' => [
                ['user' => 'Ahmet Yılmaz', 'action' => 'Yeni görev ekledi', 'time' => '5 dk önce'],
                ['user' => 'Ayşe Kaya', 'action' => 'Rapor güncelledi', 'time' => '15 dk önce'],
                ['user' => 'Mehmet Demir', 'action' => 'Belge yükledi', 'time' => '1 saat önce']
            ]
        ];
    }

    private static function getCalendarData(): array
    {
        return [
            'title' => 'Takvim',
            'events' => [
                ['date' => '2024-01-15', 'title' => 'Toplantı', 'type' => 'meeting'],
                ['date' => '2024-01-20', 'title' => 'Deadline', 'type' => 'deadline']
            ]
        ];
    }

    private static function getTodoList(): array
    {
        return [
            'title' => 'Yapılacaklar',
            'items' => [
                ['id' => 1, 'text' => 'Rapor hazırla', 'done' => false],
                ['id' => 2, 'text' => 'E-postaları kontrol et', 'done' => true],
                ['id' => 3, 'text' => 'Veri analizi yap', 'done' => false]
            ]
        ];
    }

    private static function getTeamMembers(): array
    {
        return [
            'title' => 'Ekip Üyeleri',
            'members' => [
                ['name' => 'Ali Veli', 'role' => 'Koordinatör', 'avatar' => null, 'status' => 'online'],
                ['name' => 'Zeynep Çelik', 'role' => 'Uzman', 'avatar' => null, 'status' => 'away'],
                ['name' => 'Can Özkan', 'role' => 'Analist', 'avatar' => null, 'status' => 'offline']
            ]
        ];
    }
}