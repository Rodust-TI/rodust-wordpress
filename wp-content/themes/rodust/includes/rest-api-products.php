<?php
/**
 * REST API endpoint for syncing products from Laravel
 * 
 * DESABILITADO: Este endpoint foi movido para o plugin rodust-ecommerce
 * O theme deve ser agnóstico à integração com Laravel
 * 
 * @deprecated Use o plugin rodust-ecommerce para integração com Laravel
 */

// ENDPOINT DESABILITADO - Agora está no plugin rodust-ecommerce
/*
add_action('rest_api_init', function () {
    register_rest_route('rodust/v1', '/products', [
        'methods' => 'POST',
        'callback' => 'rodust_sync_product_from_laravel',
        'permission_callback' => '__return_true',
    ]);
});
*/

/**
 * Handle product sync from Laravel
 * 
 * @deprecated Função mantida apenas para compatibilidade
```
 */
function rodust_sync_product_from_laravel($request) {
    $params = $request->get_json_params();
    
    // Validar dados recebidos
    if (empty($params['sku']) || empty($params['title'])) {
        return new WP_Error('missing_data', 'SKU e title são obrigatórios', ['status' => 400]);
    }

    // 1. Buscar produto existente pelo bling_id (prioridade)
    $existing_product = null;
    $query_found_posts = 0;
    $bling_id_str = null;
    
    if (!empty($params['bling_id'])) {
        $bling_id_str = (string) $params['bling_id'];
        
        $query = new WP_Query([
            'post_type' => 'rodust_product',
            'post_status' => 'any',
            'meta_query' => [[
                'key' => '_bling_id',
                'value' => $bling_id_str,
                'compare' => '='
            ]],
            'posts_per_page' => 1
        ]);
        
        $query_found_posts = $query->found_posts;
        
        if ($query->have_posts()) {
            $existing_product = $query->posts[0];
        }
    }
    
    // 2. Se não encontrou por bling_id, buscar por SKU (fallback)
    if (!$existing_product && !empty($params['sku'])) {
        $query = new WP_Query([
            'post_type' => 'rodust_product',
            'post_status' => 'any',
            'meta_query' => [[
                'key' => '_sku',
                'value' => $params['sku'],
                'compare' => '='
            ]],
            'posts_per_page' => 1
        ]);
        
        if ($query->have_posts()) {
            $existing_product = $query->posts[0];
        }
    }

    $post_data = [
        'post_title' => sanitize_text_field($params['title']),
        'post_content' => wp_kses_post($params['description'] ?? ''),
        'post_type' => 'rodust_product',
        'post_status' => 'publish',
    ];

    if ($existing_product) {
        // Atualizar produto existente
        $post_data['ID'] = $existing_product->ID;
        $post_id = wp_update_post($post_data);
        $action = 'updated';
    } else {
        // Criar novo produto
        $post_id = wp_insert_post($post_data);
        $action = 'created';
    }

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Salvar meta fields (com underscore como padrão WordPress)
    update_post_meta($post_id, '_sku', sanitize_text_field($params['sku']));
    update_post_meta($post_id, '_price', floatval($params['price'] ?? 0));
    update_post_meta($post_id, '_stock', intval($params['stock'] ?? 0));
    update_post_meta($post_id, '_bling_id', sanitize_text_field($params['bling_id'] ?? ''));
    
    // IMPORTANTE: Salvar o Laravel product ID
    if (!empty($params['laravel_id'])) {
        update_post_meta($post_id, '_laravel_id', intval($params['laravel_id']));
    }

    // Processar imagem
    if (!empty($params['image_url'])) {
        rodust_set_product_image($post_id, $params['image_url']);
    }

    return [
        'success' => true,
        'post_id' => $post_id,
        'action' => $action,
        'laravel_id' => $params['laravel_id'] ?? null,
        'message' => $action === 'updated' ? 'Produto atualizado' : 'Produto criado',
        'debug' => [
            'bling_id_searched' => $bling_id_str,
            'query_found_posts' => $query_found_posts,
            'existing_product_id' => $existing_product->ID ?? null,
        ]
    ];
}

/**
 * Download and set product featured image
 */
function rodust_set_product_image($post_id, $image_url) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Verificar se já tem imagem
    if (has_post_thumbnail($post_id)) {
        return;
    }

    // Download da imagem
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return;
    }

    $file_array = [
        'name' => basename($image_url),
        'tmp_name' => $tmp
    ];

    // Fazer upload
    $attachment_id = media_handle_sideload($file_array, $post_id);

    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return;
    }

    // Definir como featured image
    set_post_thumbnail($post_id, $attachment_id);
}
