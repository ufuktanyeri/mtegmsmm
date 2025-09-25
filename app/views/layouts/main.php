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


    <?php if (isset($additionalStyles)): ?>
        <?php echo $additionalStyles; ?>
    <?php endif; ?>
</head>
<body class="bg-light">
    <!-- Top Navigation -->
    <nav class="navbar navbar-dark bg-primary sticky-top flex-md-nowrap p-0 shadow">
        <div class="container-fluid">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white fw-semibold" href="/home">
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
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse position-fixed top-0 bottom-0 start-0 pt-5 shadow-sm" style="z-index: 100;">
                <div class="position-sticky pt-3 overflow-auto" style="height: calc(100vh - 48px);">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link fw-medium text-dark py-2 px-3 <?php echo $activeMenu == 'dashboard' ? 'active bg-primary-subtle text-primary border-start border-primary border-3' : ''; ?>" href="/home">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Ana Sayfa
                            </a>
                        </li>

                        <h6 class="mt-3 px-3 text-muted text-uppercase small fw-bold">
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
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