<?php
/**
 * Reusable Header Component
 * Contains HTML head, meta tags, and CSS imports
 */

$pageTitle = $title ?? 'MTEGM SMM Portal';
$pageDescription = $description ?? 'Milli Eğitim Bakanlığı Sektörel Mükemmeliyet Merkezleri - Nitelikli iş gücü yetiştiriyoruz.';
$currentUrl = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="meb, mtegm, smm, mesleki eğitim, teknik eğitim, sektörel mükemmeliyet">
    <meta name="author" content="T.C. Milli Eğitim Bakanlığı">
    
    <!-- SEO Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> - Milli Eğitim Bakanlığı">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL . ($_SERVER['REQUEST_URI'] ?? ''); ?>">
    
    <title><?php echo htmlspecialchars($pageTitle); ?> - Milli Eğitim Bakanlığı</title>

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg">
    <link rel="alternate icon" href="<?php echo BASE_URL; ?>favicon.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5.3.6 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper CSS for Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css?v=<?php echo time(); ?>">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/admin-bootstrap5.css">

    <!-- Template Service CSS -->
    <?php
    // Load TemplateService for responsive CSS
    require_once __DIR__ . '/../../services/TemplateService.php';
    use App\Services\TemplateService;
    ?>
    <style>
    <?php echo TemplateService::getResponsiveCSS(); ?>
    </style>

    <!-- Common Bootstrap 5 Styles -->
    <style>
        :root {
            --bs-primary: #003C7D;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;

            --meb-primary: #003C7D;
            --meb-secondary: #0056B3;
            --meb-gradient: linear-gradient(135deg, var(--meb-primary) 0%, var(--meb-secondary) 100%);
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1d20;
            --bs-body-color: #dee2e6;
            --bs-dark: #1a1d20;
            --bs-light: #dee2e6;
            --bs-border-color: #495057;
        }

        /* Common component styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .btn {
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-radius: 50%;
            border-top: 2px solid var(--bs-primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- AssetManager CSS -->
    <?php
    if (class_exists('App\Helpers\AssetManager')) {
        echo \App\Helpers\AssetManager::renderCss(BASE_URL);
    }
    ?>

    <!-- Additional page-specific CSS -->
    <?php if (isset($additionalCss)): ?>
        <style><?php echo $additionalCss; ?></style>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass ?? 'hold-transition layout-fixed'; ?>">