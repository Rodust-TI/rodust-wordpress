/**
 * Payment Page - Main JavaScript
 * 
 * @package RodustEcommerce
 */

(function($) {
    'use strict';
    
    let checkoutData = null;
    let selectedPaymentMethod = 'pix';
    
    /**
     * Carregar dados do checkout do sessionStorage
     */
    function loadCheckoutData() {
        const dataStr = sessionStorage.getItem('checkout_data');
        
        if (!dataStr) {
            $('#no-checkout-data').removeClass('hidden');
            return false;
        }
        
        try {
            checkoutData = JSON.parse(dataStr);
            console.log('Dados do checkout carregados:', checkoutData);
            
            if (!checkoutData.cart || !checkoutData.shipping) {
                throw new Error('Dados incompletos');
            }
            
            renderOrderSummary();
            $('#payment-content').removeClass('hidden');
            return true;
        } catch (e) {
            console.error('Erro ao carregar dados:', e);
            $('#no-checkout-data').removeClass('hidden');
            return false;
        }
    }
    
    /**
     * Renderizar resumo do pedido
     */
    function renderOrderSummary() {
        // Itens do carrinho
        let itemsHtml = '';
        let subtotal = 0;
        
        checkoutData.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            
            itemsHtml += `
                <div class="order-item">
                    <div class="item-details">
                        <strong>${item.name}</strong>
                        <span class="quantity">Qtd: ${item.quantity}</span>
                    </div>
                    <div class="item-price">
                        ${RodustHelpers.formatPrice(itemTotal)}
                    </div>
                </div>
            `;
        });
        
        $('#summary-items').html(itemsHtml);
        
        // Endereço
        const addr = checkoutData.shipping_address;
        const addressText = `${addr.street}, ${addr.number}${addr.complement ? ' - ' + addr.complement : ''}<br>${addr.neighborhood} - ${addr.city}/${addr.state}<br>CEP: ${addr.postal_code}`;
        $('#summary-address-text').html(addressText);
        
        // Frete
        const shipping = checkoutData.shipping;
        const shippingText = `${shipping.company} - ${shipping.name}<br>${shipping.delivery_time} dias úteis`;
        $('#summary-shipping-text').html(shippingText);
        
        // Totais
        const shippingCost = parseFloat(shipping.price);
        const total = subtotal + shippingCost;
        
        $('.subtotal-value').text(RodustHelpers.formatPrice(subtotal));
        $('.shipping-value').text(RodustHelpers.formatPrice(shippingCost));
        $('.total-value').text(RodustHelpers.formatPrice(total));
        $('.total-mobile').text(RodustHelpers.formatPrice(total));
    }
    
    /**
     * Processar pagamento
     */
    async function finalizePay() {
        const token = sessionStorage.getItem('customer_token');
        
        if (!token) {
            alert('Sessão expirada. Faça login novamente.');
            window.location.href = RODUST_PAYMENT.home_url + '/login';
            return;
        }
        
        // Preparar dados do pedido
        const orderData = {
            customer_id: checkoutData.customer.id,
            shipping_method_id: checkoutData.shipping.id,
            shipping_cost: parseFloat(checkoutData.shipping.price),
            shipping_address: checkoutData.shipping_address,
            shipping_method: {
                name: checkoutData.shipping.name || checkoutData.shipping.company,
                company: checkoutData.shipping.company,
                delivery_time: checkoutData.shipping.delivery_time
            },
            items: checkoutData.cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                price: parseFloat(item.price || 0)
            }))
        };
        
        console.log('Processando pagamento:', selectedPaymentMethod, orderData);
        
        // Se for cartão, usar função específica do mercadopago-card.js
        if (selectedPaymentMethod === 'credit_card') {
            if (typeof window.processCardPayment === 'function') {
                const result = await window.processCardPayment(orderData);
                if (result) {
                    handlePaymentSuccess(result);
                }
            } else {
                alert('Sistema de pagamento com cartão não está carregado. Recarregue a página.');
            }
            return;
        }
        
        // Mostrar loading
        $('#payment-loading').removeClass('hidden');
        $('#btn-finalize-payment').prop('disabled', true);
        
        // Escolher endpoint baseado no método de pagamento
        let endpoint = '';
        switch(selectedPaymentMethod) {
            case 'pix':
                endpoint = '/api/payments/pix';
                break;
            case 'boleto':
                endpoint = '/api/payments/boleto';
                break;
            default:
                alert('Método de pagamento inválido');
                $('#payment-loading').addClass('hidden');
                $('#btn-finalize-payment').prop('disabled', false);
                return;
        }
        
        // Criar pedido + pagamento via API Laravel
        $.ajax({
            url: RODUST_PAYMENT.api_url + endpoint,
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify(orderData),
            success: function(response) {
                console.log('Pagamento processado:', response);
                
                $('#payment-loading').addClass('hidden');
                $('#btn-finalize-payment').prop('disabled', false);
                
                if (response.success && response.data) {
                    handlePaymentSuccess(response.data);
                } else {
                    alert(response.message || 'Erro ao processar pagamento. Tente novamente.');
                }
            },
            error: function(xhr) {
                $('#payment-loading').addClass('hidden');
                $('#btn-finalize-payment').prop('disabled', false);
                console.error('Erro ao processar pagamento:', xhr);
                
                let errorMsg = 'Erro ao processar pagamento. Tente novamente.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                alert(errorMsg);
            }
        });
    }
    
    /**
     * Handle successful payment
     */
    function handlePaymentSuccess(data) {
        // Limpar carrinho do WordPress
        clearCart();
        
        // Salvar dados do pagamento
        const paymentData = {
            order_number: data.order?.order_number || '',
            order_id: data.order?.id || '',
            order: data.order || {},
            payment: data.payment || data.pix || data.boleto || {}
        };
        
        console.log('Salvando payment_data no sessionStorage:', paymentData);
        sessionStorage.setItem('payment_data', JSON.stringify(paymentData));
        
        // Limpar dados do checkout
        sessionStorage.removeItem('checkout_data');
        
        // Redirecionar para página de confirmação
        window.location.href = RODUST_PAYMENT.home_url + '/pedido-confirmado?order=' + 
            data.order.id + '&payment=' + selectedPaymentMethod;
    }
    
    /**
     * Limpar carrinho do WordPress
     */
    function clearCart() {
        $.ajax({
            url: RODUST_PAYMENT.ajax_url,
            method: 'POST',
            data: {
                action: 'rodust_clear_cart',
                nonce: RODUST_PAYMENT.nonce
            }
        });
    }
    
    /**
     * Event Listeners
     */
    function initEventListeners() {
        // Toggle método de pagamento
        $('.payment-method').on('click', function() {
            $('.payment-method').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
            
            selectedPaymentMethod = $(this).find('input[type="radio"]').val();
            
            // Mostrar formulário correspondente
            $('.payment-form').addClass('hidden');
            $('#' + selectedPaymentMethod.replace('_', '-') + '-form').removeClass('hidden');
        });
        
        // Toggle resumo mobile
        $('#toggle-summary').on('click', function() {
            $('#summary-dropdown').toggleClass('hidden');
            $('.arrow', this).text($('#summary-dropdown').hasClass('hidden') ? '▼' : '▲');
        });
        
        // Finalizar pagamento
        $('#btn-finalize-payment').on('click', finalizePay);
    }
    
    /**
     * Inicialização
     */
    $(document).ready(function() {
        if (loadCheckoutData()) {
            initEventListeners();
            console.log('Página de pagamento inicializada com sucesso');
        }
    });
    
})(jQuery);
