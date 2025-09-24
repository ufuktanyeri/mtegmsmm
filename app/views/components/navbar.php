<?php

/**
 * Reusable Navbar Component
 * Responsive navigation with support for both public and authenticated users
 */

$currentUrl = $_GET['url'] ?? '';
$isLoggedIn = isset($_SESSION['username']);

// Import permission helper if logged in
if ($isLoggedIn) {
    require_once __DIR__ . '/../../../includes/PermissionHelper.php';
}

// Simple fallback for hasPermission function if not defined
if (!function_exists('hasPermission')) {
    function hasPermission($permissionName, $operation = 'select')
    {
        // Fallback: Only superadmin has permissions if function is missing
        return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
    }
}

// Check if user is superadmin
$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';
?>

<style>
    /* Modern Navigation Styles */
    .navbar {
        padding: 1rem 0;
        min-height: 80px;
        transition: all 0.3s ease;
    }

    .navbar-brand {
        transition: transform 0.3s ease;
        padding: 0.5rem 0;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    .navbar-brand .meb-logo {
        transition: filter 0.3s ease;
    }

    .navbar-brand:hover .meb-logo {
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
    }

    .nav-link {
        font-weight: 500;
        color: #495057 !important;
        transition: all 0.3s ease;
        margin: 0 0.1rem;
        position: relative;
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem !important;
    }

    .nav-link:hover {
        color: var(--meb-primary) !important;
        transform: translateY(-1px);
    }

    .nav-link.active {
        color: var(--meb-primary) !important;
        background-color: rgba(79, 70, 229, 0.1);
        font-weight: 600;
        border-radius: 20px;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 2px;
        background: var(--meb-primary);
        border-radius: 1px;
    }

    .dropdown-menu {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }

    .dropdown-item {
        transition: all 0.2s ease;
        border-radius: 8px;
        margin: 0 0.5rem;
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background-color: rgba(79, 70, 229, 0.1);
        color: var(--meb-primary);
        transform: translateX(5px);
    }

    /* Navbar scrolled state */
    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(15px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

    /* User dropdown */
    .user-dropdown {
        display: flex;
        align-items: center;
        background: rgba(79, 70, 229, 0.1);
        border-radius: 25px;
        padding: 0.25rem 0.75rem;
        transition: all 0.3s ease;
        border: none;
    }

    .user-dropdown:hover {
        background: rgba(79, 70, 229, 0.2);
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--meb-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 8px;
        font-size: 0.85rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Dropdown prevent cutoff */
    .navbar-nav .dropdown-menu {
        position: absolute !important;
    }

    @media (min-width: 992px) {
        .navbar-nav .dropdown:hover .dropdown-menu {
            display: block;
            animation: fadeIn 0.3s ease;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile responsive */
    @media (max-width: 991.98px) {
        .navbar-brand .meb-logo {
            height: 40px !important;
        }

        .navbar-collapse {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1040;
        }

        .nav-link {
            padding: 0.75rem 1rem !important;
            margin: 0.25rem 0;
            border-radius: 8px;
        }

        .nav-link.active::after {
            display: none;
        }

        .dropdown-menu {
            border: none;
            box-shadow: none;
            background: #f8f9fa;
            margin-left: 1rem;
        }
    }
</style>

<!-- Modern Navbar -->
<nav class="navbar navbar-expand-xl fixed-top shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px); border-bottom: 1px solid #e9ecef;">
    <div class="container-fluid" style="max-width: 90%; margin: 0 auto;">
        <!-- Brand - MEB Logo Only -->
        <a href="https://www.meb.gov.tr" class="navbar-brand d-flex align-items-center me-3" target="_blank"
           title="T.C. Milli Eğitim Bakanlığı" rel="noopener noreferrer">
            <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg"
                 alt="T.C. Milli Eğitim Bakanlığı"
                 style="height: 45px; width: auto;"
                 class="meb-logo">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Menüyü Aç/Kapat">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto">
                <!-- Common Menu Items for All Users -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentUrl === 'user/main' ? 'active' : ''; ?> rounded-pill"
                        href="<?php echo BASE_URL; ?>index.php?url=user/main">
                        <i class="fas fa-home me-1"></i>Ana Sayfa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentUrl === 'user/haberlist' ? 'active' : ''; ?> rounded-pill"
                        href="<?php echo BASE_URL; ?>index.php?url=user/haberlist">
                        <i class="fas fa-newspaper me-1"></i>Haberler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentUrl === 'home/smmnetwork' ? 'active' : ''; ?> px-3 py-2 rounded-pill"
                        href="<?php echo BASE_URL; ?>index.php?url=home/smmnetwork">
                        <i class="fas fa-map-marked-alt me-2"></i>SMM Haritası
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <!-- Admin Panel for Logged-in Users -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo empty($currentUrl) || $currentUrl === 'home/index' ? 'active' : ''; ?> px-3 py-2 rounded-pill"
                            href="<?php echo BASE_URL; ?>index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Yönetim Paneli
                        </a>
                    </li>

                    <?php if ($isSuperAdmin || hasPermission('aims.manage') || hasPermission('objectives.manage') || hasPermission('indicators.manage') || hasPermission('actions.manage')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle rounded-pill" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bullseye me-1"></i>Stratejik
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <?php if ($isSuperAdmin || hasPermission('aims.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=aim/index">
                                            <i class="fas fa-bullseye me-2 text-primary"></i>Amaçlar
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('objectives.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=objective/index">
                                            <i class="fas fa-flag me-2 text-success"></i>Hedefler
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('indicators.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=indicator/index">
                                            <i class="fas fa-chart-line me-2 text-info"></i>Göstergeler
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('actions.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=action/index">
                                            <i class="fas fa-tasks me-2 text-warning"></i>Eylemler
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=action/calendar">
                                            <i class="fas fa-calendar me-2 text-danger"></i>Takvim
                                        </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($isSuperAdmin || hasPermission('news.manage') || hasPermission('documentstrategies.manage') || hasPermission('regulations.manage')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt me-2"></i>İçerik
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <?php if ($isSuperAdmin || hasPermission('news.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=news/index">
                                            <i class="fas fa-newspaper me-2 text-primary"></i>Haberler
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('documentstrategies.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=documentstrategy/index">
                                            <i class="fas fa-file-pdf me-2 text-danger"></i>Belgeler
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('regulations.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=regulation/index">
                                            <i class="fas fa-book me-2 text-success"></i>Mevzuat
                                        </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($isSuperAdmin || hasPermission('users.manage') || hasPermission('coves.manage') || hasPermission('fields.manage') || hasPermission('logs.manage')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cogs me-2"></i>Sistem
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <?php if ($isSuperAdmin || hasPermission('users.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/manage">
                                            <i class="fas fa-users me-2 text-primary"></i>Kullanıcılar
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/roles">
                                            <i class="fas fa-shield-alt me-2 text-warning"></i>Roller
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('coves.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=cove/index">
                                            <i class="fas fa-building me-2 text-info"></i>SMM Merkezleri
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('fields.manage')): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=field/index">
                                            <i class="fas fa-th me-2 text-success"></i>SMM Alanları
                                        </a></li>
                                <?php endif; ?>
                                <?php if ($isSuperAdmin || hasPermission('logs.manage')): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=log/index">
                                            <i class="fas fa-history me-2 text-secondary"></i>Sistem Logları
                                        </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=help/index">
                            <i class="fas fa-question-circle me-2"></i>Yardım
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Right Side -->
            <div class="d-flex align-items-center">
                <div class="me-3 d-none d-lg-block">
                    <img src="<?php echo BASE_URL; ?>wwwroot/img/turkiye.svg" alt="Türkiye 100. Yıl" style="height: 45px;" class="rounded shadow-sm">
                </div>

                <?php if (!$isLoggedIn): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?url=user/login" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>SMM Portal
                    </a>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none dropdown-toggle user-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?php if (!empty($_SESSION['profile_photo']) && file_exists(dirname(__DIR__) . '/../../wwwroot/uploads/profiles/' . $_SESSION['profile_photo'])): ?>
                                    <img src="<?php echo BASE_URL; ?>uploads/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
                                        alt="Profil Fotoğrafı">
                                <?php else: ?>
                                    <img src="<?php echo BASE_URL; ?>wwwroot/img/default-avatar.svg" alt="Varsayılan Profil">
                                <?php endif; ?>
                            </div>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                    Hoşgeldiniz, <?php echo htmlspecialchars($_SESSION['realname'] ?? $_SESSION['username']); ?>
                                </div>
                                <div class="small text-muted"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Kullanıcı'); ?></div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=user/editprofile">
                                    <i class="fas fa-user-edit me-2"></i>Profil Düzenle
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?url=user/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
                                </a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>