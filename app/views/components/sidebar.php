<?php
/**
 * Enhanced Sidebar Component
 * Modern, responsive, collapsible sidebar with role-based menu
 */

require_once __DIR__ . '/../../services/UIComponentService.php';
use App\Services\UIComponentService;

// Sidebar konfigürasyonu
$sidebarConfig = UIComponentService::getSidebarConfig([
    'style' => $_SESSION['sidebar_style'] ?? 'vertical',
    'theme' => $_SESSION['sidebar_theme'] ?? 'light',
    'collapsed' => $_SESSION['sidebar_collapsed'] ?? false
]);

$currentUrl = $_GET['url'] ?? 'home/index';
?>

<style>
    /* Sidebar Styles */
    :root {
        --sidebar-width: <?php echo $sidebarConfig['width']; ?>;
        --sidebar-collapsed-width: 70px;
        --sidebar-bg-light: #ffffff;
        --sidebar-bg-dark: #1f2937;
        --sidebar-text-light: #374151;
        --sidebar-text-dark: #e5e7eb;
        --sidebar-hover-bg-light: #f3f4f6;
        --sidebar-hover-bg-dark: #374151;
        --sidebar-active-bg: rgba(79, 70, 229, 0.1);
        --sidebar-active-color: #4f46e5;
        --sidebar-border-color: #e5e7eb;
        --sidebar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Sidebar Container */
    .sidebar {
        position: fixed;
        top: 70px; /* Navbar height */
        <?php echo $sidebarConfig['position']; ?>: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: var(--sidebar-bg-<?php echo $sidebarConfig['theme']; ?>);
        border-right: 1px solid var(--sidebar-border-color);
        transition: var(--sidebar-transition);
        z-index: 1030;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid var(--sidebar-border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--sidebar-text-<?php echo $sidebarConfig['theme']; ?>);
        font-weight: 600;
        font-size: 1.1rem;
        transition: var(--sidebar-transition);
    }

    .sidebar.collapsed .sidebar-brand span {
        display: none;
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: var(--sidebar-text-<?php echo $sidebarConfig['theme']; ?>);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: var(--sidebar-transition);
    }

    .sidebar-toggle:hover {
        background: var(--sidebar-hover-bg-<?php echo $sidebarConfig['theme']; ?>);
    }

    /* Sidebar Body */
    .sidebar-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.5rem;
    }

    /* Custom Scrollbar */
    .sidebar-body::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-body::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }

    .sidebar-body::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    /* Sidebar Menu */
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu-item {
        margin-bottom: 0.25rem;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--sidebar-text-<?php echo $sidebarConfig['theme']; ?>);
        text-decoration: none;
        border-radius: 0.5rem;
        transition: var(--sidebar-transition);
        position: relative;
        font-size: 0.9rem;
    }

    .sidebar-menu-link:hover {
        background: var(--sidebar-hover-bg-<?php echo $sidebarConfig['theme']; ?>);
        color: var(--sidebar-active-color);
    }

    .sidebar-menu-link.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-active-color);
        font-weight: 600;
    }

    .sidebar-menu-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: var(--sidebar-active-color);
        border-radius: 0 3px 3px 0;
    }

    /* Menu Icon */
    .sidebar-menu-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .sidebar.collapsed .sidebar-menu-icon {
        margin-right: 0;
    }

    /* Menu Text */
    .sidebar-menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: var(--sidebar-transition);
    }

    .sidebar.collapsed .sidebar-menu-text {
        opacity: 0;
        width: 0;
    }

    /* Menu Badge */
    .sidebar-menu-badge {
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 0.125rem 0.375rem;
        border-radius: 9999px;
        font-weight: 600;
        margin-left: auto;
        flex-shrink: 0;
    }

    .sidebar.collapsed .sidebar-menu-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        padding: 0.125rem 0.25rem;
        font-size: 0.6rem;
    }

    /* Submenu */
    .sidebar-submenu {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0 2.5rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .sidebar-menu-item.open .sidebar-submenu {
        max-height: 500px;
    }

    .sidebar.collapsed .sidebar-submenu {
        display: none;
    }

    .sidebar-submenu-item {
        margin-bottom: 0.25rem;
    }

    .sidebar-submenu-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        color: var(--sidebar-text-<?php echo $sidebarConfig['theme']; ?>);
        text-decoration: none;
        border-radius: 0.375rem;
        font-size: 0.85rem;
        opacity: 0.8;
        transition: var(--sidebar-transition);
    }

    .sidebar-submenu-link:hover {
        opacity: 1;
        background: var(--sidebar-hover-bg-<?php echo $sidebarConfig['theme']; ?>);
    }

    .sidebar-submenu-link.active {
        opacity: 1;
        color: var(--sidebar-active-color);
        font-weight: 500;
    }

    /* Dropdown Arrow */
    .sidebar-menu-arrow {
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 0.75rem;
    }

    .sidebar-menu-item.open .sidebar-menu-arrow {
        transform: rotate(90deg);
    }

    .sidebar.collapsed .sidebar-menu-arrow {
        display: none;
    }

    /* Sidebar Footer */
    .sidebar-footer {
        border-top: 1px solid var(--sidebar-border-color);
        padding: 0.5rem;
        margin-top: auto;
    }

    /* Tooltip for collapsed sidebar */
    .sidebar.collapsed .sidebar-menu-link {
        position: relative;
    }

    .sidebar.collapsed .sidebar-menu-link:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #1f2937;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        white-space: nowrap;
        margin-left: 0.5rem;
        z-index: 1050;
        pointer-events: none;
    }

    .sidebar.collapsed .sidebar-menu-link:hover::before {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 6px solid transparent;
        border-right-color: #1f2937;
        margin-left: -6px;
        z-index: 1050;
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1029;
            display: none;
        }

        .sidebar-backdrop.show {
            display: block;
        }
    }

    /* Content Offset */
    .main-content {
        margin-<?php echo $sidebarConfig['position']; ?>: var(--sidebar-width);
        transition: var(--sidebar-transition);
        padding-top: 70px;
        min-height: 100vh;
    }

    .main-content.sidebar-collapsed {
        margin-<?php echo $sidebarConfig['position']; ?>: var(--sidebar-collapsed-width);
    }

    @media (max-width: 991.98px) {
        .main-content {
            margin-<?php echo $sidebarConfig['position']; ?>: 0;
        }
    }

    /* Section Divider */
    .sidebar-section-divider {
        height: 1px;
        background: var(--sidebar-border-color);
        margin: 1rem 0;
        opacity: 0.5;
    }

    .sidebar-section-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--sidebar-text-<?php echo $sidebarConfig['theme']; ?>);
        opacity: 0.6;
        padding: 0.5rem 1rem;
        letter-spacing: 0.5px;
    }

    .sidebar.collapsed .sidebar-section-title {
        display: none;
    }
