/**
 * Checkout Form Management
 * Handles form validation and payment flow
 */

// Habilitar/desabilitar botão de continuar
function enableContinueButton() {
    const hasShipping = selectedShipping !== null;
    const hasAddress = jQuery('#address_zipcode').val() && jQuery('#address_zipcode').val().replace(/\D/g, '').length === 8;
    
    if (hasShipping && hasAddress) {
        jQuery('#btn-continue-payment').prop('disabled', false);
    } else {
        jQuery('#btn-continue-payment').prop('disabled', true);
    }
}

// Evento: Continuar para Pagamento
jQuery(document).on('click', '#btn-continue-payment', function() {
    if (!selectedShipping) {
        showToast('Por favor, selecione uma opção de frete.', 'warning');
        return;
    }
    
    // Verificar se checkbox de salvar endereço está marcado
    const shouldSaveAddress = jQuery('#save_address').is(':checked');
    
    if (shouldSaveAddress && customerData) {
        // Salvar endereço antes de continuar
        saveNewAddress(function() {
            // Após salvar, continuar para pagamento
            proceedToPayment();
        });
    } else {
        // Continuar direto para pagamento
        proceedToPayment();
    }
});

// Função para salvar novo endereço
function saveNewAddress(callback) {
    const token = sessionStorage.getItem('customer_token');
    const zipcode = jQuery('#address_zipcode').val().replace(/\D/g, '');
    
    const addressData = {
        is_shipping: 1,
        is_billing: 0,
        label: 'Endereço Principal',
        zipcode: zipcode,
        address: jQuery('#address_address').val(),
        number: jQuery('#address_number').val(),
        complement: jQuery('#address_complement').val() || null,
        neighborhood: jQuery('#address_neighborhood').val(),
        city: jQuery('#address_city').val(),
        state: jQuery('#address_state').val(),
        country: 'BR'
    };
    
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/customers/addresses',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        data: JSON.stringify(addressData),
        success: function(response) {
            console.log('Endereço salvo e sincronizado com Bling:', response);
            if (callback) callback();
        },
        error: function(xhr) {
            console.error('Erro ao salvar endereço:', xhr);
            // Continuar mesmo se falhar ao salvar
            if (callback) callback();
        }
    });
}

// Função para prosseguir para página de pagamento
function proceedToPayment() {
    // Preparar dados do endereço
    const shippingAddress = {
        postal_code: jQuery('#address_zipcode').val(),
        street: jQuery('#address_address').val(),
        number: jQuery('#address_number').val(),
        complement: jQuery('#address_complement').val(),
        neighborhood: jQuery('#address_neighborhood').val(),
        city: jQuery('#address_city').val(),
        state: jQuery('#address_state').val()
    };

    // Salvar dados no sessionStorage para usar na próxima página
    const checkoutData = {
        customer: customerData,
        shipping_address: shippingAddress,
        shipping: selectedShipping,
        cart: RODUST_CHECKOUT_DATA.cart_items
    };
    
    sessionStorage.setItem('checkout_data', JSON.stringify(checkoutData));
    
    // Redirecionar para página de pagamento
    window.location.href = RODUST_CHECKOUT_DATA.payment_url;
}

// Submeter formulário
jQuery(document).on('submit', '#checkout-form', function(e) {
    e.preventDefault();
    
    // Validar se o frete foi calculado e selecionado
    if (!selectedShipping) {
        showToast('Por favor, calcule e selecione uma opção de frete antes de continuar.', 'error');
        jQuery('#shipping-section')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    // Preparar dados do pedido incluindo frete
    const orderData = {
        shipping: {
            service_id: selectedShipping.id,
            service_name: selectedShipping.name,
            company: selectedShipping.company,
            company_picture: selectedShipping.company_logo,
            price: selectedShipping.price,
            delivery_time: selectedShipping.delivery_time
        }
    };
    
    console.log('Dados do pedido com frete:', orderData);
});
