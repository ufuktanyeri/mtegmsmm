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
<!-- Bootstrap 5.3.8 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

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
// Theme Management
function initThemeToggle() {
    // Load saved theme on page load
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
}

// Initialize theme immediately (before DOM ready)
initThemeToggle();

// Additional page-specific initialization
document.addEventListener('DOMContentLoaded', function() {
    // Custom page logic here
    console.log('Template system initialized');

    // Initialize Summernote editors if present
    if (typeof $.fn.summernote !== 'undefined') {
        $('[data-summernote]').each(function() {
            const $element = $(this);
            const height = $element.data('height') || 300;
            const lang = $element.data('lang') || 'en-US';

            $element.summernote({
                height: height,
                lang: lang,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ['misc', ['undo', 'redo']]
                ],
                fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
                fontNamesIgnoreCheck: ['Arial'],
                popover: {
                    image: [
                        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                        ['float', ['floatLeft', 'floatRight', 'floatNone']],
                        ['remove', ['removeMedia']]
                    ],
                    link: [
                        ['link', ['linkDialogShow', 'unlink']]
                    ],
                    table: [
                        ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                        ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
                    ]
                },
                callbacks: {
                    onChange: function(contents, $editable) {
                        console.log('Summernote content changed');
                    }
                }
            });
        });
        console.log('Summernote editors initialized:', $('[data-summernote]').length);
    }

    // Debug info (remove in production)
    if (window.location.hostname === 'localhost') {
        console.log('Components initialized:', {
            dropdowns: document.querySelectorAll('[data-dropdown-initialized]').length,
            tooltips: document.querySelectorAll('[data-tooltip-initialized]').length,
            popovers: document.querySelectorAll('[data-popover-initialized]').length,
            forms: document.querySelectorAll('[data-form-initialized]').length,
            modals: document.querySelectorAll('[data-modal-initialized]').length,
            summernote: $('[data-summernote]').length || 0
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