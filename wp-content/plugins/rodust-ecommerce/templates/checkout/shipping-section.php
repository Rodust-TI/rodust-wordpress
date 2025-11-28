<!-- Frete / Entrega -->
<div class="checkout-section" id="shipping-section">
    <h2><?php _e('Frete e Entrega', 'rodust-ecommerce'); ?></h2>
    
    <!-- Status do cálculo -->
    <div id="shipping-status" style="display: none; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
        <p style="margin: 0; font-size: 14px;"></p>
    </div>
    
    <!-- Botão calcular frete (aparece se não tiver CEP) -->
    <div id="shipping-calculate-prompt" style="display: none; padding: 16px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; margin-bottom: 16px;">
        <p style="margin: 0 0 12px 0; font-size: 14px; color: #856404;">
            <strong>📦 Calcule o frete</strong><br>
            Informe seu CEP de entrega para calcular o frete.
        </p>
        <button type="button" id="btn-calculate-shipping" class="btn btn-primary">
            Calcular Frete
        </button>
    </div>
    
    <!-- Loader -->
    <div id="shipping-loader" style="display: none; text-align: center; padding: 20px;">
        <div class="spinner"></div>
        <p style="margin-top: 12px; color: #666;">Calculando opções de frete...</p>
    </div>
    
    <!-- Opções de frete -->
    <div id="shipping-options" style="display: none;">
        <p style="margin: 0 0 12px 0; font-size: 14px; color: #666;">
            Selecione a forma de entrega:
        </p>
        <div id="shipping-options-list"></div>
    </div>
</div>
