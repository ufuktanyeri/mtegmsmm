/*!
 * Tabler Custom JavaScript for MTEGM SMM Portal
 * Built on Tabler Core
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    initializeTooltips();
    
    // Initialize confirmation dialogs
    initializeConfirmations();
    
    // Initialize form enhancements
    initializeFormEnhancements();
    
    // Initialize table enhancements
    initializeTableEnhancements();
    
    // Initialize notifications
    initializeNotifications();
});

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover',
            delay: { show: 500, hide: 100 }
        });
    });
}

/**
 * Initialize confirmation dialogs
 */
function initializeConfirmations() {
    const deleteLinks = document.querySelectorAll('.btn-delete, .delete-confirm');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const message = this.getAttribute('data-message') || 'Bu işlem geri alınamaz. Emin misiniz?';
            const title = this.getAttribute('data-title') || 'Silme Onayı';
            
            if (confirm(title + '\n\n' + message)) {
                if (this.tagName === 'A') {
                    window.location.href = this.href;
                } else if (this.tagName === 'BUTTON' && this.form) {
                    this.form.submit();
                }
            }
        });
    });
}

/**
 * Initialize form enhancements
 */
function initializeFormEnhancements() {
    // Auto-save form data to localStorage
    const forms = document.querySelectorAll('form[data-auto-save]');
    forms.forEach(form => {
        const formId = form.getAttribute('data-auto-save');
        loadFormData(form, formId);
        
        // Save on input change
        form.addEventListener('input', function() {
            saveFormData(form, formId);
        });
        
        // Clear on successful submit
        form.addEventListener('submit', function() {
            setTimeout(() => {
                clearFormData(formId);
            }, 1000);
        });
    });
    
    // Character counters
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'text-muted small mt-1';
        counter.textContent = `0 / ${maxLength}`;
        
        textarea.parentNode.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;
            
            if (currentLength > maxLength * 0.9) {
                counter.className = 'text-warning small mt-1';
            } else if (currentLength === parseInt(maxLength)) {
                counter.className = 'text-danger small mt-1';
            } else {
                counter.className = 'text-muted small mt-1';
            }
        });
    });
}

/**
 * Initialize table enhancements
 */
function initializeTableEnhancements() {
    // Make tables responsive
    const tables = document.querySelectorAll('table:not(.table-responsive table)');
    tables.forEach(table => {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
    
    // Row click handlers
    const clickableRows = document.querySelectorAll('tr[data-href]');
    clickableRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
                window.location.href = this.getAttribute('data-href');
            }
        });
    });
    
    // Column sorting
    const sortableHeaders = document.querySelectorAll('th[data-sort]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const column = this.getAttribute('data-sort');
            sortTable(table, column);
        });
    });
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        if (!alert.querySelector('.btn-close')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }
    });
    
    // Show success message if redirected from successful action
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showNotification('İşlem başarıyla tamamlandı!', 'success');
    }
    if (urlParams.get('error')) {
        showNotification('İşlem sırasında bir hata oluştu.', 'error');
    }
}

/**
 * Utility Functions
 */

// Show notification
function showNotification(message, type = 'info', duration = 3000) {
    const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
    const icon = type === 'success' ? 'ti-check' : type === 'error' ? 'ti-alert-circle' : 'ti-info-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="ti ${icon} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    if (duration > 0) {
        setTimeout(() => {
            alert.remove();
        }, duration);
    }
}

// Save form data to localStorage
function saveFormData(form, formId) {
    const data = {};
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        if (input.name && input.type !== 'password') {
            if (input.type === 'checkbox') {
                data[input.name] = input.checked;
            } else {
                data[input.name] = input.value;
            }
        }
    });
    
    localStorage.setItem(`form_${formId}`, JSON.stringify(data));
}

// Load form data from localStorage
function loadFormData(form, formId) {
    const saved = localStorage.getItem(`form_${formId}`);
    if (saved) {
        const data = JSON.parse(saved);
        
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input && input.type !== 'password') {
                if (input.type === 'checkbox') {
                    input.checked = data[key];
                } else {
                    input.value = data[key];
                }
            }
        });
    }
}

// Clear form data from localStorage
function clearFormData(formId) {
    localStorage.removeItem(`form_${formId}`);
}

// Sort table by column
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.getAttribute(`data-${column}`) || a.querySelector(`[data-${column}]`)?.textContent || '';
        const bValue = b.getAttribute(`data-${column}`) || b.querySelector(`[data-${column}]`)?.textContent || '';
        
        return aValue.localeCompare(bValue, 'tr', { numeric: true });
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Loading state helper
function showLoading(element, text = 'Yükleniyor...') {
    element.classList.add('loading');
    element.setAttribute('data-loading-text', element.textContent);
    element.textContent = text;
    element.disabled = true;
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.textContent = element.getAttribute('data-loading-text') || element.textContent;
    element.disabled = false;
}

// AJAX helper
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    try {
        const response = await fetch(url, { ...defaultOptions, ...options });
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Bir hata oluştu');
        }
        
        return data;
    } catch (error) {
        showNotification(error.message, 'error');
        throw error;
    }
}

// Export utilities for global use
window.SMM = {
    showNotification,
    showLoading,
    hideLoading,
    apiRequest
};