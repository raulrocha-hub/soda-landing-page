/**
 * Soda Perfeita - Pedidos JavaScript
 */

class SodaPerfeitaPedidos {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initComponents();
        this.calculateOrderSummary();
    }

    bindEvents() {
        // Order form events
        this.bindOrderFormEvents();
        
        // Order actions
        this.bindOrderActions();
        
        // Order filters
        this.bindOrderFilters();
        
        // Real-time updates
        this.bindRealTimeUpdates();
    }

    initComponents() {
        // Initialize quantity selector
        this.initQuantitySelector();
        
        // Initialize date picker
        this.initDatePicker();
        
        // Initialize status filters
        this.initStatusFilters();
    }

    bindOrderFormEvents() {
        const orderForm = document.getElementById('soda-form-pedido');
        
        if (orderForm) {
            // Quantity change
            const quantitySelect = orderForm.querySelector('#quantidade_garrafas');
            if (quantitySelect) {
                quantitySelect.addEventListener('change', () => {
                    this.calculateOrderSummary();
                });
            }

            // Form submission
            orderForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitOrderForm(orderForm);
            });
        }
    }

    bindOrderActions() {
        // Approve order
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="approve-order"]')) {
                e.preventDefault();
                this.approveOrder(e.target.dataset.orderId);
            }
        });

        // Reject order
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="reject-order"]')) {
                e.preventDefault();
                this.rejectOrder(e.target.dataset.orderId);
            }
        });

        // Cancel order
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="cancel-order"]')) {
                e.preventDefault();
                this.cancelOrder(e.target.dataset.orderId);
            }
        });

        // Mark as delivered
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="deliver-order"]')) {
                e.preventDefault();
                this.deliverOrder(e.target.dataset.orderId);
            }
        });
    }

    bindOrderFilters() {
        const statusFilter = document.getElementById('filter-order-status');
        const dateFilter = document.getElementById('filter-order-date');
        const searchInput = document.getElementById('search-orders');

        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.filterOrders();
            });
        }

        if (dateFilter) {
            dateFilter.addEventListener('change', () => {
                this.filterOrders();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(() => {
                this.filterOrders();
            }, 300));
        }
    }

    bindRealTimeUpdates() {
        // Check for new orders every 30 seconds
        setInterval(() => {
            this.checkNewOrders();
        }, 30000);
    }

    initQuantitySelector() {
        const quantitySelect = document.getElementById('quantidade_garrafas');
        
        if (quantitySelect) {
            // Add custom styling or functionality if needed
            quantitySelect.classList.add('soda-quantity-select');
        }
    }

    initDatePicker() {
        const dateInput = document.getElementById('data_entrega_preferida');
        
        if (dateInput && typeof flatpickr !== 'undefined') {
            flatpickr(dateInput, {
                dateFormat: 'd/m/Y',
                minDate: 'today',
                locale: 'pt',
                disable: [
                    function(date) {
                        // Disable weekends
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ]
            });
        }
    }

    initStatusFilters() {
        // Initialize any custom status filter components
        const statusFilters = document.querySelectorAll('.soda-status-filter');
        
        statusFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.applyStatusFilter(filter.dataset.status);
            });
        });
    }

    calculateOrderSummary() {
        const quantitySelect = document.getElementById('quantidade_garrafas');
        const resumoQuantidade = document.getElementById('resumo-quantidade');
        const resumoTotal = document.getElementById('resumo-total');
        
        if (!quantitySelect || !resumoQuantidade || !resumoTotal) {
            return;
        }

        const quantity = parseInt(quantitySelect.value) || 0;
        const unitPrice = 45.00;
        const total = quantity * unitPrice;

        resumoQuantidade.textContent = quantity + ' garrafas';
        resumoTotal.textContent = 'R$ ' + total.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Update any additional summary elements
        this.updateOrderSummaryDetails(quantity, total);
    }

    updateOrderSummaryDetails(quantity, total) {
        const detailsElement = document.getElementById('resumo-detalhes');
        
        if (detailsElement) {
            const detailsHTML = `
                <div class="resumo-detail">
                    <span>Preço unitário:</span>
                    <span>R$ 45,00</span>
                </div>
                <div class="resumo-detail">
                    <span>Quantidade:</span>
                    <span>${quantity} unidades</span>
                </div>
                <div class="resumo-detail total">
                    <span>Total:</span>
                    <span>R$ ${total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                </div>
            `;
            
            detailsElement.innerHTML = detailsHTML;
        }
    }

    async submitOrderForm(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Validation
        if (!this.validateOrderForm(form)) {
            return;
        }

        // Show loading state
        this.setLoadingState(submitButton, true);

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showOrderSuccess(data.data);
            } else {
                throw new Error(data.data || 'Erro ao processar pedido.');
            }
        } catch (error) {
            this.showOrderError(error.message);
        } finally {
            this.setLoadingState(submitButton, false);
        }
    }

    validateOrderForm(form) {
        const quantity = form.querySelector('#quantidade_garrafas').value;
        
        if (!quantity || quantity < 4) {
            this.showAlert('Selecione uma quantidade válida (mínimo 4 garrafas).', 'error');
            return false;
        }

        const date = form.querySelector('#data_entrega_preferida').value;
        if (date) {
            const selectedDate = new Date(date);
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            if (selectedDate < tomorrow) {
                this.showAlert('A data de entrega deve ser pelo menos 1 dia após hoje.', 'error');
                return false;
            }
        }

        return true;
    }

    async approveOrder(orderId) {
        if (!confirm('Tem certeza que deseja aprovar este pedido?')) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_approve_order',
                    order_id: orderId,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Pedido aprovado com sucesso!', 'success');
                this.updateOrderStatus(orderId, 'aprovado');
            } else {
                throw new Error(data.data || 'Erro ao aprovar pedido.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async rejectOrder(orderId) {
        const reason = prompt('Informe o motivo da rejeição:');
        
        if (!reason) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_reject_order',
                    order_id: orderId,
                    reason: reason,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Pedido rejeitado com sucesso!', 'success');
                this.updateOrderStatus(orderId, 'rejeitado');
            } else {
                throw new Error(data.data || 'Erro ao rejeitar pedido.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async cancelOrder(orderId) {
        if (!confirm('Tem certeza que deseja cancelar este pedido?')) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_cancel_order',
                    order_id: orderId,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Pedido cancelado com sucesso!', 'success');
                this.updateOrderStatus(orderId, 'cancelado');
            } else {
                throw new Error(data.data || 'Erro ao cancelar pedido.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async deliverOrder(orderId) {
        if (!confirm('Marcar pedido como entregue?')) {
            return;
        }

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_deliver_order',
                    order_id: orderId,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Pedido marcado como entregue!', 'success');
                this.updateOrderStatus(orderId, 'entregue');
                
                // Update meritocracy points
                this.updateMeritocracyPoints(data.data.points_awarded);
            } else {
                throw new Error(data.data || 'Erro ao marcar pedido como entregue.');
            }
        } catch (error) {
            this.showAlert(error.message, 'error');
        }
    }

    async filterOrders() {
        const status = document.getElementById('filter-order-status')?.value || '';
        const date = document.getElementById('filter-order-date')?.value || '';
        const search = document.getElementById('search-orders')?.value || '';

        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_filter_orders',
                    status: status,
                    date: date,
                    search: search,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateOrdersList(data.data.orders);
                this.updateOrdersStats(data.data.stats);
            }
        } catch (error) {
            console.error('Erro ao filtrar pedidos:', error);
        }
    }

    async checkNewOrders() {
        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'soda_check_new_orders',
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();

            if (data.success && data.data.new_orders > 0) {
                this.showNewOrdersNotification(data.data.new_orders);
            }
        } catch (error) {
            console.error('Erro ao verificar novos pedidos:', error);
        }
    }

    showOrderSuccess(data) {
        this.showAlert('Pedido solicitado com sucesso! Aguarde a aprovação.', 'success');
        
        // Redirect or clear form
        if (data.redirect_url) {
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 2000);
        } else {
            this.resetOrderForm();
        }
    }

    showOrderError(message) {
        this.showAlert(message, 'error');
    }

    resetOrderForm() {
        const form = document.getElementById('soda-form-pedido');
        
        if (form) {
            form.reset();
            this.calculateOrderSummary();
        }
    }

    updateOrderStatus(orderId, newStatus) {
        const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
        
        if (orderElement) {
            const statusElement = orderElement.querySelector('.order-status');
            const actionsElement = orderElement.querySelector('.order-actions');
            
            if (statusElement) {
                statusElement.className = `order-status status-${newStatus}`;
                statusElement.textContent = this.getStatusLabel(newStatus);
            }
            
            if (actionsElement) {
                actionsElement.innerHTML = this.getStatusActions(newStatus);
            }
        }
    }

    updateOrdersList(orders) {
        const ordersContainer = document.getElementById('orders-list');
        
        if (ordersContainer) {
            ordersContainer.innerHTML = orders;
        }
    }

    updateOrdersStats(stats) {
        // Update any statistics displays
        Object.keys(stats).forEach(statKey => {
            const element = document.getElementById(`stat-${statKey}`);
            if (element) {
                element.textContent = stats[statKey];
            }
        });
    }

    updateMeritocracyPoints(points) {
        const pointsElement = document.getElementById('meritocracy-points');
        
        if (pointsElement) {
            const currentPoints = parseInt(pointsElement.textContent) || 0;
            const newPoints = currentPoints + points;
            
            // Animate points update
            this.animateValue(pointsElement, currentPoints, newPoints, 1000);
        }
    }

    showNewOrdersNotification(count) {
        // Create or update notification badge
        let notification = document.getElementById('new-orders-notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'new-orders-notification';
            notification.className = 'soda-notification-badge';
            
            const ordersLink = document.querySelector('a[href*="pedidos"]');
            if (ordersLink) {
                ordersLink.appendChild(notification);
            }
        }
        
        notification.textContent = count;
        notification.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.style.display = 'none';
        }, 5000);
    }

    getStatusLabel(status) {
        const labels = {
            'solicitado': 'Solicitado',
            'aprovado': 'Aprovado',
            'faturado': 'Faturado',
            'enviado': 'Enviado',
            'entregue': 'Entregue',
            'cancelado': 'Cancelado',
            'rejeitado': 'Rejeitado'
        };
        
        return labels[status] || status;
    }

    getStatusActions(status) {
        const actions = {
            'solicitado': `
                <button class="btn btn-success" data-action="approve-order" data-order-id="ORDER_ID">
                    Aprovar
                </button>
                <button class="btn btn-danger" data-action="reject-order" data-order-id="ORDER_ID">
                    Rejeitar
                </button>
            `,
            'aprovado': `
                <button class="btn btn-warning" data-action="cancel-order" data-order-id="ORDER_ID">
                    Cancelar
                </button>
            `,
            'faturado': `
                <button class="btn btn-success" data-action="deliver-order" data-order-id="ORDER_ID">
                    Entregue
                </button>
            `,
            'entregue': `
                <span class="text-success">Concluído</span>
            `
        };
        
        return actions[status] || '';
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
        alert.className = `soda-alert soda-alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <span class="alert-icon"></span>
                <span class="alert-message">${message}</span>
                <button class="alert-close">&times;</button>
            </div>
        `;
        
        // Add to page
        const container = document.querySelector('.soda-perfeita-frontend') || document.body;
        container.prepend(alert);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            this.removeAlert(alert);
        }, 5000);
        
        // Add close functionality
        const closeButton = alert.querySelector('.alert-close');
        closeButton.addEventListener('click', () => {
            this.removeAlert(alert);
        });
    }

    removeAlerts() {
        const alerts = document.querySelectorAll('.soda-alert');
        alerts.forEach(alert => this.removeAlert(alert));
    }

    removeAlert(alert) {
        if (alert && alert.parentNode) {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

    animateValue(element, start, end, duration) {
        const range = end - start;
        const startTime = performance.now();
        
        function updateValue(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = start + (range * progress);
            element.textContent = Math.round(currentValue).toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateValue);
            }
        }
        
        requestAnimationFrame(updateValue);
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
    window.sodaPerfeitaPedidos = new SodaPerfeitaPedidos();
});

// Global functions for use in templates
function sodaSolicitarPedido() {
    const form = document.getElementById('soda-form-pedido');
    if (form && window.sodaPerfeitaPedidos) {
        form.dispatchEvent(new Event('submit'));
    }
}

function sodaAprovarPedido(orderId) {
    if (window.sodaPerfeitaPedidos) {
        window.sodaPerfeitaPedidos.approveOrder(orderId);
    }
}

function sodaRejeitarPedido(orderId) {
    if (window.sodaPerfeitaPedidos) {
        window.sodaPerfeitaPedidos.rejectOrder(orderId);
    }
}

function sodaCancelarPedido(orderId) {
    if (window.sodaPerfeitaPedidos) {
        window.sodaPerfeitaPedidos.cancelOrder(orderId);
    }
}

function sodaEntregarPedido(orderId) {
    if (window.sodaPerfeitaPedidos) {
        window.sodaPerfeitaPedidos.deliverOrder(orderId);
    }
}