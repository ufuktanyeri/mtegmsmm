/*!
 * Bootstrap 5 Admin Theme JavaScript
 * Replaces AdminLTE functionality with Bootstrap 5 equivalent
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar functionality
    initializeSidebar();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Theme toggle removed - using light theme only
    
    // Initialize treeview navigation
    initializeTreeview();
});

/**
 * Sidebar functionality
 */
function initializeSidebar() {
    const pushmenuBtn = document.querySelector('[data-widget="pushmenu"]');
    const body = document.body;
    const sidebar = document.querySelector('.main-sidebar');
    
    if (pushmenuBtn) {
        pushmenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    // Handle responsive sidebar
    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('sidebar-open');
        } else {
            body.classList.toggle('sidebar-collapse');
        }
    }
    
    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && sidebar.classList.contains('sidebar-open')) {
            if (!sidebar.contains(e.target) && !pushmenuBtn.contains(e.target)) {
                sidebar.classList.remove('sidebar-open');
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('sidebar-open');
        }
    });
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Theme functions removed - using light theme only
 */

/**
 * Treeview navigation functionality
 */
function initializeTreeview() {
    const treeviewLinks = document.querySelectorAll('.nav-link[data-widget="treeview"], .nav-sidebar .nav-link:has(.fa-angle-left)');
    
    treeviewLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const parentLi = this.closest('.nav-item');
            const submenu = parentLi.querySelector('.nav-treeview');
            const icon = this.querySelector('.fa-angle-left');
            
            if (submenu) {
                // Toggle submenu
                if (submenu.style.display === 'none' || submenu.style.display === '') {
                    submenu.style.display = 'block';
                    parentLi.classList.add('menu-open');
                    if (icon) icon.style.transform = 'rotate(-90deg)';
                } else {
                    submenu.style.display = 'none';
                    parentLi.classList.remove('menu-open');
                    if (icon) icon.style.transform = 'rotate(0deg)';
                }
                
                // Close other open submenus at same level
                const siblingItems = parentLi.parentElement.querySelectorAll('.nav-item');
                siblingItems.forEach(item => {
                    if (item !== parentLi) {
                        const siblingSubmenu = item.querySelector('.nav-treeview');
                        const siblingIcon = item.querySelector('.fa-angle-left');
                        if (siblingSubmenu) {
                            siblingSubmenu.style.display = 'none';
                            item.classList.remove('menu-open');
                            if (siblingIcon) siblingIcon.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            }
        });
    });
    
    // Set active menu items
    const currentUrl = window.location.href;
    const allNavLinks = document.querySelectorAll('.nav-sidebar .nav-link');
    
    allNavLinks.forEach(link => {
        if (link.href && currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
            
            // Open parent treeview if exists
            const parentTreeview = link.closest('.nav-treeview');
            if (parentTreeview) {
                parentTreeview.style.display = 'block';
                const parentLi = parentTreeview.closest('.nav-item');
                parentLi.classList.add('menu-open');
                const parentIcon = parentLi.querySelector('.fa-angle-left');
                if (parentIcon) parentIcon.style.transform = 'rotate(-90deg)';
            }
        }
    });
}

/**
 * Bootstrap 5 Modal helpers
 */
function showModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

function hideModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
}

/**
 * Bootstrap 5 Toast helpers
 */
function showToast(message, type = 'info', duration = 5000) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-${type} text-white">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Bildirim</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: duration
    });
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * DataTables Bootstrap 5 integration (if DataTables is used)
 */
if (typeof $.fn.dataTable !== 'undefined') {
    // Set DataTables defaults for Bootstrap 5
    $.extend(true, $.fn.dataTable.defaults, {
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "language": {
            "search": "",
            "searchPlaceholder": "Ara...",
            "lengthMenu": "_MENU_ kayıt göster",
            "info": "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
            "infoEmpty": "Gösterilecek kayıt yok",
            "infoFiltered": "(_MAX_ kayıt içerisinden filtrelendi)",
            "zeroRecords": "Eşleşen kayıt bulunamadı",
            "emptyTable": "Tabloda herhangi bir veri mevcut değil",
            "paginate": {
                "first": "İlk",
                "last": "Son",
                "next": "Sonraki",
                "previous": "Önceki"
            }
        }
    });
}

// Global helpers for backward compatibility
window.AdminBST = {
    showModal: showModal,
    hideModal: hideModal,
    showToast: showToast,
    toggleSidebar: function() {
        const pushmenuBtn = document.querySelector('[data-widget="pushmenu"]');
        if (pushmenuBtn) {
            pushmenuBtn.click();
        }
    }
};