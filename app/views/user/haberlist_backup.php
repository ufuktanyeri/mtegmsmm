<?php
// Page configuration
$title = 'Haberler';
$description = 'Sektörel Mükemmeliyet Merkezleri haberleri ve güncellemeleri';
$bodyClass = 'hold-transition layout-fixed news-page';

// Additional page-specific styles
$additionalCss = '

    <style>
        .blog-header-logo {
            font-family: "Playfair Display", Georgia, "Times New Roman", serif;
            font-size: 2.25rem;
        }

        .blog-post-title {
            font-size: 2rem;
        }

        .blog-post-meta {
            color: #6c757d;
        }

        .blog-thumb {
            max-height: 180px;
            object-fit: cover;
        }

        .card-hover:hover {
            box-shadow: 0 0 10px #ccc;
            transition: box-shadow 0.2s;
        }

        /* Modern Navigation Styles */
        .navbar {
            padding: 1rem 0;
            min-height: 80px;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.02);
        }

        .nav-link {
            font-weight: 500;
            color: #495057 !important;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
            position: relative;
        }

        .nav-link:hover {
            color: #0d6efd !important;
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.1);
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 2px;
            background: #0d6efd;
            border-radius: 1px;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }

        .dropdown-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0 0.5rem;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        /* Mobile navbar improvements */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 12px;
                padding: 1rem;
                margin-top: 1rem;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            }

            .nav-link {
                padding: 0.75rem 1rem !important;
                margin: 0.25rem 0;
                border-radius: 8px;
            }

            .dropdown-menu {
                border: none;
                box-shadow: none;
                background: #f8f9fa;
                margin-left: 1rem;
            }
        }
    </style>
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">

        <!-- Modern Navbar -->
        <nav class="navbar navbar-expand-lg fixed-top shadow-sm" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px); border-bottom: 1px solid #e9ecef;">
            <div class="container">
                <!-- Brand -->
                <a href="https://meb.gov.tr/" class="navbar-brand d-flex align-items-center me-4">
                    <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg" alt="MEB" style="height: 55px; width: auto;" class="me-3">
                    <div class="d-none d-lg-block">
                        <div class="fw-bold text-primary" style="font-size: 1.1rem; line-height: 1.2;">
                            T.C. Milli Eğitim Bakanlığı
                        </div>
                        <div class="text-body-secondary" style="font-size: 0.85rem;">
                            Mesleki ve Teknik Eğitim Genel Müdürlüğü
                        </div>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Menüyü Aç/Kapat">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=user/main">
                                <i class="fas fa-home me-2"></i>Ana Sayfa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active px-3 py-2 rounded-pill fw-semibold" href="<?php echo BASE_URL; ?>index.php?url=user/haberlist">
                                <i class="fas fa-newspaper me-2"></i>Haberler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=home/smmnetwork">
                                <i class="fas fa-map-marked-alt me-2"></i>SMM Haritası
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side -->
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-none d-lg-block">
                            <img src="<?php echo BASE_URL; ?>wwwroot/img/turkiye.svg" alt="Türkiye 100. Yıl" style="height: 45px;" class="rounded shadow-sm">
                        </div>
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/login" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fas fa-sign-in-alt me-2"></i>SMM Portal
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container" style="margin-top:95px;">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <h2 class="pb-4 mb-4 fst-italic border-bottom">Tüm Haberler</h2>
                    <div class="row">
                        <?php if (!empty($newsList)): ?>
                            <?php foreach ($newsList as $news): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card card-hover h-100">
                                        <?php if ($news->getFrontpageImage()): ?>
                                            <img src="<?php echo htmlspecialchars($news->getFrontpageImage()); ?>" class="card-img-top blog-thumb" alt="Haber Görseli">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h3 class="blog-post-title">
                                                <a href="index.php?url=user/haberler&id=<?php echo $news->getId(); ?>" class="text-dark text-decoration-none">
                                                    <?php echo htmlspecialchars($news->getTitle()); ?>
                                                </a>
                                            </h3>
                                            <p class="blog-post-meta mb-2">
                                                <?php echo date('d.m.Y H:i', strtotime($news->getCreatedDate())); ?>
                                                <?php if ($news->getHeadline()): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Manşet</span>
                                                <?php endif; ?>
                                            </p>
                                            <p>
                                                <?php
                                                $details = strip_tags($news->getDetails());
                                                echo mb_substr($details, 0, 180) . (mb_strlen($details) > 180 ? '...' : '');
                                                ?>
                                            </p>
                                            <a href="index.php?url=user/haberler&id=<?php echo $news->getId(); ?>" class="btn btn-outline-primary btn-sm">Devamını Oku</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning mt-4">Hiç haber bulunamadı.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Pagination -->
                    <?php
                    $totalPages = isset($totalNews) && isset($perPage) ? ceil($totalNews / $perPage) : 1;
                    $currentPage = isset($currentPage) ? $currentPage : 1;
                    ?>
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Sayfalar" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item<?php if ($currentPage <= 1) echo ' disabled'; ?>">
                                    <a class="page-link" href="?url=user/haberlist&page=<?php echo max(1, $currentPage - 1); ?>" tabindex="-1">Önceki</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item<?php if ($i == $currentPage) echo ' active'; ?>">
                                        <a class="page-link" href="?url=user/haberlist&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item<?php if ($currentPage >= $totalPages) echo ' disabled'; ?>">
                                    <a class="page-link" href="?url=user/haberlist&page=<?php echo min($totalPages, $currentPage + 1); ?>">Sonraki</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>