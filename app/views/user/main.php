<?php
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");

// Page configuration
$title = 'Ana Sayfa';
$description = 'Milli Eğitim Bakanlığı Sektörel Mükemmeliyet Merkezleri - Nitelikli iş gücü yetiştiriyoruz.';
$bodyClass = 'hold-transition layout-fixed home-page';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['username']);

// Additional page-specific styles
$additionalCss = '
    /* Homepage specific styles */
    .main-hero {
        padding-bottom: 5rem !important; /* Extra padding for buttons */
    }

    .action-buttons-section {
        margin-bottom: 3rem;
    }

    .action-buttons-section .btn {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .action-buttons-section .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }

    /* News carousel improvements */
    .carousel-item {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        margin: 1rem;
    }

    .carousel-item h2 {
        color: var(--meb-primary);
        font-weight: 700;
    }

    .carousel-item p {
        color: #6c757d;
        line-height: 1.7;
    }

    /* Stats section */
    .stats-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--meb-gradient);
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--meb-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Document cards */
    .document-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s ease;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    }

    .document-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
    }

    .document-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        background: var(--meb-gradient);
    }

    /* Section styling */
    .section {
        padding: 4rem 0;
    }

    .section-title {
        position: relative;
        font-weight: 700;
        color: var(--meb-primary);
        margin-bottom: 1rem;
    }

    .section-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    /* Badge improvements */
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }
';

// Additional page-specific JavaScript
$additionalJs = '
    // Initialize carousels
    const carousels = document.querySelectorAll(".carousel");
    carousels.forEach(function(carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            ride: "carousel"
        });
    });

    // Animate stats on scroll
    function animateNumbers() {
        const statsNumbers = document.querySelectorAll(".stats-number");
        statsNumbers.forEach(function(stat) {
            const target = parseInt(stat.textContent);
            const increment = target / 100;
            let current = 0;
            
            const timer = setInterval(function() {
                current += increment;
                stat.textContent = Math.floor(current);
                
                if (current >= target) {
                    stat.textContent = target;
                    clearInterval(timer);
                }
            }, 20);
        });
    }

    // Intersection Observer for animations
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate__animated", "animate__fadeInUp");
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const animatedElements = document.querySelectorAll(".stats-card, .document-card");
        animatedElements.forEach(function(el) {
            observer.observe(el);
        });
    });
';

// Load header component
include __DIR__ . '/../components/header.php';
?>

