<?php
/**
 * API Client - Generic HTTP client for Laravel API
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

/**
 * Generic API client for communicating with any REST API
 * Follows Single Responsibility Principle
 */
class Rodust_API_Client {
    
    /**
     * API base URL
     *
     * @var string
     */
    private $api_url;

    /**
     * Request timeout in seconds
     *
     * @var int
     */
    private $timeout;

    /**
     * Constructor
     *
     * @param string $api_url Optional API URL (uses settings if not provided)
     * @param int $timeout Request timeout in seconds
     */
    public function __construct($api_url = null, $timeout = 30) {
        if ($api_url) {
            $this->api_url = $api_url;
        } else {
            // Buscar das configurações do plugin ou usar helper
            $this->api_url = rodust_plugin_get_api_url();
        }
        
        $this->timeout = $timeout;
    }

    /**
     * Perform GET request
     *
     * @param string $endpoint API endpoint (e.g., '/products')
     * @param array $params Query parameters
     * @return array Response with 'success', 'data', 'status', 'error'
     */
    public function get($endpoint, $params = []) {
        return $this->request('GET', $endpoint, $params);
    }

    /**
     * Perform POST request
     *
     * @param string $endpoint API endpoint
     * @param array $data Request body data
     * @param array $params Query parameters
     * @return array Response
     */
    public function post($endpoint, $data = [], $params = []) {
        return $this->request('POST', $endpoint, $params, $data);
    }

    /**
     * Perform PUT request
     *
     * @param string $endpoint API endpoint
     * @param array $data Request body data
     * @return array Response
     */
    public function put($endpoint, $data = []) {
        return $this->request('PUT', $endpoint, [], $data);
    }

    /**
     * Perform DELETE request
     *
     * @param string $endpoint API endpoint
     * @return array Response
     */
    public function delete($endpoint) {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Generic HTTP request method
     *
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @param array $body Request body
     * @return array Response
     */
    private function request($method, $endpoint, $params = [], $body = null) {
        $url = rtrim($this->api_url, '/') . '/' . ltrim($endpoint, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $args = [
            'method' => $method,
            'timeout' => $this->timeout,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        if ($body !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $args['body'] = json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        return $this->handle_response($response);
    }

    /**
     * Handle and normalize API response
     *
     * @param array|WP_Error $response WordPress HTTP response
     * @return array Normalized response
     */
    private function handle_response($response) {
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error' => $response->get_error_message(),
                'status' => 0,
                'data' => null,
            ];
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $success = $status >= 200 && $status < 300;

        return [
            'success' => $success,
            'status' => $status,
            'data' => $data,
            'error' => $success ? null : ($data['message'] ?? $data['error'] ?? 'Unknown error'),
        ];
    }

    /**
     * Test API connection
     *
     * @return bool True if connection is successful
     */
    public function test_connection() {
        $result = $this->get('/products', ['per_page' => 1]);
        return $result['success'];
    }

    /**
     * Set API URL
     *
     * @param string $url API base URL
     */
    public function set_api_url($url) {
        $this->api_url = rtrim($url, '/');
    }

    /**
     * Get current API URL
     *
     * @return string
     */
    public function get_api_url() {
        return $this->api_url;
    }
}
