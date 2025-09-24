<?php
/**
 * Hero Section Component
 * Standardized hero section for all pages
 *
 * Usage:
 * <?php
 * $heroConfig = [
 *     'title' => 'Page Title',
 *     'subtitle' => 'Optional subtitle',
 *     'icon' => 'fas fa-icon-name',
 *     'gradient' => true,
 *     'type' => 'section' // or 'card'
 * ];
 * include __DIR__ . '/components/hero.php';
 * ?>
 */

// Default values
$title = $heroConfig['title'] ?? 'Başlık';
$subtitle = $heroConfig['subtitle'] ?? '';
$icon = $heroConfig['icon'] ?? '';
$gradient = $heroConfig['gradient'] ?? true;
$type = $heroConfig['type'] ?? 'section';
$additionalClasses = $heroConfig['classes'] ?? '';

// Gradient style - MEB colors
$gradientStyle = $gradient ? 'background: linear-gradient(135deg, var(--bs-primary, #003C7D) 0%, var(--meb-secondary, #0056B3) 100%);' : '';

// Add styles for dark mode support
?>
<style>
    [data-bs-theme="dark"] .hero-section {
        background: linear-gradient(135deg, #002855 0%, #003d7a 100%) !important;
    }

    [data-bs-theme="dark"] .card.hero-card {
        background: linear-gradient(135deg, #002855 0%, #003d7a 100%) !important;
    }
</style>
<?php

if ($type === 'section'): ?>
    <!-- Hero Section -->
    <section class="hero-section text-center text-white <?php echo $additionalClasses; ?>" style="<?php echo $gradientStyle; ?> padding: 4rem 0 3rem; margin-top: 100px;">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">
                <?php if($icon): ?><i class="<?php echo htmlspecialchars($icon); ?> me-2"></i><?php endif; ?>
                <?php
                // Allow safe HTML tags like <br> in title
                echo strip_tags($title, '<br><strong><em><span>');
                ?>
            </h1>
            <?php if($subtitle): ?>
                <p class="lead opacity-90">
                    <?php
                    // Allow safe HTML tags in subtitle
                    echo strip_tags($subtitle, '<br><strong><em><span>');
                    ?>
                </p>
            <?php endif; ?>
        </div>
    </section>
<?php elseif ($type === 'card'): ?>
    <!-- Hero Card -->
    <div class="container py-4" style="margin-top: 100px;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm hero-card <?php echo $additionalClasses; ?>" style="<?php echo $gradientStyle; ?>">
                    <div class="card-body text-white p-4">
                        <h1 class="h3 mb-3 text-white text-center">
                            <?php if($icon): ?><i class="<?php echo htmlspecialchars($icon); ?> me-2"></i><?php endif; ?>
                            <?php
                            // Allow safe HTML tags like <br> in title
                            echo strip_tags($title, '<br><strong><em><span>');
                            ?>
                        </h1>
                        <?php if($subtitle): ?>
                            <h2 class="h4 text-white text-center mb-0">
                                <?php
                                // Allow safe HTML tags in subtitle
                                echo strip_tags($subtitle, '<br><strong><em><span>');
                                ?>
                            </h2>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>