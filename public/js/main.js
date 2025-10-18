/**
 * Main JavaScript file cho Hệ thống Quản lý Sản xuất 4Ever
 */

// Global variables
window.FactoryApp = {
    config: {
        baseUrl: window.location.origin + '/4Ever/',
        csrfToken: '',
        debug: true
    },
    
    // Utility functions
    utils: {
        // Format number with thousands separator
        formatNumber: function(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        },
        
        // Format currency
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        },
        
        // Format date
        formatDate: function(date) {
            return new Intl.DateTimeFormat('vi-VN').format(new Date(date));
        },
        
        // Format datetime
        formatDateTime: function(datetime) {
            return new Intl.DateTimeFormat('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).format(new Date(datetime));
        },
        
        // Show loading state
        showLoading: function(element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            if (element) {
                element.classList.add('loading');
                element.style.pointerEvents = 'none';
            }
        },
        
        // Hide loading state
        hideLoading: function(element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            if (element) {
                element.classList.remove('loading');
                element.style.pointerEvents = '';
            }
        },
        
        // Show notification
        showNotification: function(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            notification.innerHTML = `
                <i class="fas ${icons[type] || icons.info} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after duration
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, duration);
        },
        
        // Confirm dialog
        confirm: function(message, callback) {
            if (confirm(message)) {
                if (typeof callback === 'function') {
                    callback();
                }
                return true;
            }
            return false;
        },
        
        // AJAX helper
        ajax: function(options) {
            const defaults = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            options = Object.assign(defaults, options);
            
            return fetch(options.url, options)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    this.showNotification('Có lỗi xảy ra: ' + error.message, 'error');
                    throw error;
                });
        }
    },
    
    // Components
    components: {
        // DataTable wrapper
        dataTable: function(selector, options = {}) {
            const table = document.querySelector(selector);
            if (!table) return;
            
            // Add search functionality
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-3';
            searchInput.placeholder = 'Tìm kiếm...';
            
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
            
            table.parentNode.insertBefore(searchInput, table);
            
            // Add sorting
            const headers = table.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    this.sortTable(table, index);
                });
            });
        },
        
        // Sort table
        sortTable: function(table, column) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = table.dataset.sortOrder !== 'asc';
            
            rows.sort((a, b) => {
                const aText = a.cells[column].textContent.trim();
                const bText = b.cells[column].textContent.trim();
                
                // Try to parse as numbers
                const aNum = parseFloat(aText.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bText.replace(/[^\d.-]/g, ''));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAscending ? aNum - bNum : bNum - aNum;
                }
                
                // Compare as strings
                return isAscending ? 
                    aText.localeCompare(bText, 'vi') : 
                    bText.localeCompare(aText, 'vi');
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
            
            // Update sort indicator
            table.dataset.sortOrder = isAscending ? 'asc' : 'desc';
            
            // Update header icons
            const headers = table.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                const icon = header.querySelector('.sort-icon');
                if (icon) icon.remove();
                
                if (index === column) {
                    const sortIcon = document.createElement('i');
                    sortIcon.className = `fas fa-chevron-${isAscending ? 'up' : 'down'} sort-icon ms-2`;
                    header.appendChild(sortIcon);
                }
            });
        },
        
        // Chart helper
        createChart: function(canvas, config) {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            return new Chart(ctx, config);
        },
        
        // Modal helper
        modal: function(options) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog ${options.size || ''}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${options.title || ''}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${options.body || ''}
                        </div>
                        <div class="modal-footer">
                            ${options.footer || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>'}
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Remove modal from DOM after hidden
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
            });
            
            return bsModal;
        }
    },
    
    // Page-specific functions
    pages: {
        dashboard: {
            init: function() {
                console.log('Dashboard initialized');
                this.loadCharts();
                this.setupRefresh();
            },
            
            loadCharts: function() {
                // Production progress chart
                const progressCanvas = document.getElementById('productionChart');
                if (progressCanvas) {
                    FactoryApp.components.createChart(progressCanvas, {
                        type: 'line',
                        data: {
                            labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                            datasets: [{
                                label: 'Kế hoạch',
                                data: [100, 120, 110, 130, 125, 140, 135],
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Thực tế',
                                data: [95, 115, 105, 125, 120, 135, 130],
                                borderColor: '#764ba2',
                                backgroundColor: 'rgba(118, 75, 162, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            },
            
            setupRefresh: function() {
                const refreshBtn = document.getElementById('refreshDashboard');
                if (refreshBtn) {
                    refreshBtn.addEventListener('click', () => {
                        location.reload();
                    });
                }
            }
        },
        
        productionPlans: {
            init: function() {
                console.log('Production Plans initialized');
                this.setupFilters();
                this.setupDatePickers();
            },
            
            setupFilters: function() {
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', this.filterPlans);
                }
            },
            
            setupDatePickers: function() {
                // Add date picker functionality if needed
            },
            
            filterPlans: function() {
                // Implement filtering logic
            }
        }
    }
};

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Factory Management System loaded');
    
    // Initialize sidebar toggle
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (toggleBtn && sidebar && mainContent) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }
    
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize popovers
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
    
    // Auto-dismiss alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
    
    // Initialize page-specific code
    const body = document.body;
    if (body.classList.contains('page-dashboard')) {
        FactoryApp.pages.dashboard.init();
    } else if (body.classList.contains('page-production-plans')) {
        FactoryApp.pages.productionPlans.init();
    }
    
    // Global error handler
    window.addEventListener('error', function(e) {
        if (FactoryApp.config.debug) {
            console.error('Global error:', e.error);
        }
    });
    
    // Handle AJAX errors globally
    window.addEventListener('unhandledrejection', function(e) {
        if (FactoryApp.config.debug) {
            console.error('Unhandled promise rejection:', e.reason);
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.dataset.message || 'Bạn có chắc chắn muốn xóa?';
            FactoryApp.utils.confirm(message, () => {
                if (this.href) {
                    window.location.href = this.href;
                } else if (this.form) {
                    this.form.submit();
                }
            });
        });
    });
    
    // Auto-save draft forms
    const draftForms = document.querySelectorAll('.auto-save');
    draftForms.forEach(function(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(function(input) {
            input.addEventListener('change', function() {
                // Implement auto-save logic
                console.log('Auto-saving form data...');
            });
        });
    });
});

// Export for global use
window.FactoryApp = FactoryApp;
