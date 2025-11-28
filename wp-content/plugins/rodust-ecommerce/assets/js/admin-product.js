jQuery(document).ready(function($) {
    // Verificar se estamos na página de edição de produto
    var laravelId = #laravel_product_id.val();
    
    if (!laravelId) {
        return; // Não é um produto sincronizado
    }

    // Buscar dados do Laravel e preencher campos como read-only
    function loadLaravelData() {
        var apiUrl = rodustAdmin.laravelApiUrl + '/api/products/' + laravelId;
        
        #rodust-loading-indicator.show();
        
        $.ajax({
            url: apiUrl,
            method: 'GET',
            success: function(response) {
                if (response.success ; response.data) {
                    var product = response.data;
                    
                    // Preencher campos como read-only
                    setReadOnlyField('#product_sku', product.sku);
                    setReadOnlyField('#product_price', product.price);
                    setReadOnlyField('#product_promotional_price', product.promotional_price || '');
                    setReadOnlyField('#product_stock', product.stock);
                    setReadOnlyField('#product_width', product.width || '');
                    setReadOnlyField('#product_height', product.height || '');
                    setReadOnlyField('#product_length', product.length || '');
                    setReadOnlyField('#product_weight', product.weight || '');
                    
                    #rodust-loading-indicator.hide();
                    showNotice('success', 'Dados carregados do Laravel');
                }
            },
            error: function() {
                #rodust-loading-indicator.hide();
                showNotice('error', 'Erro ao carregar dados do Laravel');
            }
        });
    }
    
    function setReadOnlyField(selector, value) {
        var  = ;
        .val(value).prop('readonly', true).css('background-color', '#f0f0f1');
        
        // Adicionar aviso ao lado do campo
        if (!.next('.laravel-data-badge').length) {
            .after('<span class=" laravel-data-badge\
