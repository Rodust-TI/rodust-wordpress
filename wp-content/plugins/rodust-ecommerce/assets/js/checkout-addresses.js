/**
 * Checkout Addresses Management
 * Handles address selection, modal, and CEP lookup
 */

let savedAddresses = [];

// Buscar endereços salvos
function loadSavedAddresses() {
    const token = sessionStorage.getItem('customer_token');
    
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/customers/addresses',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success && response.data.addresses) {
                savedAddresses = response.data.addresses;
                displayAddresses();
            }
        },
        error: function(xhr) {
            console.error('Erro ao carregar endereços:', xhr);
        }
    });
}

// Exibir endereços: box com padrão ou formulário
function displayAddresses() {
    // Procurar endereço de entrega (is_shipping = true)
    const shippingAddress = savedAddresses.find(addr => addr.is_shipping === true || addr.is_shipping === 1);
    
    if (shippingAddress) {
        // Mostrar box com endereço selecionado
        showSelectedAddress(shippingAddress);
    } else if (savedAddresses.length > 0) {
        // Se não tem endereço de entrega mas tem endereços, mostrar lista
        showAddressesList();
    } else {
        // Se não tem nenhum endereço, mostrar formulário
        jQuery('#selected-address-box').hide();
        jQuery('#addresses-list-section').hide();
        jQuery('#address-form-section').show();
    }
}

// Mostrar box com endereço selecionado
function showSelectedAddress(address) {
    const label = address.label || 'Endereço de Entrega';
    const line1 = `${address.address}, ${address.number}${address.complement ? ' - ' + address.complement : ''}`;
    const line2 = `${address.neighborhood} - ${address.city}/${address.state} - CEP ${formatCEP(address.zipcode)}`;
    
    jQuery('#selected-address-label').text(label);
    jQuery('#selected-address-line1').text(line1);
    jQuery('#selected-address-line2').text(line2);
    
    // Preencher campos do formulário principal com endereço selecionado
    fillAddressFields(address);
    
    jQuery('#selected-address-box').show();
    jQuery('#addresses-list-section').hide();
    jQuery('#address-form-section').hide();
    
    // Mostrar prompt para calcular frete
    jQuery('#shipping-calculate-prompt').show();
    jQuery('#shipping-options').hide();
    selectedShipping = null;
    updateOrderTotal();
}

// Preencher campos do formulário
function fillAddressFields(address) {
    jQuery('#address_zipcode').val(formatCEP(address.zipcode));
    jQuery('#address_address').val(address.address);
    jQuery('#address_number').val(address.number);
    jQuery('#address_complement').val(address.complement || '');
    jQuery('#address_neighborhood').val(address.neighborhood);
    jQuery('#address_city').val(address.city);
    jQuery('#address_state').val(address.state);
}

// Mostrar lista de endereços
function showAddressesList() {
    const $list = jQuery('#addresses-list');
    $list.empty();
    
    if (savedAddresses.length === 0) {
        $list.html('<p style="color: #666; text-align: center; padding: 20px;">Nenhum endereço cadastrado</p>');
    } else {
        savedAddresses.forEach(function(addr) {
            const isShipping = addr.is_shipping === true || addr.is_shipping === 1;
            const isBilling = addr.is_billing === true || addr.is_billing === 1;
            
            let badge = '';
            if (isShipping) badge += '<span style="background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-right: 4px;">ENTREGA</span>';
            if (isBilling) badge += '<span style="background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">COBRANÇA</span>';
            
            const label = addr.label || (isShipping ? 'Endereço de Entrega' : (isBilling ? 'Endereço de Cobrança' : 'Endereço'));
            const line1 = `${addr.address}, ${addr.number}${addr.complement ? ' - ' + addr.complement : ''}`;
            const line2 = `${addr.neighborhood} - ${addr.city}/${addr.state}`;
            
            const html = `
                <div style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px; margin-bottom: 10px; cursor: pointer; transition: border-color 0.2s;" 
                     data-address-id="${addr.id}"
                     onclick="selectAddress(${addr.id})">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div style="margin-bottom: 4px;">
                                <strong>${label}</strong> ${badge}
                            </div>
                            <p style="margin: 2px 0; font-size: 14px;">${line1}</p>
                            <p style="margin: 2px 0; font-size: 14px; color: #666;">${line2}</p>
                            <p style="margin: 2px 0; font-size: 13px; color: #999;">CEP ${formatCEP(addr.zipcode)}</p>
                        </div>
                    </div>
                </div>
            `;
            
            $list.append(html);
        });
    }
    
    jQuery('#selected-address-box').hide();
    jQuery('#addresses-list-section').show();
    jQuery('#address-form-section').hide();
}

