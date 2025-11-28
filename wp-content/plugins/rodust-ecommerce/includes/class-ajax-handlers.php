<?php
/**
 * AJAX Handlers
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Ajax_Handlers {
    
    private static $instance = null;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Admin AJAX
        add_action('wp_ajax_rodust_test_api_connection', [$this, 'test_api_connection']);
        add_action('wp_ajax_rodust_sync_product', [$this, 'sync_product']);
        
        // Frontend AJAX (logged in and not logged in)
        add_action('wp_ajax_rodust_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_nopriv_rodust_add_to_cart', [$this, 'add_to_cart']);
        
        add_action('wp_ajax_rodust_update_cart', [$this, 'update_cart']);
        add_action('wp_ajax_nopriv_rodust_update_cart', [$this, 'update_cart']);
        
        add_action('wp_ajax_rodust_remove_from_cart', [$this, 'remove_from_cart']);
        add_action('wp_ajax_nopriv_rodust_remove_from_cart', [$this, 'remove_from_cart']);
        
        add_action('wp_ajax_rodust_clear_cart', [$this, 'clear_cart']);
        add_action('wp_ajax_nopriv_rodust_clear_cart', [$this, 'clear_cart']);
        
        add_action('wp_ajax_rodust_get_cart_count', [$this, 'get_cart_count']);
        add_action('wp_ajax_nopriv_rodust_get_cart_count', [$this, 'get_cart_count']);
        
        add_action('wp_ajax_rodust_calculate_shipping', [$this, 'calculate_shipping']);
        add_action('wp_ajax_nopriv_rodust_calculate_shipping', [$this, 'calculate_shipping']);
        
        add_action('wp_ajax_rodust_process_checkout', [$this, 'process_checkout']);
        add_action('wp_ajax_nopriv_rodust_process_checkout', [$this, 'process_checkout']);
    }

    /**
     * Test API connection (Admin only)
     */
    public function test_api_connection() {
        check_ajax_referer('rodust_test_connection', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permissão negada']);
        }

        $api_url = sanitize_text_field($_POST['api_url'] ?? '');

        if (empty($api_url)) {
            wp_send_json_error(['message' => 'URL da API não informada']);
        }

        $client = new Rodust_API_Client($api_url);
        $success = $client->test_connection();

        if ($success) {
            wp_send_json_success(['message' => 'Conexão bem-sucedida!']);
        } else {
            wp_send_json_error(['message' => 'Não foi possível conectar à API']);
        }
    }

    /**
     * Sync product with Laravel (Admin only)
     */
    public function sync_product() {
        check_ajax_referer('rodust_sync_product', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permissão negada']);
        }

        $product_id = absint($_POST['product_id'] ?? 0);

        if (!$product_id) {
            wp_send_json_error(['message' => 'ID do produto inválido']);
        }

        // TODO: Implementar sincronização via Rodust_Product_Sync
        wp_send_json_success(['message' => 'Produto sincronizado com sucesso']);
    }

    /**
     * Add product to cart
     */
    public function add_to_cart() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $product_id = absint($_POST['product_id'] ?? 0);
        $quantity = absint($_POST['quantity'] ?? 1);
        $product_data = $_POST['product_data'] ?? [];

        if (!$product_id) {
            wp_send_json_error(['message' => 'Produto inválido']);
        }

        $cart = Rodust_Cart_Manager::instance();
        $success = $cart->add_to_cart($product_id, $quantity, $product_data);

        if ($success) {
            wp_send_json_success([
                'message' => 'Produto adicionado ao carrinho',
                'cart_count' => $cart->get_cart_count(),
                'subtotal' => $cart->get_subtotal(),
            ]);
        } else {
            wp_send_json_error(['message' => 'Erro ao adicionar produto']);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update_cart() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $product_id = absint($_POST['product_id'] ?? 0);
        $quantity = absint($_POST['quantity'] ?? 1);

        if (!$product_id) {
            wp_send_json_error(['message' => 'Produto inválido']);
        }

        $cart = Rodust_Cart_Manager::instance();
        $success = $cart->update_quantity($product_id, $quantity);

        if ($success) {
            wp_send_json_success([
                'message' => 'Carrinho atualizado',
                'cart_count' => $cart->get_cart_count(),
                'subtotal' => $cart->get_subtotal(),
                'total' => $cart->get_total(),
            ]);
        } else {
            wp_send_json_error(['message' => 'Erro ao atualizar carrinho']);
        }
    }

    /**
     * Remove product from cart
     */
    public function remove_from_cart() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $product_id = absint($_POST['product_id'] ?? 0);

        if (!$product_id) {
            wp_send_json_error(['message' => 'Produto inválido']);
        }

        $cart = Rodust_Cart_Manager::instance();
        $success = $cart->remove_from_cart($product_id);

        if ($success) {
            wp_send_json_success([
                'message' => 'Produto removido do carrinho',
                'cart_count' => $cart->get_cart_count(),
                'subtotal' => $cart->get_subtotal(),
                'total' => $cart->get_total(),
            ]);
        } else {
            wp_send_json_error(['message' => 'Erro ao remover produto']);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear_cart() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $cart = Rodust_Cart_Manager::instance();
        $cart->clear_cart();

        wp_send_json_success([
            'message' => 'Carrinho limpo',
            'cart_count' => 0,
        ]);
    }

    /**
     * Get cart count
     */
    public function get_cart_count() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $cart = Rodust_Cart_Manager::instance();

        wp_send_json_success([
            'count' => $cart->get_cart_count(),
        ]);
    }

    /**
     * Calculate shipping
     */
    public function calculate_shipping() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $postal_code = sanitize_text_field($_POST['postal_code'] ?? '');

        if (empty($postal_code)) {
            wp_send_json_error(['message' => 'CEP não informado']);
        }

        $calculator = new Rodust_Shipping_Calculator();
        $options = $calculator->calculate_shipping($postal_code);

        if (is_wp_error($options)) {
            wp_send_json_error(['message' => $options->get_error_message()]);
        }

        wp_send_json_success([
            'options' => $options,
        ]);
    }

    /**
     * Process checkout
     */
    public function process_checkout() {
        check_ajax_referer('rodust_ecommerce_nonce', 'nonce');

        $form_data = $_POST['form_data'] ?? [];
        
        if (empty($form_data)) {
            wp_send_json_error(['message' => 'Dados do formulário não enviados']);
        }

        // Parse form data
        $checkout_data = [];
        foreach ($form_data as $field) {
            $checkout_data[$field['name']] = sanitize_text_field($field['value']);
        }

        // Validate cart
        $cart = Rodust_Cart_Manager::instance();
        
        if ($cart->is_empty()) {
            wp_send_json_error(['message' => 'Carrinho vazio']);
        }

        $errors = $cart->validate_cart();
        
        if (!empty($errors)) {
            wp_send_json_error(['message' => implode('<br>', $errors)]);
        }

        // Prepare order data for Laravel API
        $cart_data = $cart->prepare_for_checkout();
        
        $order_data = [
            'customer' => [
                'name' => $checkout_data['customer_name'] ?? '',
                'email' => $checkout_data['customer_email'] ?? '',
                'phone' => $checkout_data['customer_phone'] ?? '',
                'document' => $checkout_data['customer_document'] ?? '',
            ],
            'items' => $cart_data['items'],
            'subtotal' => $cart_data['subtotal'],
            'shipping' => [
                'cost' => floatval($checkout_data['shipping_cost'] ?? 0),
                'method' => $checkout_data['shipping_method'] ?? '',
            ],
            'total' => $cart_data['subtotal'] + floatval($checkout_data['shipping_cost'] ?? 0),
            'payment_method' => $checkout_data['payment_method'] ?? 'mercadopago',
        ];

        // Create order in Laravel API
        $api_client = new Rodust_API_Client();
        $order_response = $api_client->post('orders', $order_data);

        if (is_wp_error($order_response)) {
            wp_send_json_error(['message' => 'Erro ao criar pedido: ' . $order_response->get_error_message()]);
        }

        $order_number = $order_response['order_number'] ?? '';
        $order_data['order_number'] = $order_number;

        // Process payment
        $payment_gateway = new Rodust_Payment_Gateway();
        
        if ($checkout_data['payment_method'] === 'pix') {
            $payment_response = $payment_gateway->create_pix_payment($order_data);
        } else {
            $payment_response = $payment_gateway->create_payment($order_data);
        }

        if (is_wp_error($payment_response)) {
            wp_send_json_error(['message' => 'Erro ao processar pagamento: ' . $payment_response->get_error_message()]);
        }

        // Clear cart
        $cart->clear_cart();

        // Return payment redirect URL
        wp_send_json_success([
            'message' => 'Pedido criado com sucesso!',
            'order_number' => $order_number,
            'redirect_url' => $payment_response['init_point'] ?? '',
            'payment_data' => $payment_response,
        ]);
    }
}
