<?php

namespace App\Services;

/**
 * Template Management Service
 * Handles template initialization, component management, and JavaScript reinit
 */
class TemplateService
{
    private static $initialized = false;
    private static $components = [];

    /**
     * Initialize template system
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;
        self::registerComponents();
    }

    /**
     * Register all template components
     */
    private static function registerComponents(): void
    {
        self::$components = [
            'navbar' => [
                'path' => '/app/views/components/navbar.php',
                'requires_auth' => false,
                'cache' => false
            ],
            'sidebar' => [
                'path' => '/app/views/components/sidebar.php',
                'requires_auth' => true,
                'cache' => false
            ],
            'footer' => [
                'path' => '/app/views/components/footer.php',
                'requires_auth' => false,
                'cache' => true
            ],
            'scripts' => [
                'path' => '/app/views/components/scripts.php',
                'requires_auth' => false,
                'cache' => false
            ]
        ];
    }

    /**
     * Get JavaScript for component reinitialization
     */
    public static function getReinitScript(): string
    {
        return <<<'JS'
        // Template Reinitializer
        window.TemplateManager = {
            initialized: false,

            // Initialize all components
            init: function() {
                this.initDropdowns();
                this.initTooltips();
                this.initPopovers();
                this.initNavbar();
                this.initForms();
                this.initModals();
                this.fixResponsive();
                this.initialized = true;
            },

            // Reinitialize after AJAX
            reinit: function() {
                this.destroyComponents();
                this.init();
            },

            // Destroy existing components
            destroyComponents: function() {
                // Destroy dropdowns
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                    const instance = bootstrap.Dropdown.getInstance(el);
                    if (instance) {
                        instance.dispose();
                    }
                    el.removeAttribute('data-dropdown-initialized');
                });

                // Destroy tooltips
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    const instance = bootstrap.Tooltip.getInstance(el);
                    if (instance) {
                        instance.dispose();
                    }
                });

                // Destroy popovers
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                    const instance = bootstrap.Popover.getInstance(el);
                    if (instance) {
                        instance.dispose();
                    }
                });

                // Clear modal instances
                document.querySelectorAll('.modal').forEach(el => {
                    const instance = bootstrap.Modal.getInstance(el);
                    if (instance) {
                        instance.dispose();
                    }
                });
            },

            // Initialize dropdowns with proper event handling
            initDropdowns: function() {
                const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                dropdowns.forEach(dropdown => {
                    // Skip if already initialized
                    if (dropdown.hasAttribute('data-dropdown-initialized')) {
                        return;
                    }

                    // Mark as initialized
                    dropdown.setAttribute('data-dropdown-initialized', 'true');

                    // Initialize with options
                    new bootstrap.Dropdown(dropdown, {
                        boundary: 'viewport',
                        reference: 'toggle',
                        display: 'dynamic',
                        popperConfig: function(defaultBsPopperConfig) {
                            return {
                                ...defaultBsPopperConfig,
                                modifiers: [
                                    {
                                        name: 'preventOverflow',
                                        options: {
                                            boundary: 'viewport'
                                        }
                                    }
                                ]
                            };
                        }
                    });

                    // Add click handler for dropdown items
                    const menu = dropdown.nextElementSibling;
                    if (menu && menu.classList.contains('dropdown-menu')) {
                        menu.addEventListener('click', function(e) {
                            if (e.target.classList.contains('dropdown-item')) {
                                // Don't close if it has sub-items
                                if (!e.target.querySelector('.dropdown-menu')) {
                                    const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown);
                                    if (dropdownInstance) {
                                        dropdownInstance.hide();
                                    }
                                }
                            }
                        });
                    }
                });
            },

            // Initialize tooltips
            initTooltips: function() {
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    if (!tooltip.hasAttribute('data-tooltip-initialized')) {
                        tooltip.setAttribute('data-tooltip-initialized', 'true');
                        new bootstrap.Tooltip(tooltip);
                    }
                });
            },

            // Initialize popovers
            initPopovers: function() {
                const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
                popovers.forEach(popover => {
                    if (!popover.hasAttribute('data-popover-initialized')) {
                        popover.setAttribute('data-popover-initialized', 'true');
                        new bootstrap.Popover(popover);
                    }
                });
            },

            // Initialize navbar behaviors
            initNavbar: function() {
                const navbar = document.querySelector('.navbar');
                if (!navbar) return;

                // Remove any existing scroll listeners
                const oldScrollHandler = navbar._scrollHandler;
                if (oldScrollHandler) {
                    window.removeEventListener('scroll', oldScrollHandler);
                }

                // Navbar scroll effect
                let lastScrollTop = 0;
                const scrollHandler = function() {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                    // Add scrolled class
                    if (scrollTop > 100) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }

                    // Auto-hide on scroll down (desktop only)
                    if (window.innerWidth > 768) {
                        if (scrollTop > lastScrollTop && scrollTop > 200) {
                            navbar.style.transform = 'translateY(-100%)';
                        } else {
                            navbar.style.transform = 'translateY(0)';
                        }
                    }

                    lastScrollTop = Math.max(0, scrollTop);
                };

                // Store reference for cleanup
                navbar._scrollHandler = scrollHandler;
                window.addEventListener('scroll', scrollHandler, { passive: true });

                // Mobile menu fix
                const navbarToggler = navbar.querySelector('.navbar-toggler');
                if (navbarToggler) {
                    navbarToggler.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const target = document.querySelector(this.dataset.bsTarget);
                        if (target) {
                            target.classList.toggle('show');
                        }
                    });
                }
            },

            // Initialize forms
            initForms: function() {
                const forms = document.querySelectorAll('.needs-validation');
                forms.forEach(form => {
                    if (form.hasAttribute('data-form-initialized')) {
                        return;
                    }

                    form.setAttribute('data-form-initialized', 'true');
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();

                            // Focus first invalid field
                            const firstInvalid = form.querySelector(':invalid');
                            if (firstInvalid) {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            },

            // Initialize modals
            initModals: function() {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (!modal.hasAttribute('data-modal-initialized')) {
                        modal.setAttribute('data-modal-initialized', 'true');

                        // Auto-focus first input when modal opens
                        modal.addEventListener('shown.bs.modal', function() {
                            const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
                            if (firstInput) {
                                firstInput.focus();
                            }
                        });
                    }
                });
            },

            // Fix responsive issues
            fixResponsive: function() {
                // Fix header overflow
                const header = document.querySelector('.page-header-internal, .navbar');
                if (header) {
                    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

                    if (viewportWidth < 768) {
                        // Mobile fixes
                        header.style.maxWidth = '100%';
                        header.style.overflowX = 'hidden';

                        // Fix container width
                        const containers = header.querySelectorAll('.container-fluid');
                        containers.forEach(container => {
                            container.style.paddingLeft = '15px';
                            container.style.paddingRight = '15px';
                            container.style.maxWidth = '100%';
                        });
                    } else {
                        // Desktop fixes
                        header.style.maxWidth = '';
                        header.style.overflowX = '';
                    }
                }

                // Fix dropdown positioning
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    const rect = menu.getBoundingClientRect();
                    if (rect.right > window.innerWidth) {
                        menu.classList.add('dropdown-menu-end');
                    }
                });
            },

            // Handle AJAX complete
            onAjaxComplete: function() {
                // Wait for DOM updates
                setTimeout(() => {
                    this.reinit();
                }, 100);
            }
        };

        // Initialize on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            TemplateManager.init();

            // Reinit on window resize (debounced)
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    TemplateManager.fixResponsive();
                }, 250);
            });
        });

        // Hook into jQuery AJAX if available
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ajaxComplete(function() {
                TemplateManager.onAjaxComplete();
            });
        }

        // Hook into fetch API
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                // Reinit after fetch completes
                setTimeout(() => {
                    TemplateManager.onAjaxComplete();
                }, 100);
                return response;
            });
        };
        JS;
    }

    /**
     * Get responsive CSS fixes
     */
    public static function getResponsiveCSS(): string
    {
        return <<<'CSS'
        /* Responsive Template Fixes */

        /* Prevent horizontal scroll */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Fixed navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        /* Navbar responsive */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 0.5rem 0;
                min-height: 60px;
            }

            .navbar-brand img {
                max-height: 40px;
            }

            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                border-top: 1px solid #e5e7eb;
                max-height: calc(100vh - 60px);
                overflow-y: auto;
            }

            .navbar-nav {
                padding: 1rem 0;
            }

            .dropdown-menu {
                position: static !important;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: #f8f9fa;
                border: 0;
                box-shadow: none;
            }
        }

        /* Content spacing */
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-top: 60px;
                min-height: calc(100vh - 60px);
            }
        }

        /* Page header responsive */
        .page-header-internal {
            overflow: hidden;
            position: relative;
        }

        .page-header-internal .container-fluid {
            max-width: 1400px;
            padding-left: 15px;
            padding-right: 15px;
        }

        @media (max-width: 768px) {
            .page-header-internal {
                padding: 2rem 0;
            }

            .page-header-internal h1 {
                font-size: 1.5rem;
            }

            .breadcrumb {
                font-size: 0.875rem;
            }
        }

        /* Dropdown fixes */
        .dropdown-menu {
            min-width: 200px;
            max-width: 90vw;
        }

        .dropdown-item {
            white-space: normal;
            word-wrap: break-word;
        }

        /* Prevent dropdown cutoff */
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Z-index management */
        .navbar { z-index: 1030; }
        .dropdown-menu { z-index: 1040; }
        .modal-backdrop { z-index: 1050; }
        .modal { z-index: 1060; }
        .tooltip { z-index: 1070; }
        .popover { z-index: 1080; }
        CSS;
    }
}