<!DOCTYPE html>
<html lang="tr" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription ?? 'Milli Eğitim Bakanlığı Sektörel Mükemmeliyet Merkezleri'); ?>">
    <meta name="keywords" content="meb, mtegm, smm, mesleki eğitim, teknik eğitim">

    <title><?php echo htmlspecialchars($pageTitle ?? 'Ana Sayfa'); ?> - MTEGM SMM Portal</title>

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg">
    <link rel="alternate icon" href="<?php echo BASE_URL; ?>favicon.ico">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5.3.8 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --bs-primary: #003C7D;
            --meb-primary: #003C7D;
            --meb-secondary: #0056B3;
            --meb-gradient: linear-gradient(135deg, var(--meb-primary) 0%, var(--meb-secondary) 100%);
        }

        body {
            padding-top: 80px; /* For fixed navbar */
        }
    </style>

    <?php if (isset($additionalCss)): ?>
        <style><?php echo $additionalCss; ?></style>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">

<!-- Include Regular Navbar for Public Pages -->
<?php include __DIR__ . '/../components/navbar.php'; ?>

<!-- Breadcrumb Navigation -->
<?php if (!empty($breadcrumb) && is_array($breadcrumb)): ?>
<nav aria-label="breadcrumb">
    <div class="container mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Ana Sayfa</a></li>
            <?php foreach ($breadcrumb as $index => $item): ?>
                <?php if (!is_array($item) || !isset($item['title']) || empty($item['title'])) continue; ?>
                <?php if ($index === count($breadcrumb) - 1): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($item['title']); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item">
                        <?php if (!empty($item['url'])): ?>
                            <a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($item['title']); ?>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
<?php endif; ?>

<!-- Main Content -->
<div class="wrapper">
    <?php echo $content ?? ''; ?>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../components/footer.php'; ?>

<!-- Bootstrap 5.3.8 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
// Theme Management
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
});
</script>

<?php if (isset($additionalJs)): ?>
    <script><?php echo $additionalJs; ?></script>
<?php endif; ?>

</body>
</html>