// Selecionar endereço da lista
window.selectAddress = function(addressId) {
    const address = savedAddresses.find(a => a.id === addressId);
    if (address) {
        showSelectedAddress(address);
    }
};

// Botão "Trocar endereço"
jQuery(document).on('click', '#btn-change-address', function() {
    showAddressesList();
});

// Botão "Adicionar outro endereço"
jQuery(document).on('click', '#btn-add-new-address', function() {
    openNewAddressModal();
});

// Abrir modal de novo endereço
function openNewAddressModal() {
    // Verificar limite de 5 endereços
    if (savedAddresses.length >= 5) {
        showToast('Limite atingido: 5 endereços. Para cadastrar um novo endereço é necessário apagar um endereço anterior.', 'error');
        return;
    }
    
    // Limpar campos
    jQuery('#modal_postal_code').val('');
    jQuery('#modal_street').val('');
    jQuery('#modal_number').val('');
    jQuery('#modal_complement').val('');
    jQuery('#modal_neighborhood').val('');
    jQuery('#modal_city').val('');
    jQuery('#modal_state').val('');
    jQuery('#modal_label').val('');
    
    jQuery('#new-address-modal').css('display', 'flex');
}

// Fechar modal
jQuery(document).on('click', '#btn-cancel-new-address', function() {
    jQuery('#new-address-modal').hide();
});

// Buscar CEP no modal
jQuery(document).on('click', '#modal-search-postal-code', function() {
    const zipcode = jQuery('#modal_postal_code').val().replace(/\D/g, '');
    
    if (zipcode.length !== 8) {
        showToast('Digite um CEP válido', 'error');
        return;
    }
    
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/addresses/search-zipcode/' + zipcode,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                jQuery('#modal_street').val(response.data.address);
                jQuery('#modal_complement').val(response.data.complement);
                jQuery('#modal_neighborhood').val(response.data.neighborhood);
                jQuery('#modal_city').val(response.data.city);
                jQuery('#modal_state').val(response.data.state);
                jQuery('#modal_number').focus();
            }
        },
        error: function() {
            showToast('CEP não encontrado', 'error');
        }
    });
});

// Máscara CEP no modal
jQuery(document).on('input', '#modal_postal_code', function() {
    let value = jQuery(this).val().replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    jQuery(this).val(value);
});

// Salvar novo endereço
jQuery(document).on('click', '#btn-save-new-address', function() {
    const zipcode = jQuery('#modal_postal_code').val().replace(/\D/g, '');
    
    if (!zipcode || !jQuery('#modal_street').val() || !jQuery('#modal_number').val() || 
        !jQuery('#modal_neighborhood').val() || !jQuery('#modal_city').val() || !jQuery('#modal_state').val()) {
        showToast('Preencha todos os campos obrigatórios', 'error');
        return;
    }
    
    const token = sessionStorage.getItem('customer_token');
    const data = {
        type: null, // Endereço adicional
        label: jQuery('#modal_label').val(),
        zipcode: zipcode,
        address: jQuery('#modal_street').val(),
        number: jQuery('#modal_number').val(),
        complement: jQuery('#modal_complement').val(),
        neighborhood: jQuery('#modal_neighborhood').val(),
        city: jQuery('#modal_city').val(),
        state: jQuery('#modal_state').val(),
        is_default: false
    };
    
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/customers/addresses',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        data: JSON.stringify(data),
        success: function(response) {
            showToast('Endereço adicionado com sucesso!', 'success');
            jQuery('#new-address-modal').hide();
            loadSavedAddresses(); // Recarregar lista
        },
        error: function(xhr) {
            let errorMsg = 'Erro ao salvar endereço.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast(errorMsg, 'error');
        }
    });
});

// Buscar CEP no formulário principal (usando ViaCEP)
jQuery(document).on('click', '#btn-search-cep', function() {
    const cep = jQuery('#address_zipcode').val().replace(/\D/g, '');
    
    if (cep.length !== 8) {
        showToast('CEP inválido. Digite 8 dígitos.', 'error');
        return;
    }
    
    jQuery.ajax({
        url: `https://viacep.com.br/ws/${cep}/json/`,
        method: 'GET',
        success: function(data) {
            if (!data.erro) {
                jQuery('#address_address').val(data.logradouro);
                jQuery('#address_neighborhood').val(data.bairro);
                jQuery('#address_city').val(data.localidade);
                jQuery('#address_state').val(data.uf);
                jQuery('#address_number').focus();
            } else {
                showToast('CEP não encontrado.', 'error');
            }
        },
        error: function() {
            showToast('Erro ao buscar CEP. Tente novamente.', 'error');
        }
    });
});

// Máscara CEP no formulário principal
jQuery(document).on('input', '#address_zipcode', function() {
    let value = jQuery(this).val().replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    jQuery(this).val(value);
});
