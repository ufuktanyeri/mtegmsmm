<?php
$title = $news ? $news->getTitle() : 'Haber Detayı';
$description = $news ? substr(strip_tags($news->getContent()), 0, 160) : 'Haberler ve duyurular';
$bodyClass = 'hold-transition layout-fixed news-detail-page';

header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");

// Load header component
include __DIR__ . '/../components/header.php';
?>

<div class="wrapper">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

<style>
    /* Page specific styles for news detail */
    .news-content {
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .news-meta {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid var(--meb-primary);
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .gallery-item img {
        transition: transform 0.3s ease;
    }
</style>

<!-- Main Content -->
<div class="container py-4" style="margin-top: 100px;">
    <?php if ($news): ?>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- News Article -->
                <article class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h1 class="display-6 fw-bold text-dark mb-3">
                            <?php echo htmlspecialchars($news->getTitle()); ?>
                        </h1>

                        <div class="news-meta mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <small class="text-body-secondary">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <?php echo htmlspecialchars($news->getCreatedDate()); ?>
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($news->getHeadline()); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="news-content">
                            <?php echo $news->getContent(); ?>
                        </div>
                    </div>
                </article>

                <!-- Gallery Section -->
                <?php if ($gallery && !empty($gallery)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-images me-2"></i>Galeri
                            </h4>
                            <div class="row g-3">
                                <?php foreach ($gallery as $galleryItem): ?>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="gallery-item">
                                            <img src="<?php echo BASE_URL; ?>uploads/news/<?php echo htmlspecialchars($galleryItem->getPhoto()); ?>"
                                                class="img-fluid rounded"
                                                alt="Galeri Görseli"
                                                style="width: 100%; height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-exclamation-triangle text-warning display-1 mb-3"></i>
                        <h3 class="text-body-secondary">Haber Bulunamadı</h3>
                        <p class="text-body-secondary">Aradığınız haber mevcut değil veya kaldırılmış olabilir.</p>
                        <a href="<?php echo BASE_URL; ?>index.php?url=user/haberlist" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Haberlere Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</div>

<?php include __DIR__ . '/../components/scripts.php'; ?>