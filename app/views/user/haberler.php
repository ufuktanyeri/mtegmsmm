<?php
/**
 * News Detail Page - Modern Bootstrap 5.3.8 Design
 * Matching the list page design with minimal custom CSS
 */

// Page configuration for public layout
$title = $news ? $news->getTitle() : 'Haber Detayı';
$description = $news ? substr(strip_tags($news->getDetails()), 0, 160) : 'Haberler ve duyurular';
$pageTitle = $title;
$pageDescription = $description;

// Minimal custom styles
$additionalCss = '
    /* Hero gradient - matching list page */
    .news-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin-top: 76px;
    }

    /* Gallery hover effect */
    .gallery-item img {
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    /* Share button hover */
    .share-btn {
        transition: all 0.3s ease;
    }

    .share-btn:hover {
        transform: translateY(-2px);
    }

    /* Reading progress bar */
    .reading-progress {
        position: fixed;
        top: 76px;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        z-index: 1000;
        transition: width 0.2s ease;
    }
';

// JavaScript for news detail page
$additionalJs = '
// Reading progress indicator
window.addEventListener("scroll", function() {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById("readingProgress").style.width = scrolled + "%";
});

// Share functions
function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, "_blank");
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank");
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}`, "_blank");
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Show success message
        const btn = event.target.closest("button");
        const originalText = btn.innerHTML;
        btn.innerHTML = "<i class=\"fas fa-check me-2\"></i>Kopyalandı!";
        btn.classList.remove("btn-outline-primary");
        btn.classList.add("btn-success");

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove("btn-success");
            btn.classList.add("btn-outline-primary");
        }, 2000);
    });
}

// Gallery modal image click
document.querySelectorAll("[data-bs-slide-to]").forEach(img => {
    img.addEventListener("click", function() {
        const slideIndex = this.getAttribute("data-bs-slide-to");
        const carousel = document.querySelector("#galleryCarousel");
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);
        bsCarousel.to(parseInt(slideIndex));
    });
});
';
?>

    <!-- Reading Progress Bar -->
    <div class="reading-progress" id="readingProgress"></div>

    <?php if ($news): ?>
    <!-- Hero Section -->
    <section class="news-hero text-white py-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 bg-white bg-opacity-10 p-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="<?php echo BASE_URL; ?>" class="text-white text-decoration-none">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/haberlist" class="text-white text-decoration-none">
                            Haberler
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">
                        <?php echo mb_substr(htmlspecialchars($news->getTitle()), 0, 30) . '...'; ?>
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                <?php echo htmlspecialchars($news->getTitle()); ?>
            </h1>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="d-flex align-items-center">
                    <i class="far fa-calendar me-2"></i>
                    <?php echo date('d.m.Y', strtotime($news->getCreatedDate())); ?>
                </span>
                <span class="d-flex align-items-center">
                    <i class="far fa-clock me-2"></i>
                    <?php
                    $wordCount = str_word_count(strip_tags($news->getDetails()));
                    $readTime = ceil($wordCount / 200);
                    echo $readTime . ' dk okuma';
                    ?>
                </span>
                <?php if ($news->getHeadline()): ?>
                    <span class="badge bg-white text-primary px-3 py-2">
                        <i class="fas fa-star me-1"></i>Manşet
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <article class="bg-white rounded-3 shadow-sm p-4 p-lg-5 mb-4">

                    <!-- Featured Image -->
                    <?php if ($news->getFrontpageImage()): ?>
                        <figure class="figure w-100 mb-4">
                            <img src="<?php echo htmlspecialchars($news->getFrontpageImage()); ?>"
                                 class="figure-img img-fluid rounded-3 w-100"
                                 alt="<?php echo htmlspecialchars($news->getTitle()); ?>">
                            <figcaption class="figure-caption text-center mt-2">
                                <?php echo htmlspecialchars($news->getTitle()); ?>
                            </figcaption>
                        </figure>
                    <?php endif; ?>

                    <!-- Article Text -->
                    <div class="article-content fs-5 lh-lg">
                        <?php echo $news->getDetails(); ?>
                    </div>

                    <!-- Tags -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="fas fa-tag me-1"></i>Eğitim
                            </span>
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="fas fa-tag me-1"></i>Teknoloji
                            </span>
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="fas fa-tag me-1"></i>Yenilik
                            </span>
                        </div>
                    </div>
                </article>

                <!-- Gallery Section -->
                <?php if ($gallery && !empty($gallery)): ?>
                    <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-images text-primary me-2"></i>
                            Fotoğraf Galerisi
                        </h4>
                        <div class="row g-3">
                            <?php foreach ($gallery as $index => $galleryItem): ?>
                                <div class="col-md-4 col-6">
                                    <div class="gallery-item position-relative overflow-hidden rounded-3 ratio ratio-1x1">
                                        <img src="<?php echo BASE_URL; ?>uploads/news/<?php echo htmlspecialchars($galleryItem->getPhoto()); ?>"
                                             class="object-fit-cover cursor-pointer"
                                             alt="Galeri Görseli <?php echo $index + 1; ?>"
                                             data-bs-toggle="modal"
                                             data-bs-target="#galleryModal"
                                             data-bs-slide-to="<?php echo $index; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Share Buttons -->
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4 sticky-lg-top" style="top: 100px;">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-share-alt text-primary me-2"></i>
                        Paylaş
                    </h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary share-btn" onclick="shareOnTwitter()">
                            <i class="fab fa-twitter me-2"></i>Twitter'da Paylaş
                        </button>
                        <button class="btn btn-outline-primary share-btn" onclick="shareOnFacebook()">
                            <i class="fab fa-facebook-f me-2"></i>Facebook'ta Paylaş
                        </button>
                        <button class="btn btn-outline-primary share-btn" onclick="shareOnLinkedIn()">
                            <i class="fab fa-linkedin-in me-2"></i>LinkedIn'de Paylaş
                        </button>
                        <button class="btn btn-outline-primary share-btn" onclick="copyLink()">
                            <i class="fas fa-link me-2"></i>Linki Kopyala
                        </button>
                    </div>
                </div>

                <!-- Related News -->
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-newspaper text-primary me-2"></i>
                        İlgili Haberler
                    </h5>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-3">
                            <h6 class="mb-1">Eğitimde Dijital Dönüşüm Hızlanıyor</h6>
                            <small class="text-muted">2 gün önce</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-3">
                            <h6 class="mb-1">Yeni Müfredat Çalışmaları Başladı</h6>
                            <small class="text-muted">5 gün önce</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-3">
                            <h6 class="mb-1">Öğretmen Eğitimi Programları Güncellendi</h6>
                            <small class="text-muted">1 hafta önce</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <?php if ($gallery && !empty($gallery)): ?>
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner">
                            <?php foreach ($gallery as $index => $galleryItem): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo BASE_URL; ?>uploads/news/<?php echo htmlspecialchars($galleryItem->getPhoto()); ?>"
                                         class="d-block w-100 rounded-3"
                                         alt="Galeri Görseli <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($gallery) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Önceki</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Sonraki</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-newspaper text-muted" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">Haber Bulunamadı</h3>
                        <p class="text-muted mb-4">
                            Aradığınız haber mevcut değil veya kaldırılmış olabilir.
                        </p>
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/haberlist"
                           class="btn btn-primary btn-lg rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>
                            Tüm Haberlere Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>