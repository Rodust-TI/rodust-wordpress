<?php
/**
 * Template: Checkout Page
 * Refatorado para aplicar SRP (Single Responsibility Principle)
 * 
 * Shortcode: [rodust_checkout]
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

$cart = Rodust_Cart_Manager::instance();

if ($cart->is_empty()) {
    echo '<div class="empty-cart-notice">';
    echo '<p>' . __('Seu carrinho está vazio. Adicione produtos antes de finalizar a compra.', 'rodust-ecommerce') . '</p>';
    echo '<a href="' . get_post_type_archive_link('rodust_product') . '" class="btn btn-primary">' . __('Ver Produtos', 'rodust-ecommerce') . '</a>';
    echo '</div>';
    return;
}

$cart_items = $cart->get_cart();
$subtotal = $cart->get_subtotal();

// Enqueue CSS
wp_enqueue_style(
    'rodust-checkout',
    plugin_dir_url(dirname(__FILE__)) . 'assets/css/checkout.css',
    [],
    '1.0.0'
);

// Enqueue JavaScript modules
wp_enqueue_script('jquery');

// Utilities (sem dependências extras)
wp_enqueue_script(
    'rodust-checkout-utils',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-utils.js',
    ['jquery'],
    '1.0.0',
    true
);

// Customer management
wp_enqueue_script(
    'rodust-checkout-customer',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-customer.js',
    ['jquery', 'rodust-checkout-utils'],
    '1.0.0',
    true
);

// Address management
wp_enqueue_script(
    'rodust-checkout-addresses',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-addresses.js',
    ['jquery', 'rodust-checkout-utils'],
    '1.0.0',
    true
);

// Shipping calculation
wp_enqueue_script(
    'rodust-checkout-shipping',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-shipping.js',
    ['jquery', 'rodust-checkout-utils'],
    '1.0.0',
    true
);

// Form validation and submission
wp_enqueue_script(
    'rodust-checkout-form',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-form.js',
    ['jquery', 'rodust-checkout-customer', 'rodust-checkout-addresses', 'rodust-checkout-shipping'],
    '1.0.0',
    true
);

// Main initialization
wp_enqueue_script(
    'rodust-checkout-init',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/checkout-init.js',
    ['jquery', 'rodust-checkout-form'],
    '1.0.0',
    true
);

// Preparar dados do carrinho com dimensões para JavaScript
$js_cart = array_map(function($item) {
    $product_id = $item['product_id'];
    
    return [
        'id' => $product_id,
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'width' => floatval(get_post_meta($product_id, '_product_width', true) ?: 11),
        'height' => floatval(get_post_meta($product_id, '_product_height', true) ?: 2),
        'length' => floatval(get_post_meta($product_id, '_product_length', true) ?: 17),
        'weight' => floatval(get_post_meta($product_id, '_product_weight', true) ?: 0.3),
    ];
}, $cart_items);

// Localizar dados para JavaScript
wp_localize_script('rodust-checkout-init', 'RODUST_CHECKOUT_DATA', [
    'cart_items' => array_values($js_cart),
    'home_url' => home_url(),
    'login_url' => home_url('/login'),
    'payment_url' => home_url('/checkout-payment'),
    'nonce' => wp_create_nonce('wp_rest'),
]);
?>

<div class="rodust-checkout">
    <div class="container">
        
        <h1 class="page-title"><?php _e('Finalizar Compra', 'rodust-ecommerce'); ?></h1>
        
        <div class="checkout-layout">
            
            <!-- Formulário de Checkout -->
            <div class="checkout-form-section">
                
                <form id="checkout-form" method="post">
                    
                    <?php 
                    // Componente: Dados do Cliente
                    include plugin_dir_path(__FILE__) . 'checkout/customer-form.php'; 
                    ?>
                    
                    <?php 
                    // Componente: Endereço de Entrega
                    include plugin_dir_path(__FILE__) . 'checkout/address-section.php'; 
                    ?>
                    
                    <?php 
                    // Componente: Frete e Entrega
                    include plugin_dir_path(__FILE__) . 'checkout/shipping-section.php'; 
                    ?>
                    
                </form>
                
                <?php 
                // Componente: Modal de Novo Endereço
                include plugin_dir_path(__FILE__) . 'checkout/modal-add-address.php'; 
                ?>
                
            </div>
            
            <?php 
            // Componente: Resumo do Pedido
            include plugin_dir_path(__FILE__) . 'checkout/order-summary.php'; 
            ?>
            
        </div>
        
    </div>
</div>

<?php get_footer(); ?>
