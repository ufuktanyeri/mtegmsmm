<?php
/**
 * Enhanced Navbar with Sidebar Component
 * Bootstrap 5.3.8 Compatible
 * Independent from header - can be used standalone
 */

$currentUrl = $_GET['url'] ?? '';
$isLoggedIn = isset($_SESSION['username']);

// Permission helper
if ($isLoggedIn) {
    require_once __DIR__ . '/../../../includes/PermissionHelper.php';
}

// Fallback for hasPermission
if (!function_exists('hasPermission')) {
    function hasPermission($permissionName, $operation = 'select') {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }
}

$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';

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
    /* CSS Variables for theming */
    :root {
        --sidebar-width: 260px;
        --sidebar-collapsed-width: 70px;
        --navbar-height: 60px;
        --sidebar-bg: #1e293b;
        --sidebar-text: #cbd5e1;
        --sidebar-hover: #334155;
        --sidebar-active: #4f46e5;
        --content-padding: 20px;
    }

    [data-bs-theme="dark"] {
        --sidebar-bg: #0f172a;
        --sidebar-text: #94a3b8;
        --sidebar-hover: #1e293b;
        --sidebar-active: #6366f1;
    }

    /* Layout Structure */
    .app-wrapper {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    /* Enhanced Navbar */
    .navbar-enhanced {
        position: fixed;
        top: 0;
        left: var(--sidebar-width);
        right: 0;
        height: var(--navbar-height);
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        z-index: 1030;
        transition: left 0.3s ease;
    }

    .sidebar-collapsed .navbar-enhanced {
        left: var(--sidebar-collapsed-width);
    }

    [data-bs-theme="dark"] .navbar-enhanced {
        background: #1e293b;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        transition: all 0.3s ease;
        z-index: 1040;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar-collapsed .sidebar {
        width: var(--sidebar-collapsed-width);
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: var(--navbar-height);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        color: white;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .sidebar-logo:hover {
        transform: scale(1.05);
    }

    .sidebar-logo img {
        height: 35px;
        margin-right: 10px;
        filter: brightness(0) invert(1);
    }

    .sidebar-collapsed .sidebar-logo span {
        display: none;
    }

    /* Sidebar Menu */
    .sidebar-menu {
        padding: 1rem 0;
    }

    .sidebar-section-title {
        color: var(--sidebar-text);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
        opacity: 0.6;
        letter-spacing: 0.5px;
    }

    .sidebar-collapsed .sidebar-section-title {
        display: none;
    }

    .sidebar-item {
        position: relative;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .sidebar-link:hover {
        background: var(--sidebar-hover);
        color: white;
    }

    .sidebar-link.active {
        background: var(--sidebar-active);
        color: white;
    }

    .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: white;
    }

    .sidebar-link i {
        width: 24px;
        font-size: 1.1rem;
        margin-right: 12px;
        text-align: center;
    }

    .sidebar-collapsed .sidebar-link {
        justify-content: center;
        padding: 1rem;
    }

    .sidebar-collapsed .sidebar-link i {
        margin-right: 0;
    }

    .sidebar-collapsed .sidebar-link span {
        display: none;
    }

    /* Sidebar Badge */
    .sidebar-badge {
        margin-left: auto;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
    }

    .sidebar-collapsed .sidebar-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 2px 4px;
        font-size: 0.6rem;
    }

    /* Sidebar Submenu */
    .sidebar-submenu {
        background: rgba(0, 0, 0, 0.2);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .sidebar-submenu.show {
        max-height: 500px;
    }

    .sidebar-submenu .sidebar-link {
        padding-left: 3rem;
        font-size: 0.9rem;
    }

    .sidebar-collapsed .sidebar-submenu {
        display: none;
    }

    /* Content Area */
    .main-content {
        margin-left: var(--sidebar-width);
        padding-top: var(--navbar-height);
        min-height: 100vh;
        transition: margin-left 0.3s ease;
        background: #f8f9fa;
    }

    .sidebar-collapsed .main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    [data-bs-theme="dark"] .main-content {
        background: #0f172a;
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .navbar-enhanced {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar-toggle-mobile {
            display: block !important;
        }
    }

    /* Toggle Button */
    .sidebar-toggle {
        background: transparent;
        border: none;
        color: var(--sidebar-text);
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .sidebar-toggle:hover {
        color: white;
        transform: rotate(180deg);
    }

    /* Search Box in Navbar */
    .navbar-search {
        position: relative;
        flex: 1;
        max-width: 400px;
        margin: 0 1rem;
    }

    .navbar-search input {
        width: 100%;
        padding: 0.5rem 2.5rem 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .navbar-search input:focus {
        outline: none;
        border-color: var(--sidebar-active);
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .navbar-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    /* User Menu in Navbar */
    .navbar-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .navbar-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--sidebar-active);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    /* Tooltip for collapsed sidebar */
    .sidebar-collapsed .sidebar-link {
        position: relative;
    }

    .sidebar-collapsed .sidebar-link:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #1e293b;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        white-space: nowrap;
        z-index: 1050;
        margin-left: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>" class="sidebar-logo">
                <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg" alt="MEB">
                <span class="fw-bold">SMM Portal</span>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-menu">
            <?php foreach ($sidebarMenuItems as $item): ?>
                <?php if (!shouldShowMenuItem($item, $isSuperAdmin)) continue; ?>

                <?php if (isset($item['type']) && $item['type'] === 'section'): ?>
                    <div class="sidebar-section-title"><?php echo htmlspecialchars($item['title']); ?></div>
                    <?php if (isset($item['children'])): ?>
                        <?php foreach ($item['children'] as $child): ?>
                            <?php if (!shouldShowMenuItem($child, $isSuperAdmin)) continue; ?>
                            <div class="sidebar-item">
                                <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $child['url']; ?>"
                                   class="sidebar-link <?php echo $currentUrl === $child['url'] ? 'active' : ''; ?>"
                                   data-tooltip="<?php echo htmlspecialchars($child['title']); ?>">
                                    <i class="<?php echo $child['icon']; ?>"></i>
                                    <span><?php echo htmlspecialchars($child['title']); ?></span>
                                    <?php if (isset($child['badge']) && $child['badge'] > 0): ?>
                                        <span class="sidebar-badge"><?php echo $child['badge']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="sidebar-item">
                        <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>"
                           class="sidebar-link <?php echo $currentUrl === $item['url'] ? 'active' : ''; ?>"
                           data-tooltip="<?php echo htmlspecialchars($item['title']); ?>">
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
    </aside>

    <!-- Top Navbar -->
    <nav class="navbar navbar-enhanced">
        <div class="container-fluid d-flex align-items-center">
            <!-- Mobile Toggle -->
            <button class="sidebar-toggle sidebar-toggle-mobile d-lg-none" id="sidebarToggleMobile">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Search Box -->
            <div class="navbar-search">
                <input type="text" placeholder="Ara... (Ctrl+K)" id="globalSearch">
                <i class="fas fa-search navbar-search-icon"></i>
            </div>

            <!-- Right Section -->
            <div class="navbar-user">
                <!-- Notifications -->
                <button class="btn btn-link text-dark position-relative">
                    <i class="fas fa-bell fs-5"></i>
                </button>

                <!-- Theme Toggle -->
                <button class="btn btn-link text-dark" id="themeToggle">
                    <i class="fas fa-moon fs-5"></i>
                </button>

                <!-- User Dropdown -->
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center"
                                type="button" data-bs-toggle="dropdown">
                            <div class="navbar-user-avatar me-2">
                                <?php
                                $initials = strtoupper(substr($_SESSION['realname'] ?? $_SESSION['username'], 0, 1));
                                echo $initials;
                                ?>
                            </div>
                            <div class="text-start d-none d-md-block">
                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($_SESSION['realname'] ?? $_SESSION['username']); ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($_SESSION['role'] ?? 'Kullanıcı'); ?>
                                </div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/editprofile">
                                <i class="fas fa-user me-2"></i>Profil
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/settings">
                                <i class="fas fa-cog me-2"></i>Ayarlar
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?url=user/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>index.php?url=user/login" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container-fluid p-4">
            <!-- Content will be loaded here -->
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
    const appWrapper = document.querySelector('.app-wrapper');

    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            appWrapper.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', appWrapper.classList.contains('sidebar-collapsed'));
        });
    }

    // Mobile sidebar toggle
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // Load sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        appWrapper.classList.add('sidebar-collapsed');
    }

    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update icon
            const icon = this.querySelector('i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun fs-5' : 'fas fa-moon fs-5';
        });
    }

    // Global Search
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        // Ctrl+K shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                globalSearch.focus();
            }
        });

        // ESC to blur
        globalSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.blur();
            }
        });
    }

    // Close mobile sidebar on outside click
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992 &&
            !sidebar.contains(e.target) &&
            !sidebarToggleMobile.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
});
</script>