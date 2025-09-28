<!doctype html>
<html lang="tr" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MTEGM SMM Portal - Milli Eğitim Bakanlığı Strateji Yönetim Sistemi">
    <meta name="author" content="MEB MTEGM">
    <title><?php echo $title ?? 'MTEGM SMM Portal'; ?> - MEB</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --meb-primary: #1e3c72;
            --meb-secondary: #2a5298;
            --meb-light: #f8f9fa;
            --meb-sidebar-width: 280px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f5f7fa;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--meb-primary) 0%, var(--meb-secondary) 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .navbar .user-menu {
            display: flex;
            align-items: center;
        }

        .navbar .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .navbar .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: var(--meb-sidebar-width);
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed {
            margin-left: calc(var(--meb-sidebar-width) * -1);
        }

        .sidebar-sticky {
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: rgba(30, 60, 114, 0.05);
            color: var(--meb-primary);
            border-left-color: var(--meb-primary);
        }

        .sidebar .nav-link.active {
            background: rgba(30, 60, 114, 0.08);
            color: var(--meb-primary);
            border-left-color: var(--meb-primary);
            font-weight: 500;
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1.5rem 1.5rem 0.5rem;
            font-weight: 700;
            color: #6c757d;
            letter-spacing: 0.05em;
        }

        /* Main Content */
        main {
            margin-left: var(--meb-sidebar-width);
            padding: 1.5rem;
            min-height: calc(100vh - 56px);
            margin-top: 56px;
            transition: margin-left 0.3s ease;
        }

        main.full-width {
            margin-left: 0;
        }

        /* Breadcrumb Styles */
        .breadcrumb {
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #6c757d;
        }

        /* Card Improvements */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-radius: 0.5rem;
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.03), rgba(42, 82, 152, 0.03));
            border-bottom: 2px solid rgba(30, 60, 114, 0.1);
            font-weight: 600;
            padding: 1rem;
        }

        /* Role Badge Styles */
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }

        .role-badge.superadmin {
            background: #dc3545;
            color: white;
        }

        .role-badge.admin {
            background: #fd7e14;
            color: white;
        }

        .role-badge.coordinator {
            background: #0dcaf0;
            color: white;
        }

        .role-badge.user {
            background: #6c757d;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
            }

            main {
                margin-left: 0;
                margin-top: 0;
            }

            .navbar-toggler {
                display: block;
            }
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(30, 60, 114, 0.05);
            color: var(--meb-primary);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <?php
    // Load MenuHelper
    require_once dirname(__DIR__, 2) . '/helpers/MenuHelper.php';

    $userRole = $_SESSION['role_id'] ?? 4;
    $userName = $_SESSION['name'] ?? 'Kullanıcı';
    $roleName = MenuHelper::getRoleName($userRole);
    ?>

    <!-- Top Navigation -->
    <nav class="navbar navbar-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler d-md-none" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="/home">
                <i class="bi bi-mortarboard-fill me-2"></i>
                MTEGM SMM Portal
            </a>

            <div class="user-menu ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <span class="d-none d-md-inline">
                            <?php echo htmlspecialchars($userName); ?>
                            <span class="role-badge <?php echo strtolower(str_replace(' ', '', $roleName)); ?> ms-2">
                                <?php echo $roleName; ?>
                            </span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($userName); ?>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="/profile">
                                <i class="bi bi-person me-2"></i>Profilim
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/auth/changePassword">
                                <i class="bi bi-key me-2"></i>Şifre Değiştir
                            </a>
                        </li>
                        <?php if ($userRole == 1): ?>
                        <li>
                            <a class="dropdown-item" href="/settings">
                                <i class="bi bi-gear me-2"></i>Ayarlar
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/auth/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav id="sidebarMenu" class="sidebar">
        <div class="sidebar-sticky">
            <?php echo MenuHelper::renderMenu($activeMenu ?? '', $userRole); ?>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent">
        <!-- Breadcrumb -->
        <?php echo MenuHelper::renderBreadcrumb(); ?>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-diamond me-2"></i>
                <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <?php echo $_SESSION['info']; unset($_SESSION['info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="content">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('full-width');
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Tooltip initialization
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Popover initialization
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    </script>

    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>