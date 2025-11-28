<?php
/**
 * Shipping Calculator - Calcula frete usando API Melhor Envio
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Shipping_Calculator {

    /**
     * Melhor Envio API settings
     */
    private $api_url = 'https://melhorenvio.com.br/api/v2/me';
    private $api_token;
    private $sandbox_mode;

    /**
     * Constructor
     */
    public function __construct() {
        $settings = get_option('rodust_ecommerce_settings', []);
        $this->api_token = $settings['melhorenvio_token'] ?? '';
        $this->sandbox_mode = isset($settings['melhorenvio_sandbox']) && $settings['melhorenvio_sandbox'];

        // Use sandbox URL if enabled
        if ($this->sandbox_mode) {
            $this->api_url = 'https://sandbox.melhorenvio.com.br/api/v2/me';
        }
    }

    /**
     * Calculate shipping options for cart
     *
     * @param string $to_postal_code Destination postal code
     * @return array|WP_Error Shipping options or error
     */
    public function calculate_shipping($to_postal_code) {
        if (empty($this->api_token)) {
            return new WP_Error('no_token', __('Token da API Melhor Envio não configurado.', 'rodust-ecommerce'));
        }

        // Get origin postal code from settings
        $settings = get_option('rodust_ecommerce_settings', []);
        $from_postal_code = $settings['origin_postal_code'] ?? '';

        if (empty($from_postal_code)) {
            return new WP_Error('no_origin', __('CEP de origem não configurado.', 'rodust-ecommerce'));
        }

        // Prepare cart data (dimensions and weight)
        $cart_data = $this->prepare_cart_dimensions();

        $body = [
            'from' => ['postal_code' => $this->sanitize_postal_code($from_postal_code)],
            'to' => ['postal_code' => $this->sanitize_postal_code($to_postal_code)],
            'package' => $cart_data,
            'services' => '1,2,3,17', // Correios PAC, SEDEX, Jadlog, Loggi
        ];

        $response = wp_remote_post($this->api_url . '/shipment/calculate', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($body),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code !== 200) {
            return new WP_Error('api_error', $body['message'] ?? __('Erro ao calcular frete.', 'rodust-ecommerce'));
        }

        // Format shipping options
        return $this->format_shipping_options($body);
    }

    /**
     * Prepare cart dimensions and weight
     *
     * @return array Package data
     */
    private function prepare_cart_dimensions() {
        $cart = Rodust_Cart_Manager::instance()->get_cart();
        
        // Default package dimensions (você pode pegar do produto na API)
        $total_weight = 0;
        $max_height = 2; // cm
        $max_width = 11; // cm
        $max_length = 16; // cm

        foreach ($cart as $item) {
            // Aqui você pode buscar dimensões reais de cada produto via API
            // Por enquanto, usa valores padrão
            $total_weight += ($item['weight'] ?? 0.3) * $item['quantity']; // kg
        }

        return [
            'height' => $max_height,
            'width' => $max_width,
            'length' => $max_length,
            'weight' => max(0.3, $total_weight), // Peso mínimo 0.3kg
        ];
    }

    /**
     * Format shipping options for display
     *
     * @param array $response API response
     * @return array Formatted options
     */
    private function format_shipping_options($response) {
        $options = [];

        foreach ($response as $option) {
            if (isset($option['error']) || !isset($option['price'])) {
                continue;
            }

            $options[] = [
                'id' => $option['id'],
                'name' => $option['name'],
                'company' => $option['company']['name'] ?? '',
                'price' => floatval($option['price']),
                'delivery_time' => intval($option['delivery_time'] ?? 0),
                'delivery_range' => $option['delivery_range'] ?? [],
                'formatted_price' => Rodust_Helpers::format_price($option['price']),
                'formatted_time' => sprintf(
                    _n('%d dia útil', '%d dias úteis', $option['delivery_time'] ?? 0, 'rodust-ecommerce'),
                    $option['delivery_time'] ?? 0
                ),
            ];
        }

        // Sort by price (cheapest first)
        usort($options, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        return $options;
    }

    /**
     * Sanitize postal code (remove non-numeric)
     *
     * @param string $postal_code
     * @return string
     */
    private function sanitize_postal_code($postal_code) {
        return Rodust_Helpers::sanitize_postal_code($postal_code);
    }

    /**
     * Validate postal code format
     *
     * @param string $postal_code
     * @return bool
     */
    public function validate_postal_code($postal_code) {
        $clean = $this->sanitize_postal_code($postal_code);
        return strlen($clean) === 8;
    }
}
