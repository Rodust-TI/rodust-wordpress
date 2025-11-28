<?php
// Template individual para cada slide no admin
$slide = $slide ?? array();
$slide_id = $slide_id ?? uniqid('slide_');
?>

<div class="slide-header">
    <h3>
        <?php if (!empty($slide['title'])) : ?>
            📷 <?php echo esc_html($slide['title']); ?>
        <?php else : ?>
            🆕 Novo Slide
        <?php endif; ?>
    </h3>
    <div class="slide-controls">
        <span class="dashicons dashicons-menu drag-handle" title="Arrastar para reordenar"></span>
        <button type="button" class="button button-small save-slide">💾 Salvar</button>
        <button type="button" class="button button-small delete-slide">🗑️ Excluir</button>
    </div>
</div>

<div class="slide-form">
    <div class="slide-row">
        <div class="slide-col">
            <label>Título Interno (opcional)</label>
            <input type="text" class="slide-title" value="<?php echo esc_attr($slide['title'] ?? ''); ?>" placeholder="Identificador para organização">
            <p class="description">Este título não aparece no site, é apenas para seu controle.</p>
        </div>
        
        <div class="slide-col">
            <label>Texto do Link</label>
            <input type="text" class="slide-link-text" value="<?php echo esc_attr($slide['link_text'] ?? ''); ?>" placeholder="Ex: Saiba Mais">
        </div>
    </div>
    
    <div class="slide-row">
        <div class="slide-col">
            <label>Imagem</label>
            <div class="image-upload">
                <button type="button" class="button upload-image">🖼️ Selecionar Imagem</button>
                <div class="image-preview">
                    <?php if (!empty($slide['image'])) : ?>
                        <img src="<?php echo esc_url($slide['image']); ?>" style="max-width: 200px; height: auto;">
                    <?php endif; ?>
                </div>
                <input type="hidden" class="slide-image" value="<?php echo esc_attr($slide['image'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="slide-col">
            <label>Link (URL)</label>
            <input type="url" class="slide-link" value="<?php echo esc_attr($slide['link'] ?? ''); ?>" placeholder="https://exemplo.com">
            <p class="description">💡 Use links inteligentes: home, produtos, contato</p>
        </div>
    </div>
    
    <div class="slide-row">
        <div class="slide-col-full">
            <label>Descrição</label>
            <textarea class="slide-description" rows="3" placeholder="Descrição opcional do slide"><?php echo esc_textarea($slide['description'] ?? ''); ?></textarea>
        </div>
    </div>
    
    <input type="hidden" class="slide-order" value="<?php echo esc_attr($slide['order'] ?? 0); ?>">
</div>