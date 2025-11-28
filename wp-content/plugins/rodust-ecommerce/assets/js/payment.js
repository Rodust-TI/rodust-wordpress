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
                        R$ ${itemTotal.toFixed(2).replace('.', ',')}
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
        
        $('.subtotal-value').text(`R$ ${subtotal.toFixed(2).replace('.', ',')}`);
        $('.shipping-value').text(`R$ ${shippingCost.toFixed(2).replace('.', ',')}`);
        $('.total-value').text(`R$ ${total.toFixed(2).replace('.', ',')}`);
        $('.total-mobile').text(`R$ ${total.toFixed(2).replace('.', ',')}`);
    }
    
    /**
     * Processar pagamento
     */
    function finalizePay() {
        const token = sessionStorage.getItem('customer_token');
        
        if (!token) {
            alert('Sessão expirada. Faça login novamente.');
            window.location.href = RODUST_PAYMENT.home_url + '/login';
            return;
        }
        
        // Mostrar loading
        $('#payment-loading').removeClass('hidden');
        
        // Preparar dados do pedido
        const orderData = {
            customer_id: checkoutData.customer.id,
            shipping_method_id: checkoutData.shipping.id,
            shipping_cost: parseFloat(checkoutData.shipping.price),
            shipping_address: checkoutData.shipping_address,
            items: checkoutData.cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                price: parseFloat(item.price || 0)
            }))
        };
        
        console.log('Processando pagamento:', selectedPaymentMethod, orderData);
        
        // Escolher endpoint baseado no método de pagamento
        let endpoint = '';
        switch(selectedPaymentMethod) {
            case 'pix':
                endpoint = '/api/payments/pix';
                break;
            case 'boleto':
                endpoint = '/api/payments/boleto';
                break;
            case 'credit_card':
                endpoint = '/api/payments/card';
                break;
            default:
                alert('Método de pagamento inválido');
                $('#payment-loading').addClass('hidden');
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
                
                if (response.success && response.data) {
                    // Limpar carrinho do WordPress
                    clearCart();
                    
                    // Salvar dados do pagamento
                    const paymentData = {
                        order_number: response.data.order?.order_number || '',
                        order_id: response.data.order?.id || '',
                        order: response.data.order || {},
                        ...response.data.payment
                    };
                    sessionStorage.setItem('payment_data', JSON.stringify(paymentData));
                    
                    // Limpar dados do checkout
                    sessionStorage.removeItem('checkout_data');
                    
                    // Redirecionar para página de confirmação
                    window.location.href = RODUST_PAYMENT.home_url + '/pedido-confirmado?order=' + 
                        response.data.order.id + '&payment=' + selectedPaymentMethod;
                } else {
                    $('#payment-loading').addClass('hidden');
                    alert(response.message || 'Erro ao processar pagamento. Tente novamente.');
                }
            },
            error: function(xhr) {
                $('#payment-loading').addClass('hidden');
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
