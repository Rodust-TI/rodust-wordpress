<?php
/**
 * Template: Payment Page
 * 
 * Shortcode: [rodust_payment]
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

<div class="rodust-payment">
    <div class="container">
        
        <h1 class="page-title"><?php _e('Pagamento', 'rodust-ecommerce'); ?></h1>
        
        <!-- Mensagem de erro se não houver dados -->
        <div id="no-checkout-data" class="hidden" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; text-align: center; margin: 40px 0;">
            <p style="margin: 0 0 16px 0; color: #856404; font-size: 16px;">
                ⚠️ <?php _e('Nenhum dado de checkout encontrado.', 'rodust-ecommerce'); ?>
            </p>
            <a href="<?php echo home_url('/checkout'); ?>" class="btn-primary" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">
                <?php _e('Voltar ao Checkout', 'rodust-ecommerce'); ?>
            </a>
        </div>
        
        <!-- Conteúdo da página de pagamento -->
        <div id="payment-content" class="payment-layout hidden">
            
            <!-- Coluna Principal: Métodos de Pagamento -->
            <div class="payment-main">
                
                <!-- Resumo Rápido (Mobile) -->
                <div class="order-summary-mobile">
                    <button type="button" id="toggle-summary" class="summary-toggle">
                        <span><?php _e('Ver resumo do pedido', 'rodust-ecommerce'); ?></span>
                        <span class="total-mobile">R$ 0,00</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div id="summary-dropdown" class="summary-dropdown hidden">
                        <!-- Resumo será inserido aqui via JS -->
                    </div>
                </div>
                
                <!-- Seleção de Método de Pagamento -->
                <div class="payment-section">
                    <h2><?php _e('Escolha a forma de pagamento', 'rodust-ecommerce'); ?></h2>
                    
                    <div class="payment-methods">
                        
                        <!-- PIX -->
                        <label class="payment-method" data-method="pix">
                            <input type="radio" name="payment_method" value="pix" checked>
                            <div class="method-icon">
                                <svg width="32" height="32" viewBox="0 0 512 512" fill="none">
                                    <path d="M242.4 292.5C247.8 287.1 257.1 287.1 262.5 292.5L339.5 369.5C349.7 379.7 364.6 384 379.4 383.1L394.1 381.9C408.9 380.9 422.2 373.3 431.1 361.1L475.8 307.5C496.3 283.2 496.3 246.8 475.8 222.5L431.1 168.9C422.2 156.7 408.9 149.1 394.1 148.1L379.4 146.9C364.6 145.1 349.7 150.3 339.5 160.5L262.5 237.5C257.1 242.9 247.8 242.9 242.4 237.5L165.4 160.5C155.2 150.3 140.3 145.1 125.5 146.9L110.8 148.1C96 149.1 82.7 156.7 73.8 168.9L29.1 222.5C8.6 246.8 8.6 283.2 29.1 307.5L73.8 361.1C82.7 373.3 96 380.9 110.8 381.9L125.5 383.1C140.3 384 155.2 379.7 165.4 369.5L242.4 292.5Z" fill="url(#paint0_linear)"/>
                                    <defs>
                                        <linearGradient id="paint0_linear" x1="252" y1="148" x2="252" y2="383" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#32BCAD"/>
                                            <stop offset="1" stop-color="#1DB88E"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <div class="method-info">
                                <strong><?php _e('PIX', 'rodust-ecommerce'); ?></strong>
                                <span><?php _e('Aprovação instantânea', 'rodust-ecommerce'); ?></span>
                                <span class="recommended-badge"><?php _e('Recomendado', 'rodust-ecommerce'); ?></span>
                            </div>
                        </label>
                        
                        <!-- Boleto -->
                        <label class="payment-method" data-method="boleto">
                            <input type="radio" name="payment_method" value="boleto">
                            <div class="method-icon">🧾</div>
                            <div class="method-info">
                                <strong><?php _e('Boleto Bancário', 'rodust-ecommerce'); ?></strong>
                                <span><?php _e('Vencimento em 3 dias úteis', 'rodust-ecommerce'); ?></span>
                            </div>
                        </label>
                        
                        <!-- Cartão de Crédito -->
                        <label class="payment-method" data-method="credit_card">
                            <input type="radio" name="payment_method" value="credit_card">
                            <div class="method-icon">💳</div>
                            <div class="method-info">
                                <strong><?php _e('Cartão de Crédito', 'rodust-ecommerce'); ?></strong>
                                <span><?php _e('Parcelamento em até 12x', 'rodust-ecommerce'); ?></span>
                            </div>
                        </label>
                        
                    </div>
                </div>
                
                <!-- Container para formulários específicos de pagamento -->
                <div id="payment-form-container">
                    
                    <!-- Formulário PIX (padrão visível) -->
                    <div id="pix-form" class="payment-form">
                        <div class="info-box">
                            <p><strong>✓</strong> <?php _e('Pagamento instantâneo e seguro', 'rodust-ecommerce'); ?></p>
                            <p><strong>✓</strong> <?php _e('Aprovação em segundos', 'rodust-ecommerce'); ?></p>
                            <p><strong>✓</strong> <?php _e('Disponível 24 horas por dia', 'rodust-ecommerce'); ?></p>
                        </div>
                    </div>
                    
                    <!-- Formulário Boleto -->
                    <div id="boleto-form" class="payment-form hidden">
                        <div class="info-box">
                            <p><strong>⚠️</strong> <?php _e('O boleto vence em 3 dias úteis', 'rodust-ecommerce'); ?></p>
                            <p><strong>ℹ️</strong> <?php _e('Após o pagamento, pode levar até 2 dias úteis para confirmação', 'rodust-ecommerce'); ?></p>
                        </div>
                    </div>
                    
                    <!-- Formulário Cartão de Crédito -->
                    <div id="credit-card-form" class="payment-form hidden">
                        <div id="mercadopago-card-form">
                            <!-- Mercado Pago Card Form será inserido aqui via JS -->
                            <div style="padding: 20px; text-align: center; color: #666;">
                                <p><?php _e('Carregando formulário seguro...', 'rodust-ecommerce'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Botão de Finalizar -->
                <button type="button" id="btn-finalize-payment" class="btn-finalize-payment">
                    <span class="btn-text"><?php _e('Finalizar Pagamento', 'rodust-ecommerce'); ?></span>
                    <span class="btn-icon">🔒</span>
                </button>
                
                <!-- Loading Overlay -->
                <div id="payment-loading" class="hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center;">
                        <div class="spinner" style="margin: 0 auto 20px; width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <p style="margin: 0; font-size: 16px; color: #333;"><?php _e('Processando pagamento...', 'rodust-ecommerce'); ?></p>
                    </div>
                </div>
                
            </div>
            
            <!-- Sidebar: Resumo do Pedido -->
            <div class="order-summary-section">
                <div class="order-summary sticky">
                    <h2><?php _e('Resumo do Pedido', 'rodust-ecommerce'); ?></h2>
                    
                    <!-- Produtos -->
                    <div class="order-items" id="summary-items">
                        <!-- Será preenchido via JS -->
                    </div>
                    
                    <!-- Endereço de Entrega -->
                    <div class="summary-address">
                        <h3><?php _e('Entrega', 'rodust-ecommerce'); ?></h3>
                        <p id="summary-address-text">-</p>
                    </div>
                    
                    <!-- Frete -->
                    <div class="summary-shipping">
                        <h3><?php _e('Frete', 'rodust-ecommerce'); ?></h3>
                        <p id="summary-shipping-text">-</p>
                    </div>
                    
                    <!-- Totais -->
                    <div class="order-totals">
                        <div class="total-row">
                            <span><?php _e('Subtotal', 'rodust-ecommerce'); ?></span>
                            <span class="subtotal-value">R$ 0,00</span>
                        </div>
                        
                        <div class="total-row">
                            <span><?php _e('Frete', 'rodust-ecommerce'); ?></span>
                            <span class="shipping-value">R$ 0,00</span>
                        </div>
                        
                        <div class="total-row total-row-final">
                            <strong><?php _e('Total', 'rodust-ecommerce'); ?></strong>
                            <strong class="total-value">R$ 0,00</strong>
                        </div>
                    </div>
                    
                    <div class="security-badges">
                        <p>
                            <span class="icon">🔒</span>
                            <?php _e('Pagamento 100% seguro', 'rodust-ecommerce'); ?>
                        </p>
                        <p style="font-size: 12px; color: #666; margin-top: 8px;">
                            <?php _e('Seus dados estão protegidos', 'rodust-ecommerce'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<style>
/* Container Principal */
.rodust-payment {
    padding: 40px 0;
    background: #f5f7fa;
    min-height: 100vh;
}

