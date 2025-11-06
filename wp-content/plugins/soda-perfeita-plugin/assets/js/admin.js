/**
 * Soda Perfeita - Admin JavaScript
 */

class SodaPerfeitaAdmin {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initComponents();
        this.initCharts();
    }

    bindEvents() {
        // Bind form submissions
        this.bindFormSubmissions();
        
        // Bind bulk actions
        this.bindBulkActions();
        
        // Bind quick actions
        this.bindQuickActions();
        
        // Bind search and filters
        this.bindSearchFilters();
    }

    initComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize sortable tables
        this.initSortableTables();
        
        // Initialize date pickers
        this.initDatePickers();
    }

    initCharts() {
        // Initialize dashboard charts if ApexCharts is available
        if (typeof ApexCharts !== 'undefined') {
            this.initDashboardCharts();
        }
    }

    bindFormSubmissions() {
        // AJAX form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            if (form.classList.contains('soda-ajax-form')) {
                e.preventDefault();
                this.handleAjaxForm(form);
            }
        });
    }

    bindBulkActions() {
        const bulkActions = document.querySelectorAll('.soda-bulk-action');
        
        bulkActions.forEach(action => {
            action.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleBulkAction(e.target);
            });
        });
    }

    bindQuickActions() {
        const quickActions = document.querySelectorAll('.soda-quick-action');
        
        quickActions.forEach(action => {
            action.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleQuickAction(e.target);
            });
        });
    }

    bindSearchFilters() {
        const searchInputs = document.querySelectorAll('.soda-search-input');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', this.debounce((e) => {
                this.handleSearch(e.target);
            }, 300));
        });
    }

    initTooltips() {
        // Initialize tooltips using WordPress dashicons
        const tooltips = document.querySelectorAll('[data-soda-tooltip]');
        
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    }

    initSortableTables() {
        // Make tables sortable
        const sortableHeaders = document.querySelectorAll('.soda-table th[data-sortable]');
        
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(header);
            });
        });
    }

    initDatePickers() {
        // Initialize date pickers if flatpickr is available
        if (typeof flatpickr !== 'undefined') {
            const dateInputs = document.querySelectorAll('.soda-date-picker');
            
            dateInputs.forEach(input => {
                flatpickr(input, {
                    dateFormat: 'd/m/Y',
                    locale: 'pt'
                });
            });
        }
    }

    initDashboardCharts() {
        // Sales chart
        const salesChart = new ApexCharts(
            document.querySelector('#soda-sales-chart'),
            this.getSalesChartOptions()
        );
        salesChart.render();

        // Customers chart
        const customersChart = new ApexCharts(
            document.querySelector('#soda-customers-chart'),
            this.getCustomersChartOptions()
        );
        customersChart.render();
    }

    getSalesChartOptions() {
        return {
            series: [{
                name: 'Vendas',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Vendas Mensais',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set'],
            }
        };
    }

    getCustomersChartOptions() {
        return {
            series: [44, 55, 41],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['Tier 1', 'Tier 2', 'Tier 3'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
    }

    async handleAjaxForm(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Show loading state
        this.setLoadingState(submitButton, true);

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(data.data.message || 'Ação realizada com sucesso!', 'success');
                
                // Redirect if needed
                if (data.data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1000);
                }
                
                // Reload if needed
                if (data.data.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                throw new Error(data.data || 'Erro ao processar requisição.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        } finally {
            this.setLoadingState(submitButton, false);
        }
    }

    async handleBulkAction(button) {
        const action = button.dataset.action;
        const items = this.getSelectedItems();
        
        if (items.length === 0) {
            this.showAlert('Selecione pelo menos um item para realizar esta ação.', 'warning');
            return;
        }

        if (!confirm(`Tem certeza que deseja ${this.getActionLabel(action)} os itens selecionados?`)) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_bulk_action',
                    bulk_action: action,
                    items: items,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(data.data.message || 'Ação realizada com sucesso!', 'success');
                window.location.reload();
            } else {
                throw new Error(data.data || 'Erro ao processar ação.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async handleQuickAction(button) {
        const action = button.dataset.action;
        const id = button.dataset.id;

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_quick_action',
                    quick_action: action,
                    id: id,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(data.data.message || 'Ação realizada com sucesso!', 'success');
                
                if (data.data.update_ui) {
                    this.updateUIElement(button, data.data);
                }
            } else {
                throw new Error(data.data || 'Erro ao processar ação.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async handleSearch(input) {
        const searchTerm = input.value.trim();
        const searchType = input.dataset.searchType;

        if (searchTerm.length < 2 && searchTerm.length > 0) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_search',
                    search_term: searchTerm,
                    search_type: searchType,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateSearchResults(data.data.results, searchType);
            }
        } catch (error) {
            console.error('Erro na busca:', error);
        }
    }

    getSelectedItems() {
        const checkboxes = document.querySelectorAll('input[name="soda_items[]"]:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }

    getActionLabel(action) {
        const labels = {
            'approve': 'aprovar',
            'reject': 'rejeitar',
            'delete': 'excluir',
            'export': 'exportar'
        };
        
        return labels[action] || action;
    }

    setLoadingState(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `
                <span class="soda-spinner"></span>
                Processando...
            `;
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText;
        }
    }

    showAlert(message, type = 'info') {
        // Remove existing alerts
        this.removeAlerts();
        
        const alert = document.createElement('div');
        alert.className = `notice notice-${type} is-dismissible soda-alert`;
        alert.innerHTML = `
            <p>${message}</p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dispensar este aviso.</span>
            </button>
        `;
        
        // Add to page
        const header = document.querySelector('.wrap h1');
        if (header) {
            header.parentNode.insertBefore(alert, header.nextSibling);
        } else {
            document.querySelector('.wrap').prepend(alert);
        }
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            this.removeAlert(alert);
        }, 5000);
        
        // Add dismiss functionality
        const dismissButton = alert.querySelector('.notice-dismiss');
        dismissButton.addEventListener('click', () => {
            this.removeAlert(alert);
        });
    }

    removeAlerts() {
        const alerts = document.querySelectorAll('.soda-alert');
        alerts.forEach(alert => alert.remove());
    }

    removeAlert(alert) {
        if (alert && alert.parentNode) {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

    showTooltip(e) {
        const tooltipText = e.target.dataset.sodaTooltip;
        const tooltip = document.createElement('div');
        tooltip.className = 'soda-tooltip';
        tooltip.textContent = tooltipText;
        
        document.body.appendChild(tooltip);
        
        const rect = e.target.getBoundingClientRect();
        tooltip.style.left = rect.left + 'px';
        tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
    }

    hideTooltip(e) {
        const tooltip = document.querySelector('.soda-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    sortTable(header) {
        const table = header.closest('table');
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const isAscending = header.classList.contains('asc');
        
        // Remove sort classes from all headers
        table.querySelectorAll('th').forEach(th => {
            th.classList.remove('asc', 'desc');
        });
        
        // Add sort class to current header
        header.classList.add(isAscending ? 'desc' : 'asc');
        
        // Sort rows
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.children[columnIndex].textContent.trim();
            const bValue = b.children[columnIndex].textContent.trim();
            
            let comparison = 0;
            if (aValue > bValue) {
                comparison = 1;
            } else if (aValue < bValue) {
                comparison = -1;
            }
            
            return isAscending ? comparison : -comparison;
        });
        
        // Reappend sorted rows
        rows.forEach(row => tbody.appendChild(row));
    }

    updateUIElement(element, data) {
        if (data.new_status) {
            const statusBadge = element.closest('tr').querySelector('.soda-status-badge');
            if (statusBadge) {
                statusBadge.className = `soda-status-badge soda-status-${data.new_status}`;
                statusBadge.textContent = this.getStatusLabel(data.new_status);
            }
        }
        
        if (data.new_content) {
            element.innerHTML = data.new_content;
        }
    }

    updateSearchResults(results, searchType) {
        const resultsContainer = document.querySelector(`[data-search-results="${searchType}"]`);
        if (resultsContainer) {
            resultsContainer.innerHTML = results;
        }
    }

    getStatusLabel(status) {
        const labels = {
            'active': 'Ativo',
            'pending': 'Pendente',
            'inactive': 'Inativo',
            'completed': 'Concluído'
        };
        
        return labels[status] || status;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.sodaPerfeitaAdmin = new SodaPerfeitaAdmin();
});

// Global functions for use in templates
function sodaApproveItem(itemId, itemType) {
    if (window.sodaPerfeitaAdmin) {
        window.sodaPerfeitaAdmin.handleQuickAction({
            dataset: {
                action: 'approve',
                id: itemId
            }
        });
    }
}

function sodaRejectItem(itemId, itemType) {
    if (window.sodaPerfeitaAdmin) {
        window.sodaPerfeitaAdmin.handleQuickAction({
            dataset: {
                action: 'reject',
                id: itemId
            }
        });
    }
}

function sodaDeleteItem(itemId, itemType) {
    if (confirm('Tem certeza que deseja excluir este item?')) {
        if (window.sodaPerfeitaAdmin) {
            window.sodaPerfeitaAdmin.handleQuickAction({
                dataset: {
                    action: 'delete',
                    id: itemId
                }
            });
        }
    }
}

// Export data function
function sodaExportData(dataType, format = 'csv') {
    const params = new URLSearchParams({
        action: 'soda_export_data',
        data_type: dataType,
        format: format,
        nonce: sodaPerfeitaAjax.nonce
    });

    window.open(sodaPerfeitaAjax.ajaxurl + '?' + params.toString(), '_blank');
}