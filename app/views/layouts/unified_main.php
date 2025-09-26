<!DOCTYPE html>
<html lang="tr" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription ?? 'MTEGM SMM Portal'); ?>">
    <meta name="keywords" content="meb, mtegm, smm, mesleki eğitim, teknik eğitim">

    <title><?php echo htmlspecialchars($pageTitle ?? 'Ana Sayfa'); ?> - SMM Portal</title>

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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main content area */
        .main-wrapper {
            flex: 1;
            display: flex;
            width: 100%;
        }

        /* When logged in with sidebar */
        body.has-sidebar .main-wrapper {
            padding-top: 56px; /* Navbar height */
        }

        body.has-sidebar .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: 250px;
            background: white;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            z-index: 100;
        }

        body.has-sidebar .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 2rem;
        }

        /* When not logged in or no sidebar */
        body:not(.has-sidebar) .main-wrapper {
            padding-top: 80px;
        }

        body:not(.has-sidebar) .main-content {
            width: 100%;
            padding: 2rem 0;
        }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            body.has-sidebar .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            body.has-sidebar .sidebar.show {
                transform: translateX(0);
            }

            body.has-sidebar .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Footer at bottom */
        footer {
            margin-top: auto;
        }
    </style>

    <?php if (isset($additionalCss)): ?>
        <style><?php echo $additionalCss; ?></style>
    <?php endif; ?>
</head>
<body class="<?php echo isset($_SESSION['user_id']) ? 'has-sidebar' : ''; ?> <?php echo $bodyClass ?? ''; ?>">

<?php
// Determine which navbar to use based on login status
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['username']);

if ($isLoggedIn) {
    // Use navbar with sidebar for logged in users
    include __DIR__ . '/../components/navbar_with_sidebar.php';
} else {
    // Use regular navbar for public pages
    include __DIR__ . '/../components/navbar.php';
}
?>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <?php if ($isLoggedIn): ?>
        <!-- Sidebar for logged in users -->
        <aside class="sidebar" id="sidebar">
            <?php include __DIR__ . '/../components/sidebar_menu.php'; ?>
        </aside>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container-fluid">
            <?php echo $content ?? ''; ?>
        </div>
    </main>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../components/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
// Theme toggle
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    // Update server-side session
    fetch('<?php echo BASE_URL; ?>index.php?url=user/toggleTheme', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({theme: newTheme})
    });
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>

<?php if (isset($additionalJs)): ?>
    <script><?php echo $additionalJs; ?></script>
<?php endif; ?>

</body>
</html>