/**
 * Template Fix - Dropdown and Navbar Issues
 */

(function() {
    'use strict';

    // Wait for DOM and Bootstrap to be ready
    function waitForBootstrap(callback) {
        if (typeof bootstrap !== 'undefined') {
            callback();
        } else {
            setTimeout(function() {
                waitForBootstrap(callback);
            }, 50);
        }
    }

    // Initialize template fixes
    function initTemplateFixes() {
        console.log('Initializing template fixes...');

        // Fix dropdowns
        fixDropdowns();

        // Fix navbar
        fixNavbar();

        // Fix responsive issues
        fixResponsive();

        // Reinit on AJAX
        setupAjaxHandlers();
    }

    // Fix dropdown issues
    function fixDropdowns() {
        const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');

        dropdowns.forEach(dropdown => {
            // Remove existing instances
            const existingInstance = bootstrap.Dropdown.getInstance(dropdown);
            if (existingInstance) {
                existingInstance.dispose();
            }

            // Create new instance with proper config
            const dropdownInstance = new bootstrap.Dropdown(dropdown, {
                boundary: 'viewport',
                reference: 'toggle',
                display: 'dynamic',
                popperConfig: {
                    placement: 'bottom-start',
                    modifiers: [
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary: 'viewport'
                            }
                        },
                        {
                            name: 'flip',
                            options: {
                                fallbackPlacements: ['bottom-end', 'top-start', 'top-end']
                            }
                        }
                    ]
                }
            });

            // Add click handler for manual toggle
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle dropdown manually
                if (dropdown.getAttribute('aria-expanded') === 'true') {
                    dropdownInstance.hide();
                } else {
                    // Close all other dropdowns first
                    document.querySelectorAll('[data-bs-toggle="dropdown"][aria-expanded="true"]').forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            const otherInstance = bootstrap.Dropdown.getInstance(otherDropdown);
                            if (otherInstance) {
                                otherInstance.hide();
                            }
                        }
                    });
                    dropdownInstance.show();
                }
            });

            // Hover effect for desktop
            if (window.innerWidth >= 992) {
                const parentLi = dropdown.closest('li.nav-item');
                if (parentLi) {
                    let hoverTimeout;

                    parentLi.addEventListener('mouseenter', function() {
                        clearTimeout(hoverTimeout);
                        dropdownInstance.show();
                    });

                    parentLi.addEventListener('mouseleave', function() {
                        hoverTimeout = setTimeout(function() {
                            dropdownInstance.hide();
                        }, 300);
                    });
                }
            }
        });

        console.log('Fixed', dropdowns.length, 'dropdowns');
    }

    // Fix navbar issues
    function fixNavbar() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        // Remove transform that might hide navbar
        navbar.style.transform = 'none';

        // Fix navbar toggler for mobile
        const toggler = navbar.querySelector('.navbar-toggler');
        if (toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = toggler.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);

                if (target) {
                    target.classList.toggle('show');
                    toggler.setAttribute('aria-expanded', target.classList.contains('show'));
                }
            });
        }

        console.log('Navbar fixed');
    }

    // Fix responsive issues
    function fixResponsive() {
        // Fix header overflow
        const header = document.querySelector('.page-header-internal');
        if (header) {
            header.style.maxWidth = '100%';
            header.style.overflow = 'hidden';
        }

        // Fix container widths
        document.querySelectorAll('.container-fluid').forEach(container => {
            const rect = container.getBoundingClientRect();
            if (rect.width > window.innerWidth) {
                container.style.maxWidth = '100%';
                container.style.paddingLeft = '15px';
                container.style.paddingRight = '15px';
            }
        });

        console.log('Responsive issues fixed');
    }

    // Setup AJAX handlers for reinit
    function setupAjaxHandlers() {
        // jQuery AJAX
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ajaxComplete(function() {
                setTimeout(function() {
                    fixDropdowns();
                    fixNavbar();
                }, 200);
            });
        }

        // Fetch API
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                setTimeout(function() {
                    fixDropdowns();
                    fixNavbar();
                }, 200);
                return response;
            });
        };

        // MutationObserver for dynamic content
        const observer = new MutationObserver(function(mutations) {
            let shouldReinit = false;

            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.querySelector && node.querySelector('[data-bs-toggle="dropdown"]')) {
                                shouldReinit = true;
                            }
                        }
                    });
                }
            });

            if (shouldReinit) {
                setTimeout(fixDropdowns, 100);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        console.log('AJAX handlers setup complete');
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            waitForBootstrap(initTemplateFixes);
        });
    } else {
        waitForBootstrap(initTemplateFixes);
    }

    // Reinit on window resize (debounced)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            fixDropdowns();
            fixResponsive();
        }, 250);
    });

    // Export for manual reinit
    window.TemplateFix = {
        init: initTemplateFixes,
        fixDropdowns: fixDropdowns,
        fixNavbar: fixNavbar,
        fixResponsive: fixResponsive
    };

})();