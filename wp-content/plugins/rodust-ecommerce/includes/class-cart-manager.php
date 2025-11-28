<?php
/**
 * Cart Manager - Gerencia o carrinho de compras
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Cart_Manager {

    /**
     * Session key for cart data
     */
    const CART_SESSION_KEY = 'rodust_cart';

    /**
     * Get cart instance (singleton pattern)
     */
    public static function instance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Constructor - Initialize session
     */
    public function __construct() {
        // Session será iniciada via hook WordPress (init)
        // Não chamar session_start() aqui para evitar "headers already sent"
        add_action('init', [$this, 'start_session'], 1);
    }

    /**
     * Start session if not already started
     */
    public function start_session() {
        if (!session_id() && !headers_sent()) {
            session_start();
        }
    }

    /**
     * Add product to cart
     *
     * @param int $product_id Product ID (from Laravel API)
     * @param int $quantity Quantity to add
     * @param array $product_data Product data (name, price, image, etc)
     * @return bool
     */
    public function add_to_cart($product_id, $quantity = 1, $product_data = []) {
        $cart = $this->get_cart();
        
        $quantity = absint($quantity);
        if ($quantity < 1) {
            return false;
        }

        // Se produto já existe no carrinho, incrementa quantidade
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] += $quantity;
        } else {
            // Adiciona novo produto
            $cart[$product_id] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'name' => $product_data['name'] ?? '',
                'price' => floatval($product_data['price'] ?? 0),
                'image' => $product_data['image'] ?? '',
                'sku' => $product_data['sku'] ?? '',
                'stock' => $product_data['stock'] ?? 0,
                'added_at' => current_time('mysql'),
            ];
        }

        return $this->save_cart($cart);
    }

    /**
     * Update cart item quantity
     *
     * @param int $product_id
     * @param int $quantity
     * @return bool
     */
    public function update_quantity($product_id, $quantity) {
        $cart = $this->get_cart();
        
        $quantity = absint($quantity);
        
        if (!isset($cart[$product_id])) {
            return false;
        }

        // Se quantidade for 0, remove o item
        if ($quantity === 0) {
            return $this->remove_from_cart($product_id);
        }

        $cart[$product_id]['quantity'] = $quantity;
        return $this->save_cart($cart);
    }

    /**
     * Remove product from cart
     *
     * @param int $product_id
     * @return bool
     */
    public function remove_from_cart($product_id) {
        $cart = $this->get_cart();
        
        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            return $this->save_cart($cart);
        }

        return false;
    }

    /**
     * Get cart contents
     *
     * @return array
     */
    public function get_cart() {
        if (!isset($_SESSION)) {
            return [];
        }
        
        if (!isset($_SESSION[self::CART_SESSION_KEY])) {
            $_SESSION[self::CART_SESSION_KEY] = [];
        }
        
        return $_SESSION[self::CART_SESSION_KEY];
    }

    /**
     * Get cart item count
     *
     * @return int Total number of items
     */
    public function get_cart_count() {
        $cart = $this->get_cart();
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }

    /**
     * Get cart subtotal (without shipping)
     *
     * @return float
     */
    public function get_subtotal() {
        $cart = $this->get_cart();
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }

    /**
     * Get cart total (with shipping and discounts)
     *
     * @param float $shipping_cost
     * @param float $discount
     * @return float
     */
    public function get_total($shipping_cost = 0, $discount = 0) {
        $subtotal = $this->get_subtotal();
        return $subtotal + $shipping_cost - $discount;
    }

    /**
     * Clear cart
     *
     * @return bool
     */
    public function clear_cart() {
        $_SESSION[self::CART_SESSION_KEY] = [];
        return true;
    }

    /**
     * Check if cart is empty
     *
     * @return bool
     */
    public function is_empty() {
        return empty($this->get_cart());
    }

    /**
     * Save cart to session
     *
     * @param array $cart
     * @return bool
     */
    private function save_cart($cart) {
        if (!isset($_SESSION)) {
            return false;
        }
        
        $_SESSION[self::CART_SESSION_KEY] = $cart;
        
        // Trigger action hook for external plugins
        do_action('rodust_cart_updated', $cart);
        
        return true;
    }

    /**
     * Validate cart items against API (check stock, prices)
     *
     * @return array Validation errors
     */
    public function validate_cart() {
        $cart = $this->get_cart();
        $errors = [];
        $api_client = new Rodust_API_Client();

        foreach ($cart as $product_id => $item) {
            // Buscar dados atualizados do produto na API
            $product = $api_client->get("products/{$product_id}");

            if (is_wp_error($product)) {
                $errors[] = sprintf(
                    __('Produto "%s" não está mais disponível.', 'rodust-ecommerce'),
                    $item['name']
                );
                continue;
            }

            // Verificar estoque
            if ($product['stock'] < $item['quantity']) {
                $errors[] = sprintf(
                    __('Produto "%s" tem apenas %d unidades em estoque.', 'rodust-ecommerce'),
                    $item['name'],
                    $product['stock']
                );
            }

            // Verificar se preço mudou
            if (abs($product['price'] - $item['price']) > 0.01) {
                $errors[] = sprintf(
                    __('O preço de "%s" foi atualizado de %s para %s.', 'rodust-ecommerce'),
                    $item['name'],
                    Rodust_Helpers::format_price($item['price']),
                    Rodust_Helpers::format_price($product['price'])
                );
            }
        }

        return $errors;
    }

    /**
     * Prepare cart data for Laravel API checkout
     *
     * @return array
     */
    public function prepare_for_checkout() {
        $cart = $this->get_cart();
        $items = [];

        foreach ($cart as $product_id => $item) {
            $items[] = [
                'product_id' => $product_id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ];
        }

        return [
            'items' => $items,
            'subtotal' => $this->get_subtotal(),
            'item_count' => $this->get_cart_count(),
        ];
    }
}
