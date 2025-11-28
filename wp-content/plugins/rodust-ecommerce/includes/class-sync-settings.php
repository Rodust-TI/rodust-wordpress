<?php
/**
 * Sync Settings Page - Sincronização com Laravel/Bling
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Sync_Settings {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page'], 25);
        add_action('admin_post_rodust_sync_products', [$this, 'handle_sync_products']);
        add_action('admin_post_rodust_sync_customers', [$this, 'handle_sync_customers']);
    }

    public function add_menu_page() {
        add_submenu_page(
            'rodust-ecommerce',
            'Sincronização',
            'Sincronização',
            'manage_options',
            'rodust-sync',
            [$this, 'render_page']
        );
    }

    public function render_page() {
        // Verificar mensagens de sucesso/erro
        $message = get_transient('rodust_sync_message');
        $message_type = get_transient('rodust_sync_message_type');
        
        if ($message) {
            delete_transient('rodust_sync_message');
            delete_transient('rodust_sync_message_type');
        }

        ?>
        <div class="wrap">
            <h1>🔄 Sincronização de Dados</h1>
            <p class="description">Sincronize produtos e dados entre Bling, Laravel e WordPress.</p>

            <?php if ($message): ?>
                <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                    <p><?php echo esc_html($message); ?></p>
                </div>
            <?php endif; ?>

            <div class="card" style="margin-top: 20px;">
                <h2>📦 Produtos</h2>
                <p>Sincronizar produtos do Bling → Laravel → WordPress</p>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top: 15px;">
                    <?php wp_nonce_field('rodust_sync_products', 'rodust_sync_nonce'); ?>
                    <input type="hidden" name="action" value="rodust_sync_products">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="sync_limit">Limite de Produtos</label>
                            </th>
                            <td>
                                <input 
                                    type="number" 
                                    name="sync_limit" 
                                    id="sync_limit" 
                                    value="100" 
                                    min="1" 
                                    max="1000"
                                    class="small-text"
                                >
                                <p class="description">Quantidade máxima de produtos a sincronizar (padrão: 100)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="force_sync">Forçar Sincronização</label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="force_sync" id="force_sync" value="1">
                                    Atualizar todos os produtos, mesmo sem alterações
                                </label>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button('🔄 Sincronizar Produtos Agora', 'primary', 'submit', false); ?>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>👥 Clientes</h2>
                <p>Sincronizar clientes do WordPress → Laravel → Bling</p>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top: 15px;">
                    <?php wp_nonce_field('rodust_sync_customers', 'rodust_sync_customers_nonce'); ?>
                    <input type="hidden" name="action" value="rodust_sync_customers">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="sync_customer_limit">Limite de Clientes</label>
                            </th>
                            <td>
                                <input 
                                    type="number" 
                                    name="sync_limit" 
                                    id="sync_customer_limit" 
                                    value="100" 
                                    min="1" 
                                    max="1000"
                                    class="small-text"
                                >
                                <p class="description">Quantidade máxima de clientes a sincronizar (padrão: 100)</p>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button('👥 Sincronizar Clientes Agora', 'primary', 'submit', false); ?>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>🛒 Pedidos</h2>
                <p>Enviar pedidos do WordPress → Laravel → Bling</p>
                <button class="button" disabled>⏳ Em Desenvolvimento</button>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>📊 Estoques</h2>
                <p>Atualizar quantidades de estoque do Bling</p>
                <button class="button" disabled>⏳ Em Desenvolvimento</button>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>⚙️ Sincronização Automática</h2>
                <p>
                    <strong>Status:</strong> 
                    <?php 
                    $auto_sync = get_option('rodust_auto_sync_enabled', false);
                    echo $auto_sync ? '✅ Ativa (a cada 1 hora)' : '❌ Desativada'; 
                    ?>
                </p>
                <p class="description">
                    Configure a sincronização automática em 
                    <a href="<?php echo admin_url('admin.php?page=rodust-settings'); ?>">Configurações</a>
                </p>
            </div>

            <div style="margin-top: 30px; padding: 15px; background: #f0f0f1; border-left: 4px solid #2271b1;">
                <h3 style="margin-top: 0;">📖 Como Funciona</h3>
                <ol>
                    <li><strong>Bling → Laravel:</strong> O Laravel busca produtos da API do Bling e salva no banco de dados</li>
                    <li><strong>Laravel → WordPress:</strong> O WordPress recebe produtos via API REST do Laravel</li>
                    <li><strong>Resultado:</strong> Produtos aparecem automaticamente no site como Custom Post Type</li>
                </ol>
                <p><strong>Tempo estimado:</strong> ~1 segundo por produto (100 produtos = ~2 minutos)</p>
            </div>
        </div>

        <style>
            .card h2 {
                margin-top: 0;
                font-size: 18px;
            }
        </style>
        <?php
    }

    public function handle_sync_products() {
        // Verificar permissões e nonce
        if (!current_user_can('manage_options')) {
            wp_die('Você não tem permissão para acessar esta página.');
        }

        check_admin_referer('rodust_sync_products', 'rodust_sync_nonce');

        $limit = isset($_POST['sync_limit']) ? intval($_POST['sync_limit']) : 100;
        $force = isset($_POST['force_sync']) && $_POST['force_sync'] === '1';

        try {
            $api_client = new Rodust_API_Client();
            
            // DEBUG: Log API URL
            error_log('=== RODUST SYNC DEBUG ===');
            error_log('API URL: ' . get_option('rodust_api_url', 'http://localhost/api'));
            error_log('Limit: ' . $limit . ', Force: ' . ($force ? 'true' : 'false'));
            
            // Chamar endpoint de sincronização do Laravel
            error_log('Calling POST /products/sync-from-bling...');
            $response = $api_client->post('/products/sync-from-bling', [
                'limit' => $limit,
                'force' => $force
            ]);

            error_log('Response status: ' . ($response['success'] ? 'SUCCESS' : 'FAILED'));
            error_log('Response status code: ' . ($response['status'] ?? 'N/A'));
            error_log('Full response: ' . print_r($response, true));

            if ($response['success']) {
                $data = $response['data'];
                $total_products = isset($data['total_products']) ? $data['total_products'] : 0;
                
                error_log('Total products from Bling: ' . $total_products);
                
                // Agora buscar produtos do Laravel para sincronizar no WordPress
                $sync_response = $api_client->get('/products', ['per_page' => 1000]);
                
                error_log('Laravel get products response: ' . print_r($sync_response, true));
                
                if ($sync_response['success']) {
                    $products_data = $sync_response['data'];
                    $products = isset($products_data['data']) ? $products_data['data'] : [];
                    
                    error_log('Products count: ' . count($products));
                    
                    $wp_stats = $this->sync_products_to_wordpress($products);
                    
                    error_log('WordPress sync stats: ' . print_r($wp_stats, true));
                    
                    set_transient('rodust_sync_message', 
                        sprintf(
                            'Sincronização concluída! Bling→Laravel: %d produtos. WordPress: %d criados, %d atualizados, %d deletados.',
                            $total_products,
                            $wp_stats['created'],
                            $wp_stats['updated'],
                            $wp_stats['deleted']
                        ), 
                        30
                    );
                    set_transient('rodust_sync_message_type', 'success', 30);
                } else {
                    $error_msg = $sync_response['error'] ?? 'Desconhecido';
                    error_log('ERROR getting products from Laravel: ' . $error_msg);
                    
                    set_transient('rodust_sync_message', 
                        'Produtos sincronizados no Laravel (' . $total_products . '), mas erro ao buscar para WordPress: ' . $error_msg, 
                        30
                    );
                    set_transient('rodust_sync_message_type', 'warning', 30);
                }
            } else {
                $error_msg = $response['error'] ?? 'Erro desconhecido';
                error_log('ERROR syncing from Bling: ' . $error_msg);
                error_log('Full response: ' . print_r($response, true));
                
                set_transient('rodust_sync_message', 'Erro ao sincronizar com Bling: ' . $error_msg, 30);
                set_transient('rodust_sync_message_type', 'error', 30);
            }
        } catch (Exception $e) {
            error_log('EXCEPTION in sync: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Mensagem de erro mais detalhada
            $error_details = sprintf(
                'Erro na sincronização: %s (Linha: %d, Arquivo: %s)',
                $e->getMessage(),
                $e->getLine(),
                basename($e->getFile())
            );
            
            set_transient('rodust_sync_message', $error_details, 30);
            set_transient('rodust_sync_message_type', 'error', 30);
        } catch (Throwable $e) {
            error_log('FATAL ERROR in sync: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            set_transient('rodust_sync_message', 'Erro fatal: ' . $e->getMessage(), 30);
            set_transient('rodust_sync_message_type', 'error', 30);
        }

        wp_redirect(admin_url('admin.php?page=rodust-sync'));
        exit;
    }

    private function sync_products_to_wordpress($products) {
        $stats = ['created' => 0, 'updated' => 0, 'deleted' => 0];

        // IDs do Bling que existem (para deletar produtos removidos)
        $bling_ids_from_api = array_column($products, 'bling_id');

        foreach ($products as $product_data) {
            try {
                // Validar dados essenciais
                if (empty($product_data['bling_id']) || empty($product_data['name'])) {
                    error_log('Produto inválido (sem bling_id ou nome): ' . print_r($product_data, true));
                    continue;
                }

                // Buscar produto existente
                $existing = $this->find_product_by_bling_id($product_data['bling_id']);

                $post_data = [
                    'post_type' => 'rodust_product',
                    'post_title' => sanitize_text_field($product_data['name']),
                    'post_content' => wp_kses_post($product_data['description'] ?? ''),
                    'post_status' => ($product_data['active'] ?? true) ? 'publish' : 'draft',
                ];

                if ($existing) {
                    $post_data['ID'] = $existing->ID;
                    wp_update_post($post_data);
                    $stats['updated']++;
                    $product_id = $existing->ID;
                } else {
                    $product_id = wp_insert_post($post_data);
                    $stats['created']++;
                }

                // Atualizar meta fields
                update_post_meta($product_id, '_sku', sanitize_text_field($product_data['sku'] ?? ''));
                update_post_meta($product_id, '_price', floatval($product_data['price'] ?? 0));
                update_post_meta($product_id, '_stock', intval($product_data['stock'] ?? 0));
                update_post_meta($product_id, '_bling_id', sanitize_text_field($product_data['bling_id']));

                // Imagem (se houver)
                if (!empty($product_data['image'])) {
                    $this->set_featured_image($product_id, $product_data['image']);
                }
            } catch (Exception $e) {
                error_log('Erro ao sincronizar produto: ' . $e->getMessage());
                error_log('Dados do produto: ' . print_r($product_data, true));
            }
        }

        // Deletar produtos que não existem mais no Bling
        if (!empty($bling_ids_from_api)) {
            $stats['deleted'] = $this->delete_removed_products($bling_ids_from_api);
        }

        return $stats;
    }

    private function find_product_by_bling_id($bling_id) {
        $query = new WP_Query([
            'post_type' => 'rodust_product',
            'meta_query' => [
                [
                    'key' => '_bling_id',
                    'value' => $bling_id,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => 1
        ]);

        return $query->have_posts() ? $query->posts[0] : null;
    }

    private function delete_removed_products($bling_ids_from_api) {
        // Buscar todos os produtos do WordPress que tem bling_id
        $all_wp_products = new WP_Query([
            'post_type' => 'rodust_product',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_bling_id',
                    'compare' => 'EXISTS'
                ]
            ]
        ]);

        $deleted_count = 0;

        if ($all_wp_products->have_posts()) {
            foreach ($all_wp_products->posts as $wp_product) {
                $wp_bling_id = get_post_meta($wp_product->ID, '_bling_id', true);
                
                // Se o bling_id não está mais na lista do Bling, deletar
                if (!in_array($wp_bling_id, $bling_ids_from_api)) {
                    error_log("Deletando produto removido do Bling: {$wp_product->post_title} (ID Bling: {$wp_bling_id})");
                    wp_delete_post($wp_product->ID, true); // true = forçar deleção permanente
                    $deleted_count++;
                }
            }
        }

        return $deleted_count;
    }

    private function set_featured_image($post_id, $image_url) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = [
            'name' => basename($image_url),
            'tmp_name' => $tmp
        ];

        $attachment_id = media_handle_sideload($file_array, $post_id);
        if (is_wp_error($attachment_id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        set_post_thumbnail($post_id, $attachment_id);
        return $attachment_id;
    }

    public function handle_sync_customers() {
        // Verificar permissões e nonce
        if (!current_user_can('manage_options')) {
            wp_die('Você não tem permissão para acessar esta página.');
        }

        check_admin_referer('rodust_sync_customers', 'rodust_sync_customers_nonce');

        $limit = isset($_POST['sync_limit']) ? intval($_POST['sync_limit']) : 100;

        try {
            // Buscar usuários do WordPress
            $users = get_users([
                'number' => $limit,
                'orderby' => 'registered',
                'order' => 'DESC'
            ]);

            error_log('=== RODUST CUSTOMER SYNC DEBUG ===');
            error_log('Total WordPress users found: ' . count($users));

            $customers_data = [];
            
            foreach ($users as $user) {
                // Coletar dados do usuário
                $customer = [
                    'name' => $user->display_name ?: $user->user_login,
                    'email' => $user->user_email,
                    'phone' => get_user_meta($user->ID, 'billing_phone', true) ?: get_user_meta($user->ID, 'phone', true),
                    'cpf' => get_user_meta($user->ID, 'billing_cpf', true) ?: get_user_meta($user->ID, 'cpf', true),
                    'cnpj' => get_user_meta($user->ID, 'billing_cnpj', true) ?: get_user_meta($user->ID, 'cnpj', true),
                    'person_type' => get_user_meta($user->ID, 'person_type', true) ?: 'F',
                    'birth_date' => get_user_meta($user->ID, 'birth_date', true),
                    'fantasy_name' => get_user_meta($user->ID, 'fantasy_name', true),
                    'nfe_email' => get_user_meta($user->ID, 'nfe_email', true),
                    'phone_commercial' => get_user_meta($user->ID, 'phone_commercial', true),
                ];

                // Adicionar apenas se tiver email
                if (!empty($customer['email'])) {
                    $customers_data[] = $customer;
                }
            }

            error_log('Customers prepared for sync: ' . count($customers_data));

            // Enviar para Laravel
            $api_client = new Rodust_API_Client();
            $response = $api_client->post('/customers/sync-from-wordpress', [
                'customers' => $customers_data
            ]);

            error_log('Laravel response: ' . print_r($response, true));

            if ($response['success']) {
                $stats = $response['data']['stats'] ?? ['created' => 0, 'updated' => 0, 'synced_to_bling' => 0, 'errors' => 0];
                
                set_transient('rodust_sync_message', 
                    sprintf(
                        'Sincronização concluída! %d clientes criados, %d atualizados, %d sincronizados no Bling, %d erros.',
                        $stats['created'],
                        $stats['updated'],
                        $stats['synced_to_bling'],
                        $stats['errors']
                    ), 
                    30
                );
                set_transient('rodust_sync_message_type', 'success', 30);
            } else {
                $error_msg = $response['error'] ?? 'Erro desconhecido';
                error_log('ERROR syncing customers: ' . $error_msg);
                
                set_transient('rodust_sync_message', 'Erro ao sincronizar clientes: ' . $error_msg, 30);
                set_transient('rodust_sync_message_type', 'error', 30);
            }

        } catch (Exception $e) {
            error_log('EXCEPTION in customer sync: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            set_transient('rodust_sync_message', 'Erro na sincronização: ' . $e->getMessage(), 30);
            set_transient('rodust_sync_message_type', 'error', 30);
        }

        wp_redirect(admin_url('admin.php?page=rodust-sync'));
        exit;
    }
}

// Initialize
new Rodust_Sync_Settings();
