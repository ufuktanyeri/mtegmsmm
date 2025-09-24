<?php

/**
 * Enhanced Navbar Component with Dynamic Features
 * Includes: Dynamic badges, contextual menus, widgets, mobile optimization
 */

// NavbarService'i yükle
require_once __DIR__ . '/../../services/NavbarService.php';

use App\Services\NavbarService;

$currentUrl = $_GET['url'] ?? '';
$isLoggedIn = isset($_SESSION['username']);

// Permission helper
if ($isLoggedIn) {
    require_once __DIR__ . '/../../../includes/PermissionHelper.php';
}

// Fallback for hasPermission
if (!function_exists('hasPermission')) {
    function hasPermission($permissionName, $operation = 'select')
    {
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }
}

// Dynamic data
$badges = $isLoggedIn ? NavbarService::getDynamicBadges() : [];
$contextualMenu = NavbarService::getContextualMenuItems();
$widgets = NavbarService::getWidgets();
$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
?>

<style>
    /* Enhanced Navbar Styles */
    :root {
        --navbar-height: 70px;
        --navbar-bg: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        --navbar-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --badge-bg: #ef4444;
        --widget-bg: #f3f4f6;
    }

    .navbar-enhanced {
        min-height: var(--navbar-height);
        background: var(--navbar-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1040;
    }

    .navbar-enhanced.scrolled {
        box-shadow: var(--navbar-shadow);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Dynamic Badges */
    .nav-badge {
        position: absolute;
        top: -5px;
        right: -8px;
        background: var(--badge-bg);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 5px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
        animation: badge-pulse 2s infinite;
    }

    @keyframes badge-pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* Contextual Menu */
    .contextual-menu-item {
        position: relative;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .contextual-menu-item:hover {
        background: rgba(79, 70, 229, 0.1);
        transform: translateX(4px);
    }

    .contextual-menu-item.has-badge::after {
        content: attr(data-badge);
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--badge-bg);
        color: white;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
    }

    /* Widgets */
    .navbar-widgets {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .widget-item {
        position: relative;
        padding: 8px;
        border-radius: 8px;
        background: var(--widget-bg);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .widget-item:hover {
        background: rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }

    .widget-notification-bell {
        position: relative;
    }

    .widget-notification-bell .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--badge-bg);
        color: white;
        font-size: 10px;
        padding: 2px 5px;
        border-radius: 10px;
        min-width: 16px;
        text-align: center;
    }

    /* Search Widget */
    .search-widget {
        position: relative;
        display: flex;
        align-items: center;
        background: #f3f4f6;
        border-radius: 20px;
        padding: 6px 12px;
        transition: all 0.3s ease;
        min-width: 200px;
    }

    .search-widget:focus-within {
        background: white;
        box-shadow: 0 0 0 2px var(--primary-color);
        min-width: 250px;
    }

    .search-widget input {
        border: none;
        background: none;
        outline: none;
        padding: 4px 8px;
        font-size: 14px;
        width: 100%;
    }

    .search-shortcut {
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
    }

    /* Mobile Optimization */
    .mobile-menu-toggle {
        display: none;
        position: relative;
        width: 30px;
        height: 24px;
        cursor: pointer;
    }

    .mobile-menu-toggle span {
        position: absolute;
        left: 0;
        width: 100%;
        height: 3px;
        background: #374151;
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .mobile-menu-toggle span:nth-child(1) {
        top: 0;
    }

    .mobile-menu-toggle span:nth-child(2) {
        top: 10px;
    }

    .mobile-menu-toggle span:nth-child(3) {
        top: 20px;
    }

    .mobile-menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg);
        top: 10px;
    }

    .mobile-menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg);
        top: 10px;
    }

    /* Mobile Menu */
    .mobile-menu {
        display: none;
        position: fixed;
        top: var(--navbar-height);
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        max-height: calc(100vh - var(--navbar-height));
        overflow-y: auto;
        z-index: 1030;
    }

    .mobile-menu.active {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mobile-menu-item {
        padding: 12px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .mobile-menu-item:active {
        background: #f3f4f6;
    }

    .mobile-menu-group {
        background: #f9fafb;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #6b7280;
    }

    /* Responsive Breakpoints */
    @media (max-width: 991.98px) {

        .navbar-nav,
        .navbar-widgets {
            display: none;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .search-widget {
            min-width: 150px;
        }

        .search-widget:focus-within {
            min-width: 180px;
        }
    }

    @media (max-width: 575.98px) {
        .navbar-brand .brand-text {
            display: none;
        }

        .user-dropdown .user-info {
            display: none;
        }
    }

    /* Notification Dropdown */
    .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        width: 360px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }

    .notification-dropdown.show {
        display: block;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notification-item.unread {
        background: #eff6ff;
        border-left: 3px solid var(--primary-color);
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        gap: 8px;
        padding: 8px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .quick-action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        background: white;
        border: 1px solid #e5e7eb;
        font-size: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quick-action-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }
</style>

<!-- Enhanced Navbar -->
<nav class="navbar navbar-enhanced navbar-expand-xl fixed-top">
    <div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">

        <!-- Brand -->
        <a href="<?php echo BASE_URL; ?>" class="navbar-brand d-flex align-items-center">
            <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg" alt="MEB" style="height: 40px;" class="me-2">
            <div class="brand-text">
                <div class="fw-bold text-primary" style="font-size: 1rem; line-height: 1.1;">
                    SMM Portal
                </div>
                <div class="text-body-secondary" style="font-size: 0.7rem;">
                    MTEGM - Mesleki ve Teknik Eğitim Genel Müdürlüğü
                </div>
            </div>
        </a>

        <!-- Mobile Menu Toggle -->
        <div class="mobile-menu-toggle" id="mobileMenuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Desktop Navigation -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <?php foreach ($contextualMenu as $key => $item): ?>
                    <?php if (($item['type'] ?? '') === 'dropdown'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle position-relative" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="<?php echo $item['icon']; ?> me-1"></i>
                                <?php echo htmlspecialchars($item['title']); ?>
                                <?php if (($item['badge'] ?? 0) > 0): ?>
                                    <span class="nav-badge"><?php echo $item['badge']; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <?php foreach ($item['items'] ?? [] as $subItem): ?>
                                    <li>
                                        <a class="dropdown-item contextual-menu-item <?php echo ($subItem['badge'] ?? 0) > 0 ? 'has-badge' : ''; ?>"
                                            href="<?php echo BASE_URL; ?>index.php?url=<?php echo $subItem['url']; ?>"
                                            <?php if (($subItem['badge'] ?? 0) > 0): ?>data-badge="<?php echo $subItem['badge']; ?>" <?php endif; ?>>
                                            <i class="<?php echo $subItem['icon']; ?> me-2" style="width: 20px;"></i>
                                            <?php echo htmlspecialchars($subItem['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative <?php echo $currentUrl === $item['url'] ? 'active' : ''; ?>"
                                href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>">
                                <i class="<?php echo $item['icon']; ?> me-1"></i>
                                <?php echo htmlspecialchars($item['title']); ?>
                                <?php if (($item['badge'] ?? 0) > 0): ?>
                                    <span class="nav-badge"><?php echo $item['badge']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <!-- Widgets -->
            <div class="navbar-widgets">
                <?php foreach ($widgets as $widgetId => $widget): ?>
                    <?php if ($widget['type'] === 'search-box'): ?>
                        <!-- Search Widget -->
                        <div class="search-widget">
                            <i class="fas fa-search text-gray-400"></i>
                            <input type="text" placeholder="<?php echo $widget['placeholder']; ?>"
                                id="globalSearch" autocomplete="off">
                            <span class="search-shortcut"><?php echo $widget['shortcut']; ?></span>
                        </div>
                    <?php elseif ($widget['type'] === 'notification-bell'): ?>
                        <!-- Notification Widget -->
                        <div class="widget-item widget-notification-bell" id="notificationBell">
                            <i class="fas fa-bell"></i>
                            <?php if ($widget['badge'] > 0): ?>
                                <span class="badge"><?php echo $widget['badge']; ?></span>
                            <?php endif; ?>

                            <!-- Notification Dropdown -->
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Bildirimler</h6>
                                        <small><a href="#" class="text-primary">Tümünü Gör</a></small>
                                    </div>
                                </div>
                                <div class="notification-list">
                                    <?php foreach ($widget['dropdown_content'] ?? [] as $notification): ?>
                                        <div class="notification-item <?php echo $notification['is_read'] ?? false ? '' : 'unread'; ?>">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <i class="<?php echo $notification['icon']; ?>"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1" style="font-size: 14px;">
                                                        <?php echo htmlspecialchars($notification['title']); ?>
                                                    </h6>
                                                    <p class="mb-1 text-body-secondary" style="font-size: 12px;">
                                                        <?php echo htmlspecialchars($notification['message']); ?>
                                                    </p>
                                                    <small class="text-body-secondary">
                                                        <?php echo htmlspecialchars($notification['time']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="p-2 border-top">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=notifications/all"
                                        class="btn btn-sm btn-primary w-100">
                                        Tüm Bildirimleri Gör
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Quick Actions -->
                <?php if ($isLoggedIn): ?>
                    <div class="quick-actions d-none d-lg-flex">
                        <button class="quick-action-btn" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=action/create'">
                            <i class="fas fa-plus"></i> Yeni
                        </button>
                        <button class="quick-action-btn" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=action/calendar'">
                            <i class="fas fa-calendar"></i> Takvim
                        </button>
                    </div>
                <?php endif; ?>

                <!-- User Menu -->
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown ms-3">
                        <button class="btn btn-link text-decoration-none dropdown-toggle user-dropdown"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?php
                                $initials = strtoupper(substr($_SESSION['realname'] ?? $_SESSION['username'], 0, 1));
                                if (!empty($_SESSION['profile_photo'])): ?>
                                    <img src="<?php echo BASE_URL; ?>uploads/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
                                        alt="Profil">
                                <?php else: ?>
                                    <?php echo $initials; ?>
                                <?php endif; ?>
                            </div>
                            <div class="user-info text-start">
                                <div class="fw-semibold text-dark" style="font-size: 14px;">
                                    <?php echo htmlspecialchars($_SESSION['realname'] ?? $_SESSION['username']); ?>
                                </div>
                                <div class="text-body-secondary" style="font-size: 12px;">
                                    <?php echo htmlspecialchars($_SESSION['role'] ?? 'Kullanıcı'); ?>
                                </div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/editprofile">
                                    <i class="fas fa-user-edit me-2"></i>Profil
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/settings">
                                    <i class="fas fa-cog me-2"></i>Ayarlar
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?url=user/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Çıkış
                                </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>index.php?url=user/login"
                        class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <?php
        $mobileMenu = NavbarService::getMobileMenuData();
        $currentGroup = '';
        foreach ($mobileMenu as $item):
            if (isset($item['group']) && $item['group'] !== $currentGroup):
                $currentGroup = $item['group'];
        ?>
                <div class="mobile-menu-group"><?php echo htmlspecialchars($currentGroup); ?></div>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>"
                class="mobile-menu-item text-decoration-none text-dark">
                <div>
                    <i class="<?php echo $item['icon']; ?> me-2" style="width: 20px;"></i>
                    <?php echo htmlspecialchars($item['title']); ?>
                </div>
                <?php if (($item['badge'] ?? 0) > 0): ?>
                    <span class="badge bg-danger"><?php echo $item['badge']; ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileMenu.classList.toggle('active');
            });
        }

        // Notification Bell
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBell && notificationDropdown) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
            });

            // Close on outside click
            document.addEventListener('click', function() {
                notificationDropdown.classList.remove('show');
            });

            notificationDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Global Search Shortcut (Ctrl+K)
        const globalSearch = document.getElementById('globalSearch');
        if (globalSearch) {
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    globalSearch.focus();
                }
            });

            // Search functionality
            globalSearch.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                if (query.length > 2) {
                    // TODO: Implement search API call
                    console.log('Searching for:', query);
                }
            });
        }

        // Navbar scroll effect
        const navbar = document.querySelector('.navbar-enhanced');
        let lastScroll = 0;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Hide/show on scroll (optional)
            if (currentScroll > lastScroll && currentScroll > 200) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });

        // Auto-refresh badges every 30 seconds
        setInterval(function() {
            // TODO: Implement AJAX call to refresh badges
            console.log('Refreshing badges...');
        }, 30000);
    });
</script>