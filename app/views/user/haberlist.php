<?php
/**
 * News List Page - Modern Bootstrap 5.3.8 Design
 * Clean, minimal design with Bootstrap utilities
 */

// Page configuration
$title = 'Haberler';
$description = 'Sektörel Mükemmeliyet Merkezleri haberleri ve güncellemeleri';

// Minimal custom styles (only for elements Bootstrap can't handle)
$additionalCss = '
    /* Hero gradient - cannot be done with utilities */
    .news-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin-top: 76px;
    }

    /* Card hover effect */
    .news-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    /* Image zoom on hover */
    .news-card .card-img-top {
        transition: transform 0.3s ease;
    }

    .news-card:hover .card-img-top {
        transform: scale(1.05);
    }

    /* Text truncation */
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
';

// Set page variables for public layout
$pageTitle = $title;
$pageDescription = $description;

// Page-specific JavaScript
$additionalJs = '
document.addEventListener("DOMContentLoaded", function() {
    // Search functionality
    const searchInput = document.getElementById("newsSearch");
    const newsItems = document.querySelectorAll(".news-item");

    if (searchInput) {
        searchInput.addEventListener("input", function() {
            const searchTerm = this.value.toLowerCase();

            newsItems.forEach(item => {
                const title = item.querySelector(".card-title").textContent.toLowerCase();
                const text = item.querySelector(".card-text").textContent.toLowerCase();

                if (title.includes(searchTerm) || text.includes(searchTerm)) {
                    item.style.display = "";
                } else {
                    item.style.display = "none";
                }
            });
        });
    }

    // Category filter
    const categoryButtons = document.querySelectorAll("[data-category]");

    categoryButtons.forEach(button => {
        button.addEventListener("click", function() {
            // Update active state
            categoryButtons.forEach(btn => btn.classList.remove("active", "btn-primary"));
            categoryButtons.forEach(btn => btn.classList.add("btn-outline-primary"));

            this.classList.remove("btn-outline-primary");
            this.classList.add("active", "btn-primary");

            // Filter items (would need backend integration for real filtering)
            const category = this.dataset.category;
            newsItems.forEach(item => {
                item.style.display = category === "all" ? "" : "";
            });
        });
    });

    // Sort functionality
    const sortSelect = document.getElementById("newsSort");

    if (sortSelect) {
        sortSelect.addEventListener("change", function() {
            // This would typically trigger a page reload with sort parameter
            console.log("Sorting by:", this.value);
        });
    }

    // Load more button
    const loadMoreBtn = document.getElementById("loadMore");

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", function() {
            // This would typically load more items via AJAX
            this.innerHTML = \'<span class="spinner-border spinner-border-sm me-2"></span>Yükleniyor...\';

            setTimeout(() => {
                this.innerHTML = \'<i class="fas fa-plus-circle me-2"></i>Daha Fazla Haber Yükle\';
            }, 1500);
        });
    }
});
';
?>

