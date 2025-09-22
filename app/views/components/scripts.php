<?php
/**
 * Enhanced Scripts Component with Template Management
 */

// Load TemplateService
require_once __DIR__ . '/../../services/TemplateService.php';
use App\Services\TemplateService;
?>

<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- AssetManager JS -->
<?php
if (class_exists('App\Helpers\AssetManager')) {
    echo \App\Helpers\AssetManager::renderJs(BASE_URL);
}
?>

<!-- Template Management System -->
<script>
<?php echo TemplateService::getReinitScript(); ?>
</script>

<!-- Page-specific JavaScript -->
<script>
// Additional page-specific initialization
document.addEventListener('DOMContentLoaded', function() {
    // Custom page logic here
    console.log('Template system initialized');

    // Debug info (remove in production)
    if (window.location.hostname === 'localhost') {
        console.log('Components initialized:', {
            dropdowns: document.querySelectorAll('[data-dropdown-initialized]').length,
            tooltips: document.querySelectorAll('[data-tooltip-initialized]').length,
            popovers: document.querySelectorAll('[data-popover-initialized]').length,
            forms: document.querySelectorAll('[data-form-initialized]').length,
            modals: document.querySelectorAll('[data-modal-initialized]').length
        });
    }
});

// Global error handler for debugging
window.addEventListener('error', function(e) {
    if (window.location.hostname === 'localhost') {
        console.error('JavaScript Error:', e.message, 'at', e.filename, ':', e.lineno);
    }
});

// AJAX global handlers for reinit
if (typeof jQuery !== 'undefined') {
    // Show loading indicator
    jQuery(document).ajaxStart(function() {
        // Optional: Add loading indicator
    });

    // Hide loading and reinit components
    jQuery(document).ajaxStop(function() {
        // Optional: Remove loading indicator
        if (window.TemplateManager) {
            window.TemplateManager.reinit();
        }
    });

    // Handle AJAX errors
    jQuery(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        if (window.location.hostname === 'localhost') {
            console.error('AJAX Error:', thrownError, 'URL:', settings.url);
        }
    });
}
</script>