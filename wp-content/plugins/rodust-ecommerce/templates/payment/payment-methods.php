<?php
/**
 * Payment - Payment Methods Selection
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

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
