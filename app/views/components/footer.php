<?php
/**
 * Reusable Footer Component
 * Modern footer with links and copyright information
 */
?>

<style>
    /* Minimal footer styles - using Bootstrap utilities where possible */
    .footer {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        position: relative;
    }

    [data-bs-theme="dark"] .footer {
        background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--meb-gradient);
    }

    .footer .btn-link {
        transition: all 0.3s ease;
    }

    .footer .btn-link:hover {
        transform: translateY(-1px);
    }

    .social-link {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .footer-logo {
        filter: brightness(0) invert(1);
    }
</style>

<!-- Footer with Bootstrap Utilities -->
<footer class="footer text-white py-5 mt-5 overflow-hidden">
    <div class="container">
        <div class="row">
            <!-- Left Column - Info -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo BASE_URL; ?>wwwroot/img/MEB_Logo.svg" alt="MEB Logo" class="footer-logo me-3 opacity-75" height="50">
                    <div>
                        <h5 class="mb-1 fw-bold">MTEGM</h5>
                        <p class="mb-0 small">Sektörel Mükemmeliyet Merkezleri</p>
                    </div>
                </div>
                <p class="text-light opacity-75 small">
                    Mesleki ve teknik eğitimde kaliteyi artırmak ve sektör ihtiyaçlarına uygun 
                    nitelikli iş gücü yetiştirmek amacıyla kurulmuş modern eğitim merkezleri.
                </p>
                <div class="social-links mt-3">
                    <a href="https://twitter.com/mebgovtr" target="_blank" title="Twitter" class="social-link d-inline-flex align-items-center justify-content-center rounded-circle text-white text-opacity-75 me-1">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/mebgovtr" target="_blank" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/c/MilliEğitimBakanlığı" target="_blank" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/mebgovtr" target="_blank" title="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>

            <!-- Middle Column - Quick Links -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h5 class="mb-3 fw-bold">Hızlı Erişim</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/main" class="btn-link text-white text-opacity-75 text-decoration-none fw-medium">
                            <i class="fas fa-home me-2 small"></i>Ana Sayfa
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/haberlist" class="btn-link">
                            <i class="fas fa-newspaper me-2 small"></i>Haberler
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>index.php?url=home/smmnetwork" class="btn-link">
                            <i class="fas fa-map-marked-alt me-2 small"></i>SMM Haritası
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/login" class="btn-link">
                            <i class="fas fa-sign-in-alt me-2 small"></i>SMM Portal
                        </a>
                    </li>
                    <?php if (isset($_SESSION['username'])): ?>
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>index.php?url=help/index" class="btn-link">
                            <i class="fas fa-question-circle me-2 small"></i>Yardım
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Right Column - Contact & External Links -->
            <div class="col-lg-4">
                <h5 class="mb-3 fw-bold">Kurumsal Bağlantılar</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="https://meb.gov.tr" target="_blank" class="btn-link">
                            <i class="fas fa-external-link-alt me-2 small"></i>Milli Eğitim Bakanlığı
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://mtegm.meb.gov.tr" target="_blank" class="btn-link">
                            <i class="fas fa-external-link-alt me-2 small"></i>MTEGM Ana Sayfası
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://mevzuat.meb.gov.tr" target="_blank" class="btn-link">
                            <i class="fas fa-book me-2 small"></i>Mevzuat
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://www.turkiye.gov.tr" target="_blank" class="btn-link">
                            <i class="fas fa-flag me-2 small"></i>e-Devlet Kapısı
                        </a>
                    </li>
                </ul>

                <!-- Contact Info -->
                <div class="mt-4">
                    <h6 class="mb-2 fw-semibold">İletişim</h6>
                    <p class="mb-1 small opacity-75">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Emniyet Mahallesi, Milas Sk. No:21, 06560 Yenimahalle/Ankara
                    </p>
                    <p class="mb-0 small opacity-75">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+903124133000" class="btn-link">+90 312 413 30 00</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-divider"></div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 small opacity-75">
                    &copy; <?php echo date('Y'); ?> T.C. Milli Eğitim Bakanlığı. Tüm hakları saklıdır.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                    <a href="#" class="btn-link me-3 small">Gizlilik Politikası</a>
                    <a href="#" class="btn-link me-3 small">Kullanım Şartları</a>
                    <a href="#" class="btn-link small">Erişebilirlik</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary rounded-circle position-fixed d-none shadow-lg"
        style="bottom: 20px; right: 20px; z-index: 1050; width: 50px; height: 50px;">
    <i class="fas fa-arrow-up"></i>
</button>


<script>
// Back to top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('d-none');
            } else {
                backToTopBtn.classList.add('d-none');
            }
        });

        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>