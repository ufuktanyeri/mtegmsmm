<?php
// Page configuration
$title = 'Haberler';
$description = 'Sektörel Mükemmeliyet Merkezleri haberleri ve güncellemeleri';
$bodyClass = 'hold-transition layout-fixed news-page';

// Additional page-specific styles
$additionalCss = '
    .news-hero {
        background: var(--meb-gradient);
        color: white;
        padding: 4rem 0 3rem;
        margin-top: 100px;
    }

    /* View Toggle Buttons */
    .view-toggle {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .view-toggle .btn {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .view-toggle .btn.active {
        background: var(--meb-gradient);
        color: white;
        transform: scale(1.05);
    }

    /* Carousel Styles */
    .carousel-container {
        display: none;
        position: relative;
        margin-bottom: 3rem;
        min-height: 450px;
    }

    .carousel-container.active {
        display: block;
    }

    .swiper {
        width: 100%;
        padding: 20px 0 50px;
    }

    .swiper-slide {
        height: auto;
    }

    .swiper-pagination-bullet {
        background: var(--meb-primary);
        opacity: 0.3;
    }

    .swiper-pagination-bullet-active {
        opacity: 1;
        background: var(--meb-primary);
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: var(--meb-primary);
        background: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 18px;
        font-weight: bold;
    }

    /* Grid View */
    .grid-container {
        display: block;
    }

    .grid-container.hidden {
        display: none;
    }

    .news-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .news-card-img {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-card-img {
        transform: scale(1.05);
    }

    .news-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .news-excerpt {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.6;
    }

    .pagination .page-link {
        border-radius: 8px;
        margin: 0 2px;
        border: 1px solid #e9ecef;
        color: var(--meb-primary);
        font-weight: 500;
    }

    .pagination .page-item.active .page-link {
        background: var(--meb-gradient);
        border-color: var(--meb-primary);
    }

    .pagination .page-link:hover {
        background-color: rgba(79, 70, 229, 0.1);
        border-color: var(--meb-primary);
    }

    /* Search and filter section */
    .filter-section {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    }

    .search-input {
        border-radius: 25px;
        border: 1px solid #e9ecef;
        padding: 0.75rem 1.25rem;
    }

    .search-input:focus {
        border-color: var(--meb-primary);
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
';

// Load header component
include __DIR__ . '/../components/header.php';
?>

<div class="wrapper">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <?php
    // Hero section configuration
    $heroConfig = [
        'title' => 'Haberler',
        'subtitle' => 'Sektörel Mükemmeliyet Merkezlerinden son haberler ve güncellemeler',
        'icon' => 'fas fa-newspaper',
        'gradient' => true,
        'type' => 'section'
    ];
    include __DIR__ . '/../components/hero.php';
    ?>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Search and Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control search-input border-start-0"
                               placeholder="Haberler içinde ara..." id="newsSearch">
                    </div>
                </div>
                <div class="col-md-3 text-center mt-3 mt-md-0">
                    <div class="view-toggle">
                        <button type="button" class="btn btn-outline-primary active" id="gridViewBtn"
                                onclick="(function(){
                                    document.getElementById('gridView').style.display='block';
                                    document.getElementById('carouselView').style.display='none';
                                    document.getElementById('gridViewBtn').classList.add('active');
                                    document.getElementById('carouselViewBtn').classList.remove('active');
                                })()">
                            <i class="fas fa-th-large"></i> Izgara
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="carouselViewBtn"
                                onclick="(function(){
                                    document.getElementById('gridView').style.display='none';
                                    document.getElementById('carouselView').style.display='block';
                                    document.getElementById('gridViewBtn').classList.remove('active');
                                    document.getElementById('carouselViewBtn').classList.add('active');
                                    setTimeout(function(){
                                        if(typeof Swiper !== 'undefined' && !window.newsSwiper){
                                            window.newsSwiper = new Swiper('.newsSwiper', {
                                                slidesPerView: 1,
                                                spaceBetween: 20,
                                                pagination: { el: '.swiper-pagination', clickable: true },
                                                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                                                breakpoints: {
                                                    640: { slidesPerView: 2 },
                                                    1024: { slidesPerView: 3 }
                                                }
                                            });
                                        }
                                    }, 100);
                                })()">
                            <i class="fas fa-images"></i> Slayt
                        </button>
                    </div>
                </div>
                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                    <small class="text-muted">
                        <?php echo count($newsList ?? []); ?> haber bulundu
                    </small>
                </div>
            </div>
        </div>

        <!-- Carousel View -->
        <div class="carousel-container" id="carouselView">
            <?php if (isset($newsList) && count($newsList) > 0): ?>
                <div class="swiper newsSwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($newsList as $news): ?>
                            <div class="swiper-slide">
                                <article class="card news-card">
                                    <?php if ($news->getFrontpageImage()): ?>
                                        <div class="position-relative overflow-hidden">
                                            <img src="<?php echo htmlspecialchars($news->getFrontpageImage()); ?>"
                                                 class="card-img-top news-card-img"
                                                 alt="<?php echo htmlspecialchars($news->getTitle()); ?>">
                                            <div class="position-absolute top-0 start-0 m-3">
                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                    <i class="fas fa-newspaper me-1"></i>Haber
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-body p-4">
                                        <div class="news-meta mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <?php echo htmlspecialchars($news->getCreatedDate()); ?>
                                        </div>

                                        <h5 class="card-title fw-bold mb-3">
                                            <a href="<?php echo BASE_URL; ?>index.php?url=user/haberler&id=<?php echo $news->getId(); ?>"
                                               class="text-decoration-none text-dark stretched-link">
                                                <?php echo htmlspecialchars($news->getTitle()); ?>
                                            </a>
                                        </h5>

                                        <p class="card-text news-excerpt text-muted">
                                            <?php
                                            $details = strip_tags($news->getDetails());
                                            echo htmlspecialchars(mb_substr($details, 0, 150) . (mb_strlen($details) > 150 ? '...' : ''));
                                            ?>
                                        </p>

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <span class="btn btn-outline-primary btn-sm rounded-pill">
                                                Devamını Oku <i class="fas fa-arrow-right ms-1"></i>
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-tag me-1"></i>
                                                <?php echo $news->getHeadline() ? 'Manşet' : 'Haber'; ?>
                                            </small>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grid View -->
        <div class="grid-container" id="gridView">
            <div class="row g-4" id="newsContainer">
            <?php if (isset($newsList) && count($newsList) > 0): ?>
                <?php foreach ($newsList as $news): ?>
                    <div class="col-lg-4 col-md-6 news-item">
                        <article class="card news-card">
                            <?php if ($news->getFrontpageImage()): ?>
                                <div class="position-relative overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($news->getFrontpageImage()); ?>"
                                         class="card-img-top news-card-img"
                                         alt="<?php echo htmlspecialchars($news->getTitle()); ?>">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <i class="fas fa-newspaper me-1"></i>Haber
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="card-body p-4">
                                <div class="news-meta mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <?php echo htmlspecialchars($news->getCreatedDate()); ?>
                                </div>

                                <h5 class="card-title fw-bold mb-3">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=user/haberler&id=<?php echo $news->getId(); ?>"
                                       class="text-decoration-none text-dark stretched-link">
                                        <?php echo htmlspecialchars($news->getTitle()); ?>
                                    </a>
                                </h5>

                                <p class="card-text news-excerpt text-muted">
                                    <?php
                                    $details = strip_tags($news->getDetails());
                                    echo htmlspecialchars(mb_substr($details, 0, 150) . (mb_strlen($details) > 150 ? '...' : ''));
                                    ?>
                                </p>

                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <span class="btn btn-outline-primary btn-sm rounded-pill">
                                        Devamını Oku <i class="fas fa-arrow-right ms-1"></i>
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo $news->getHeadline() ? 'Manşet' : 'Haber'; ?>
                                    </small>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Henüz haber bulunmuyor</h4>
                        <p class="text-muted">Yakında güncel haberlerimizle sizlerle olacağız.</p>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php
        $totalPages = isset($totalNews) && isset($perPage) ? ceil($totalNews / $perPage) : 1;
        $currentPage = isset($currentPage) ? $currentPage : 1;
        ?>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Haberler sayfaları" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Previous -->
                    <li class="page-item<?php echo $currentPage <= 1 ? ' disabled' : ''; ?>">
                        <a class="page-link"
                           href="<?php echo $currentPage > 1 ? BASE_URL . 'index.php?url=user/haberlist&page=' . ($currentPage - 1) : '#'; ?>"
                           aria-label="Önceki sayfa">
                            <i class="fas fa-chevron-left me-1"></i>Önceki
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    ?>

                    <?php if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo BASE_URL; ?>index.php?url=user/haberlist&page=1">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item<?php echo $i == $currentPage ? ' active' : ''; ?>">
                            <a class="page-link" href="<?php echo BASE_URL; ?>index.php?url=user/haberlist&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo BASE_URL; ?>index.php?url=user/haberlist&page=<?php echo $totalPages; ?>">
                                <?php echo $totalPages; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next -->
                    <li class="page-item<?php echo $currentPage >= $totalPages ? ' disabled' : ''; ?>">
                        <a class="page-link"
                           href="<?php echo $currentPage < $totalPages ? BASE_URL . 'index.php?url=user/haberlist&page=' . ($currentPage + 1) : '#'; ?>"
                           aria-label="Sonraki sayfa">
                            Sonraki<i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Page Info -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    Sayfa <?php echo $currentPage; ?> / <?php echo $totalPages; ?>
                    (Toplam <?php echo $totalNews ?? 0; ?> haber)
                </small>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer included by unified layout -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</div>

