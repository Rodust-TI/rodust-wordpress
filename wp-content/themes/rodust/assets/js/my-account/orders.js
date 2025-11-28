/**
 * My Account - Orders Module
 * 
 * Gerencia a listagem e detalhes dos pedidos
 */

const MyAccountOrders = (function($) {
    'use strict';
    
    let ordersLoaded = false;
    let currentPage = 1;
    let totalPages = 1;
    let allOrders = [];
    
    /**
     * Inicialização
     */
    function init() {
        console.log('[Orders] Inicializando...');
        // Escutar quando a aba for trocada
        $(document).on('myaccount:tab-changed', function(e, tab) {
            console.log('[Orders] Tab changed:', tab);
            if (tab === 'pedidos') {
                // Carregar apenas se ainda não carregou nenhuma vez
                if (!ordersLoaded) {
                    loadOrders();
                }
            }
        });
        
        // Event listeners
        $('#btn-close-order-details').on('click', closeModal);
        
        $('#order-details-modal').on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
    
    /**
     * Carregar pedidos
     */
    function loadOrders() {
        const token = MyAccount.getToken();
        console.log('[Orders] Loading orders, token:', token ? 'OK' : 'MISSING');
        
        $('#orders-loading').removeClass('hidden');
        $('#orders-list').addClass('hidden');
        $('#orders-empty').addClass('hidden');
        
        $.ajax({
            url: window.RODUST_API_URL + '/api/orders?per_page=10&page=' + currentPage,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('[Orders] Response recebida:', response);
                ordersLoaded = true;
                $('#orders-loading').addClass('hidden');
                
                // Laravel retorna paginação: {data: [...], current_page: 1, total: X, last_page: Y}
                const orders = response.data || [];
                currentPage = response.current_page || 1;
                totalPages = response.last_page || 1;
                const total = response.total || 0;
                
                console.log('[Orders] Página:', currentPage, '/', totalPages, '- Total:', total);
                
                if (orders.length > 0) {
                    renderOrders(orders);
                    renderPagination(response);
                    $('#orders-list').removeClass('hidden');
                } else {
                    console.log('[Orders] Nenhum pedido encontrado');
                    $('#orders-empty').removeClass('hidden');
                }
            },
            error: function(xhr, status, error) {
                console.error('[Orders] Erro ao carregar pedidos:');
                console.error('  Status:', status);
                console.error('  Error:', error);
                console.error('  Response:', xhr.responseText);
                
                ordersLoaded = false;
                $('#orders-loading').addClass('hidden');
                $('#orders-empty').removeClass('hidden');
                MyAccount.showToast('error', 'Erro ao carregar pedidos.');
            }
        });
    }
    
    /**
     * Renderizar lista de pedidos
     */
    function renderOrders(orders) {
        const $list = $('#orders-list');
        let html = '';
        
        orders.forEach(order => {
            const statusBadge = getStatusBadge(order.status);
            const paymentBadge = getPaymentStatusBadge(order.payment_status);
            const date = new Date(order.created_at);
            const dateStr = date.toLocaleDateString('pt-BR');
            const timeStr = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            
            const totalItems = order.items ? order.items.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0) : 0;
            const itemsCount = order.items ? order.items.length : 0;
            
            html += `
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="MyAccountOrders.viewDetails(${order.id})">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">Pedido #${order.order_number}</h3>
                            <p class="text-sm text-gray-600">${dateStr} às ${timeStr}</p>
                        </div>
                        <div class="flex flex-col gap-1 items-end">
                            <span class="${statusBadge.class}">${statusBadge.text}</span>
                            ${paymentBadge ? `<span class="${paymentBadge.class}">${paymentBadge.text}</span>` : ''}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Total</p>
                            <p class="font-semibold text-gray-900">R$ ${parseFloat(order.total_amount || order.total || 0).toFixed(2).replace('.', ',')}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Itens</p>
                            <p class="font-semibold text-gray-900">${totalItems} ${totalItems === 1 ? 'item' : 'itens'} (${itemsCount} ${itemsCount === 1 ? 'produto' : 'produtos'})</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Pagamento</p>
                            <p class="font-semibold text-gray-900">${getPaymentMethodLabel(order.payment_method)}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            ${order.items && order.items.length > 0 ? order.items.slice(0, 2).map(item => item.product_name).join(', ') : 'Sem itens'}
                            ${order.items && order.items.length > 2 ? '...' : ''}
                        </p>
                        <span class="text-blue-600 font-semibold text-sm">Ver detalhes →</span>
                    </div>
                </div>
            `;
        });
        
        $list.html(html);
    }
    
    /**
     * Ver detalhes do pedido
     */
    function viewDetails(orderId) {
        const token = MyAccount.getToken();
        
        $.ajax({
            url: window.RODUST_API_URL + '/api/orders/' + orderId,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    showDetailsModal(response.data);
                }
            },
            error: function() {
                MyAccount.showToast('error', 'Erro ao carregar detalhes do pedido.');
            }
        });
    }
    
    /**
     * Exibir modal de detalhes
     */
    function showDetailsModal(order) {
        const statusBadge = getStatusBadge(order.status);
        const paymentBadge = getPaymentStatusBadge(order.payment_status);
        const date = new Date(order.created_at);
        const dateStr = date.toLocaleDateString('pt-BR');
        const timeStr = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        
        let itemsHtml = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const unitPrice = parseFloat(item.unit_price || 0);
                const totalPrice = parseFloat(item.total_price || 0);
                
                itemsHtml += `
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${item.product_name || 'Produto sem nome'}</p>
                            <p class="text-sm text-gray-600">Qtd: ${item.quantity} x R$ ${unitPrice.toFixed(2).replace('.', ',')}</p>
                        </div>
                        <p class="font-semibold text-gray-900">R$ ${totalPrice.toFixed(2).replace('.', ',')}</p>
                    </div>
                `;
            });
        } else {
            itemsHtml = '<p class="text-gray-500 text-center py-4">Nenhum item encontrado</p>';
        }
        
        const subtotal = parseFloat(order.subtotal_amount || order.subtotal || 0);
        const shipping = parseFloat(order.shipping_cost || order.shipping || 0);
        const total = parseFloat(order.total_amount || order.total || 0);
        
        const html = `
            <div class="space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">Pedido #${order.order_number}</h4>
                        <p class="text-sm text-gray-600">${dateStr} às ${timeStr}</p>
                    </div>
                    <div class="flex flex-col gap-2 items-end">
                        <span class="${statusBadge.class}">${statusBadge.text}</span>
                        ${paymentBadge ? `<span class="${paymentBadge.class}">${paymentBadge.text}</span>` : ''}
                    </div>
                </div>
                
                <div>
                    <h5 class="font-semibold text-gray-900 mb-3">Itens do Pedido</h5>
                    <div class="space-y-2">${itemsHtml}</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Frete</span>
                        <span class="text-gray-900">R$ ${shipping.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">R$ ${total.toFixed(2).replace('.', ',')}</span>
                    </div>
                </div>
                
                <div>
                    <h5 class="font-semibold text-gray-900 mb-2">Pagamento</h5>
                    <p class="text-sm text-gray-800">${getPaymentMethodLabel(order.payment_method)}</p>
                </div>
            </div>
        `;
        
        $('#modal-order-title').text('Pedido #' + order.order_number);
        $('#order-details-content').html(html);
        $('#order-details-modal').removeClass('hidden');
    }
    
    /**
     * Fechar modal
     */
    function closeModal() {
        $('#order-details-modal').addClass('hidden');
    }
    
    /**
     * Renderizar paginação
     */
    function renderPagination(paginationData) {
        const $pagination = $('#orders-pagination');
        
        if (paginationData.last_page <= 1) {
            $pagination.addClass('hidden');
            return;
        }
        
        let html = '';
        const currentPage = paginationData.current_page;
        const lastPage = paginationData.last_page;
        
        // Botão Anterior
        if (currentPage > 1) {
            html += `<button onclick="MyAccountOrders.loadPage(${currentPage - 1})" 
                     class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                     ← Anterior
                     </button>`;
        }
        
        // Números de página
        for (let i = 1; i <= lastPage; i++) {
            // Mostrar primeira, última, atual e adjacentes
            if (i === 1 || i === lastPage || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const isActive = i === currentPage;
                const bgClass = isActive ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50';
                html += `<button onclick="MyAccountOrders.loadPage(${i})" 
                         class="px-4 py-2 border border-gray-300 rounded-lg font-medium ${bgClass}">
                         ${i}
                         </button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2">...</span>`;
            }
        }
        
        // Botão Próximo
        if (currentPage < lastPage) {
            html += `<button onclick="MyAccountOrders.loadPage(${currentPage + 1})" 
                     class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                     Próximo →
                     </button>`;
        }
        
        $pagination.html(html).removeClass('hidden');
    }
    
    /**
     * Carregar página específica
     */
    function loadPage(page) {
        currentPage = page;
        ordersLoaded = false; // Força recarregar
        loadOrders();
        
        // Scroll suave para o topo da lista
        $('html, body').animate({
            scrollTop: $('#tab-pedidos').offset().top - 100
        }, 300);
    }
    
    /**
     * Helpers - Status Badges
     */
    function getStatusBadge(status) {
        const badges = {
            'pending': { text: 'Pendente', class: 'px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold' },
            'processing': { text: 'Processando', class: 'px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold' },
            'shipped': { text: 'Enviado', class: 'px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold' },
            'delivered': { text: 'Entregue', class: 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold' },
            'cancelled': { text: 'Cancelado', class: 'px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold' },
        };
        return badges[status] || { text: status, class: 'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold' };
    }
    
    function getPaymentStatusBadge(paymentStatus) {
        if (!paymentStatus) return null;
        
        const badges = {
            'pending': { text: '💳 Aguardando Pagamento', class: 'px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full text-xs font-semibold border border-yellow-300' },
            'approved': { text: '✅ Pagamento Aprovado', class: 'px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold border border-green-300' },
            'in_process': { text: '⏳ Pagamento em Análise', class: 'px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold border border-blue-300' },
            'rejected': { text: '❌ Pagamento Recusado', class: 'px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold border border-red-300' },
            'cancelled': { text: '🚫 Pagamento Cancelado', class: 'px-3 py-1 bg-gray-50 text-gray-700 rounded-full text-xs font-semibold border border-gray-300' },
        };
        return badges[paymentStatus] || { text: `💳 ${paymentStatus}`, class: 'px-3 py-1 bg-gray-50 text-gray-700 rounded-full text-xs font-semibold border border-gray-300' };
    }
    
    function getPaymentMethodLabel(method) {
        const methods = {
            'pix': 'PIX',
            'boleto': 'Boleto Bancário',
            'credit_card': 'Cartão de Crédito',
            'debit_card': 'Cartão de Débito'
        };
        return methods[method] || method || 'Não informado';
    }
    
    // Public API
    return {
        init,
        viewDetails,
        loadPage
    };
    
})(jQuery);

// Inicializar quando documento estiver pronto
jQuery(document).ready(function() {
    MyAccountOrders.init();
});
