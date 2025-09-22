<?php

/**
 * Unified Layout Template - Updated to use modular components
 * This template works for both logged-in and logged-out users
 * Uses the new component system for header, navbar, footer, and scripts
 */

// Import permission helper
require_once __DIR__ . '/../../../includes/PermissionHelper.php';

$title = $title ?? 'MTEGM SMM Portal';
$safePageTitle = $page_title ?? $title;
$isLoggedIn = isset($_SESSION['username']);
$currentUrl = $_GET['url'] ?? '';

// Default body class
$bodyClass = $bodyClass ?? 'hold-transition layout-fixed';

// Page-specific styles for unified layout
$additionalCss = '
    /* Main content area */
    .main-content {
        margin-top: 95px;
        min-height: calc(100vh - 95px);
        background-color: #f8f9fa;
    }

    /* Page header for internal pages */
    .page-header-internal {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .page-header-internal h1 {
        color: white;
        font-weight: 600;
        margin: 0;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0.5rem 0 0 0;
    }

    .breadcrumb-item {
        color: rgba(255, 255, 255, 0.8);
    }

    .breadcrumb-item.active {
        color: white;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: white;
    }

    @media (max-width: 991.98px) {
        .main-content {
            margin-top: 80px;
        }
    }
';

// Load header component (includes <head> tag and all necessary CSS/meta tags)
include __DIR__ . '/../components/header.php';
?>

<!-- Unified Layout Body -->
    <div class="wrapper">
        <?php include __DIR__ . '/../components/navbar.php'; ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <?php if ($isLoggedIn && (!isset($hidePageHeader) || !$hidePageHeader)): ?>
                <!-- Page Header for Internal Pages -->
                <div class="page-header-internal">
                    <div class="container-fluid" style="max-width: 90%; margin: 0 auto;">
                        <h1><?php echo htmlspecialchars($safePageTitle); ?></h1>
                        <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Ana Sayfa</a></li>
                                    <?php foreach ($breadcrumb as $index => $item): ?>
                                        <?php
                                        // Ensure item has required data
                                        if (!is_array($item) || !isset($item['title']) || empty($item['title'])) {
                                            continue;
                                        }
                                        ?>
                                        <?php if ($index === count($breadcrumb) - 1): ?>
                                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($item['title']); ?></li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item">
                                                <?php if (isset($item['url']) && !empty($item['url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="<?php echo $isLoggedIn ? 'py-4' : ''; ?>" style="max-width: 90%; margin: 0 auto;">
                <?php echo $content ?? ''; ?>
            </div>
        </main>

        <?php include __DIR__ . '/../components/footer.php'; ?>
    </div>

    <?php include __DIR__ . '/../components/scripts.php'; ?>

    <!-- Template Fix Script for Dropdowns -->
    <script src="<?php echo BASE_URL; ?>js/template-fix.js"></script>
</body>
</html>