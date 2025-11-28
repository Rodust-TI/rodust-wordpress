<?php
/**
 * Shortcodes for frontend display
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Shortcodes {
    
    private static $instance = null;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('rodust_products', [$this, 'products_list']);
        add_shortcode('rodust_cart', [$this, 'cart']);
        add_shortcode('rodust_checkout', [$this, 'checkout']);
        add_shortcode('rodust_payment', [$this, 'payment']);
        add_shortcode('rodust_order_confirmation', [$this, 'order_confirmation']);
        add_shortcode('rodust_cart_count', [$this, 'cart_count']);
    }
    
    /**
     * Products list shortcode
     * Usage: [rodust_products limit="12" category="ferramentas"]
     */
    public function products_list($atts) {
        $atts = shortcode_atts([
            'limit' => 12,
            'category' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ], $atts);
        
        ob_start();
        include RODUST_ECOMMERCE_PATH . 'templates/archive-products.php';
        return ob_get_clean();
    }
    
    /**
     * Cart shortcode
     * Usage: [rodust_cart]
     */
    public function cart($atts) {
        ob_start();
        include RODUST_ECOMMERCE_PATH . 'templates/cart.php';
        return ob_get_clean();
    }
    
    /**
     * Checkout shortcode
     * Usage: [rodust_checkout]
     */
    public function checkout($atts) {
        ob_start();
        include RODUST_ECOMMERCE_PATH . 'templates/checkout.php';
        return ob_get_clean();
    }
    
    /**
     * Payment shortcode
     * Usage: [rodust_payment]
     */
    public function payment($atts) {
        ob_start();
        include RODUST_ECOMMERCE_PATH . 'templates/payment.php';
        return ob_get_clean();
    }
    
    /**
     * Order confirmation shortcode
     * Usage: [rodust_order_confirmation]
     */
    public function order_confirmation($atts) {
        ob_start();
        include RODUST_ECOMMERCE_PATH . 'templates/order-confirmation.php';
        return ob_get_clean();
    }
    
    /**
     * Cart count badge shortcode
     * Usage: [rodust_cart_count]
     */
    public function cart_count($atts) {
        $cart = Rodust_Cart_Manager::instance();
        $count = $cart->get_cart_count();
        
        return sprintf(
            '<span class="rodust-cart-count cart-count" style="display: %s;">%d</span>',
            $count > 0 ? 'inline-block' : 'none',
            $count
        );
    }
}
