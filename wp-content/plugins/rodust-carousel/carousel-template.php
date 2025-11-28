<?php
/**
 * Template do Carousel Frontend
 */
// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

$carousel_id = 'carousel-' . uniqid();
$height = $atts['height'] ?? '300px';
$class = $atts['class'] ?? '';
$show_arrows = $settings['show_arrows'] ?? true;
$show_dots = $settings['show_dots'] ?? true;
$autoplay = $settings['autoplay'] ?? true;
$autoplay_speed = $settings['autoplay_speed'] ?? 5000;
?>

<div class="rodust-carousel <?php echo esc_attr($class); ?>" 
     id="<?php echo esc_attr($carousel_id); ?>"
     style="height: <?php echo esc_attr($height); ?>;"
     data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
     data-autoplay-speed="<?php echo esc_attr($autoplay_speed); ?>"
     data-show-arrows="<?php echo $show_arrows ? 'true' : 'false'; ?>"
     data-show-dots="<?php echo $show_dots ? 'true' : 'false'; ?>">
     
    <div class="carousel-slides">
        <?php foreach ($slides as $index => $slide) : ?>
            <?php
            // Processa link inteligente se plugin Smart Links estiver ativo
            $slide_link = $slide['link'] ?? '';
            if (class_exists('Smart_Menu_Links') && !empty($slide_link)) {
                $smart_links = new Smart_Menu_Links();
                $slide_link = $smart_links->convert_smart_link($slide_link);
            }
            ?>
            
            <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('<?php echo esc_url($slide['image']); ?>');">
                 
                <?php if (!empty($slide['description']) || (!empty($slide_link) && !empty($slide['link_text']))) : ?>
                    <div class="carousel-slide-content">
                        <?php if (!empty($slide['description'])) : ?>
                            <p class="carousel-slide-description">
                                <?php echo esc_html($slide['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($slide_link) && !empty($slide['link_text'])) : ?>
                            <a href="<?php echo esc_url($slide_link); ?>" 
                               class="carousel-slide-link"
                               <?php echo strpos($slide_link, 'http') === 0 && strpos($slide_link, home_url()) === false ? 'target="_blank" rel="noopener"' : ''; ?>>
                                <?php echo esc_html($slide['link_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Controles são adicionados via JavaScript -->
</div>