<?php
/**
 * Template: Single Product (rodust_product)
 * 
 * Layout estilo Loja do Mecânico: 2 colunas superior (galeria + box de compra)
 * DADOS VÊM DA API LARAVEL (dados completos em tempo real)
 *
 * @package Rodust
 */

get_header();

while (have_posts()) : the_post();
    
    // Buscar ID do produto no Laravel
    $laravel_id = get_post_meta(get_the_ID(), '_laravel_id', true);
    
    // Se não tiver ID Laravel, tentar usar _laravel_product_id (compatibilidade)
    if (!$laravel_id) {
        $laravel_id = get_post_meta(get_the_ID(), '_laravel_product_id', true);
    }
    
    // Buscar dados COMPLETOS da API Laravel
    $product = null;
    $api_error = false;
    
    if ($laravel_id) {
        $api_url = 'http://localhost:8000/api/products/' . $laravel_id;
        $response = wp_remote_get($api_url, ['timeout' => 10]);
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $product = json_decode(wp_remote_retrieve_body($response), true);
        } else {
            $api_error = true;
        }
    }
    
    // FALLBACK: Se API falhar, usar dados básicos do WordPress
    if (!$product) {
        $product = [
            'id' => get_the_ID(),
            'name' => get_the_title(),
            'description' => get_the_content(),
            'price' => (float) get_post_meta(get_the_ID(), '_price', true) ?: 0,
            'promotional_price' => (float) get_post_meta(get_the_ID(), '_promotional_price', true) ?: null,
            'stock' => (int) get_post_meta(get_the_ID(), '_stock', true) ?: 0,
            'sku' => get_post_meta(get_the_ID(), '_sku', true) ?: '',
            'brand' => '',
            'images' => [],
            'width' => 0,
            'height' => 0,
            'length' => 0,
            'weight' => 0,
        ];
    }
    
    // Calcular desconto se houver preço promocional
    $discount_percent = 0;
    $final_price = $product['price'];
    
    if (!empty($product['promotional_price']) && $product['promotional_price'] < $product['price']) {
        $discount_percent = round((($product['price'] - $product['promotional_price']) / $product['price']) * 100);
        $final_price = $product['promotional_price'];
    }
    
    // Parcelamento (até 12x sem juros)
    $installment_price = $final_price / 12;
    
    // Galeria de imagens (priorizar da API Laravel, senão WordPress)
    $gallery_ids = [];
    $thumbnail_id = get_post_thumbnail_id();
    
    if (!empty($product['images']) && is_array($product['images'])) {
        // Usar imagens da API Laravel (URLs diretas)
        $use_api_images = true;
        $api_images = $product['images'];
    } else {
        // Fallback: usar imagens do WordPress
        $use_api_images = false;
        if ($thumbnail_id) {
            $gallery_ids[] = $thumbnail_id;
        }
        $gallery_meta = get_post_meta(get_the_ID(), '_product_gallery', true);
        if ($gallery_meta && is_string($gallery_meta)) {
            $additional_ids = array_filter(explode(',', $gallery_meta));
            $gallery_ids = array_merge($gallery_ids, $additional_ids);
        }
        $gallery_ids = array_unique(array_filter($gallery_ids));
    }
    
    $has_images = ($use_api_images && !empty($api_images)) || (!$use_api_images && !empty($gallery_ids));
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center gap-2 text-gray-600">
                <li><a href="<?php echo home_url(); ?>" class="hover:text-blue-600">Início</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                <li><a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" class="hover:text-blue-600">Produtos</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                <li class="text-gray-900 font-medium"><?php the_title(); ?></li>
            </ol>
        </nav>

        <!-- CONTAINER SUPERIOR: 2 Colunas -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 p-6 lg:p-8">
                
                <!-- COLUNA ESQUERDA: Galeria + Descrição -->
                <div class="lg:col-span-8">
                    
                    <!-- Galeria de Imagens -->
                    <div class="mb-6">
                        <?php if ($has_images) : ?>
                            <!-- Imagem Principal -->
                            <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 1/1; max-height: 500px;">
                                <?php if ($use_api_images) : ?>
                                    <img id="main-product-image" 
                                         src="<?php echo esc_url($api_images[0]); ?>" 
                                         alt="<?php echo esc_attr($product['name']); ?>"
                                         class="w-full h-full object-contain p-4">
                                <?php else : ?>
                                    <img id="main-product-image" 
                                         src="<?php echo wp_get_attachment_image_url($gallery_ids[0], 'large'); ?>" 
                                         alt="<?php echo esc_attr($product['name']); ?>"
                                         class="w-full h-full object-contain p-4">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Thumbnails (se tiver mais de 1 imagem) -->
                            <?php if (($use_api_images && count($api_images) > 1) || (!$use_api_images && count($gallery_ids) > 1)) : ?>
                                <div class="grid grid-cols-5 gap-2">
                                    <?php if ($use_api_images) : ?>
                                        <?php foreach ($api_images as $index => $img_url) : ?>
                                            <button type="button" 
                                                    class="gallery-thumb aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 hover:border-blue-500 transition-colors <?php echo $index === 0 ? 'border-blue-500' : 'border-transparent'; ?>"
                                                    data-full-image="<?php echo esc_url($img_url); ?>">
                                                <img src="<?php echo esc_url($img_url); ?>" 
                                                     alt="Imagem <?php echo $index + 1; ?>"
                                                     class="w-full h-full object-cover">
                                            </button>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <?php foreach ($gallery_ids as $index => $img_id) : 
                                            $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                            if (!$thumb_url) continue;
                                        ?>
                                        <button type="button" 
                                                class="gallery-thumb aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 hover:border-blue-500 transition-colors <?php echo $index === 0 ? 'border-blue-500' : 'border-transparent'; ?>"
                                                data-full-image="<?php echo wp_get_attachment_image_url($img_id, 'large'); ?>">
                                            <img src="<?php echo $thumb_url; ?>" 
                                                 alt="Imagem <?php echo $index + 1; ?>"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <!-- Placeholder -->
                            <div class="aspect-square bg-gradient-to-br from-gray-200 via-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nome do Produto -->
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3"><?php echo esc_html($product['name']); ?></h1>
                    
                    <!-- Marca (se houver - DADOS DO LARAVEL) -->
                    <?php if (!empty($product['brand'])) : ?>
                        <div class="mb-3 text-sm text-gray-600">
                            Marca: <span class="font-semibold text-gray-900"><?php echo esc_html($product['brand']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- SKU e Estoque -->
                    <div class="flex items-center gap-4 mb-6 text-sm">
                        <?php if (!empty($product['sku'])) : ?>
                            <span class="text-gray-600">SKU: <span class="font-medium text-gray-900"><?php echo esc_html($product['sku']); ?></span></span>
                        <?php endif; ?>
                        
                        <?php if ($product['stock'] > 0) : ?>
                            <span class="inline-flex items-center gap-1 text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Em estoque (<?php echo $product['stock']; ?> unidades)
                            </span>
                        <?php else : ?>
                            <span class="inline-flex items-center gap-1 text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Produto esgotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Aviso de dados em tempo real (se API funcionou) -->
                    <?php if (!$api_error && $laravel_id) : ?>
                        <div class="mb-4 px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-xs text-green-700">
                            ✓ Preço e estoque atualizados em tempo real
                        </div>
                    <?php elseif ($api_error) : ?>
                        <div class="mb-4 px-3 py-2 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700">
                            ⚠️ Alguns dados podem estar desatualizados
                        </div>
                    <?php endif; ?>

                    <!-- Descrição Curta -->
                    <div class="prose prose-sm max-w-none mb-6 text-gray-700">
                        <?php echo wpautop(wp_trim_words($product['description'], 50)); ?>
                    </div>

                    <!-- Descrição Completa -->
                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrição do Produto</h2>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            <?php echo wpautop($product['description']); ?>
                        </div>
                    </div>

                </div>

                <!-- COLUNA DIREITA: Box de Compra -->
                <div class="lg:col-span-4">
                    
                    <!-- Ícones de Ação (Wishlist e WhatsApp) -->
                    <div class="flex justify-end gap-2 mb-4">
                        <!-- Botão Wishlist -->
                        <button 
                            id="wishlist-toggle-btn"
                            data-product-id="<?php echo esc_attr($product['id']); ?>"
                            class="p-2 rounded-full hover:bg-gray-100 transition-colors group"
                            title="Adicionar aos favoritos">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-pink-500 transition-colors wishlist-icon" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        
                        <!-- Botão WhatsApp -->
                        <a 
                            href="https://wa.me/?text=<?php echo urlencode('Olha esse produto: ' . $product['name'] . ' - ' . get_permalink()); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="p-2 rounded-full hover:bg-green-50 transition-colors group"
                            title="Compartilhar no WhatsApp">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-green-500 transition-colors" 
                                 fill="currentColor" 
                                 viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="lg:sticky lg:top-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            
                            <!-- Badge de Desconto (se houver) -->
                            <?php if ($discount_percent > 0) : ?>
                                <div class="inline-block bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full mb-4">
                                    <?php echo $discount_percent; ?>% OFF
                                </div>
                            <?php endif; ?>

                            <!-- Preços -->
                            <div class="mb-6">
                                <?php if ($discount_percent > 0) : ?>
                                    <!-- Preço Normal Riscado -->
                                    <div class="text-gray-500 text-lg line-through mb-1">
                                        R$ <?php echo number_format($product['price'], 2, ',', '.'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Preço Atual (Grande) -->
                                <div class="text-4xl font-bold text-blue-600 mb-2">
                                    R$ <?php echo number_format($final_price, 2, ',', '.'); ?>
                                </div>
                                
                                <!-- Parcelamento -->
                                <div class="text-sm text-gray-600">
                                    ou <span class="font-semibold text-gray-900">12x de R$ <?php echo number_format($installment_price, 2, ',', '.'); ?></span> sem juros
                                </div>
                            </div>

                            <?php if ($product['stock'] > 0) : ?>
                                <!-- Quantidade -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden w-42">
                                        <button type="button" 
                                                id="qty-decrease-single" 
                                                class="px-4 py-3 bg-gray-100 hover:bg-gray-200 transition-colors flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" 
                                               id="qty-input-single" 
                                               value="1" 
                                               min="1" 
                                               max="<?php echo esc_attr($product['stock']); ?>"
                                               class="flex-1 text-center border-0 focus:ring-0 py-3 text-lg font-medium"
                                               readonly>
                                        <button type="button" 
                                                id="qty-increase-single" 
                                                class="px-4 py-3 bg-gray-100 hover:bg-gray-200 transition-colors flex-shrink-0"
                                                data-max="<?php echo esc_attr($product['stock']); ?>">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Botão Adicionar ao Carrinho -->
                                <button class="btn-add-to-cart w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-3 mb-3" 
                                        data-product-id="<?php echo esc_attr($product['id']); ?>"
                                        data-name="<?php echo esc_attr($product['name']); ?>"
                                        data-price="<?php echo esc_attr($final_price); ?>"
                                        data-image="<?php echo esc_attr($use_api_images && !empty($api_images) ? $api_images[0] : wp_get_attachment_image_url($thumbnail_id, 'thumbnail')); ?>"
                                        data-sku="<?php echo esc_attr($product['sku']); ?>"
                                        data-stock="<?php echo esc_attr($product['stock']); ?>"
                                        data-width="<?php echo esc_attr($product['width']); ?>"
                                        data-height="<?php echo esc_attr($product['height']); ?>"
                                        data-length="<?php echo esc_attr($product['length']); ?>"
                                        data-weight="<?php echo esc_attr($product['weight']); ?>">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Adicionar ao Carrinho
                                </button>
                                
                                <!-- Botão Comprar Agora (Opcional) -->
                                <a href="<?php echo home_url('/checkout'); ?>" 
                                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors duration-200 text-center">
                                    Comprar Agora
                                </a>

                            <?php else : ?>
                                <!-- Produto Esgotado -->
                                <button disabled class="w-full bg-gray-300 text-gray-600 font-bold py-4 px-6 rounded-lg cursor-not-allowed">
                                    Produto Indisponível
                                </button>
                            <?php endif; ?>

                            <!-- Calculadora de Frete -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                    </svg>
                                    Calcular Frete
                                </h3>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           id="shipping-cep-single" 
                                           placeholder="00000-000" 
                                           maxlength="9"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <button type="button" 
                                            id="calculate-shipping-single"
                                            class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                                        OK
                                    </button>
                                </div>
                                <div id="shipping-results-single" class="mt-3"></div>
                                <a href="#" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Não sei meu CEP</a>
                            </div>

                            <!-- Informações Adicionais -->
                            <div class="mt-6 pt-6 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Compra 100% segura
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Parcelamento sem juros
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Devolução grátis em 7 dias
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SEÇÕES INFERIORES (para implementar depois) -->
        <div class="grid lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Especificações Técnicas -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Especificações Técnicas</h2>
                <div class="text-sm text-gray-600">
                    <p>Seção de especificações (implementar depois)</p>
                </div>
            </div>

            <!-- Produtos Relacionados -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Produtos Relacionados</h2>
                <div class="text-sm text-gray-600">
                    <p>Produtos relacionados (implementar depois)</p>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
// Controles de quantidade
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('qty-input-single');
    const decreaseBtn = document.getElementById('qty-decrease-single');
    const increaseBtn = document.getElementById('qty-increase-single');
    
    if (increaseBtn) {
        increaseBtn.addEventListener('click', function() {
            const max = parseInt(this.dataset.max);
            let val = parseInt(qtyInput.value);
            if (val < max) {
                qtyInput.value = val + 1;
            }
        });
    }
    
    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function() {
            let val = parseInt(qtyInput.value);
            if (val > 1) {
                qtyInput.value = val - 1;
            }
        });
    }
    
    // Trocar imagem da galeria
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const fullImage = this.dataset.fullImage;
            document.getElementById('main-product-image').src = fullImage;
            
            // Remover borda de todas
            document.querySelectorAll('.gallery-thumb').forEach(t => {
                t.classList.remove('border-blue-500');
                t.classList.add('border-transparent');
            });
            
            // Adicionar borda na clicada
            this.classList.add('border-blue-500');
            this.classList.remove('border-transparent');
        });
    });
    
    // Máscara CEP
    const cepInput = document.getElementById('shipping-cep-single');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5, 8);
            }
            e.target.value = value;
        });
    }
    
    // ==========================================
    // WISHLIST - Toggle Favorito
    // ==========================================
    
    const wishlistBtn = document.getElementById('wishlist-toggle-btn');
    
    if (wishlistBtn) {
        const productId = wishlistBtn.dataset.productId;
        
        // Verificar se já está na wishlist
        checkWishlistStatus(productId);
        
        // Toggle ao clicar
        wishlistBtn.addEventListener('click', function() {
            const token = sessionStorage.getItem('customer_token');
            
            if (!token) {
                alert('Você precisa estar logado para adicionar aos favoritos.');
                window.location.href = '/login';
                return;
            }
            
            toggleWishlist(productId);
        });
    }
    
    function checkWishlistStatus(productId) {
        const token = sessionStorage.getItem('customer_token');
        if (!token) return;
        
        fetch(window.RODUST_API_URL + '/api/wishlist/check/' + productId, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.in_wishlist) {
                setWishlistActive();
            }
        })
        .catch(error => console.error('Erro ao verificar wishlist:', error));
    }
    
    function toggleWishlist(productId) {
        const token = sessionStorage.getItem('customer_token');
        const icon = wishlistBtn.querySelector('.wishlist-icon');
        const isActive = icon.getAttribute('fill') === 'currentColor';
        
        const url = window.RODUST_API_URL + '/api/wishlist' + (isActive ? '/' + productId : '');
        const method = isActive ? 'DELETE' : 'POST';
        const body = isActive ? null : JSON.stringify({ product_id: parseInt(productId) });
        
        console.log('Wishlist Toggle:', { productId, method, body, url });
        
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
                setWishlistInactive();
            } else {
                setWishlistActive();
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
    
    function setWishlistActive() {
        const icon = wishlistBtn.querySelector('.wishlist-icon');
        icon.setAttribute('fill', 'currentColor');
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-pink-500');
        wishlistBtn.title = 'Remover dos favoritos';
    }
    
    function setWishlistInactive() {
        const icon = wishlistBtn.querySelector('.wishlist-icon');
        icon.setAttribute('fill', 'none');
        icon.classList.remove('text-pink-500');
        icon.classList.add('text-gray-400');
        wishlistBtn.title = 'Adicionar aos favoritos';
    }
});
</script>

<?php
endwhile;
get_footer();
?>
