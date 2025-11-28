<?php
/**
 * Template: Archive Produtos (rodust_product)
 * 
 * Layout: Sidebar com filtros + Grid de produtos
 * DADOS VÊM DA API LARAVEL (listagem otimizada)
 *
 * @package Rodust
 */

get_header();

// Paginação
$paged = get_query_var('paged', 1);

// Filtros
$search = get_search_query();
$current_category = get_query_var('product_category');
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';

// Buscar produtos da API Laravel (usar service name no Docker)
$api_url = 'http://laravel.test/api/products?page=' . $paged . '&per_page=20';

// Adicionar busca se existir
if (!empty($search)) {
    $api_url .= '&search=' . urlencode($search);
}

// Fazer requisição
$response = wp_remote_get($api_url, ['timeout' => 15]);
$api_products = [];
$total_products = 0;
$total_pages = 1;

if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
    $api_data = json_decode(wp_remote_retrieve_body($response), true);
    if (!empty($api_data['data'])) {
        $api_products = $api_data['data'];
        $total_products = $api_data['pagination']['total'] ?? count($api_products);
        $total_pages = $api_data['pagination']['last_page'] ?? 1;
    }
}
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Produtos</h1>
            <p class="mt-2 text-sm text-gray-600">
                <?php 
                printf(_n('%s produto encontrado', '%s produtos encontrados', $total_products, 'rodust'), number_format_i18n($total_products)); 
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Buscar</h3>
                        <form method="get" class="relative">
                            <input type="search" 
                                   name="s" 
                                   value="<?php echo get_search_query(); ?>"
                                   placeholder="Buscar produtos..."
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Categorias</h3>
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
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Faixa de Preço</h3>
                        <form method="get" id="price-filter-form">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Mínimo</label>
                                    <input type="number" 
                                           name="min_price" 
                                           value="<?php echo esc_attr($min_price); ?>"
                                           placeholder="R$ 0" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Máximo</label>
                                    <input type="number" 
                                           name="max_price" 
                                           value="<?php echo esc_attr($max_price); ?>"
                                           placeholder="R$ 9999" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    <!-- Limpar Filtros -->
                    <?php if (!empty($min_price) || !empty($max_price) || !empty($current_category) || get_search_query()) : ?>
                    <div class="mt-4">
                        <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" 
                           class="block text-center text-sm text-gray-600 hover:text-gray-900 py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Limpar todos os filtros
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
                        <?php printf(_n('Exibindo %s produto', 'Exibindo %s produtos', count($api_products), 'rodust'), count($api_products)); ?>
                    </span>
                    <form method="get" class="flex items-center gap-2">
                        <label for="orderby" class="text-sm text-gray-600">Ordenar por:</label>
                        <select name="orderby" 
                                id="orderby" 
                                onchange="this.form.submit()"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="date" <?php selected($orderby, 'date'); ?>>Mais recentes</option>
                            <option value="title" <?php selected($orderby, 'title'); ?>>Nome A-Z</option>
                            <option value="price" <?php selected($orderby, 'price'); ?>>Menor preço</option>
                            <option value="price-desc" <?php selected($orderby, 'price-desc'); ?>>Maior preço</option>
                        </select>
                    </form>
                </div>

                <?php if (!empty($api_products)) : ?>
                    
                    <!-- Grid de Produtos (DADOS DA API LARAVEL) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($api_products as $product) : 
                            // Buscar permalink do WordPress (SEO)
                            $wp_posts = get_posts([
                                'post_type' => 'rodust_product',
                                'meta_key' => '_laravel_id',
                                'meta_value' => $product['id'],
                                'posts_per_page' => 1,
                                'fields' => 'ids'
                            ]);
                            $permalink = !empty($wp_posts) ? get_permalink($wp_posts[0]) : '#';
                            
                            // Calcular preço final
                            $final_price = !empty($product['promotional_price']) ? $product['promotional_price'] : $product['price'];
                            $has_discount = !empty($product['promotional_price']) && $product['promotional_price'] < $product['price'];
                            
                            // Imagem
                            $image_url = !empty($product['images'][0]) ? $product['images'][0] : $product['image'];
                        ?>
                            <!-- Card do Produto (DADOS API LARAVEL) -->
                            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group relative">
                                
                                <!-- Ícones de Ação (Canto Superior Esquerdo) -->
                                <div class="absolute top-2 left-2 z-10 flex gap-1">
                                    <!-- Botão Wishlist -->
                                    <button 
                                        class="wishlist-toggle-archive bg-white/90 backdrop-blur-sm p-1.5 rounded-full hover:bg-white shadow-sm transition-all group/wishlist"
                                        data-product-id="<?php echo esc_attr($product['id']); ?>"
                                        title="Adicionar aos favoritos">
                                        <svg class="w-4 h-4 text-gray-600 group-hover/wishlist:text-pink-500 transition-colors wishlist-icon-<?php echo $product['id']; ?>" 
                                             fill="none" 
                                             stroke="currentColor" 
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Botão WhatsApp -->
                                    <a 
                                        href="https://wa.me/?text=<?php echo urlencode('Olha esse produto: ' . $product['name'] . ' - ' . $permalink); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="bg-white/90 backdrop-blur-sm p-1.5 rounded-full hover:bg-white shadow-sm transition-all group/whats"
                                        title="Compartilhar no WhatsApp">
                                        <svg class="w-4 h-4 text-gray-600 group-hover/whats:text-green-500 transition-colors" 
                                             fill="currentColor" 
                                             viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                </div>
                                
                                <!-- Imagem com Placeholder Tailwind -->
                                <a href="<?php echo esc_url($permalink); ?>" class="block relative bg-gray-100 aspect-square overflow-hidden">
                                    <?php if ($image_url) : ?>
                                        <img src="<?php echo esc_url($image_url); ?>" 
                                             alt="<?php echo esc_attr($product['name']); ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy">
                                    <?php else : ?>
                                        <!-- Placeholder Skeleton Tailwind -->
                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 via-gray-100 to-gray-200 flex items-center justify-center">
                                            <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge de Estoque -->
                                    <?php if ($product['stock'] <= 0) : ?>
                                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            Esgotado
                                        </span>
                                    <?php elseif ($product['stock'] < 5) : ?>
                                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            Só <?php echo $product['stock']; ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Badge de Desconto -->
                                    <?php if ($has_discount) : ?>
                                        <span class="absolute bottom-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                            <?php echo round((($product['price'] - $product['promotional_price']) / $product['price']) * 100); ?>% OFF
                                        </span>
                                    <?php endif; ?>
                                </a>

                                <!-- Conteúdo do Card -->
                                <div class="p-4">
                                    <!-- Título -->
                                    <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 hover:text-blue-600 transition-colors">
                                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($product['name']); ?></a>
                                    </h3>
                                    
                                    <!-- SKU -->
                                    <?php if (!empty($product['sku'])) : ?>
                                        <p class="text-xs text-gray-500 mb-2">SKU: <?php echo esc_html($product['sku']); ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Preço -->
                                    <div class="mb-3">
                                        <?php if ($has_discount) : ?>
                                            <div class="text-xs text-gray-500 line-through mb-1">
                                                R$ <?php echo number_format($product['price'], 2, ',', '.'); ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="text-2xl font-bold text-blue-600">
                                            R$ <?php echo number_format($final_price, 2, ',', '.'); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Seletor de Quantidade + Botões -->
                                    <?php if ($product['stock'] > 0) : ?>
                                        <div class="space-y-2">
                                            <!-- Quantidade -->
                                            <div class="flex items-center justify-center border border-gray-300 rounded-lg overflow-hidden">
                                                <button type="button" 
                                                        class="qty-decrease px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                                                        data-product-id="<?php echo esc_attr($product['id']); ?>">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input type="number" 
                                                       value="1" 
                                                       min="1" 
                                                       max="<?php echo esc_attr($product['stock']); ?>"
                                                       class="qty-input w-16 text-center border-0 focus:ring-0 py-2 text-sm font-medium"
                                                       data-product-id="<?php echo esc_attr($product['id']); ?>"
                                                       readonly>
                                                <button type="button" 
                                                        class="qty-increase px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                                                        data-product-id="<?php echo esc_attr($product['id']); ?>"
                                                        data-max="<?php echo esc_attr($product['stock']); ?>">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Adicionar ao Carrinho -->
                                            <button class="btn-add-to-cart w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2" 
                                                    data-product-id="<?php echo esc_attr($product['id']); ?>"
                                                    data-name="<?php echo esc_attr($product['name']); ?>"
                                                    data-price="<?php echo esc_attr($final_price); ?>"
                                                    data-image="<?php echo esc_attr($image_url); ?>"
                                                    data-sku="<?php echo esc_attr($product['sku']); ?>"
                                                    data-stock="<?php echo esc_attr($product['stock']); ?>"
                                                    data-width="<?php echo esc_attr($product['width']); ?>"
                                                    data-height="<?php echo esc_attr($product['height']); ?>"
                                                    data-length="<?php echo esc_attr($product['length']); ?>"
                                                    data-weight="<?php echo esc_attr($product['weight']); ?>">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                Adicionar
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <button disabled class="w-full bg-gray-300 text-gray-600 font-medium py-2.5 px-4 rounded-lg cursor-not-allowed">
                                            Indisponível
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginação API Laravel -->
                    <?php if ($total_pages > 1) : ?>
                        <div class="mt-8">
                            <nav class="flex justify-center items-center gap-2">
                                <!-- Botão Anterior -->
                                <?php if ($paged > 1) : ?>
                                    <a href="?page=<?php echo ($paged - 1); ?>" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Anterior
                                    </a>
                                <?php endif; ?>

                                <!-- Números das páginas -->
                                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                    <?php if ($i == $paged) : ?>
                                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
                                            <?php echo $i; ?>
                                        </span>
                                    <?php else : ?>
                                        <a href="?page=<?php echo $i; ?>" 
                                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <!-- Botão Próxima -->
                                <?php if ($paged < $total_pages) : ?>
                                    <a href="?page=<?php echo ($paged + 1); ?>" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        Próxima
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    
                    <!-- Nenhum Produto Encontrado (API VAZIA) -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Nenhum produto disponível no momento</h2>
                        <p class="text-gray-600 mb-2">Ainda não há produtos cadastrados.</p>
                        <?php if (is_wp_error($api_response)) : ?>
                            <p class="text-red-500 text-sm">Erro na API: <?php echo $api_response->get_error_message(); ?></p>
                        <?php endif; ?>
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
    
    // ==========================================
    // WISHLIST - Toggle em Cards de Produtos
    // ==========================================
    
    // Verificar status de todos os produtos visíveis
    checkAllWishlistStatus();
    
    // Adicionar listeners aos botões
    document.querySelectorAll('.wishlist-toggle-archive').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const token = sessionStorage.getItem('customer_token');
            
            if (!token) {
                alert('Você precisa estar logado para adicionar aos favoritos.');
                window.location.href = '/login';
                return;
            }
            
            const productId = this.dataset.productId;
            toggleWishlistArchive(productId);
        });
    });
    
    function checkAllWishlistStatus() {
        const token = sessionStorage.getItem('customer_token');
        if (!token) return;
        
        // Buscar toda a wishlist de uma vez
        fetch(window.RODUST_API_URL + '/api/wishlist', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.wishlist && data.wishlist.length > 0) {
                data.wishlist.forEach(item => {
                    const icon = document.querySelector(`.wishlist-icon-${item.product_id}`);
                    if (icon) {
                        icon.setAttribute('fill', 'currentColor');
                        icon.classList.remove('text-gray-600');
                        icon.classList.add('text-pink-500');
                    }
                });
            }
        })
        .catch(error => console.error('Erro ao verificar wishlist:', error));
    }
    
    function toggleWishlistArchive(productId) {
        const token = sessionStorage.getItem('customer_token');
        const icon = document.querySelector(`.wishlist-icon-${productId}`);
        const isActive = icon.getAttribute('fill') === 'currentColor';
        
        const url = window.RODUST_API_URL + '/api/wishlist' + (isActive ? '/' + productId : '');
        const method = isActive ? 'DELETE' : 'POST';
        const body = isActive ? null : JSON.stringify({ product_id: parseInt(productId) });
        
        console.log('Wishlist Archive Toggle:', { productId, method, body, url });
        
        fetch(url, {
            method: method,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: body
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.message && data.product_id_received) {
                console.error('Produto não encontrado:', data);
                alert(`Erro: ${data.message}. ID recebido: ${data.product_id_received}`);
                return;
            }
            
            if (isActive) {
                icon.setAttribute('fill', 'none');
                icon.classList.remove('text-pink-500');
                icon.classList.add('text-gray-600');
            } else {
                icon.setAttribute('fill', 'currentColor');
                icon.classList.remove('text-gray-600');
                icon.classList.add('text-pink-500');
            }
            
            // Atualizar contador no header
            if (typeof updateWishlistCount === 'function') {
                updateWishlistCount();
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar wishlist:', error);
            alert('Erro ao atualizar favoritos. Tente novamente.');
        });
    }
});
</script>

<?php get_footer(); ?>