<div class="wrapper">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <?php
    // Hero section configuration
    $heroConfig = [
        'title' => 'Milli Eğitim Bakanlığı<br>Mesleki ve Teknik Eğitim Genel Müdürlüğü',
        'subtitle' => 'Sektörel Mükemmeliyet Merkezleri ile nitelikli iş gücü yetiştiriyoruz.<br>Modern eğitim anlayışı ve sektör işbirliği ile geleceği şekillendiriyoruz.',
        'icon' => '',  // No icon for main page
        'gradient' => true,
        'type' => 'section',
        'classes' => 'main-hero'
    ];
    include __DIR__ . '/../components/hero.php';
    ?>

    <!-- Action Buttons Section -->
    <section class="action-buttons-section" style="margin-top: -50px; position: relative; z-index: 10;">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="#haberSlayt" class="btn btn-light btn-lg px-4 rounded-pill shadow">
                    <i class="fas fa-newspaper me-2"></i>Haberler
                </a>
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary btn-lg px-4 rounded-pill shadow">
                        <i class="fas fa-tachometer-alt me-2"></i>Yönetim Paneli
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>index.php?url=user/login" class="btn btn-primary btn-lg px-4 rounded-pill shadow">
                        <i class="fas fa-sign-in-alt me-2"></i>SMM Portal
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- News Carousel Section -->
    <div class="container section">
        <div id="haberSlayt" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
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
            
            <div class="carousel-inner" style="min-height: 400px;">
                <?php if (isset($headlineNews) && count($headlineNews) > 0): ?>
                    <?php foreach ($headlineNews as $i => $haber): ?>
                        <div class="carousel-item p-4 <?php echo $i === 0 ? 'active' : ''; ?>">
                            <div class="container-fluid">
                                <div class="row mb-4">
                                    <div class="col-12 text-center">
                                        <h2 class="fw-bold"><?php echo htmlspecialchars($haber->getTitle()); ?></h2>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-6 d-flex align-items-center justify-content-center mb-3 mb-md-0">
                                        <?php if ($haber->getFrontpageImage()): ?>
                                            <img src="<?php echo htmlspecialchars($haber->getFrontpageImage()); ?>"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height: 300px; object-fit: cover;"
                                                alt="<?php echo htmlspecialchars($haber->getTitle()); ?>">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center" style="height: 300px; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px;">
                                                <i class="fas fa-newspaper text-white" style="font-size: 4rem; opacity: 0.8;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column justify-content-center">
                                        <p class="text-body-secondary mb-4" style="line-height: 1.7;">
                                            <?php echo nl2br(htmlspecialchars($haber->getDetails())); ?>
                                        </p>
                                        <div>
                                            <a class="btn btn-primary btn-lg rounded-pill"
                                                href="<?php echo BASE_URL; ?>index.php?url=user/haberler&id=<?php echo $haber->getId(); ?>"
                                                role="button">
                                                <i class="fas fa-arrow-right me-2"></i>Detayları Görüntüle
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active p-4">
                        <div class="container-fluid text-center py-5">
                            <h2 class="fw-bold text-body-secondary mb-3">Hoş Geldiniz</h2>
                            <p class="text-body-secondary">Yakında güncel haberlerimizle sizlerle olacağız.</p>
                        </div>
                    </div>
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
    </div>

    <!-- Important Documents Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Önemli Belgeler</h2>
                    <p class="section-subtitle">Mesleki ve teknik eğitimde kalite ve standartları belirleyen temel düzenlemeler</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card document-card h-100 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="document-icon me-3">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1 fw-bold">SMM Çalışma Yönergesi</h4>
                                    <span class="badge bg-primary">Yönerge</span>
                                </div>
                            </div>
                            <p class="card-text text-body-secondary mb-4">
                                Sektörel Mükemmeliyet Merkezlerinin kuruluşundan işleyişine kadar birçok kritik düzenlemeyi
                                içeren önemli yönerge. Mesleki ve teknik eğitimde kaliteyi artırmayı ve sektörlerle güçlü
                                entegrasyon sağlamayı hedefliyor.
                            </p>
                            <a class="btn btn-primary rounded-pill" target="_blank"
                                href="https://mevzuat.meb.gov.tr/dosyalar/2208.pdf" role="button">
                                <i class="fas fa-download me-2"></i>Yönergeyi İndir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card document-card h-100 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="document-icon me-3" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1 fw-bold">Politika Belgesi</h4>
                                    <span class="badge bg-success">Strateji</span>
                                </div>
                            </div>
                            <p class="card-text text-body-secondary mb-4">
                                Mesleki ve Teknik Eğitimin kalitesinin artırılmasına yönelik stratejik hedefler ve uygulanacak
                                faaliyetleri içeren kapsamlı politika belgesi.
                            </p>
                            <a class="btn btn-success rounded-pill" target="_blank"
                                href="https://mtegm.meb.gov.tr/meb_iys_dosyalar/2024_09/18170207_16_09_2024_mtgmpolitikabelgesi.pdf" role="button">
                                <i class="fas fa-download me-2"></i>Belgeyi İndir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="hakkimizda" class="section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Hakkımızda</h2>
                    <p class="section-subtitle">Mesleki ve Teknik Eğitimde Mükemmelliğin Adresi</p>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="fw-bold mb-3">Sektörel Mükemmeliyet Merkezleri</h3>
                    <p class="mb-3" style="line-height: 1.7;">
                        Milli Eğitim Bakanlığı Mesleki ve Teknik Eğitim Genel Müdürlüğü bünyesinde faaliyet gösteren
                        Sektörel Mükemmeliyet Merkezleri, mesleki ve teknik eğitimde kaliteyi artırmak ve sektör
                        ihtiyaçlarına uygun nitelikli iş gücü yetiştirmek amacıyla kurulmuştur.
                    </p>
                    <p class="mb-4" style="line-height: 1.7;">
                        Modern eğitim teknolojileri ve sektörle güçlü işbirliği ile öğrencilerimizi geleceğe hazırlıyoruz.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">Nitelikli Eğitim</span>
                        <span class="badge bg-success">Sektör İşbirliği</span>
                        <span class="badge bg-info">Modern Teknoloji</span>
                        <span class="badge bg-warning">İnovatif Yaklaşım</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">15</div>
                                <small class="text-body-secondary fw-semibold">SMM Merkezi</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">25</div>
                                <small class="text-body-secondary fw-semibold">Pilot Mesleki Alan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">3000</div>
                                <small class="text-body-secondary fw-semibold">Öğretmen İşbaşı Eğitimi</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">22</div>
                                <small class="text-body-secondary fw-semibold">Milyon € Proje Bütçesi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer included by unified layout -->
</div>
<!-- ./wrapper -->

<?php
// Load footer and scripts components
include __DIR__ . '/../components/footer.php';
include __DIR__ . '/../components/scripts.php';
?>