<?php
// Additional JavaScript for search functionality and Swiper
$additionalJs = '
    // Initialize Swiper when page loads
    let newsSwiper = null;

    function initSwiper() {
        if (newsSwiper) {
            newsSwiper.destroy();
        }

        newsSwiper = new Swiper(".newsSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });
    }

    // View switcher function
    function switchView(view) {
        const gridView = document.getElementById("gridView");
        const carouselView = document.getElementById("carouselView");
        const gridBtn = document.getElementById("gridViewBtn");
        const carouselBtn = document.getElementById("carouselViewBtn");

        if (view === "carousel") {
            gridView.classList.add("hidden");
            carouselView.classList.add("active");
            gridBtn.classList.remove("active");
            carouselBtn.classList.add("active");

            // Initialize Swiper when switching to carousel
            setTimeout(() => {
                initSwiper();
            }, 100);
        } else {
            gridView.classList.remove("hidden");
            carouselView.classList.remove("active");
            gridBtn.classList.add("active");
            carouselBtn.classList.remove("active");
        }
    }

    // Live search functionality
    document.getElementById("newsSearch").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        const newsItems = document.querySelectorAll(".news-item");
        const swiperSlides = document.querySelectorAll(".swiper-slide");

        newsItems.forEach(function(item) {
            const title = item.querySelector(".card-title a").textContent.toLowerCase();
            const excerpt = item.querySelector(".news-excerpt").textContent.toLowerCase();

            if (title.includes(filter) || excerpt.includes(filter)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });

        // Also filter carousel items
        swiperSlides.forEach(function(slide) {
            const titleEl = slide.querySelector(".card-title a");
            const excerptEl = slide.querySelector(".news-excerpt");

            if (titleEl && excerptEl) {
                const title = titleEl.textContent.toLowerCase();
                const excerpt = excerptEl.textContent.toLowerCase();

                if (title.includes(filter) || excerpt.includes(filter)) {
                    slide.style.display = "block";
                } else {
                    slide.style.display = "none";
                }
            }
        });

        // Update count
        const visibleItems = document.querySelectorAll(".news-item:not([style*=\"none\"])");
        const countElement = document.querySelector(".filter-section small");
        if (countElement) {
            countElement.textContent = visibleItems.length + " haber bulundu";
        }

        // Reinitialize Swiper if in carousel view
        if (document.getElementById("carouselView").classList.contains("active")) {
            setTimeout(() => {
                if (newsSwiper) {
                    newsSwiper.update();
                }
            }, 100);
        }
    });

    // Smooth scrolling for pagination
    document.querySelectorAll(".pagination .page-link").forEach(function(link) {
        link.addEventListener("click", function(e) {
            if (!this.parentNode.classList.contains("disabled") &&
                !this.parentNode.classList.contains("active") &&
                this.getAttribute("href") !== "#") {
                document.querySelector(".news-hero").scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
';

// Include scripts.php first
include __DIR__ . '/../components/scripts.php';
?>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Custom JavaScript for Haberlist -->
<script>
(function() {
    'use strict';

    // Debug log
    console.log('Haberlist script loading...');

    let newsSwiper = null;

    function initSwiper() {
        console.log('Initializing Swiper...');

        // Destroy existing instance
        if (newsSwiper) {
            newsSwiper.destroy(true, true);
            newsSwiper = null;
        }

        // Create new instance
        newsSwiper = new Swiper('.newsSwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: false, // Disable loop for testing
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });

        console.log('Swiper initialized:', newsSwiper);
    }

    function switchView(view) {
        console.log('Switching to view:', view);

        const gridView = document.getElementById('gridView');
        const carouselView = document.getElementById('carouselView');
        const gridBtn = document.getElementById('gridViewBtn');
        const carouselBtn = document.getElementById('carouselViewBtn');

        if (!gridView || !carouselView || !gridBtn || !carouselBtn) {
            console.error('Required elements not found!');
            return;
        }

        if (view === 'carousel') {
            // Hide grid, show carousel
            gridView.style.display = 'none';
            carouselView.style.display = 'block';
            carouselView.classList.add('active');

            // Update button states
            gridBtn.classList.remove('active');
            carouselBtn.classList.add('active');

            // Initialize Swiper after DOM update
            setTimeout(function() {
                initSwiper();
            }, 100);
        } else {
            // Show grid, hide carousel
            gridView.style.display = 'block';
            carouselView.style.display = 'none';
            carouselView.classList.remove('active');

            // Update button states
            gridBtn.classList.add('active');
            carouselBtn.classList.remove('active');

            // Destroy Swiper
            if (newsSwiper) {
                newsSwiper.destroy(true, true);
                newsSwiper = null;
            }
        }
    }

    // Wait for DOM
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up event listeners...');

        // Get buttons
        const gridBtn = document.getElementById('gridViewBtn');
        const carouselBtn = document.getElementById('carouselViewBtn');

        if (gridBtn && carouselBtn) {
            // Add click event listeners
            gridBtn.addEventListener('click', function(e) {
                e.preventDefault();
                switchView('grid');
            });

            carouselBtn.addEventListener('click', function(e) {
                e.preventDefault();
                switchView('carousel');
            });

            console.log('Event listeners attached');
        } else {
            console.error('Toggle buttons not found!');
        }

        // Search functionality
        const searchInput = document.getElementById('newsSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const newsItems = document.querySelectorAll('.news-item');

                newsItems.forEach(function(item) {
                    const title = item.querySelector('.card-title a');
                    const excerpt = item.querySelector('.news-excerpt');

                    if (title && excerpt) {
                        const titleText = title.textContent.toLowerCase();
                        const excerptText = excerpt.textContent.toLowerCase();

                        if (titleText.includes(filter) || excerptText.includes(filter)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });

                // Update count
                const visibleItems = document.querySelectorAll('.news-item:not([style*="display: none"])');
                const countElement = document.querySelector('.filter-section small');
                if (countElement) {
                    countElement.textContent = visibleItems.length + ' haber bulundu';
                }
            });
        }
    });

    // Make switchView globally accessible for debugging
    window.debugSwitchView = switchView;
    window.debugInitSwiper = initSwiper;
})();
</script>