<?php
/**
 * Payment - Boleto Payment Form
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

<div id="boleto-form" class="payment-form hidden">
    <div class="info-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0 0 10px 0;"><strong>⚠️</strong> <?php _e('O boleto vence em 3 dias úteis', 'rodust-ecommerce'); ?></p>
        <p style="margin: 0;"><strong>ℹ️</strong> <?php _e('Após o pagamento, pode levar até 2 dias úteis para confirmação', 'rodust-ecommerce'); ?></p>
    </div>
    
    <div class="form-instructions" style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
        <h4 style="margin: 0 0 10px 0;"><?php _e('Como funciona:', 'rodust-ecommerce'); ?></h4>
        <ol style="margin: 0; padding-left: 20px;">
            <li><?php _e('Clique em "Finalizar Pagamento"', 'rodust-ecommerce'); ?></li>
            <li><?php _e('O boleto será gerado automaticamente', 'rodust-ecommerce'); ?></li>
            <li><?php _e('Você pode pagar pela linha digitável ou código de barras', 'rodust-ecommerce'); ?></li>
            <li><?php _e('Após a confirmação do pagamento, seu pedido será processado', 'rodust-ecommerce'); ?></li>
        </ol>
    </div>
</div>
