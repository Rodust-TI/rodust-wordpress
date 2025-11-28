<?php
/**
 * API Proxy - WordPress → Laravel
 * 
 * Este arquivo permite que requisições AJAX do navegador (HTTPS)
 * sejam repassadas para o Laravel (HTTP) sem erro de Mixed Content.
 */

// Registrar endpoint do proxy
add_action('rest_api_init', function () {
    // Endpoint de teste
    register_rest_route('rodust-proxy/v1', '/test', [
        'methods' => 'GET',
        'callback' => function() {
            return ['success' => true, 'message' => 'Proxy funcionando!'];
        },
        'permission_callback' => '__return_true',
    ]);
    
    // Endpoint principal do proxy - captura tudo após o namespace
    register_rest_route('rodust-proxy/v1', '/api/(?P<path>.*)', [
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        'callback' => 'rodust_api_proxy_handler',
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Handler do proxy
 */
function rodust_api_proxy_handler(WP_REST_Request $request) {
    // Obter caminho da API
    $path = $request->get_param('path');
    $method = $request->get_method();
    
    // Limpar path
    $path = ltrim($path, '/');
    
    // Obter base URL da API
    $base_url = rodust_get_api_url();
    
    // URL da API Laravel
    $api_url = $base_url . '/api/' . $path;
    
    // Preparar argumentos da requisição
    $args = [
        'method' => $method,
        'timeout' => 30,
        'headers' => [],
    ];
    
    // Copiar cabeçalhos importantes
    $headers_to_copy = ['Authorization', 'Content-Type', 'Accept'];
    foreach ($headers_to_copy as $header) {
        $value = $request->get_header($header);
        if ($value) {
            $args['headers'][$header] = $value;
        }
    }
    
    // Adicionar body se for POST/PUT/PATCH
    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        $args['body'] = $request->get_body();
        if (!isset($args['headers']['Content-Type'])) {
            $args['headers']['Content-Type'] = 'application/json';
        }
    }
    
    // Fazer requisição para Laravel
    $response = wp_remote_request($api_url, $args);
    
    // Verificar erro
    if (is_wp_error($response)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => $response->get_error_message(),
        ], 500);
    }
    
    // Obter corpo da resposta
    $body = wp_remote_retrieve_body($response);
    $status_code = wp_remote_retrieve_response_code($response);
    
    // Decodificar JSON
    $data = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Se não for JSON, retornar raw
        return new WP_REST_Response($body, $status_code);
    }
    
    // Retornar resposta
    return new WP_REST_Response($data, $status_code);
}
