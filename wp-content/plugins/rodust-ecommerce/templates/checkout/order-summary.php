<!-- Resumo do Pedido -->
<div class="order-summary-section">
    <div class="order-summary sticky">
        <h2><?php _e('Resumo do Pedido', 'rodust-ecommerce'); ?></h2>
        
        <div class="order-items">
            <?php foreach ($cart_items as $item) : ?>
                <div class="order-item">
                    <?php if ($item['image']) : ?>
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                    <?php endif; ?>
                    <div class="item-details">
                        <strong><?php echo esc_html($item['name']); ?></strong>
                        <span class="quantity">Qtd: <?php echo esc_html($item['quantity']); ?></span>
                    </div>
                    <div class="item-price">
                        R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-totals">
            <div class="total-row">
                <span><?php _e('Subtotal', 'rodust-ecommerce'); ?></span>
                <span class="subtotal-value">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
            </div>
            
            <div class="total-row shipping-row">
                <span><?php _e('Frete', 'rodust-ecommerce'); ?></span>
                <span class="shipping-value">A calcular</span>
            </div>
            
            <div class="total-row total-row-final">
                <strong><?php _e('Total', 'rodust-ecommerce'); ?></strong>
                <strong class="total-value">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></strong>
            </div>
        </div>
        
        <button type="button" id="btn-continue-payment" class="btn-continue-payment" disabled>
            <span class="btn-text">Continuar para Pagamento</span>
            <span class="btn-arrow">→</span>
        </button>
        
        <div class="security-badges">
            <p>
                <span class="icon">🔒</span>
                <?php _e('Pagamento 100% seguro', 'rodust-ecommerce'); ?>
            </p>
        </div>
    </div>
</div>
