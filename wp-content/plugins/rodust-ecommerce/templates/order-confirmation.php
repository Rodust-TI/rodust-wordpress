<?php
/**
 * Template: Confirmação de Pedido
 * 
 * Exibe detalhes do pedido, QR Code PIX e status do pagamento
 */

defined('ABSPATH') || exit;

// Obter dados da URL
$order_id = isset($_GET['order']) ? intval($_GET['order']) : 0;
$payment_method = isset($_GET['payment']) ? sanitize_text_field($_GET['payment']) : '';

if (!$order_id) {
    echo '<div class="rodust-error">Pedido não encontrado.</div>';
    return;
}

// Obter dados do pagamento do sessionStorage (via JavaScript)
?>

<style>
    .order-confirmation-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    }

    .confirmation-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
    }

    .confirmation-header .icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .confirmation-header h1 {
        margin: 0 0 10px 0;
        font-size: 32px;
        font-weight: 700;
    }

    .confirmation-header p {
        margin: 0;
        font-size: 18px;
        opacity: 0.9;
    }

    .order-number {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        margin-top: 15px;
    }

    .payment-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .payment-section h2 {
        margin: 0 0 20px 0;
        font-size: 24px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .payment-method-badge {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* PIX */
    .pix-container {
        text-align: center;
        padding: 20px;
    }

    .qr-code-wrapper {
        background: white;
        padding: 20px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .qr-code-wrapper img {
        max-width: 300px;
        width: 100%;
        height: auto;
        display: block;
    }

    .pix-code-container {
        background: #f7f7f7;
        border: 2px dashed #667eea;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        position: relative;
    }

    .pix-code {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        word-break: break-all;
        color: #333;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .copy-button {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .copy-button:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .copy-button.copied {
        background: #10b981;
    }

    /* Instruções */
    .instructions {
        background: #f0f9ff;
        border-left: 4px solid #3b82f6;
        padding: 20px;
        margin: 20px 0;
        border-radius: 8px;
    }

    .instructions h3 {
        margin: 0 0 15px 0;
        color: #1e40af;
        font-size: 18px;
    }

    .instructions ol {
        margin: 0;
        padding-left: 20px;
    }

    .instructions li {
        margin-bottom: 10px;
        line-height: 1.6;
        color: #334155;
    }

    /* Detalhes do Pedido */
    .order-details {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .order-details h2 {
        margin: 0 0 20px 0;
        font-size: 24px;
        color: #333;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: #6b7280;
    }

    .detail-value {
        font-weight: 600;
        color: #111827;
    }

    .detail-value.total {
        font-size: 20px;
        color: #667eea;
    }

    /* Status */
    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }

    /* Loading */
    .loading {
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }

    .loading-spinner {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Erro */
    .rodust-error {
        background: #fee2e2;
        border: 1px solid #ef4444;
        color: #991b1b;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin: 40px auto;
        max-width: 600px;
    }

    /* Botões de Ação */
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    @media (max-width: 768px) {
        .order-confirmation-container {
            padding: 15px;
        }

        .confirmation-header h1 {
            font-size: 24px;
        }

        .payment-section,
        .order-details {
            padding: 20px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="order-confirmation-container">
    <div class="confirmation-header">
        <div class="icon">✓</div>
        <h1>Pedido Realizado com Sucesso!</h1>
        <p>Obrigado por confiar na Rodust</p>
        <p style="font-size: 14px; color: #666; margin-top: 8px;">Após o pagamento seu pedido será separado</p>
        <div class="order-number" id="orderNumber">
            <div class="loading-spinner" style="width: 20px; height: 20px; border-width: 2px; margin: 0;"></div>
        </div>
    </div>

    <!-- Loading -->
    <div id="loadingSection" class="loading">
        <div class="loading-spinner"></div>
        <p>Carregando informações do pedido...</p>
    </div>

    <!-- Seção de Pagamento (será preenchida via JS) -->
    <div id="paymentSection" style="display: none;"></div>

    <!-- Detalhes do Pedido -->
    <div id="orderDetailsSection" class="order-details" style="display: none;">
        <h2>📋 Detalhes do Pedido</h2>
        <div id="orderDetailsContent"></div>
    </div>

    <!-- Botões de Ação -->
    <div class="action-buttons">
        <a href="<?php echo home_url('/produtos'); ?>" class="btn btn-primary">
            ← Continuar Comprando
        </a>
        <a href="<?php echo home_url('/minha-conta?tab=pedidos'); ?>" class="btn btn-primary">
            Ver Meus Pedidos →
        </a>
    </div>
</div>

<script>
(function() {
    const orderId = <?php echo $order_id; ?>;
    const paymentMethod = '<?php echo esc_js($payment_method); ?>';

    console.log('Ordem ID:', orderId);
    console.log('Método de pagamento:', paymentMethod);

    // Buscar dados do sessionStorage
    const paymentDataStr = sessionStorage.getItem('payment_data');
    const checkoutDataStr = sessionStorage.getItem('checkout_data');

    console.log('Payment data (sessionStorage):', paymentDataStr);
    console.log('Checkout data (sessionStorage):', checkoutDataStr);

    if (!paymentDataStr) {
        document.getElementById('loadingSection').innerHTML = 
            '<div class="rodust-error">Dados do pagamento não encontrados. Por favor, entre em contato com o suporte.</div>';
        return;
    }

    const paymentData = JSON.parse(paymentDataStr);
    const checkoutData = checkoutDataStr ? JSON.parse(checkoutDataStr) : null;

    console.log('Payment data parsed:', paymentData);
    console.log('Checkout data parsed:', checkoutData);

    // Ocultar loading
    document.getElementById('loadingSection').style.display = 'none';

    // Atualizar número do pedido
    document.getElementById('orderNumber').textContent = paymentData.order_number || `#${orderId}`;

    // Renderizar seção de pagamento baseado no método
    renderPaymentSection(paymentMethod, paymentData);

    // Renderizar detalhes do pedido
    renderOrderDetails(paymentData, checkoutData);

    /**
     * Renderizar seção de pagamento
     */
    function renderPaymentSection(method, data) {
        const section = document.getElementById('paymentSection');
        let html = '';

        if (method === 'pix') {
            html = `
                <div class="payment-section">
                    <h2>
                        💳 Pagamento via PIX
                        <span class="payment-method-badge">PIX</span>
                    </h2>
                    
                    <div class="pix-container">
                        ${data.qr_code_base64 ? `
                            <div class="qr-code-wrapper">
                                <img src="data:image/png;base64,${data.qr_code_base64}" alt="QR Code PIX">
                            </div>
                        ` : ''}
                        
                        <div class="pix-code-container">
                            <p style="margin: 0 0 10px 0; font-weight: 600; color: #333;">Código PIX Copia e Cola:</p>
                            <div class="pix-code">${data.qr_code || 'Código não disponível'}</div>
                            <button class="copy-button" onclick="copyPixCode('${data.qr_code}')">
                                📋 Copiar Código PIX
                            </button>
                        </div>

                        <div class="instructions">
                            <h3>📱 Como pagar com PIX:</h3>
                            <ol>
                                <li>Abra o app do seu banco</li>
                                <li>Acesse a área PIX</li>
                                <li>Escolha "Pagar com QR Code" ou "Pix Copia e Cola"</li>
                                <li>Escaneie o QR Code acima ou cole o código</li>
                                <li>Confirme o pagamento</li>
                            </ol>
                            <p style="margin: 15px 0 0 0; color: #dc2626; font-weight: 600;">
                                ⏰ O pagamento deve ser realizado em até 30 minutos
                            </p>
                        </div>
                    </div>
                </div>
            `;
        } else if (method === 'boleto') {
            html = `
                <div class="payment-section">
                    <h2>
                        🧾 Pagamento via Boleto
                        <span class="payment-method-badge">Boleto</span>
                    </h2>
                    
                    <div style="text-align: center; padding: 20px;">
                        ${data.ticket_url ? `
                            <a href="${data.ticket_url}" target="_blank" class="btn btn-primary" style="display: inline-flex; margin-bottom: 20px;">
                                📄 Visualizar/Imprimir Boleto
                            </a>
                        ` : ''}

                        <div class="instructions">
                            <h3>📋 Instruções:</h3>
                            <ol>
                                <li>Clique no botão acima para visualizar o boleto</li>
                                <li>Você pode imprimir ou pagar online</li>
                                <li>O boleto também foi enviado por e-mail</li>
                                <li>Vencimento: até 3 dias úteis</li>
                            </ol>
                            <p style="margin: 15px 0 0 0; color: #dc2626; font-weight: 600;">
                                ⏰ Após o pagamento, o pedido será processado em até 2 dias úteis
                            </p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            html = `
                <div class="payment-section">
                    <h2>💳 Pagamento</h2>
                    <p>Método: ${method}</p>
                    <p>Status: Aguardando confirmação</p>
                </div>
            `;
        }

        section.innerHTML = html;
        section.style.display = 'block';
    }

    /**
     * Renderizar detalhes do pedido
     */
    function renderOrderDetails(payment, checkout) {
        const section = document.getElementById('orderDetailsSection');
        const content = document.getElementById('orderDetailsContent');

        // Usar dados da API (payment.order) se disponível, senão fallback para checkout
        const order = payment.order || {};
        const subtotal = order.subtotal || (checkout?.cart?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0);
        const shipping = order.shipping || parseFloat(checkout?.shipping?.price || 0);
        const total = order.total || (subtotal + shipping);
        const items = order.items || checkout?.cart || [];

        let html = `
            <div class="detail-row">
                <span class="detail-label">Pedido</span>
                <span class="detail-value">${payment.order_number || order.order_number || `#${orderId}`}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="status-badge status-pending">Aguardando Pagamento</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Método de Pagamento</span>
                <span class="detail-value">${paymentMethod.toUpperCase()}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Subtotal</span>
                <span class="detail-value">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Frete</span>
                <span class="detail-value">R$ ${shipping.toFixed(2).replace('.', ',')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total</span>
                <span class="detail-value total">R$ ${total.toFixed(2).replace('.', ',')}</span>
            </div>
        `;

        content.innerHTML = html;
        section.style.display = 'block';
    }

    /**
     * Copiar código PIX
     */
    window.copyPixCode = function(code) {
        if (!code || code === 'Código não disponível') {
            alert('Código PIX não disponível');
            return;
        }

        navigator.clipboard.writeText(code).then(() => {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '✓ Código Copiado!';
            btn.classList.add('copied');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('copied');
            }, 3000);
        }).catch(err => {
            console.error('Erro ao copiar:', err);
            alert('Erro ao copiar código. Tente selecionar e copiar manualmente.');
        });
    };
})();
</script>
