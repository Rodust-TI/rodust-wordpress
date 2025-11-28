<?php
// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎠 Carousel Rodust</h1>
    
    <div class="carousel-admin-container">
        
        <!-- Área de slides -->
        <div class="carousel-slides-section">
            <h2>🖼️ Gerenciar Slides</h2>
            
            <div class="slide-actions">
                <button type="button" class="button button-primary" id="add-new-slide">
                    ➕ Adicionar Novo Slide
                </button>
                
                <div class="shortcode-info">
                    <strong>Shortcode:</strong> 
                    <code>[rodust_carousel]</code>
                    <p class="description">Use este shortcode em posts, páginas ou no tema.</p>
                </div>
            </div>
            
            <div id="slides-container" class="slides-container">
                <?php if (!empty($slides)) : ?>
                    <?php 
                    // Ordena slides por order
                    uasort($slides, function($a, $b) {
                        return ($a['order'] ?? 0) - ($b['order'] ?? 0);
                    });
                    ?>
                    
                    <?php foreach ($slides as $slide_id => $slide) : ?>
                        <div class="slide-item" data-slide-id="<?php echo esc_attr($slide_id); ?>">
                            <?php include 'slide-form.php'; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="no-slides">
                        <p>📭 Nenhum slide criado ainda.</p>
                        <p>Clique em "Adicionar Novo Slide" para começar!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Template para novo slide -->
<script type="text/template" id="slide-template">
    <div class="slide-item" data-slide-id="">
        <div class="slide-header">
            <h3>🆕 Novo Slide</h3>
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
                    <input type="text" class="slide-title" placeholder="Identificador para organização">
                    <p class="description">Este título não aparece no site, é apenas para seu controle.</p>
                </div>
                
                <div class="slide-col">
                    <label>Texto do Link</label>
                    <input type="text" class="slide-link-text" placeholder="Ex: Saiba Mais">
                </div>
            </div>
            
            <div class="slide-row">
                <div class="slide-col">
                    <label>Imagem</label>
                    <div class="image-upload">
                        <button type="button" class="button upload-image">🖼️ Selecionar Imagem</button>
                        <div class="image-preview"></div>
                        <input type="hidden" class="slide-image">
                    </div>
                </div>
                
                <div class="slide-col">
                    <label>Link (URL)</label>
                    <input type="url" class="slide-link" placeholder="https://exemplo.com">
                </div>
            </div>
            
            <div class="slide-row">
                <div class="slide-col-full">
                    <label>Descrição</label>
                    <textarea class="slide-description" rows="3" placeholder="Descrição opcional do slide"></textarea>
                </div>
            </div>
        </div>
    </div>
</script>