.rodust-payment .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 32px;
    color: #1a202c;
}

/* Layout em 2 Colunas */
.payment-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 32px;
    align-items: start;
}

/* Resumo Mobile (oculto no desktop) */
.order-summary-mobile {
    display: none;
    margin-bottom: 24px;
}

.summary-toggle {
    width: 100%;
    padding: 16px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}

.summary-toggle .total-mobile {
    color: #007bff;
    font-size: 16px;
}

.summary-dropdown {
    margin-top: 12px;
    padding: 16px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

/* Seção de Pagamento */
.payment-section {
    background: white;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 24px;
}

.payment-section h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 24px;
    color: #1a202c;
}

/* Métodos de Pagamento */
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.payment-method:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.payment-method input[type="radio"] {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.payment-method input[type="radio"]:checked + .method-icon + .method-info {
    color: #007bff;
}

.payment-method input[type="radio"]:checked ~ * {
    color: #007bff;
}

.payment-method.selected {
    border-color: #007bff;
    background: #f0f8ff;
}

.method-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.method-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.method-info strong {
    font-size: 16px;
    font-weight: 600;
}

.method-info span {
    font-size: 13px;
    color: #666;
}

.recommended-badge {
    display: inline-block;
    padding: 2px 8px;
    background: #10b981;
    color: white;
    border-radius: 12px;
    font-size: 11px !important;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 4px;
}

/* Formulários de Pagamento */
#payment-form-container {
    margin-bottom: 24px;
}

