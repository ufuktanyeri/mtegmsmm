<?php
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");

// Page configuration
$title = 'Ana Sayfa';
$description = 'Milli Eğitim Bakanlığı Sektörel Mükemmeliyet Merkezleri - Nitelikli iş gücü yetiştiriyoruz.';
$bodyClass = 'hold-transition layout-fixed home-page';

// Additional page-specific styles
$additionalCss = '
    /* Homepage specific styles */
    .hero-section {
      min-height: 60vh;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(54, 120, 207, 0.1) 0%, rgba(139, 90, 211, 0.1) 100%);
      opacity: 0.1;
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .min-vh-50 {
      min-height: 50vh;
    }

    .card {
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    /* Carousel improvements */
    .carousel-item h1 {
      color: #343a40;
      font-weight: 600;
    }

    .carousel-item p {
      color: #6c757d;
      line-height: 1.6;
    }

    /* Button improvements */
    .btn {
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

    /* Navbar scrolled state */
    .navbar.scrolled {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(15px);
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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
            <div class="text-muted" style="font-size: 0.85rem;">
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
              <a class="nav-link <?php echo $currentUrl === 'user/main' ? 'active fw-semibold' : ''; ?> px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=user/main">
                <i class="fas fa-home me-2"></i>Ana Sayfa
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo $currentUrl === 'user/haberlist' ? 'active fw-semibold' : ''; ?> px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=user/haberlist">
                <i class="fas fa-newspaper me-2"></i>Haberler
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo $currentUrl === 'home/smmnetwork' ? 'active fw-semibold' : ''; ?> px-3 py-2 rounded-pill" href="<?php echo BASE_URL; ?>index.php?url=home/smmnetwork">
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

    <!-- Hero Section -->
    <section class="hero-section text-white py-5" style="margin-top: 95px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
      <div class="container">
        <div class="row align-items-center min-vh-50">
          <div class="col-lg-8 col-md-10 mx-auto text-center">
            <div class="hero-content py-5">
              <h1 class="display-4 fw-bold mb-4 text-white">
                Milli Eğitim Bakanlığı Mesleki ve Teknik Eğitim Genel Müdürlüğü
              </h1>
              <p class="lead mb-4 text-white opacity-90">
                Sektörel Mükemmeliyet Merkezleri ile nitelikli iş gücü yetiştiriyoruz.
                Modern eğitim anlayışı ve sektör işbirliği ile geleceği şekillendiriyoruz.
              </p>
              <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="#haberSlayt" class="btn btn-light btn-lg px-4">
                  <i class="fas fa-newspaper me-2"></i>Haberler
                </a>
                <a href="/index.php?url=user/login" class="btn btn-outline-light btn-lg px-4">
                  <i class="fas fa-sign-in-alt me-2"></i>SMM Portal
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="container" style="margin-top:30px;">
      <div id="haberSlayt" class="carousel slide mt-5" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
          <?php if (isset($headlineNews) && count($headlineNews) > 0): ?>
            <?php foreach ($headlineNews as $i => $haber): ?>
              <button type="button" data-bs-target="#haberSlayt" data-bs-slide-to="<?php echo $i; ?>"
                class="<?php echo $i === 0 ? 'active' : ''; ?>"
                <?php echo $i === 0 ? 'aria-current="true"' : ''; ?>
                aria-label="Slide <?php echo $i + 1; ?>"></button>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="carousel-inner rounded shadow" style="background: #ffffff; min-height: 400px;">
          <?php if (isset($headlineNews) && count($headlineNews) > 0): ?>
            <?php foreach ($headlineNews as $i => $haber): ?>
              <div class="carousel-item p-4 <?php echo $i === 0 ? 'active' : ''; ?>">
                <div class="container-fluid">
                  <div class="row mb-4">
                    <div class="col-12 text-center">
                      <h2 class="fw-bold text-primary"><?php echo htmlspecialchars($haber->getTitle()); ?></h2>
                    </div>
                  </div>
                  <div class="row align-items-center">
                    <div class="col-md-6 d-flex align-items-center justify-content-center mb-3 mb-md-0">
                      <?php if ($haber->getFrontpageImage()): ?>
                        <img src="<?php echo htmlspecialchars($haber->getFrontpageImage()); ?>"
                          class="img-fluid rounded shadow-sm"
                          style="max-height: 300px; object-fit: cover;"
                          alt="Haber Görseli">
                      <?php endif; ?>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center">
                      <p class="text-muted mb-4" style="line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($haber->getDetails())); ?>
                      </p>
                      <div>
                        <a class="btn btn-primary btn-lg"
                          href="index.php?url=user/haberler&id=<?php echo $haber->getId(); ?>"
                          role="button">
                          <i class="fas fa-arrow-right me-2"></i>Detayları Görüntüle
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#haberSlayt" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Önceki</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#haberSlayt" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Sonraki</span>
        </button>
      </div>
      <script>
        // Bootstrap 5 Carousel initialization
        document.addEventListener('DOMContentLoaded', function() {
          const carousel = document.getElementById('haberSlayt');
          if (carousel) {
            new bootstrap.Carousel(carousel, {
              interval: 4000,
              ride: 'carousel'
            });
          }

          // Modern Navbar Scroll Effect
          const navbar = document.querySelector('.navbar');
          let lastScrollTop = 0;

          window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 100) {
              navbar.classList.add('scrolled');
            } else {
              navbar.classList.remove('scrolled');
            }

            // Hide/show navbar on scroll
            if (scrollTop > lastScrollTop && scrollTop > 200) {
              navbar.style.transform = 'translateY(-100%)';
            } else {
              navbar.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;
          });

          // Smooth scroll for anchor links
          document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
              e.preventDefault();
              const target = document.querySelector(this.getAttribute('href'));
              if (target) {
                target.scrollIntoView({
                  behavior: 'smooth',
                  block: 'start'
                });
              }
            });
          });

          // Active nav link highlighting
          const navLinks = document.querySelectorAll('.nav-link');
          navLinks.forEach(link => {
            link.addEventListener('click', function() {
              navLinks.forEach(l => l.classList.remove('active'));
              this.classList.add('active');
            });
          });
        });
      </script>

      <!-- Önemli Belgeler Section -->
      <section class="py-5 bg-light">
        <div class="container">
          <div class="row">
            <div class="col-12 text-center mb-5">
              <h2 class="fw-bold text-primary">Önemli Belgeler</h2>
              <p class="text-muted">Mesleki ve teknik eğitimde kalite ve standartları belirleyen temel düzenlemeler</p>
            </div>
          </div>
          <div class="row g-4">
            <div class="col-lg-6">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                      <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-0 text-primary">SMM Çalışma Yönergesi</h4>
                  </div>
                  <p class="card-text text-muted">
                    Sektörel Mükemmeliyet Merkezlerinin kuruluşundan işleyişine kadar birçok kritik düzenlemeyi
                    içeren önemli yönerge. Mesleki ve teknik eğitimde kaliteyi artırmayı ve sektörlerle güçlü
                    entegrasyon sağlamayı hedefliyor.
                  </p>
                  <a class="btn btn-primary btn-sm" target="_blank"
                    href="https://mevzuat.meb.gov.tr/dosyalar/2208.pdf" role="button">
                    <i class="fas fa-download me-2"></i>Yönergeyi İndir
                  </a>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-success text-white rounded-circle p-3 me-3">
                      <i class="fas fa-clipboard-list fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-0 text-success">Politika Belgesi</h4>
                  </div>
                  <p class="card-text text-muted">
                    Mesleki ve Teknik Eğitimin kalitesinin artırılmasına yönelik stratejik hedefler ve uygulanacak
                    faaliyetleri içeren kapsamlı politika belgesi.
                  </p>
                  <a class="btn btn-success btn-sm" target="_blank"
                    href="https://mtegm.meb.gov.tr/meb_iys_dosyalar/2024_09/18170207_16_09_2024_mtgmpolitikabelgesi.pdf" role="button">
                    <i class="fas fa-download me-2"></i>Belgeyi İndir
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Hakkımızda Section -->
      <section id="hakkimizda" class="py-5">
        <div class="container">
          <div class="row">
            <div class="col-12 text-center mb-5">
              <h2 class="fw-bold text-primary">Hakkımızda</h2>
              <p class="text-muted">Mesleki ve Teknik Eğitimde Mükemmelliğin Adresi</p>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
              <h3 class="fw-bold mb-3">Sektörel Mükemmeliyet Merkezleri</h3>
              <p class="mb-3">
                Milli Eğitim Bakanlığı Mesleki ve Teknik Eğitim Genel Müdürlüğü bünyesinde faaliyet gösteren
                Sektörel Mükemmeliyet Merkezleri, mesleki ve teknik eğitimde kaliteyi artırmak ve sektör
                ihtiyaçlarına uygun nitelikli iş gücü yetiştirmek amacıyla kurulmuştur.
              </p>
              <p class="mb-4">
                Modern eğitim teknolojileri ve sektörle güçlü işbirliği ile öğrencilerimizi geleceğe hazırlıyoruz.
              </p>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary px-3 py-2">Nitelikli Eğitim</span>
                <span class="badge bg-success px-3 py-2">Sektör İşbirliği</span>
                <span class="badge bg-info px-3 py-2">Modern Teknoloji</span>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="row g-3">
                <div class="col-6">
                  <div class="text-center p-3 bg-light rounded shadow-sm">
                    <h4 class="fw-bold text-primary mb-1">15</h4>
                    <small class="text-muted fw-semibold">SMM Merkezi</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-3 bg-light rounded shadow-sm">
                    <h4 class="fw-bold text-success mb-1">25</h4>
                    <small class="text-muted fw-semibold">Pilot Mesleki Alan</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-3 bg-light rounded shadow-sm">
                    <h4 class="fw-bold text-info mb-1">3.000</h4>
                    <small class="text-muted fw-semibold">Öğretmen İşbaşı Eğitimi</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-3 bg-light rounded shadow-sm">
                    <h4 class="fw-bold text-warning mb-1">22M €</h4>
                    <small class="text-muted fw-semibold">Proje Bütçesi</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <!-- Custom Admin JS -->
  <!-- AdminLTE App -->
  <script src="<?php echo BASE_URL; ?>js/admin-bootstrap5.js"></script>
</body>

</html>