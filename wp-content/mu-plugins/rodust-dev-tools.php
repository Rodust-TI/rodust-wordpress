<?php
/**
 * Plugin Name: Rodust Dev Tools
 * Description: Ferramentas de desenvolvimento e manutenção para Rodust (apenas para ambientes dev/local)
 * Version: 1.0.0
 * Author: Rodust TI
 * 
 * Must-Use Plugin - Carrega automaticamente no WordPress
 */

// Segurança: Impedir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Verificar se está em ambiente de desenvolvimento
define('RODUST_IS_DEV', in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8080', 'localhost:8443', '127.0.0.1']));

// Só carregar em ambiente de desenvolvimento
if (!RODUST_IS_DEV) {
    return;
}

/**
 * Adicionar menu no admin
 */
add_action('admin_menu', function() {
    add_menu_page(
        'Rodust Dev Tools',           // Page title
        'Dev Tools',                  // Menu title
        'manage_options',             // Capability
        'rodust-dev-tools',           // Menu slug
        'rodust_dev_tools_page',      // Callback
        'dashicons-admin-tools',      // Icon
        100                           // Position
    );
    
    // Submenu: Limpar Produtos
    add_submenu_page(
        'rodust-dev-tools',
        'Limpar Produtos',
        'Limpar Produtos',
        'manage_options',
        'rodust-clean-products',
        'rodust_clean_products_page'
    );
    
    // Submenu: Flush Rewrite Rules
    add_submenu_page(
        'rodust-dev-tools',
        'Flush Rewrite Rules',
        'Flush Rewrite',
        'manage_options',
        'rodust-flush-rewrite',
        'rodust_flush_rewrite_page'
    );
    
    // Submenu: Plugin Manager
    add_submenu_page(
        'rodust-dev-tools',
        'Gerenciar Plugins',
        'Plugins',
        'manage_options',
        'rodust-plugin-manager',
        'rodust_plugin_manager_page'
    );
    
    // Submenu: Test API
    add_submenu_page(
        'rodust-dev-tools',
        'Testar API Laravel',
        'Test API',
        'manage_options',
        'rodust-test-api',
        'rodust_test_api_page'
    );
});

/**
 * Página principal do Dev Tools
 */
function rodust_dev_tools_page() {
    ?>
    <div class="wrap">
        <h1>🛠️ Rodust Dev Tools</h1>
        <p>Ferramentas de desenvolvimento e manutenção para o WordPress.</p>
        
        <div class="card" style="max-width: 800px;">
            <h2>⚠️ Ambiente: <?php echo RODUST_IS_DEV ? '<span style="color: orange;">DESENVOLVIMENTO</span>' : '<span style="color: green;">PRODUÇÃO</span>'; ?></h2>
            <p>Este painel está disponível apenas em ambiente de desenvolvimento (localhost).</p>
            
            <h3>Ferramentas Disponíveis:</h3>
            <ul>
                <li>🗑️ <strong>Limpar Produtos</strong> - Remove todos os produtos do tipo <code>rodust_product</code></li>
                <li>🔄 <strong>Flush Rewrite Rules</strong> - Atualiza as regras de reescrita de URL</li>
                <li>🔌 <strong>Gerenciar Plugins</strong> - Ativar/desativar plugins rapidamente</li>
                <li>🔗 <strong>Test API</strong> - Testar conexão com Laravel API</li>
            </ul>
            
            <hr>
            
            <h3>Informações do Sistema:</h3>
            <table class="widefat">
                <tbody>
                    <tr>
                        <td><strong>WordPress Version:</strong></td>
                        <td><?php echo get_bloginfo('version'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>PHP Version:</strong></td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Site URL:</strong></td>
                        <td><?php echo get_site_url(); ?></td>
                    </tr>
                    <tr>
                        <td><strong>API URL:</strong></td>
                        <td><?php echo defined('RODUST_API_URL') ? RODUST_API_URL : 'Não configurado'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Debug Mode:</strong></td>
                        <td><?php echo WP_DEBUG ? '✅ Ativo' : '❌ Inativo'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Página: Limpar Produtos
 */
function rodust_clean_products_page() {
    // Processar limpeza se confirmado
    if (isset($_POST['confirmar_limpeza']) && check_admin_referer('rodust_clean_products')) {
        $args = [
            'post_type' => 'rodust_product',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ];
        
        $query = new WP_Query($args);
        $deleted = 0;
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post(get_the_ID(), true);
                $deleted++;
            }
            wp_reset_postdata();
        }
        
        echo '<div class="notice notice-success"><p>✅ <strong>' . $deleted . ' produtos deletados com sucesso!</strong></p></div>';
    }
    
    // Listar produtos
    $args = [
        'post_type' => 'rodust_product',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'orderby' => 'ID',
        'order' => 'ASC'
    ];
    
    $query = new WP_Query($args);
    
    ?>
    <div class="wrap">
        <h1>🗑️ Limpar Produtos</h1>
        <p>Total de produtos encontrados: <strong><?php echo $query->found_posts; ?></strong></p>
        
        <?php if ($query->have_posts()): ?>
            <div class="card" style="max-width: 1200px; overflow-x: auto;">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>SKU</th>
                            <th>Bling ID</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($query->have_posts()): $query->the_post(); ?>
                            <tr>
                                <td><?php echo get_the_ID(); ?></td>
                                <td><strong><?php the_title(); ?></strong></td>
                                <td><?php echo get_post_meta(get_the_ID(), '_sku', true) ?: 'N/A'; ?></td>
                                <td><?php echo get_post_meta(get_the_ID(), '_bling_id', true) ?: 'N/A'; ?></td>
                                <td>R$ <?php echo number_format((float)get_post_meta(get_the_ID(), '_price', true), 2, ',', '.'); ?></td>
                                <td><?php echo get_post_meta(get_the_ID(), '_stock', true) ?: '0'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            
            <div class="card" style="max-width: 600px; background: #fff3cd;">
                <h2 style="color: #856404;">⚠️ Atenção</h2>
                <p>Esta ação é <strong>IRREVERSÍVEL</strong> e deletará <strong>TODOS os <?php echo $query->found_posts; ?> produtos</strong>.</p>
                
                <form method="post" onsubmit="return confirm('Tem certeza que deseja deletar TODOS os produtos? Esta ação não pode ser desfeita!');">
                    <?php wp_nonce_field('rodust_clean_products'); ?>
                    <button type="submit" name="confirmar_limpeza" class="button button-primary button-large" style="background: #dc3545;">
                        🗑️ Deletar Todos os Produtos
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="notice notice-info"><p>✅ Nenhum produto encontrado no banco de dados.</p></div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
}

/**
 * Página: Flush Rewrite Rules
 */
function rodust_flush_rewrite_page() {
    // Processar flush se solicitado
    if (isset($_POST['flush_rewrite']) && check_admin_referer('rodust_flush_rewrite')) {
        flush_rewrite_rules(true);
        echo '<div class="notice notice-success"><p>✅ <strong>Rewrite rules atualizadas com sucesso!</strong></p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>🔄 Flush Rewrite Rules</h1>
        <p>Atualiza as regras de reescrita de URL do WordPress. Útil quando:</p>
        <ul>
            <li>Alterou permalinks</li>
            <li>Registrou novo post type</li>
            <li>URLs retornam 404 incorretamente</li>
        </ul>
        
        <div class="card" style="max-width: 800px;">
            <h2>Post Types Personalizados</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Post Type</th>
                        <th>Has Archive</th>
                        <th>Rewrite Slug</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $post_types = get_post_types(['_builtin' => false], 'objects');
                    foreach ($post_types as $post_type):
                    ?>
                        <tr>
                            <td><code><?php echo $post_type->name; ?></code></td>
                            <td><?php echo $post_type->has_archive ? '✅ Sim' : '❌ Não'; ?></td>
                            <td><?php echo $post_type->rewrite['slug'] ?? 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <br>
            
            <form method="post">
                <?php wp_nonce_field('rodust_flush_rewrite'); ?>
                <button type="submit" name="flush_rewrite" class="button button-primary button-large">
                    🔄 Atualizar Rewrite Rules
                </button>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Página: Plugin Manager
 */
function rodust_plugin_manager_page() {
    // Processar ações
    if (isset($_POST['plugin_action']) && check_admin_referer('rodust_plugin_manager')) {
        $plugin = sanitize_text_field($_POST['plugin']);
        $action = sanitize_text_field($_POST['plugin_action']);
        
        if ($action === 'activate') {
            activate_plugin($plugin);
            echo '<div class="notice notice-success"><p>✅ Plugin ativado!</p></div>';
        } elseif ($action === 'deactivate') {
            deactivate_plugins($plugin);
            echo '<div class="notice notice-success"><p>✅ Plugin desativado!</p></div>';
        }
    }
    
    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);
    
    ?>
    <div class="wrap">
        <h1>🔌 Gerenciar Plugins</h1>
        
        <div class="card" style="max-width: 1000px;">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Versão</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_plugins as $plugin_path => $plugin_data): ?>
                        <?php $is_active = in_array($plugin_path, $active_plugins); ?>
                        <tr>
                            <td><strong><?php echo $plugin_data['Name']; ?></strong></td>
                            <td><?php echo $plugin_data['Version']; ?></td>
                            <td>
                                <?php if ($is_active): ?>
                                    <span style="color: green;">✅ Ativo</span>
                                <?php else: ?>
                                    <span style="color: gray;">❌ Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('rodust_plugin_manager'); ?>
                                    <input type="hidden" name="plugin" value="<?php echo esc_attr($plugin_path); ?>">
                                    <?php if ($is_active): ?>
                                        <button type="submit" name="plugin_action" value="deactivate" class="button">Desativar</button>
                                    <?php else: ?>
                                        <button type="submit" name="plugin_action" value="activate" class="button button-primary">Ativar</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Página: Test API
 */
function rodust_test_api_page() {
    $api_url = defined('RODUST_API_URL') ? RODUST_API_URL : '';
    $test_result = null;
    
    // Testar API se solicitado
    if (isset($_POST['test_api']) && check_admin_referer('rodust_test_api')) {
        $response = wp_remote_get($api_url . '/products', [
            'timeout' => 10
        ]);
        
        if (is_wp_error($response)) {
            $test_result = [
                'success' => false,
                'message' => $response->get_error_message()
            ];
        } else {
            $test_result = [
                'success' => true,
                'status' => wp_remote_retrieve_response_code($response),
                'body' => wp_remote_retrieve_body($response)
            ];
        }
    }
    
    ?>
    <div class="wrap">
        <h1>🔗 Testar API Laravel</h1>
        <p>URL da API: <code><?php echo $api_url ?: 'Não configurado'; ?></code></p>
        
        <div class="card" style="max-width: 800px;">
            <form method="post">
                <?php wp_nonce_field('rodust_test_api'); ?>
                <button type="submit" name="test_api" class="button button-primary button-large">
                    🔗 Testar Conexão
                </button>
            </form>
            
            <?php if ($test_result): ?>
                <hr>
                
                <?php if ($test_result['success']): ?>
                    <div class="notice notice-success inline"><p>✅ <strong>Conexão OK!</strong> Status: <?php echo $test_result['status']; ?></p></div>
                    
                    <h3>Resposta:</h3>
                    <pre style="background: #f5f5f5; padding: 15px; overflow-x: auto;"><?php echo esc_html(substr($test_result['body'], 0, 1000)); ?></pre>
                <?php else: ?>
                    <div class="notice notice-error inline"><p>❌ <strong>Erro:</strong> <?php echo esc_html($test_result['message']); ?></p></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
