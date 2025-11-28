<?php
/**
 * Template: Single Product (Produto Individual)
 * 
 * Para usar: copie para seu tema em /rodust-ecommerce/single-product.php
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    $product_id = get_post_meta(get_the_ID(), '_laravel_id', true);
    $price = get_post_meta(get_the_ID(), '_price', true);
    $stock = get_post_meta(get_the_ID(), '_stock', true);
    $sku = get_post_meta(get_the_ID(), '_sku', true);
?>

<div class="rodust-single-product">
    <div class="container">
        
        <div class="product-layout">
            
            <!-- Galeria de Imagens -->
            <div class="product-gallery">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="main-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php else : ?>
                    <div class="main-image">
                        <img src="<?php echo RODUST_ECOMMERCE_URL . 'assets/images/placeholder.png'; ?>" alt="<?php the_title(); ?>">
                    </div>
                <?php endif; ?>
                
                <!-- Thumbnails (se houver gallery) -->
                <?php 
                $gallery_images = get_post_meta(get_the_ID(), '_product_gallery', true);
                if (!empty($gallery_images)) : ?>
                    <div class="gallery-thumbnails">
                        <?php foreach ($gallery_images as $image_id) : ?>
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Informações do Produto -->
            <div class="product-summary">
                
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <?php if ($sku) : ?>
                    <div class="product-meta">
                        <span class="sku-label"><?php _e('SKU:', 'rodust-ecommerce'); ?></span>
                        <span class="sku-value"><?php echo esc_html($sku); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="product-price">
                    <span class="price-label"><?php _e('Preço:', 'rodust-ecommerce'); ?></span>
                    <span class="price-value"><?php echo Rodust_Helpers::format_price($price); ?></span>
                </div>
                
                <div class="product-stock">
                    <?php if ($stock > 0) : ?>
                        <span class="in-stock">
                            <span class="icon">✓</span>
                            <?php printf(__('%d em estoque', 'rodust-ecommerce'), $stock); ?>
                        </span>
                    <?php else : ?>
                        <span class="out-of-stock">
                            <span class="icon">✗</span>
                            <?php _e('Fora de estoque', 'rodust-ecommerce'); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Descrição Curta -->
                <?php if (has_excerpt()) : ?>
                    <div class="product-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de Adicionar ao Carrinho -->
                <?php if ($stock > 0) : ?>
                    <form class="cart-form" data-product-id="<?php echo esc_attr($product_id); ?>">
                        <div class="quantity-selector">
                            <label for="quantity"><?php _e('Quantidade:', 'rodust-ecommerce'); ?></label>
                            <input type="number" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo esc_attr($stock); ?>"
                                   step="1">
                        </div>
                        
                        <button type="submit" 
                                class="btn-add-to-cart btn-large"
                                data-product-id="<?php echo esc_attr($product_id); ?>"
                                data-name="<?php echo esc_attr(get_the_title()); ?>"
                                data-price="<?php echo esc_attr($price); ?>"
                                data-image="<?php echo esc_attr(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?>"
                                data-sku="<?php echo esc_attr($sku); ?>"
                                data-stock="<?php echo esc_attr($stock); ?>">
                            <span class="icon">🛒</span>
                            <?php _e('Adicionar ao Carrinho', 'rodust-ecommerce'); ?>
                        </button>
                    </form>
                <?php else : ?>
                    <div class="out-of-stock-notice">
                        <p><?php _e('Este produto está temporariamente indisponível.', 'rodust-ecommerce'); ?></p>
                        <button class="btn-notify-stock"><?php _e('Avise-me quando chegar', 'rodust-ecommerce'); ?></button>
                    </div>
                <?php endif; ?>
                
                <!-- Categorias e Tags -->
                <div class="product-taxonomies">
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'product_category');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                        <div class="product-categories">
                            <span class="label"><?php _e('Categorias:', 'rodust-ecommerce'); ?></span>
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo get_term_link($category); ?>" class="category-link">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    $tags = get_the_terms(get_the_ID(), 'product_tag');
                    if ($tags && !is_wp_error($tags)) :
                    ?>
                        <div class="product-tags">
                            <span class="label"><?php _e('Tags:', 'rodust-ecommerce'); ?></span>
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo get_term_link($tag); ?>" class="tag-link">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
            
        </div>
        
        <!-- Tabs: Descrição, Especificações, Avaliações -->
        <div class="product-tabs">
            <ul class="tabs-nav">
                <li class="active"><a href="#tab-description"><?php _e('Descrição', 'rodust-ecommerce'); ?></a></li>
                <li><a href="#tab-specs"><?php _e('Especificações', 'rodust-ecommerce'); ?></a></li>
                <li><a href="#tab-reviews"><?php _e('Avaliações', 'rodust-ecommerce'); ?></a></li>
            </ul>
            
            <div class="tabs-content">
                <div id="tab-description" class="tab-panel active">
                    <?php the_content(); ?>
                </div>
                
                <div id="tab-specs" class="tab-panel">
                    <?php
                    $specs = get_post_meta(get_the_ID(), '_product_specs', true);
                    if ($specs) :
                    ?>
                        <table class="product-specs">
                            <?php foreach ($specs as $key => $value) : ?>
                                <tr>
                                    <th><?php echo esc_html($key); ?></th>
                                    <td><?php echo esc_html($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        <p><?php _e('Nenhuma especificação disponível.', 'rodust-ecommerce'); ?></p>
                    <?php endif; ?>
                </div>
                
                <div id="tab-reviews" class="tab-panel">
                    <p><?php _e('Sistema de avaliações em breve!', 'rodust-ecommerce'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Produtos Relacionados -->
        <div class="related-products">
            <h2><?php _e('Produtos Relacionados', 'rodust-ecommerce'); ?></h2>
            <?php
            // Buscar produtos da mesma categoria
            $categories = get_the_terms(get_the_ID(), 'product_category');
            if ($categories && !is_wp_error($categories)) {
                $category_ids = wp_list_pluck($categories, 'term_id');
                
                $related_query = new WP_Query([
                    'post_type' => 'rodust_product',
                    'posts_per_page' => 4,
                    'post__not_in' => [get_the_ID()],
                    'tax_query' => [[
                        'taxonomy' => 'product_category',
                        'field' => 'term_id',
                        'terms' => $category_ids,
                    ]],
                ]);
                
                if ($related_query->have_posts()) : ?>
                    <div class="products-grid">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); 
                            $rel_product_id = get_post_meta(get_the_ID(), '_laravel_id', true);
                            $rel_price = get_post_meta(get_the_ID(), '_price', true);
                            $rel_stock = get_post_meta(get_the_ID(), '_stock', true);
                        ?>
                            <article class="product-card">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                    <h4><?php the_title(); ?></h4>
                                    <span class="price"><?php echo Rodust_Helpers::format_price($rel_price); ?></span>
                                </a>
                            </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php endif;
            }
            ?>
        </div>
        
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