</style>

<!-- Sidebar Component -->
<aside class="sidebar <?php echo $sidebarConfig['collapsed'] ? 'collapsed' : ''; ?>" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>" class="sidebar-brand">
            <i class="fas fa-layer-group sidebar-menu-icon"></i>
            <span>SMM Portal</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Body -->
    <div class="sidebar-body">
        <ul class="sidebar-menu">
            <?php foreach ($sidebarConfig['items'] as $item): ?>
                <?php if (isset($item['children'])): ?>
                    <!-- Menu with submenu -->
                    <li class="sidebar-menu-item has-submenu">
                        <a href="#" class="sidebar-menu-link" data-tooltip="<?php echo htmlspecialchars($item['title']); ?>">
                            <i class="<?php echo $item['icon']; ?> sidebar-menu-icon"></i>
                            <span class="sidebar-menu-text"><?php echo htmlspecialchars($item['title']); ?></span>
                            <?php if ($item['badge'] > 0): ?>
                                <span class="sidebar-menu-badge"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-right sidebar-menu-arrow"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <?php foreach ($item['children'] as $child): ?>
                                <li class="sidebar-submenu-item">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $child['url']; ?>"
                                       class="sidebar-submenu-link <?php echo $currentUrl === $child['url'] ? 'active' : ''; ?>">
                                        <i class="<?php echo $child['icon']; ?> me-2" style="font-size: 0.8rem;"></i>
                                        <span><?php echo htmlspecialchars($child['title']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Simple menu item -->
                    <li class="sidebar-menu-item">
                        <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>"
                           class="sidebar-menu-link <?php echo $currentUrl === $item['url'] ? 'active' : ''; ?>"
                           data-tooltip="<?php echo htmlspecialchars($item['title']); ?>">
                            <i class="<?php echo $item['icon']; ?> sidebar-menu-icon"></i>
                            <span class="sidebar-menu-text"><?php echo htmlspecialchars($item['title']); ?></span>
                            <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                <span class="sidebar-menu-badge"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <!-- Section Divider -->
        <div class="sidebar-section-divider"></div>
        <div class="sidebar-section-title">Diğer</div>

        <!-- Footer Items -->
        <ul class="sidebar-menu">
            <?php foreach ($sidebarConfig['footer_items'] as $item): ?>
                <li class="sidebar-menu-item">
                    <a href="<?php echo BASE_URL; ?>index.php?url=<?php echo $item['url']; ?>"
                       class="sidebar-menu-link <?php echo $item['class'] ?? ''; ?>"
                       data-tooltip="<?php echo htmlspecialchars($item['title']); ?>">
                        <i class="<?php echo $item['icon']; ?> sidebar-menu-icon"></i>
                        <span class="sidebar-menu-text"><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>

<!-- Mobile backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mainContent = document.querySelector('.main-content');

    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');

            // Update main content
            if (mainContent) {
                mainContent.classList.toggle('sidebar-collapsed');
            }

            // Save state
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);

            // Update session via AJAX
            fetch('<?php echo BASE_URL; ?>index.php?url=user/saveSidebarState', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    collapsed: isCollapsed
                })
            });
        });
    }

    // Restore sidebar state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        if (mainContent) {
            mainContent.classList.add('sidebar-collapsed');
        }
    }

    // Handle submenu toggles
    const submenuItems = document.querySelectorAll('.sidebar-menu-item.has-submenu > .sidebar-menu-link');
    submenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            const parent = this.parentElement;
            const wasOpen = parent.classList.contains('open');

            // Close all other submenus
            document.querySelectorAll('.sidebar-menu-item.open').forEach(openItem => {
                if (openItem !== parent) {
                    openItem.classList.remove('open');
                }
            });

            // Toggle current submenu
            if (!wasOpen) {
                parent.classList.add('open');
            } else {
                parent.classList.remove('open');
            }
        });
    });

    // Auto open active submenu
    const activeSubmenuLink = document.querySelector('.sidebar-submenu-link.active');
    if (activeSubmenuLink) {
        const submenuItem = activeSubmenuLink.closest('.sidebar-menu-item');
        if (submenuItem) {
            submenuItem.classList.add('open');
        }
    }

    // Mobile sidebar toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
        });
    }

    // Close mobile sidebar on backdrop click
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        });
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 991) {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            }
        }, 250);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + B to toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            sidebarToggle.click();
        }

        // Ctrl/Cmd + / to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            e.preventDefault();
            const searchInput = document.querySelector('.sidebar-search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
});
</script>