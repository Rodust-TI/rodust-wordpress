<?php
/**
 * URL Configuration Helpers
 * 
 * Centraliza todas as URLs do plugin para facilitar migração entre ambientes
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

/**
 * Get Laravel API URL from environment or settings
 * 
 * Priority:
 * 1. Environment variable (wp-config.php)
 * 2. Plugin settings
 * 3. Default localhost
 * 
 * @return string Laravel API base URL
 */
function rodust_plugin_get_api_url() {
    // 1. Tentar variável de ambiente (wp-config.php)
    if (defined('RODUST_API_URL')) {
        $url = rtrim(RODUST_API_URL, '/');
        // Se página está em HTTPS, forçar URL da API para HTTPS também
        if (is_ssl() && strpos($url, 'http://') === 0) {
            $url = str_replace('http://', 'https://', $url);
            // Se for localhost com porta 8000, usar porta 8443
            if (strpos($url, 'localhost:8000') !== false) {
                $url = str_replace('localhost:8000', 'localhost:8443', $url);
            }
        }
        return $url;
    }
    
    // 2. Tentar settings do plugin
    $settings = get_option('rodust_ecommerce_settings', []);
    if (!empty($settings['api_url'])) {
        $url = rtrim($settings['api_url'], '/');
        // Se página está em HTTPS, forçar URL da API para HTTPS também
        if (is_ssl() && strpos($url, 'http://') === 0) {
            // Se for localhost/laravel.test com porta 8000, ajustar para acesso via proxy HTTPS
            if (strpos($url, 'localhost:8000') !== false) {
                $url = str_replace('localhost:8000', 'localhost:8443', $url);
                $url = str_replace('http://', 'https://', $url);
            } elseif (strpos($url, 'laravel.test') !== false) {
                // Usar proxy do WordPress para acessar Laravel via HTTPS
                // O proxy encaminha /wp-json/rodust-proxy/v1/api/... para http://laravel.test/api/...
                // Então retornamos só o base do proxy sem /api
                return home_url('/wp-json/rodust-proxy/v1');
            }
        }
        return $url;
    }
    
    // 3. Fallback para localhost (desenvolvimento)
    $protocol = is_ssl() ? 'https' : 'http';
    $host = defined('RODUST_API_HOST') ? RODUST_API_HOST : 'localhost';
    $port = defined('RODUST_API_PORT') ? RODUST_API_PORT : ($protocol === 'https' ? '8443' : '8000');
    
    return "{$protocol}://{$host}:{$port}/api";
}

/**
 * Get WordPress base URL (for callbacks from Laravel)
 * 
 * @return string WordPress site URL
 */
function rodust_plugin_get_wordpress_url() {
    return rtrim(get_site_url(), '/');
}

/**
 * Get email verification page URL
 * 
 * @return string Full URL for email verification page
 */
function rodust_plugin_get_verify_email_url() {
    return rodust_plugin_get_wordpress_url() . '/verificar-email';
}

/**
 * Get checkout page URL
 * 
 * @return string Checkout page URL
 */
function rodust_plugin_get_checkout_url() {
    $settings = get_option('rodust_ecommerce_settings', []);
    
    // Se tiver página configurada nas settings
    if (!empty($settings['checkout_page_id'])) {
        return get_permalink($settings['checkout_page_id']);
    }
    
    // Fallback: buscar por slug
    $page = get_page_by_path('checkout');
    if ($page) {
        return get_permalink($page->ID);
    }
    
    // Fallback final
    return rodust_plugin_get_wordpress_url() . '/checkout';
}

/**
 * Get all URLs configuration
 * 
 * @return array Complete URL configuration
 */
function rodust_plugin_get_urls_config() {
    return [
        'api' => [
            'base' => rodust_plugin_get_api_url(),
            'products' => rodust_plugin_get_api_url() . '/products',
            'customers' => rodust_plugin_get_api_url() . '/customers',
            'orders' => rodust_plugin_get_api_url() . '/orders',
            'cart' => rodust_plugin_get_api_url() . '/cart',
        ],
        'wordpress' => [
            'base' => rodust_plugin_get_wordpress_url(),
            'verify_email' => rodust_plugin_get_verify_email_url(),
            'checkout' => rodust_plugin_get_checkout_url(),
        ],
    ];
}

/**
 * Check if running in local development
 * 
 * @return bool
 */
function rodust_plugin_is_local_environment() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '.local') !== false ||
        strpos($host, '.test') !== false
    );
}