<!-- Main Content Start -->

    <!-- Hero Section with Bootstrap Utilities -->
    <section class="news-hero text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-newspaper me-3"></i>Haberler
                    </h1>
                    <p class="lead mb-0 opacity-90">
                        Sektörel Mükemmeliyet Merkezlerinden son haberler ve güncellemeler
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="mt-3 mt-lg-0">
                        <span class="badge bg-white text-primary px-3 py-2 fs-6">
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo date('d.m.Y'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">

        <!-- Filter Bar -->
        <div class="row g-3 mb-5">
            <div class="col-md-8">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0 ps-0"
                           placeholder="Haberlerde ara..."
                           id="newsSearch">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-lg" id="newsSort">
                    <option value="newest">En Yeni</option>
                    <option value="oldest">En Eski</option>
                    <option value="popular">Popüler</option>
                </select>
            </div>
        </div>

        <!-- Category Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button class="btn btn-primary rounded-pill px-4 active" data-category="all">
                Tümü
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" data-category="duyuru">
                <i class="fas fa-bullhorn me-2"></i>Duyurular
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" data-category="etkinlik">
                <i class="fas fa-calendar-alt me-2"></i>Etkinlikler
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" data-category="proje">
                <i class="fas fa-project-diagram me-2"></i>Projeler
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" data-category="basari">
                <i class="fas fa-trophy me-2"></i>Başarılar
            </button>
        </div>

        <!-- News Grid -->
        <div class="row g-4" id="newsGrid">
            <?php if (isset($newsList) && count($newsList) > 0): ?>
                <?php foreach ($newsList as $index => $news): ?>
                    <div class="col-lg-4 col-md-6 news-item" data-category="all">
                        <article class="card h-100 shadow-sm border-0 news-card">

                            <!-- Card Image -->
                            <?php if ($news->getFrontpageImage()): ?>
                                <div class="overflow-hidden" style="height: 220px;">
                                    <img src="<?php echo htmlspecialchars($news->getFrontpageImage()); ?>"
                                         class="card-img-top h-100 object-fit-cover"
                                         alt="<?php echo htmlspecialchars($news->getTitle()); ?>">
                                </div>
                            <?php else: ?>
                                <!-- Placeholder image with gradient -->
                                <div class="bg-gradient text-white d-flex align-items-center justify-content-center"
                                     style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-newspaper fa-4x opacity-50"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Card Body -->
                            <div class="card-body d-flex flex-column">
                                <!-- Date and Category -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        <?php
                                        $date = $news->getCreatedDate();
                                        echo date('d.m.Y', strtotime($date));
                                        ?>
                                    </small>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">
                                        <?php echo $news->getHeadline() ? 'Manşet' : 'Haber'; ?>
                                    </span>
                                </div>

                                <!-- Title -->
                                <h5 class="card-title fw-bold mb-3">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=user/haberler&id=<?php echo $news->getId(); ?>"
                                       class="text-dark text-decoration-none stretched-link">
                                        <?php echo htmlspecialchars($news->getTitle()); ?>
                                    </a>
                                </h5>

                                <!-- Excerpt -->
                                <p class="card-text text-muted text-truncate-3 flex-grow-1">
                                    <?php
                                    $details = strip_tags($news->getDetails());
                                    echo htmlspecialchars(mb_substr($details, 0, 150) . '...');
                                    ?>
                                </p>

                                <!-- Footer -->
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo BASE_URL; ?>wwwroot/img/user-avatar.svg"
                                             alt="Yazar"
                                             class="rounded-circle me-2"
                                             width="24"
                                             height="24">
                                        <small class="text-muted">Admin</small>
                                    </div>
                                    <a href="#" class="text-primary text-decoration-none small fw-semibold">
                                        Devamını Oku
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-newspaper text-muted" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">Henüz Haber Bulunmuyor</h3>
                        <p class="text-muted mb-4">
                            Yakında güncel haberlerimizle sizlerle olacağız.
                        </p>
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More Button (instead of pagination) -->
        <?php if (isset($newsList) && count($newsList) >= 9): ?>
            <div class="text-center mt-5">
                <button class="btn btn-outline-primary btn-lg rounded-pill px-5" id="loadMore">
                    <i class="fas fa-plus-circle me-2"></i>
                    Daha Fazla Haber Yükle
                </button>
            </div>
        <?php endif; ?>

        <!-- Alternative: Simple Pagination -->
        <?php
        $totalPages = isset($totalNews) && isset($perPage) ? ceil($totalNews / $perPage) : 1;
        $currentPage = isset($currentPage) ? $currentPage : 1;
        if ($totalPages > 1):
        ?>
            <nav aria-label="Sayfa navigasyonu" class="mt-5">
                <ul class="pagination pagination-lg justify-content-center">
                    <!-- Previous -->
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-start-pill"
                           href="<?php echo $currentPage > 1 ? BASE_URL . 'index.php?url=user/haberlist&page=' . ($currentPage - 1) : '#'; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= min(5, $totalPages); $i++): ?>
                        <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="<?php echo BASE_URL; ?>index.php?url=user/haberlist&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-end-pill"
                           href="<?php echo $currentPage < $totalPages ? BASE_URL . 'index.php?url=user/haberlist&page=' . ($currentPage + 1) : '#'; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Stats Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                            <i class="fas fa-newspaper text-primary fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?php echo isset($totalNews) ? $totalNews : '0'; ?></h3>
                        <p class="text-muted mb-0">Toplam Haber</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 mb-3">
                            <i class="fas fa-eye text-success fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0">15K+</h3>
                        <p class="text-muted mb-0">Görüntülenme</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 mb-3">
                            <i class="fas fa-share-alt text-warning fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0">2.3K</h3>
                        <p class="text-muted mb-0">Paylaşım</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 mb-3">
                            <i class="fas fa-users text-info fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0">500+</h3>
                        <p class="text-muted mb-0">Takipçi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Main Content End -->