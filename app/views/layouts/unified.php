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

// Page-specific styles for unified layout (minimal CSS with Bootstrap utilities)
$additionalCss = '
    /* Main content area positioning */
    .main-content {
        margin-top: 95px;
        min-height: calc(100vh - 95px);
    }

    /* Page header gradient - cannot be done with utilities */
    .page-header-internal {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    /* Breadcrumb link colors for contrast on dark background */
    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.9);
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
        <main class="main-content bg-light">
            <?php if ($isLoggedIn && (!isset($hidePageHeader) || !$hidePageHeader)): ?>
                <!-- Page Header for Internal Pages -->
                <div class="page-header-internal text-white py-5 mb-4">
                    <div class="container-fluid px-lg-5">
                        <h1 class="text-white fw-semibold mb-0"><?php echo htmlspecialchars($safePageTitle); ?></h1>
                        <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0 mt-2 mb-0">
                                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-white-50 text-decoration-none">Ana Sayfa</a></li>
                                    <?php foreach ($breadcrumb as $index => $item): ?>
                                        <?php
                                        // Ensure item has required data
                                        if (!is_array($item) || !isset($item['title']) || empty($item['title'])) {
                                            continue;
                                        }
                                        ?>
                                        <?php if ($index === count($breadcrumb) - 1): ?>
                                            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($item['title']); ?></li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item text-white-50">
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
            <div class="container-fluid <?php echo $isLoggedIn ? 'py-4' : ''; ?> px-lg-5">
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