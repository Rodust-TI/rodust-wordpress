<?php
/**
 * Payment - Order Review Summary
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

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
