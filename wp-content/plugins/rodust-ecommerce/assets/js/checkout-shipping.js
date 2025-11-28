/**
 * Checkout Shipping Management
 * Handles shipping calculation and option selection
 */

let selectedShipping = null;

// Calcular frete
function calculateShipping() {
    // Garantir que CHECKOUT_CART_ITEMS seja um array
    if (!Array.isArray(RODUST_CHECKOUT_DATA.cart_items)) {
        console.error('cart_items não é um array:', RODUST_CHECKOUT_DATA.cart_items);
        showToast('Erro: Carrinho não carregado corretamente. Recarregue a página.', 'error');
        return;
    }
    
    // Obter CEP do endereço selecionado ou formulário
    let postalCode = jQuery('#address_zipcode').val().replace(/\D/g, '');
    
    if (!postalCode || postalCode.length !== 8) {
        showToast('Informe um CEP válido para calcular o frete.', 'error');
        return;
    }
    
    // Verificar se há produtos no carrinho
    if (!RODUST_CHECKOUT_DATA.cart_items || RODUST_CHECKOUT_DATA.cart_items.length === 0) {
        showToast('Carrinho vazio. Adicione produtos para calcular o frete.', 'error');
        return;
    }
    
    // Preparar dados dos produtos com dimensões reais
    const products = RODUST_CHECKOUT_DATA.cart_items.map(item => ({
        id: String(item.id),
        width: item.width,
        height: item.height,
        length: item.length,
        weight: item.weight,
        quantity: item.quantity,
        insurance_value: parseFloat(item.price)
    }));
    
    console.log('Calculando frete para:', { postalCode, products });
    
    // Mostrar loader
    jQuery('#shipping-status').hide();
    jQuery('#shipping-calculate-prompt').hide();
    jQuery('#shipping-options').hide();
    jQuery('#shipping-loader').show();
    
    // Fazer chamada à API
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/shipping/calculate',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            postal_code: postalCode,
            products: products
        }),
        success: function(response) {
            jQuery('#shipping-loader').hide();
            
            if (response.success && response.data && response.data.length > 0) {
                renderShippingOptions(response.data);
            } else {
                jQuery('#shipping-status')
                    .html('<p style="color: #dc3545;">Nenhuma opção de frete disponível para este CEP.</p>')
                    .show();
            }
        },
        error: function(xhr) {
            jQuery('#shipping-loader').hide();
            
            let errorMsg = 'Erro ao calcular frete. Tente novamente.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            jQuery('#shipping-status')
                .html(`<p style="color: #dc3545;">${errorMsg}</p>`)
                .show();
        }
    });
}

// Renderizar opções de frete
function renderShippingOptions(options) {
    console.log('Opções de frete recebidas:', options);
    
    const $list = jQuery('#shipping-options-list');
    $list.empty();
    
    options.forEach((option, index) => {
        const isSelected = selectedShipping && selectedShipping.id === option.id;
        
        // Logo - API retorna company_logo diretamente
        const logoUrl = option.company_logo || '/wp-content/plugins/rodust-ecommerce/assets/images/shipping-default.svg';
        
        // Nome da transportadora - API retorna company como string
        const companyName = option.company || 'Transportadora';
        
        // Melhorar nome do serviço (adicionar transportadora se for genérico)
        let serviceName = option.name;
        if (serviceName === '.Com' || serviceName === '.Package') {
            serviceName = `${companyName} ${serviceName}`;
        }
        
        const $option = jQuery(`
            <div class="shipping-option ${isSelected ? 'selected' : ''}" data-shipping-id="${option.id}">
                <input type="radio" name="shipping_method" value="${option.id}" ${isSelected ? 'checked' : ''}>
                <img src="${logoUrl}" alt="${companyName}" class="shipping-option-logo" onerror="this.src='/wp-content/plugins/rodust-ecommerce/assets/images/shipping-default.svg'">
                <div class="shipping-option-info">
                    <div class="shipping-option-name">${serviceName}</div>
                    <div class="shipping-option-company">${companyName}</div>
                    <div class="shipping-option-delivery">📦 ${option.delivery_time} dias úteis</div>
                </div>
                <div class="shipping-option-price">
                    <div class="shipping-option-price-value">R$ ${parseFloat(option.price).toFixed(2).replace('.', ',')}</div>
                </div>
            </div>
        `);
        
        // Adicionar evento de clique
        $option.on('click', function() {
            jQuery('.shipping-option').removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery(this).find('input[type="radio"]').prop('checked', true);
            
            selectedShipping = {
                id: option.id,
                name: serviceName,
                company: companyName,
                company_logo: logoUrl,
                price: parseFloat(option.price),
                delivery_time: option.delivery_time
            };
            
            updateOrderTotal();
            enableContinueButton();
        });
        
        $list.append($option);
    });
    
    jQuery('#shipping-options').show();
}

// Atualizar total do pedido com frete
function updateOrderTotal() {
    // Obter subtotal do carrinho
    const subtotal = RODUST_CHECKOUT_DATA.cart_items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    
    // Adicionar frete se selecionado
    const shippingCost = selectedShipping ? selectedShipping.price : 0;
    const total = subtotal + shippingCost;
    
    // Atualizar subtotal (já está correto no HTML)
    jQuery('.subtotal-value').text(`R$ ${subtotal.toFixed(2).replace('.', ',')}`);
    
    // Atualizar frete na sidebar
    if (selectedShipping) {
        jQuery('.shipping-value').html(`
            <span style="color: #10b981;">${selectedShipping.company}</span><br>
            <strong>R$ ${shippingCost.toFixed(2).replace('.', ',')}</strong>
        `);
    } else {
        jQuery('.shipping-value').text('A calcular');
    }
    
    // Atualizar total
    jQuery('.total-value').text(`R$ ${total.toFixed(2).replace('.', ',')}`);
}

// Evento: calcular frete ao clicar no botão
jQuery(document).on('click', '#btn-calculate-shipping', function() {
    calculateShipping();
});

// Evento: mostrar botão calcular quando CEP for preenchido manualmente
jQuery(document).on('input', '#address_zipcode', function() {
    const cep = jQuery(this).val().replace(/\D/g, '');
    if (cep.length === 8) {
        jQuery('#shipping-calculate-prompt').show();
    }
});