.payment-form {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.info-box {
    padding: 20px;
    background: #f0f8ff;
    border-left: 4px solid #007bff;
    border-radius: 6px;
}

.info-box p {
    margin: 0 0 8px 0;
    color: #1a202c;
    font-size: 14px;
    line-height: 1.6;
}

.info-box p:last-child {
    margin-bottom: 0;
}

/* Botão Finalizar */
.btn-finalize-payment {
    width: 100%;
    padding: 18px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-finalize-payment:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-finalize-payment:disabled {
    background: #e0e0e0;
    color: #9e9e9e;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

/* Sidebar Resumo */
.order-summary-section {
    position: relative;
}

.order-summary {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.order-summary.sticky {
    position: sticky;
    top: 20px;
}

.order-summary h2 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1a202c;
}

.order-summary h3 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #4a5568;
}

.order-items {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
}

.order-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.item-details {
    flex: 1;
    font-size: 13px;
}

.item-details strong {
    display: block;
    margin-bottom: 4px;
    color: #1a202c;
}

.item-details .quantity {
    color: #666;
    font-size: 12px;
}

.item-price {
    font-weight: 600;
    color: #1a202c;
}

.summary-address,
.summary-shipping {
    padding: 16px 0;
    border-bottom: 1px solid #e0e0e0;
}

.summary-address p,
.summary-shipping p {
    margin: 0;
    font-size: 13px;
    color: #4a5568;
    line-height: 1.6;
}

.order-totals {
    padding-top: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.total-row-final {
    padding-top: 16px;
    margin-top: 16px;
    border-top: 2px solid #e0e0e0;
    font-size: 18px;
}

.security-badges {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e0e0e0;
    text-align: center;
}

.security-badges p {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #4a5568;
    font-size: 14px;
}

/* Spinner Animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 968px) {
    .payment-layout {
        grid-template-columns: 1fr;
    }
    
    .order-summary-mobile {
        display: block;
    }
    
    .order-summary-section {
        display: none;
    }
}

@media (max-width: 640px) {
    .rodust-payment .container {
        padding: 0 16px;
    }
    
    .payment-section {
        padding: 20px;
    }
    
    .btn-finalize-payment {
        font-size: 16px;
        padding: 16px 20px;
    }
}

/* Utility Classes */
.hidden {
    display: none !important;
}

.btn-primary {
    display: inline-block;
    padding: 12px 24px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-1px);
}
</style>

<script>
jQuery(document).ready(function($) {
    let checkoutData = null;
    let selectedPaymentMethod = 'pix';
    
    // Carregar dados do checkout do sessionStorage
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
    
    // Renderizar resumo do pedido
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
    $('#btn-finalize-payment').on('click', function() {
        finalizePay();
    });
    
    function finalizePay() {
        const token = sessionStorage.getItem('customer_token');
        
        if (!token) {
            alert('Sessão expirada. Faça login novamente.');
            window.location.href = '<?php echo home_url('/login'); ?>';
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
        
        console.log('Dados do carrinho:', checkoutData.cart);
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
                // TODO: Adicionar dados do cartão quando implementar formulário
                // orderData.card_token = ...
                // orderData.installments = ...
                break;
            default:
                alert('Método de pagamento inválido');
                $('#payment-loading').addClass('hidden');
                return;
        }
        
        // Criar pedido + pagamento via API Laravel
        $.ajax({
            url: window.RODUST_API_URL + endpoint,
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
                    
                    // Salvar dados do pagamento (agora com estrutura completa: order + payment)
                    const paymentData = {
                        order_number: response.data.order?.order_number || '',
                        order_id: response.data.order?.id || '',
                        order: response.data.order || {},
                        ...response.data.payment // qr_code, qr_code_base64, etc
                    };
                    sessionStorage.setItem('payment_data', JSON.stringify(paymentData));
                    
                    // Limpar dados do checkout
                    sessionStorage.removeItem('checkout_data');
                    
                    // Redirecionar para página de confirmação com dados do pagamento
                    window.location.href = '<?php echo home_url('/pedido-confirmado'); ?>?order=' + response.data.order.id + '&payment=' + selectedPaymentMethod;
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
    
    function clearCart() {
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            method: 'POST',
            data: {
                action: 'rodust_clear_cart',
                nonce: '<?php echo wp_create_nonce('rodust_ecommerce_nonce'); ?>'
            }
        });
    }
    
    // Inicializar
    if (loadCheckoutData()) {
        console.log('Página de pagamento carregada com sucesso');
    }
});
</script>
