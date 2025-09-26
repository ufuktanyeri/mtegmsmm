<?php
/**
 * Sidebar Menu Component
 * Used in unified layout for logged-in users
 */

$currentUrl = $_GET['url'] ?? '';
$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';

// Permission helper
require_once __DIR__ . '/../../../includes/PermissionHelper.php';

// Fallback for hasPermission
if (!function_exists('hasPermission')) {
    function hasPermission($permissionName, $operation = 'select') {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }
}

// Menu items for sidebar
$sidebarMenuItems = [
    [
        'title' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'url' => 'home/index',
        'badge' => null,
        'permission' => null
    ],
    [
        'title' => 'Stratejik Yönetim',
        'icon' => 'fas fa-bullseye',
        'type' => 'section',
        'permission' => 'aims.manage',
        'children' => [
            ['title' => 'Amaçlar', 'icon' => 'fas fa-bullseye', 'url' => 'aim/index', 'permission' => 'aims.manage'],
            ['title' => 'Hedefler', 'icon' => 'fas fa-flag', 'url' => 'objective/index', 'permission' => 'objectives.manage'],
            ['title' => 'Göstergeler', 'icon' => 'fas fa-chart-line', 'url' => 'indicator/index', 'permission' => 'indicators.manage'],
            ['title' => 'Eylemler', 'icon' => 'fas fa-tasks', 'url' => 'action/index', 'permission' => 'actions.manage'],
            ['title' => 'Takvim', 'icon' => 'fas fa-calendar', 'url' => 'action/calendar', 'permission' => 'actions.manage']
        ]
    ],
    [
        'title' => 'İçerik Yönetimi',
        'icon' => 'fas fa-file-alt',
        'type' => 'section',
        'permission' => 'news.manage',
        'children' => [
            ['title' => 'Haberler', 'icon' => 'fas fa-newspaper', 'url' => 'news/index', 'permission' => 'news.manage'],
            ['title' => 'Belgeler', 'icon' => 'fas fa-file-pdf', 'url' => 'documentstrategy/index', 'permission' => 'documentstrategies.manage'],
            ['title' => 'Mevzuat', 'icon' => 'fas fa-book', 'url' => 'regulation/index', 'permission' => 'regulations.manage']
        ]
    ],
    [
        'title' => 'Sistem Yönetimi',
        'icon' => 'fas fa-cogs',
        'type' => 'section',
        'permission' => 'users.manage',
        'children' => [
            ['title' => 'Kullanıcılar', 'icon' => 'fas fa-users', 'url' => 'user/manage', 'permission' => 'users.manage'],
            ['title' => 'Roller', 'icon' => 'fas fa-shield-alt', 'url' => 'user/roles', 'permission' => 'users.manage'],
            ['title' => 'SMM Merkezleri', 'icon' => 'fas fa-building', 'url' => 'cove/index', 'permission' => 'coves.manage'],
            ['title' => 'SMM Alanları', 'icon' => 'fas fa-th', 'url' => 'field/index', 'permission' => 'fields.manage'],
            ['title' => 'Sistem Logları', 'icon' => 'fas fa-history', 'url' => 'log/index', 'permission' => 'logs.manage']
        ]
    ]
];

// Function to check if menu item should be shown
function shouldShowMenuItem($item, $isSuperAdmin) {
    if (!isset($item['permission']) || $item['permission'] === null) {
        return true;
    }

    if ($isSuperAdmin) {
        return true;
    }

    return hasPermission($item['permission']);
}
?>

<style>
    /* Sidebar Styles */
    .sidebar {
        padding: 1rem 0;
    }

    .sidebar-header {
        padding: 0 1rem 1rem;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #212529;
    }

    .sidebar-logo img {
        width: 40px;
        height: 40px;
    }

    .sidebar-logo span {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .sidebar-section {
        margin-bottom: 1.5rem;
    }

    .sidebar-section-title {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
    }

    .sidebar-item {
        margin-bottom: 0.25rem;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 1rem;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
    }

    .sidebar-link:hover {
        background: #f8f9fa;
        color: #212529;
    }

    .sidebar-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: white;
    }

    .sidebar-link i {
        width: 20px;
        text-align: center;
        font-size: 1rem;
    }

    .sidebar-link span {
        font-size: 0.95rem;
        font-weight: 500;
    }

    .sidebar-badge {
        margin-left: auto;
        background: #dc3545;
        color: white;
        padding: 0.125rem 0.375rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Dark mode support */
    [data-bs-theme="dark"] .sidebar {
        background: #1a1a1a;
        border-color: #2d2d2d;
    }

    [data-bs-theme="dark"] .sidebar-logo {
        color: #f8f9fa;
    }

    [data-bs-theme="dark"] .sidebar-section-title {
        color: #adb5bd;
    }

    [data-bs-theme="dark"] .sidebar-link {
        color: #ced4da;
    }

    [data-bs-theme="dark"] .sidebar-link:hover {
        background: #2d2d2d;
        color: #f8f9fa;
    }
</style>

<!-- Sidebar Header -->
<div class="sidebar-header">
    <a href="<?php echo BASE_URL; ?>" class="sidebar-logo">
        <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg" alt="MEB">
        <span>SMM Portal</span>
    </a>
</div>

<!-- Sidebar Menu -->
<nav class="sidebar-menu">
    <?php foreach ($sidebarMenuItems as $item): ?>
        <?php if (!shouldShowMenuItem($item, $isSuperAdmin)) continue; ?>

        <?php if (isset($item['type']) && $item['type'] === 'section'): ?>
            <!-- Section with children -->
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?php echo htmlspecialchars($item['title']); ?></div>
                <?php if (isset($item['children'])): ?>
                    <?php foreach ($item['children'] as $child): ?>
                        <?php if (!shouldShowMenuItem($child, $isSuperAdmin)) continue; ?>
                        <div class="sidebar-item">
                            <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $child['url']; ?>"
                               class="sidebar-link <?php echo $currentUrl === $child['url'] ? 'active' : ''; ?>">
                                <i class="<?php echo $child['icon']; ?>"></i>
                                <span><?php echo htmlspecialchars($child['title']); ?></span>
                                <?php if (isset($child['badge']) && $child['badge'] > 0): ?>
                                    <span class="sidebar-badge"><?php echo $child['badge']; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Single item -->
            <div class="sidebar-item">
                <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>"
                   class="sidebar-link <?php echo $currentUrl === $item['url'] ? 'active' : ''; ?>">
                    <i class="<?php echo $item['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                    <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                        <span class="sidebar-badge"><?php echo $item['badge']; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>

<!-- User Section at Bottom -->
<div class="sidebar-section" style="margin-top: auto; border-top: 1px solid #dee2e6; padding-top: 1rem;">
    <div class="sidebar-item">
        <a href="<?php echo BASE_URL; ?>index.php?url=user/editProfile" class="sidebar-link">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? 'Kullanıcı'); ?></span>
        </a>
    </div>
    <div class="sidebar-item">
        <a href="<?php echo BASE_URL; ?>index.php?url=help/index" class="sidebar-link">
            <i class="fas fa-question-circle"></i>
            <span>Yardım</span>
        </a>
    </div>
    <div class="sidebar-item">
        <a href="<?php echo BASE_URL; ?>index.php?url=user/logout" class="sidebar-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Çıkış Yap</span>
        </a>
    </div>
</div>