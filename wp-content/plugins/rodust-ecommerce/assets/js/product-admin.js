jQuery(document).ready(function($) {
    
    // Carregar dados do Laravel quando a página carrega
    const productId = $('#post_ID').val();
    const laravel_id = $('input[name="product_laravel_id"]').val();
    
    if (laravel_id && laravel_id !== '') {
        loadProductDataFromLaravel(productId);
        makeFieldsReadOnly();
    }
    
    // Botão "Sincronizar Agora"
    $(document).on('click', '.rodust-sync-now', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const productId = button.data('product-id');
        const originalText = button.text();
        
        button.prop('disabled', true).text(rodustProductAdmin.strings.syncing);
        
        $.ajax({
            url: rodustProductAdmin.ajaxurl,
            method: 'POST',
            data: {
                action: 'rodust_sync_product',
                nonce: rodustProductAdmin.nonce,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    
                    // Recarregar dados nos campos
                    if (response.data.data) {
                        updateFields(response.data.data);
                    }
                    
                    // Atualizar timestamp
                    $('.rodust-sync-status').find('small').text(
                        'Última sincronização: ' + new Date().toLocaleString('pt-BR')
                    );
                } else {
                    showNotice('error', response.data.message || rodustProductAdmin.strings.sync_error);
                }
            },
            error: function() {
                showNotice('error', rodustProductAdmin.strings.sync_error);
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });
    
    /**
     * Carregar dados do Laravel via Ajax
     */
    function loadProductDataFromLaravel(productId) {
        $.ajax({
            url: rodustProductAdmin.ajaxurl,
            method: 'POST',
            data: {
                action: 'rodust_load_product_data',
                nonce: rodustProductAdmin.nonce,
                product_id: productId
            },
            success: function(response) {
                if (response.success && response.data.data) {
                    updateFields(response.data.data);
                }
            },
            error: function() {
                console.error('Erro ao carregar dados do Laravel');
            }
        });
    }
    
    /**
     * Atualizar campos com dados do Laravel
     */
    function updateFields(data) {
        // Dados do Produto
        if (data.sku) $('#product_sku').val(data.sku);
        if (data.price) $('#product_price').val(parseFloat(data.price).toFixed(2));
        if (data.promotional_price) $('#product_promotional_price').val(parseFloat(data.promotional_price).toFixed(2));
        if (data.stock !== undefined) $('#product_stock').val(data.stock);
        
        // Dimensões e Frete
        if (data.weight) $('#product_weight').val(parseFloat(data.weight).toFixed(3));
        if (data.length) $('#product_length').val(parseFloat(data.length).toFixed(2));
        if (data.width) $('#product_width').val(parseFloat(data.width).toFixed(2));
        if (data.height) $('#product_height').val(parseFloat(data.height).toFixed(2));
        
        // Marca (atualiza display e hidden)
        if (data.brand) {
            $('#product_brand_display').val(data.brand);
            $('#product_brand').val(data.brand);
        }
        
        // Frete Grátis
        if (data.free_shipping !== undefined) {
            $('#free_shipping').prop('checked', data.free_shipping);
        }
        
        // Informações Comerciais
        if (data.ncm) $('#product_ncm').val(data.ncm);
        if (data.origin) $('#product_origin').val(data.origin);
        if (data.warranty_months) $('#product_warranty').val(data.warranty_months);
    }
    
    /**
     * Tornar campos read-only e adicionar indicador
     */
    function makeFieldsReadOnly() {
        const fields = [
            '#product_sku',
            '#product_price', 
            '#product_promotional_price',
            '#product_stock',
            '#product_weight',
            '#product_length',
            '#product_width',
            '#product_height',
            '#product_brand_display',
            '#free_shipping',
            '#product_ncm',
            '#product_origin',
            '#product_warranty'
        ];
        
        fields.forEach(function(fieldId) {
            const field = $(fieldId);
            if (field.length) {
                field.prop('readonly', true).addClass('rodust-readonly');
                
                // Para checkbox, usar disabled ao invés de readonly
                if (field.attr('type') === 'checkbox') {
                    field.prop('disabled', true);
                }
                
                // Adicionar indicador de fonte (se ainda não existe)
                const parent = field.closest('td');
                if (parent.length && parent.find('.rodust-data-source').length === 0) {
                    const description = parent.find('.description');
                    if (description.length === 0) {
                        field.after('<p class="description rodust-data-source">📊 Dados vindos do Laravel (somente leitura)</p>');
                    } else if (!description.hasClass('rodust-data-source')) {
                        description.after('<p class="description rodust-data-source">📊 Dados vindos do Laravel (somente leitura)</p>');
                    }
                }
            }
        });
    }
    
    /**
     * Mostrar notificação no admin do WordPress
     */
    function showNotice(type, message) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap h1').after(notice);
        
        // Auto-remover após 5 segundos
        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
});
