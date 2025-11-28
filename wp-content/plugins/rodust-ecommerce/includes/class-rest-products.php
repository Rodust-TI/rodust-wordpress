<?php
/**
 * REST API Endpoints - Products Sync
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_REST_Products {
    
    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        // POST - Create or update product (usado pelo Laravel)
        register_rest_route('rodust/v1', '/products', [
            'methods' => 'POST',
            'callback' => [$this, 'create_or_update_product'],
            'permission_callback' => '__return_true',
        ]);
        
        // GET - List products (for testing)
        register_rest_route('rodust/v1', '/products', [
            'methods' => 'GET',
            'callback' => [$this, 'list_products'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * List all products (for testing API)
     */
    public function list_products(\WP_REST_Request $request) {
        $args = [
            'post_type' => 'rodust_product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];

        $products = get_posts($args);
        $result = [];

        foreach ($products as $product) {
            $result[] = [
                'id' => $product->ID,
                'title' => $product->post_title,
                'sku' => get_post_meta($product->ID, '_sku', true),
                'price' => get_post_meta($product->ID, '_price', true),
                'stock' => get_post_meta($product->ID, '_stock', true),
                'bling_id' => get_post_meta($product->ID, '_bling_id', true),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'count' => count($result),
            'products' => $result,
        ]);
    }

    /**
     * Create or update a product from Laravel
     */
    public function create_or_update_product(\WP_REST_Request $request) {
        $params = $request->get_json_params();

        // Validação básica
        if (empty($params['sku']) || empty($params['title'])) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'SKU e título são obrigatórios'
            ], 400);
        }

        try {
            // Buscar produto existente pelo SKU ou bling_id
            $existing_product = null;
            $bling_id_str = null;
            $query_found_posts = 0;
            
            if (!empty($params['bling_id'])) {
                // Forçar busca como string
                $bling_id_str = (string) $params['bling_id'];
                
                error_log("🔍 WordPress API: Buscando produto com bling_id = '{$bling_id_str}'");
                
                $query = new \WP_Query([
                    'post_type' => 'rodust_product',
                    'post_status' => 'any',
                    'meta_query' => [
                        [
                            'key' => '_bling_id',
                            'value' => $bling_id_str,
                            'compare' => '='
                        ]
                    ],
                    'posts_per_page' => 1
                ]);

                $query_found_posts = $query->found_posts;
                error_log("🔍 WordPress API: Query SQL = " . $query->request);
                error_log("🔍 WordPress API: Posts encontrados = " . $query_found_posts);

                if ($query->have_posts()) {
                    $existing_product = $query->posts[0];
                    error_log("✅ WordPress API: Produto ENCONTRADO! Post ID = {$existing_product->ID}, Status = {$existing_product->post_status}");
                } else {
                    error_log("❌ WordPress API: Produto NÃO ENCONTRADO por bling_id");
                }
            }

            // Se não encontrou por bling_id, buscar por SKU
            if (!$existing_product && !empty($params['sku'])) {
                $query = new \WP_Query([
                    'post_type' => 'rodust_product',
                    'post_status' => 'any',
                    'meta_query' => [
                        [
                            'key' => '_sku',
                            'value' => $params['sku'],
                            'compare' => '='
                        ]
                    ],
                    'posts_per_page' => 1
                ]);

                if ($query->have_posts()) {
                    $existing_product = $query->posts[0];
                }
            }

            // Preparar dados do post
            $post_data = [
                'post_type' => 'rodust_product',
                'post_title' => sanitize_text_field($params['title']),
                'post_content' => !empty($params['description']) ? wp_kses_post($params['description']) : '',
                'post_status' => 'publish',
            ];

            if ($existing_product) {
                // Atualizar produto existente
                $post_data['ID'] = $existing_product->ID;
                error_log("📝 WordPress API: ATUALIZANDO produto existente ID = {$existing_product->ID}");
                $product_id = wp_update_post($post_data);
                error_log("✅ WordPress API: Produto atualizado, ID = {$product_id}");
            } else {
                // Criar novo produto
                error_log("➕ WordPress API: CRIANDO novo produto");
                $product_id = wp_insert_post($post_data);
                error_log("✅ WordPress API: Produto criado, ID = {$product_id}");
            }

            if (is_wp_error($product_id)) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => $product_id->get_error_message()
                ], 500);
            }

            // Atualizar meta fields
            update_post_meta($product_id, '_sku', sanitize_text_field($params['sku']));
            update_post_meta($product_id, '_price', floatval($params['price'] ?? 0));
            update_post_meta($product_id, '_stock', intval($params['stock'] ?? 0));
            
            if (!empty($params['bling_id'])) {
                update_post_meta($product_id, '_bling_id', sanitize_text_field($params['bling_id']));
            }
            
            // IMPORTANTE: Salvar o Laravel product ID
            if (!empty($params['laravel_id'])) {
                update_post_meta($product_id, '_laravel_id', intval($params['laravel_id']));
            }
            
            // Atribuir marca à taxonomia (se fornecida)
            if (!empty($params['brand'])) {
                $this->assign_brand_taxonomy($product_id, $params['brand']);
            }

            // Imagem destacada (se fornecida)
            if (!empty($params['image_url'])) {
                $this->set_featured_image_from_url($product_id, $params['image_url']);
            }

            return new \WP_REST_Response([
                'success' => true,
                'post_id' => $product_id,
                'action' => $existing_product ? 'updated' : 'created',
                'laravel_id' => $params['laravel_id'] ?? null,
                'message' => $existing_product ? 'Produto atualizado' : 'Produto criado',
                'debug' => [
                    'bling_id_searched' => $bling_id_str,
                    'query_found_posts' => $query_found_posts,
                    'existing_product_id' => $existing_product->ID ?? null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync all products from Laravel API
     */
    public function sync_from_laravel(\WP_REST_Request $request) {
        $api_client = new Rodust_API_Client();
        
        $response = $api_client->get('/products');

        if (!$response['success']) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Erro ao buscar produtos do Laravel: ' . ($response['error'] ?? 'Desconhecido')
            ], 500);
        }

        $products = $response['data'] ?? [];
        $stats = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0
        ];

        foreach ($products as $product_data) {
            $sync_request = new \WP_REST_Request('POST', '/rodust/v1/products');
            $sync_request->set_body_params([
                'sku' => $product_data['sku'] ?? '',
                'title' => $product_data['name'] ?? '',
                'description' => $product_data['description'] ?? '',
                'price' => $product_data['price'] ?? 0,
                'stock' => $product_data['stock'] ?? 0,
                'image_url' => $product_data['image'] ?? '',
                'bling_id' => $product_data['bling_id'] ?? ''
            ]);

            $result = $this->create_or_update_product($sync_request);
            
            if ($result->data['success']) {
                if (str_contains($result->data['message'], 'criado')) {
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }
            } else {
                $stats['errors']++;
            }
        }

        return new \WP_REST_Response([
            'success' => true,
            'stats' => $stats,
            'message' => sprintf(
                'Sincronização concluída: %d criados, %d atualizados, %d erros',
                $stats['created'],
                $stats['updated'],
                $stats['errors']
            )
        ], 200);
    }

    /**
     * Assign brand to product taxonomy, creating term if doesn't exist
     */
    private function assign_brand_taxonomy($product_id, $brand_name) {
        if (empty($brand_name)) {
            return;
        }
        
        $brand_name = trim($brand_name);
        
        // Verificar se o termo já existe
        $term = term_exists($brand_name, 'product_brand');
        
        // Se não existe, criar
        if (!$term) {
            $term = wp_insert_term($brand_name, 'product_brand', [
                'slug' => sanitize_title($brand_name)
            ]);
            
            if (is_wp_error($term)) {
                error_log('Erro ao criar termo de marca: ' . $term->get_error_message());
                return;
            }
        }
        
        // Obter o term_id
        $term_id = is_array($term) ? $term['term_id'] : $term;
        
        // Atribuir a marca ao produto
        $result = wp_set_post_terms($product_id, [$term_id], 'product_brand', false);
        
        if (is_wp_error($result)) {
            error_log('Erro ao atribuir marca ao produto: ' . $result->get_error_message());
        }
    }

    /**
     * Download image from URL and set as featured image
     */
    private function set_featured_image_from_url($post_id, $image_url) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Download image
        $tmp = download_url($image_url);

        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = [
            'name' => basename($image_url),
            'tmp_name' => $tmp
        ];

        // Upload to media library
        $attachment_id = media_handle_sideload($file_array, $post_id);

        if (is_wp_error($attachment_id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        return $attachment_id;
    }
}
