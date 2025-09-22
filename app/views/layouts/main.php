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
            --meb-success: #28a745;
            --meb-info: #17a2b8;
            --meb-warning: #ffc107;
            --meb-danger: #dc3545;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--meb-primary) !important;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        @media (max-width: 767.98px) {
            .sidebar {
                top: 5rem;
            }
        }

        .sidebar-sticky {
            height: calc(100vh - 48px);
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
            padding: .5rem 1rem;
        }

        .sidebar .nav-link:hover {
            color: var(--meb-primary);
            background-color: rgba(30, 60, 114, 0.05);
        }

        .sidebar .nav-link.active {
            color: var(--meb-primary);
            background-color: rgba(30, 60, 114, 0.1);
            border-left: 3px solid var(--meb-primary);
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            padding: .5rem 1rem;
            font-weight: 700;
            color: #6c757d;
        }

        .navbar {
            background: linear-gradient(135deg, var(--meb-primary) 0%, var(--meb-secondary) 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255,255,255,.9);
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
        }

        main {
            padding-top: 48px;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .card-header {
            background-color: rgba(30, 60, 114, 0.03);
            border-bottom: 1px solid rgba(30, 60, 114, 0.125);
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--meb-primary);
            border-color: var(--meb-primary);
        }

        .btn-primary:hover {
            background-color: var(--meb-secondary);
            border-color: var(--meb-secondary);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--meb-primary);
            background-color: rgba(30, 60, 114, 0.03);
        }

        .badge {
            padding: 0.35em 0.65em;
        }

        .stat-card {
            border-left: 4px solid var(--meb-primary);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .meb-logo {
            height: 32px;
            margin-right: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--meb-primary);
        }
    </style>

    <?php if (isset($additionalStyles)): ?>
        <?php echo $additionalStyles; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow">
        <div class="container-fluid">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="/home">
                <i class="bi bi-mortarboard-fill me-2"></i>
                MTEGM SMM Portal
            </a>

            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-nav flex-row ms-auto me-3">
                <div class="nav-item text-nowrap">
                    <span class="nav-link px-3 text-white">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo $_SESSION['name'] ?? 'Kullanıcı'; ?>
                    </span>
                </div>
                <div class="nav-item text-nowrap">
                    <a class="nav-link px-3 text-white" href="/auth/logout">
                        <i class="bi bi-box-arrow-right"></i> Çıkış
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'dashboard' ? 'active' : ''; ?>" href="/home">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Ana Sayfa
                            </a>
                        </li>

                        <h6 class="sidebar-heading mt-3">
                            <span>Stratejik Yönetim</span>
                        </h6>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'objectives' ? 'active' : ''; ?>" href="/objective">
                                <i class="bi bi-bullseye me-2"></i>
                                Amaçlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'actions' ? 'active' : ''; ?>" href="/action">
                                <i class="bi bi-list-task me-2"></i>
                                Faaliyetler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'indicators' ? 'active' : ''; ?>" href="/indicator">
                                <i class="bi bi-graph-up me-2"></i>
                                Göstergeler
                            </a>
                        </li>

                        <h6 class="sidebar-heading mt-3">
                            <span>Yönetim</span>
                        </h6>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'users' ? 'active' : ''; ?>" href="/user">
                                <i class="bi bi-people me-2"></i>
                                Kullanıcılar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'coves' ? 'active' : ''; ?>" href="/cove">
                                <i class="bi bi-building me-2"></i>
                                Birimler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'fields' ? 'active' : ''; ?>" href="/field">
                                <i class="bi bi-tags me-2"></i>
                                Alanlar
                            </a>
                        </li>

                        <h6 class="sidebar-heading mt-3">
                            <span>Raporlar</span>
                        </h6>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'reports' ? 'active' : ''; ?>" href="/report">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                                Raporlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeMenu == 'logs' ? 'active' : ''; ?>" href="/log">
                                <i class="bi bi-clock-history me-2"></i>
                                İşlem Kayıtları
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Active menu item
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    </script>

    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>