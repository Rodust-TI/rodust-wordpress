<?php
/**
 * Template: Payment Page
 * 
 * Shortcode: [rodust_payment]
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

// Enqueue assets
wp_enqueue_style('rodust-payment', plugins_url('../assets/css/payment.css', __FILE__), [], '1.0.0');

// Mercado Pago SDK (carregado antes dos nossos scripts)
wp_enqueue_script('mercadopago-sdk', 'https://sdk.mercadopago.com/js/v2', [], null, false);

// Nossos scripts
wp_enqueue_script('rodust-payment', plugins_url('../assets/js/payment.js', __FILE__), ['jquery'], '1.0.0', true);
wp_enqueue_script('rodust-mercadopago-card', plugins_url('../assets/js/mercadopago-card.js', __FILE__), ['jquery', 'mercadopago-sdk'], '1.0.0', true);

// Localize script com variáveis PHP
wp_localize_script('rodust-payment', 'RODUST_PAYMENT', [
    'api_url' => rodust_plugin_get_api_url(),
    'home_url' => home_url(),
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('rodust_ecommerce_nonce'),
]);
?>

<div class="rodust-payment">
    <div class="container">
        
        <h1 class="page-title"><?php _e('Pagamento', 'rodust-ecommerce'); ?></h1>
        
        <!-- Mensagem de erro se não houver dados -->
        <div id="no-checkout-data" class="hidden" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; text-align: center; margin: 40px 0;">
            <p style="margin: 0 0 16px 0; color: #856404; font-size: 16px;">
                ⚠️ <?php _e('Nenhum dado de checkout encontrado.', 'rodust-ecommerce'); ?>
            </p>
            <a href="<?php echo home_url('/checkout'); ?>" class="btn-primary">
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
                <?php include __DIR__ . '/payment/payment-methods.php'; ?>
                
                <!-- Container para formulários específicos de pagamento -->
                <div id="payment-form-container">
                    <?php include __DIR__ . '/payment/pix-form.php'; ?>
                    <?php include __DIR__ . '/payment/boleto-form.php'; ?>
                    <?php include __DIR__ . '/payment/card-form.php'; ?>
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
            <?php include __DIR__ . '/payment/order-review.php'; ?>
            
        </div>
        
    </div>
</div>
