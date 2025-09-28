<!DOCTYPE html>
<html lang="tr" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription ?? 'MTEGM SMM Portal - Yönetim Paneli'); ?>">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Yönetim Paneli'); ?> - SMM Portal</title>

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg">
    <link rel="alternate icon" href="<?php echo BASE_URL; ?>favicon.ico">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5.3.8 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Admin CSS -->
    <style>
        :root {
            --bs-primary: #003C7D;
            --meb-primary: #003C7D;
            --meb-secondary: #0056B3;
        }
    </style>

    <?php if (isset($additionalCss)): ?>
        <style><?php echo $additionalCss; ?></style>
    <?php endif; ?>
</head>
<body>

<?php
// Session check
if (!isset($_SESSION['username'])) {
    header('Location: ' . BASE_URL . 'index.php?url=user/login');
    exit;
}

// Include the navbar with sidebar
include __DIR__ . '/../components/navbar_with_sidebar.php';
?>

<!-- Content will be injected into main-content by the view -->
<div style="display: none;" id="page-content">
    <?php echo $content ?? ''; ?>
</div>

<script>
// Move content to main-content area
document.addEventListener('DOMContentLoaded', function() {
    const pageContent = document.getElementById('page-content');
    const mainContent = document.querySelector('.main-content .container-fluid');

    if (pageContent && mainContent) {
        mainContent.innerHTML = pageContent.innerHTML;
        pageContent.remove();
    }

    // Set theme from session
    const savedTheme = '<?php echo $_SESSION['theme'] ?? 'light'; ?>';
    if (savedTheme) {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        const themeIcon = document.querySelector('#themeToggle i');
        if (themeIcon) {
            themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun fs-5' : 'fas fa-moon fs-5';
        }
    }
});
</script>

<!-- Bootstrap 5.3.8 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (if needed for legacy code) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php if (isset($additionalJs)): ?>
    <script><?php echo $additionalJs; ?></script>
<?php endif; ?>

</body>
</html>