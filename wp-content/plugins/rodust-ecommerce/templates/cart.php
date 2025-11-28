<?php
/**
 * Template: Cart (Carrinho de Compras)
 * 
 * Shortcode: [rodust_cart]
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

$cart = Rodust_Cart_Manager::instance();
$cart_items = $cart->get_cart();
?>

<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4" style="max-width: 1400px;">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8"><?php _e('Carrinho de Compras', 'rodust-ecommerce'); ?></h1>
        
        <?php if ($cart->is_empty()) : ?>
            
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="text-gray-300 text-8xl mb-6">🛒</div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-3"><?php _e('Seu carrinho está vazio', 'rodust-ecommerce'); ?></h2>
                <p class="text-gray-600 mb-6"><?php _e('Adicione produtos ao carrinho para continuar comprando.', 'rodust-ecommerce'); ?></p>
                <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    <?php _e('Ver Produtos', 'rodust-ecommerce'); ?>
                </a>
            </div>
            
        <?php else : ?>
            
            <!-- Layout 2 Colunas -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- COLUNA ESQUERDA: Produtos (8 colunas) -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        
                        <!-- Header da Tabela -->
                        <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-100 border-b border-gray-200 text-sm font-semibold text-gray-700">
                            <div class="col-span-1"><?php _e('Imagem', 'rodust-ecommerce'); ?></div>
                            <div class="col-span-4"><?php _e('Produto', 'rodust-ecommerce'); ?></div>
                            <div class="col-span-2 text-center"><?php _e('Preço', 'rodust-ecommerce'); ?></div>
                            <div class="col-span-3 text-center"><?php _e('Quantidade', 'rodust-ecommerce'); ?></div>
                            <div class="col-span-2 text-right"><?php _e('Subtotal', 'rodust-ecommerce'); ?></div>
                        </div>
                        
                        <!-- Lista de Produtos -->
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($cart_items as $product_id => $item) : 
                                // Buscar URL do produto no WordPress
                                $wp_post = get_posts([
                                    'post_type' => 'rodust_product',
                                    'meta_key' => '_laravel_id',
                                    'meta_value' => $product_id,
                                    'posts_per_page' => 1,
                                    'fields' => 'ids'
                                ]);
                                $product_url = !empty($wp_post) ? get_permalink($wp_post[0]) : '#';
                            ?>
                                <div class="cart-item px-4 py-3 hover:bg-gray-50 transition-colors" data-product-id="<?php echo esc_attr($product_id); ?>">
                                    
                                    <!-- Linha Principal: Tudo inline -->
                                    <div class="flex items-center gap-4">
                                        
                                        <!-- Imagem pequena -->
                                        <a href="<?php echo esc_url($product_url); ?>" class="flex-shrink-0 hover:opacity-80 transition-opacity">
                                            <?php if (!empty($item['image'])) : ?>
                                                <img src="<?php echo esc_url($item['image']); ?>" 
                                                     alt="<?php echo esc_attr($item['name']); ?>"
                                                     class="w-12 h-12 object-cover rounded border border-gray-200">
                                            <?php else : ?>
                                                <div class="w-12 h-12 bg-gradient-to-br from-gray-200 via-gray-100 to-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        
                                        <!-- Nome do Produto -->
                                        <div class="flex-1 min-w-0" style="max-width: 400px;">
                                            <a href="<?php echo esc_url($product_url); ?>" class="hover:text-blue-600 transition-colors">
                                                <h3 class="font-medium text-gray-900 line-clamp-2" style="font-size: 0.875rem !important; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo esc_html($item['name']); ?></h3>
                                            </a>
                                        </div>
                                        
                                        <!-- Preço Unitário -->
                                        <div class="text-gray-700 text-sm font-medium w-24 text-right">
                                            <?php echo Rodust_Helpers::format_price($item['price']); ?>
                                        </div>
                                        
                                        <!-- Seletor de Quantidade -->
                                        <div class="flex items-center gap-1">
                                            <button type="button" 
                                                    class="qty-minus w-6 h-6 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors" 
                                                    data-product-id="<?php echo esc_attr($product_id); ?>">
                                                <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            
                                            <input type="number" 
                                                   class="qty w-12 text-center border border-gray-300 rounded py-0.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                                   name="quantity[<?php echo esc_attr($product_id); ?>]"
                                                   value="<?php echo esc_attr($item['quantity']); ?>" 
                                                   min="1" 
                                                   max="<?php echo esc_attr($item['stock']); ?>"
                                                   data-product-id="<?php echo esc_attr($product_id); ?>">
                                            
                                            <button type="button" 
                                                    class="qty-plus w-6 h-6 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors" 
                                                    data-product-id="<?php echo esc_attr($product_id); ?>">
                                                <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Subtotal -->
                                        <div class="item-subtotal text-gray-900 font-bold text-sm w-28 text-right" data-product-id="<?php echo esc_attr($product_id); ?>">
                                            <?php echo Rodust_Helpers::format_price($item['price'] * $item['quantity']); ?>
                                        </div>
                                        
                                        <!-- Botão Remover -->
                                        <button type="button" 
                                                class="remove-item w-6 h-6 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors flex-shrink-0" 
                                                data-product-id="<?php echo esc_attr($product_id); ?>" 
                                                title="<?php _e('Remover', 'rodust-ecommerce'); ?>">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        
                                    </div>
                                    
                                    <!-- Linha Secundária: SKU e estoque -->
                                    <div class="flex items-center gap-4 mt-1 ml-16 text-xs text-gray-500">
                                        <?php if (!empty($item['sku'])) : ?>
                                            <span>SKU: <?php echo esc_html($item['sku']); ?></span>
                                        <?php endif; ?>
                                        <span><?php printf(__('%d em estoque', 'rodust-ecommerce'), $item['stock']); ?></span>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Ações do Carrinho -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-3 justify-between items-center">
                            <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" 
                               class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <?php _e('Continuar Comprando', 'rodust-ecommerce'); ?>
                            </a>
                            
                            <button type="button" 
                                    class="btn btn-link clear-cart text-red-600 hover:text-red-700 font-medium">
                                <?php _e('Limpar Carrinho', 'rodust-ecommerce'); ?>
                            </button>
                        </div>
                        
                    </div>
                </div>
                
                <!-- COLUNA DIREITA: Resumo (4 colunas) -->
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                        
                        <h2 class="text-xl font-bold text-gray-900 mb-6"><?php _e('Resumo do Pedido', 'rodust-ecommerce'); ?></h2>
                        
                        <!-- Totais -->
                        <div class="space-y-4 mb-6">
                            
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center text-gray-700">
                                <span><?php _e('Subtotal', 'rodust-ecommerce'); ?></span>
                                <span class="cart-subtotal font-semibold">
                                    <span class="amount"><?php echo Rodust_Helpers::format_price($cart->get_subtotal()); ?></span>
                                </span>
                            </div>
                            
                            <!-- Calculadora de Frete -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center text-gray-700 mb-3">
                                    <span><?php _e('Frete', 'rodust-ecommerce'); ?></span>
                                    <span class="shipping-row">
                                        <span class="amount text-gray-500 text-sm">A calcular</span>
                                    </span>
                                </div>
                                
                                <div id="shipping-calculator" class="space-y-3">
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               id="shipping-postal-code" 
                                               placeholder="00000-000" 
                                               maxlength="9"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <button type="button" 
                                                id="calculate-shipping" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors whitespace-nowrap">
                                            <?php _e('Calcular', 'rodust-ecommerce'); ?>
                                        </button>
                                    </div>
                                    <div id="shipping-options" class="text-sm"></div>
                                </div>
                            </div>
                            
                            <!-- Total -->
                            <div class="border-t-2 border-gray-800 pt-4">
                                <div class="order-total flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900"><?php _e('Total', 'rodust-ecommerce'); ?></span>
                                    <span class="text-2xl font-bold text-gray-900">
                                        <span class="amount total-amount"><?php echo Rodust_Helpers::format_price($cart->get_total()); ?></span>
                                    </span>
                                </div>
                            </div>
                            
                        </div>
                        
                        <!-- Botão Finalizar Compra -->
                        <a href="<?php echo home_url('/checkout'); ?>" 
                           class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors text-center mb-6">
                            <?php _e('Finalizar Compra', 'rodust-ecommerce'); ?>
                            <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        
                        <!-- Formas de Pagamento -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3 text-sm"><?php _e('Formas de Pagamento', 'rodust-ecommerce'); ?></h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center gap-2">
                                    <span>💳</span>
                                    <span><?php _e('Cartão de Crédito (até 12x)', 'rodust-ecommerce'); ?></span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span>📱</span>
                                    <span><?php _e('PIX (aprovação instantânea)', 'rodust-ecommerce'); ?></span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span>🧾</span>
                                    <span><?php _e('Boleto Bancário', 'rodust-ecommerce'); ?></span>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            
        <?php endif; ?>
        
    </div>
</div>
