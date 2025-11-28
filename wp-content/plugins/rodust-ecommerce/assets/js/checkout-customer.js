/**
 * Checkout Customer Management
 * Handles customer data loading and form population
 */

let customerData = null;

// Buscar dados do cliente logado
function loadCustomerData() {
    const token = sessionStorage.getItem('customer_token');
    
    if (!token) {
        // Salvar URL atual para redirecionar após login
        sessionStorage.setItem('redirect_after_login', window.location.href);
        alert('Você precisa estar logado para finalizar a compra.');
        window.location.href = RODUST_CHECKOUT_DATA.login_url;
        return;
    }
    
    // Buscar dados do cliente
    jQuery.ajax({
        url: window.RODUST_API_URL + '/api/customers/me',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success && response.data) {
                customerData = response.data;
                populateCustomerData();
                loadSavedAddresses();
            }
        },
        error: function() {
            // Salvar URL atual para redirecionar após login
            sessionStorage.setItem('redirect_after_login', window.location.href);
            alert('Erro ao carregar dados do cliente. Faça login novamente.');
            window.location.href = RODUST_CHECKOUT_DATA.login_url;
        }
    });
}

// Preencher formulário com dados do cliente
function populateCustomerData() {
    jQuery('#customer_name').val(customerData.name || '');
    jQuery('#customer_email').val(customerData.email || '');
    jQuery('#customer_phone').val(customerData.phone || '');
    
    // Verificar se tem CPF e/ou CNPJ
    const hasCPF = customerData.cpf && customerData.cpf.length === 11;
    const hasCNPJ = customerData.cnpj && customerData.cnpj.length === 14;
    const hasIE = customerData.state_registration && customerData.state_registration.length === 12;
    const hasUF = customerData.state_uf && customerData.state_uf.length === 2;
    
    // Configurar opções de documento
    if (hasCPF) {
        jQuery('input[name="document_type"][value="cpf"]').prop('checked', true);
        jQuery('#customer_document').val(formatCPF(customerData.cpf));
        jQuery('#document-label').text('CPF *');
    }
    
    // CNPJ só habilitado se tiver CNPJ + IE + UF (exigência Bling)
    if (!hasCNPJ || !hasIE || !hasUF) {
        // Desabilitar opção CNPJ
        jQuery('#cnpj-option').addClass('disabled');
        jQuery('#cnpj-option input').prop('disabled', true);
        jQuery('#cnpj-warning').removeClass('hidden');
    } else {
        // Habilitar opção CNPJ
        jQuery('#cnpj-option').removeClass('disabled');
        jQuery('#cnpj-option input').prop('disabled', false);
    }
    
    // Controlar mudança de tipo de documento
    jQuery('input[name="document_type"]').on('change', function() {
        const docType = jQuery(this).val();
        
        if (docType === 'cpf' && hasCPF) {
            jQuery('#customer_document').val(formatCPF(customerData.cpf));
            jQuery('#document-label').text('CPF *');
        } else if (docType === 'cnpj' && hasCNPJ) {
            jQuery('#customer_document').val(formatCNPJ(customerData.cnpj));
            jQuery('#document-label').text('CNPJ *');
        }
    });
}
