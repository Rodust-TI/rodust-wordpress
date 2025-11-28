<?php
/**
 * Payment Gateway - Mercado Pago Integration
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Payment_Gateway {

    /**
     * Mercado Pago API settings
     */
    private $api_url = 'https://api.mercadopago.com';
    private $access_token;
    private $public_key;
    private $sandbox_mode;

    /**
     * Constructor
     */
    public function __construct() {
        $settings = get_option('rodust_ecommerce_settings', []);
        $this->access_token = $settings['mercadopago_access_token'] ?? '';
        $this->public_key = $settings['mercadopago_public_key'] ?? '';
        $this->sandbox_mode = isset($settings['mercadopago_sandbox']) && $settings['mercadopago_sandbox'];
    }

    /**
     * Create payment preference (checkout)
     *
     * @param array $order_data Order data from cart
     * @return array|WP_Error Payment preference or error
     */
    public function create_payment($order_data) {
        if (empty($this->access_token)) {
            return new WP_Error('no_token', __('Access Token do Mercado Pago não configurado.', 'rodust-ecommerce'));
        }

        $items = [];
        foreach ($order_data['items'] as $item) {
            $items[] = [
                'id' => (string) $item['product_id'],
                'title' => $item['name'],
                'description' => $item['description'] ?? '',
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['price'],
                'currency_id' => 'BRL',
            ];
        }

        $preference_data = [
            'items' => $items,
            'payer' => [
                'name' => $order_data['customer']['name'] ?? '',
                'email' => $order_data['customer']['email'] ?? '',
                'phone' => [
                    'area_code' => '',
                    'number' => $order_data['customer']['phone'] ?? '',
                ],
                'identification' => [
                    'type' => 'CPF',
                    'number' => $order_data['customer']['document'] ?? '',
                ],
            ],
            'back_urls' => [
                'success' => home_url('/checkout/success'),
                'failure' => home_url('/checkout/failure'),
                'pending' => home_url('/checkout/pending'),
            ],
            'auto_return' => 'approved',
            'external_reference' => $order_data['order_number'] ?? '',
            'notification_url' => home_url('/wp-json/rodust/v1/payment-webhook'),
            'statement_descriptor' => get_bloginfo('name'),
            'payment_methods' => [
                'excluded_payment_methods' => [],
                'excluded_payment_types' => [],
                'installments' => 12,
            ],
        ];

        // Add shipping if provided
        if (!empty($order_data['shipping'])) {
            $preference_data['shipments'] = [
                'cost' => (float) $order_data['shipping']['cost'],
                'mode' => 'not_specified',
            ];
        }

        $response = wp_remote_post($this->api_url . '/checkout/preferences', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($preference_data),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code !== 201) {
            return new WP_Error('api_error', $body['message'] ?? __('Erro ao criar pagamento.', 'rodust-ecommerce'));
        }

        return [
            'preference_id' => $body['id'],
            'init_point' => $this->sandbox_mode ? $body['sandbox_init_point'] : $body['init_point'],
            'public_key' => $this->public_key,
        ];
    }

    /**
     * Get payment info by ID
     *
     * @param string $payment_id
     * @return array|WP_Error
     */
    public function get_payment($payment_id) {
        $response = wp_remote_get($this->api_url . "/v1/payments/{$payment_id}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body;
    }

    /**
     * Process payment webhook notification
     *
     * @param array $data Webhook data
     * @return bool
     */
    public function process_webhook($data) {
        // Log webhook for debugging
        error_log('[Rodust Ecommerce] Mercado Pago Webhook: ' . print_r($data, true));

        $type = $data['type'] ?? '';
        
        if ($type !== 'payment') {
            return false;
        }

        $payment_id = $data['data']['id'] ?? null;
        
        if (!$payment_id) {
            return false;
        }

        // Get full payment details
        $payment = $this->get_payment($payment_id);
        
        if (is_wp_error($payment)) {
            return false;
        }

        $status = $payment['status'] ?? '';
        $external_reference = $payment['external_reference'] ?? '';

        // Update order in Laravel API
        $api_client = new Rodust_API_Client();
        
        $update_data = [
            'payment_id' => $payment_id,
            'payment_status' => $this->map_payment_status($status),
            'payment_method' => $payment['payment_type_id'] ?? 'unknown',
        ];

        // Se pagamento aprovado, atualiza status do pedido
        if ($status === 'approved') {
            $update_data['status'] = 'processing';
        } elseif ($status === 'rejected' || $status === 'cancelled') {
            $update_data['status'] = 'cancelled';
        }

        // Assuming external_reference is the order ID
        $result = $api_client->put("orders/{$external_reference}", $update_data);

        return !is_wp_error($result);
    }

    /**
     * Map Mercado Pago status to internal status
     *
     * @param string $mp_status
     * @return string
     */
    private function map_payment_status($mp_status) {
        $map = [
            'approved' => 'paid',
            'pending' => 'pending',
            'in_process' => 'pending',
            'rejected' => 'failed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
        ];

        return $map[$mp_status] ?? 'pending';
    }

    /**
     * Create PIX payment
     *
     * @param array $order_data
     * @return array|WP_Error
     */
    public function create_pix_payment($order_data) {
        if (empty($this->access_token)) {
            return new WP_Error('no_token', __('Access Token do Mercado Pago não configurado.', 'rodust-ecommerce'));
        }

        $payment_data = [
            'transaction_amount' => (float) $order_data['total'],
            'description' => 'Pedido ' . ($order_data['order_number'] ?? ''),
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $order_data['customer']['email'] ?? '',
                'first_name' => $order_data['customer']['name'] ?? '',
                'identification' => [
                    'type' => 'CPF',
                    'number' => $order_data['customer']['document'] ?? '',
                ],
            ],
            'notification_url' => home_url('/wp-json/rodust/v1/payment-webhook'),
            'external_reference' => $order_data['order_number'] ?? '',
        ];

        $response = wp_remote_post($this->api_url . '/v1/payments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => $order_data['order_number'] ?? uniqid(),
            ],
            'body' => json_encode($payment_data),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code !== 201) {
            return new WP_Error('api_error', $body['message'] ?? __('Erro ao criar pagamento PIX.', 'rodust-ecommerce'));
        }

        return [
            'payment_id' => $body['id'],
            'status' => $body['status'],
            'qr_code' => $body['point_of_interaction']['transaction_data']['qr_code'] ?? '',
            'qr_code_base64' => $body['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '',
            'ticket_url' => $body['point_of_interaction']['transaction_data']['ticket_url'] ?? '',
        ];
    }
}
