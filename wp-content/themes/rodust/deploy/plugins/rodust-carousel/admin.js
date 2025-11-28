// Carousel Admin JavaScript
jQuery(document).ready(function($) {
    
    // Torna slides sortáveis (arrastar e soltar)
    $('#slides-container').sortable({
        handle: '.drag-handle',
        placeholder: 'ui-state-highlight',
        update: function() {
            updateSlidesOrder();
        }
    });
    
    // Adicionar novo slide
    $('#add-new-slide').on('click', function() {
        const template = $('#slide-template').html();
        const newSlide = $(template);
        
        $('#slides-container').append(newSlide);
        $('.no-slides').remove();
        
        // Inicializa upload de imagem para o novo slide
        initImageUpload(newSlide);
    });
    
    // Salvar slide
    $(document).on('click', '.save-slide', function() {
        const slideItem = $(this).closest('.slide-item');
        saveSlide(slideItem);
    });
    
    // Deletar slide
    $(document).on('click', '.delete-slide', function() {
        if (confirm('❌ Tem certeza que deseja excluir este slide?')) {
            const slideItem = $(this).closest('.slide-item');
            const slideId = slideItem.data('slide-id');
            
            if (slideId) {
                deleteSlide(slideId, slideItem);
            } else {
                slideItem.remove();
            }
        }
    });
    
    // Inicializa upload de imagem para slides existentes
    $('.slide-item').each(function() {
        initImageUpload($(this));
    });
    
    /**
     * Inicializa upload de imagem
     */
    function initImageUpload(slideItem) {
        slideItem.find('.upload-image').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const imageInput = slideItem.find('.slide-image');
            const imagePreview = slideItem.find('.image-preview');
            
            // Abre media library
            const mediaUploader = wp.media({
                title: 'Selecionar Imagem do Slide',
                button: {
                    text: 'Usar esta imagem'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                
                imageInput.val(attachment.url);
                imagePreview.html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;">');
            });
            
            mediaUploader.open();
        });
    }
    
    /**
     * Salva um slide
     */
    function saveSlide(slideItem) {
        const slideId = slideItem.data('slide-id') || '';
        
        const slideData = {
            action: 'save_carousel_slide',
            nonce: carousel_ajax.nonce,
            slide_id: slideId,
            title: slideItem.find('.slide-title').val(),
            image: slideItem.find('.slide-image').val(),
            link: slideItem.find('.slide-link').val(),
            link_text: slideItem.find('.slide-link-text').val(),
            description: slideItem.find('.slide-description').val(),
            order: slideItem.find('.slide-order').val() || slideItem.index()
        };
        
        // Validação básica
        if (!slideData.title || !slideData.image) {
            alert('⚠️ Título e imagem são obrigatórios!');
            return;
        }
        
        slideItem.addClass('saving');
        
        $.ajax({
            url: carousel_ajax.ajax_url,
            type: 'POST',
            data: slideData,
            success: function(response) {
                if (response.success) {
                    const newSlideId = response.data.id;
                    slideItem.attr('data-slide-id', newSlideId);
                    slideItem.find('.slide-header h3').text('📷 ' + slideData.title);
                    
                    // Feedback visual
                    slideItem.removeClass('saving');
                    slideItem.css('border-color', '#00a32a');
                    setTimeout(() => {
                        slideItem.css('border-color', '');
                    }, 2000);
                    
                    console.log('✅ Slide salvo:', response.data);
                } else {
                    alert('❌ Erro ao salvar slide!');
                }
            },
            error: function() {
                alert('❌ Erro de conexão!');
            },
            complete: function() {
                slideItem.removeClass('saving');
            }
        });
    }
    
    /**
     * Deleta um slide
     */
    function deleteSlide(slideId, slideItem) {
        $.ajax({
            url: carousel_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_carousel_slide',
                nonce: carousel_ajax.nonce,
                slide_id: slideId
            },
            success: function(response) {
                if (response.success) {
                    slideItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Se não há mais slides, mostra mensagem
                        if ($('.slide-item').length === 0) {
                            $('#slides-container').html(
                                '<div class="no-slides">' +
                                '<p>📭 Nenhum slide criado ainda.</p>' +
                                '<p>Clique em "Adicionar Novo Slide" para começar!</p>' +
                                '</div>'
                            );
                        }
                    });
                } else {
                    alert('❌ Erro ao excluir slide!');
                }
            },
            error: function() {
                alert('❌ Erro de conexão!');
            }
        });
    }
    
    /**
     * Atualiza ordem dos slides
     */
    function updateSlidesOrder() {
        const order = [];
        $('.slide-item').each(function(index) {
            const slideId = $(this).data('slide-id');
            if (slideId) {
                order.push(slideId);
                $(this).find('.slide-order').val(index);
            }
        });
        
        if (order.length > 0) {
            $.ajax({
                url: carousel_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'reorder_carousel_slides',
                    nonce: carousel_ajax.nonce,
                    order: order
                },
                success: function(response) {
                    console.log('✅ Ordem atualizada');
                }
            });
        }
    }
});