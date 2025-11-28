<?php
/**
 * Template: Archive Products (Listagem de Produtos)
 * 
 * Layout: Sidebar com filtros + Grid de produtos
 * Estilo: Loja do Mecânico / Mercado Livre
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

get_header();

// Obter categorias e filtros ativos
$current_category = get_query_var('product_category');
$current_tag = get_query_var('product_tag');
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900"><?php _e('Produtos', 'rodust-ecommerce'); ?></h1>
            <p class="mt-2 text-sm text-gray-600">
                <?php 
                global $wp_query;
                printf(_n('%s produto encontrado', '%s produtos encontrados', $wp_query->found_posts, 'rodust-ecommerce'), number_format_i18n($wp_query->found_posts)); 
                ?>
            </p>
        </div>

        <!-- Layout 2 colunas: Sidebar + Produtos -->
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            
            <!-- SIDEBAR com Filtros -->
            <aside class="hidden lg:block">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    
                    <!-- Busca Rápida -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3"><?php _e('Buscar', 'rodust-ecommerce'); ?></h3>
                        <form method="get" action="<?php echo esc_url(home_url('/produtos')); ?>" class="relative">
                            <input type="search" 
                                   name="s" 
                                   value="<?php echo get_search_query(); ?>"
                                   placeholder="<?php _e('Buscar produtos...', 'rodust-ecommerce'); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Filtro por Categorias -->
                    <?php
                    $categories = get_terms([
                        'taxonomy' => 'product_category',
                        'hide_empty' => true,
                    ]);
                    if (!empty($categories) && !is_wp_error($categories)) :
                    ?>
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3"><?php _e('Categorias', 'rodust-ecommerce'); ?></h3>
                        <ul class="space-y-2">
                            <?php foreach ($categories as $category) : ?>
                            <li>
                                <a href="<?php echo get_term_link($category); ?>" 
                                   class="flex items-center justify-between py-2 px-3 rounded-md hover:bg-gray-50 transition-colors <?php echo ($current_category === $category->slug) ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
                                    <span><?php echo esc_html($category->name); ?></span>
                                    <span class="text-sm text-gray-500">(<?php echo $category->count; ?>)</span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Filtro por Faixa de Preço -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3"><?php _e('Faixa de Preço', 'rodust-ecommerce'); ?></h3>
                        <form method="get" id="price-filter-form">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1"><?php _e('Mínimo', 'rodust-ecommerce'); ?></label>
                                    <input type="number" 
                                           name="min_price" 
                                           value="<?php echo esc_attr($min_price); ?>"
                                           placeholder="R$ 0" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1"><?php _e('Máximo', 'rodust-ecommerce'); ?></label>
                                    <input type="number" 
                                           name="max_price" 
                                           value="<?php echo esc_attr($max_price); ?>"
                                           placeholder="R$ 9999" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                <?php _e('Filtrar', 'rodust-ecommerce'); ?>
                            </button>
                        </form>
                    </div>

                    <!-- Limpar Filtros -->
                    <?php if (!empty($min_price) || !empty($max_price) || !empty($current_category) || get_search_query()) : ?>
                    <div class="mt-4">
                        <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" 
                           class="block text-center text-sm text-gray-600 hover:text-gray-900 py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            <?php _e('Limpar todos os filtros', 'rodust-ecommerce'); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </aside>

            <!-- GRID DE PRODUTOS -->
            <div class="lg:col-span-3">
                
                <!-- Barra de Ordenação -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        <?php printf(_n('Exibindo %s produto', 'Exibindo %s produtos', $wp_query->post_count, 'rodust-ecommerce'), $wp_query->post_count); ?>
                    </span>
                    <form method="get" class="flex items-center gap-2">
                        <label for="orderby" class="text-sm text-gray-600"><?php _e('Ordenar por:', 'rodust-ecommerce'); ?></label>
                        <select name="orderby" 
                                id="orderby" 
                                onchange="this.form.submit()"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="date" <?php selected($orderby, 'date'); ?>><?php _e('Mais recentes', 'rodust-ecommerce'); ?></option>
                            <option value="title" <?php selected($orderby, 'title'); ?>><?php _e('Nome A-Z', 'rodust-ecommerce'); ?></option>
                            <option value="price" <?php selected($orderby, 'price'); ?>><?php _e('Menor preço', 'rodust-ecommerce'); ?></option>
                            <option value="price-desc" <?php selected($orderby, 'price-desc'); ?>><?php _e('Maior preço', 'rodust-ecommerce'); ?></option>
                        </select>
                    </form>
                </div>

                <?php if (have_posts()) : ?>
                    
                    <!-- Grid de Produtos -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while (have_posts()) : the_post(); 
                            $product_id = get_post_meta(get_the_ID(), '_laravel_id', true) ?: get_the_ID();
                            $price = get_post_meta(get_the_ID(), '_price', true) ?: 0;
                            $stock = get_post_meta(get_the_ID(), '_stock', true) ?: 0;
                            $sku = get_post_meta(get_the_ID(), '_sku', true);
                            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        ?>
                            <!-- Card do Produto -->
                            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group">
                                
                                <!-- Imagem com Placeholder Tailwind -->
                                <a href="<?php the_permalink(); ?>" class="block relative bg-gray-100 aspect-square overflow-hidden">
                                    <?php if ($thumbnail_url) : ?>
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" 
                                             alt="<?php the_title_attribute(); ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy">
                                    <?php else : ?>
                                        <!-- Placeholder Skeleton Tailwind -->
                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 via-gray-100 to-gray-200 bg-[length:200%_200%] animate-pulse flex items-center justify-center">
                                            <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge de Estoque -->
                                    <?php if ($stock <= 0) : ?>
                                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            <?php _e('Esgotado', 'rodust-ecommerce'); ?>
                                        </span>
                                    <?php elseif ($stock < 5) : ?>
                                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            <?php printf(__('Só %d', 'rodust-ecommerce'), $stock); ?>
                                        </span>
                                    <?php endif; ?>
                                </a>

                                <!-- Conteúdo do Card -->
                                <div class="p-4">
                                    <!-- Título -->
                                    <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 hover:text-blue-600 transition-colors">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <!-- SKU -->
                                    <?php if ($sku) : ?>
                                        <p class="text-xs text-gray-500 mb-2">SKU: <?php echo esc_html($sku); ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Preço -->
                                    <div class="mb-3">
                                        <span class="text-2xl font-bold text-blue-600">
                                            <?php echo Rodust_Helpers::format_price($price); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Seletor de Quantidade + Botões -->
                                    <?php if ($stock > 0) : ?>
                                        <div class="space-y-2">
                                            <!-- Quantidade -->
                                            <div class="flex items-center justify-center border border-gray-300 rounded-lg overflow-hidden">
                                                <button type="button" 
                                                        class="qty-decrease px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                                                        data-product-id="<?php echo esc_attr($product_id); ?>">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input type="number" 
                                                       value="1" 
                                                       min="1" 
                                                       max="<?php echo esc_attr($stock); ?>"
                                                       class="qty-input w-16 text-center border-0 focus:ring-0 py-2 text-sm font-medium"
                                                       data-product-id="<?php echo esc_attr($product_id); ?>"
                                                       readonly>
                                                <button type="button" 
                                                        class="qty-increase px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                                                        data-product-id="<?php echo esc_attr($product_id); ?>"
                                                        data-max="<?php echo esc_attr($stock); ?>">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Adicionar ao Carrinho -->
                                            <button class="btn-add-to-cart w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2" 
                                                    data-product-id="<?php echo esc_attr($product_id); ?>"
                                                    data-name="<?php echo esc_attr(get_the_title()); ?>"
                                                    data-price="<?php echo esc_attr($price); ?>"
                                                    data-image="<?php echo esc_attr($thumbnail_url); ?>"
                                                    data-sku="<?php echo esc_attr($sku); ?>"
                                                    data-stock="<?php echo esc_attr($stock); ?>">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <?php _e('Adicionar', 'rodust-ecommerce'); ?>
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <button disabled class="w-full bg-gray-300 text-gray-600 font-medium py-2.5 px-4 rounded-lg cursor-not-allowed">
                                            <?php _e('Indisponível', 'rodust-ecommerce'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-8">
                        <?php
                        the_posts_pagination([
                            'mid_size' => 2,
                            'prev_text' => '<span class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg> ' . __('Anterior', 'rodust-ecommerce') . '</span>',
                            'next_text' => '<span class="flex items-center gap-2">' . __('Próxima', 'rodust-ecommerce') . ' <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>',
                            'class' => 'flex justify-center gap-2',
                        ]);
                        ?>
                    </div>

                <?php else : ?>
                    
                    <!-- Nenhum Produto Encontrado -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2"><?php _e('Nenhum produto encontrado', 'rodust-ecommerce'); ?></h2>
                        <p class="text-gray-600 mb-6"><?php _e('Tente ajustar os filtros ou fazer uma nova busca.', 'rodust-ecommerce'); ?></p>
                        <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <?php _e('Ver todos os produtos', 'rodust-ecommerce'); ?>
                        </a>
                    </div>

                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
</div>

<script>
// Seletor de quantidade (+/-)
document.addEventListener('DOMContentLoaded', function() {
    // Incrementar quantidade
    document.querySelectorAll('.qty-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            const max = parseInt(this.dataset.max);
            let val = parseInt(input.value);
            if (val < max) {
                input.value = val + 1;
            }
        });
    });
    
    // Decrementar quantidade
    document.querySelectorAll('.qty-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
            let val = parseInt(input.value);
            if (val > 1) {
                input.value = val - 1;
            }
        });
    });
});
</script>

<?php get_footer(); ?>
