<?php
/**
 * Settings management
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

/**
 * Manage plugin settings (only safe configurations)
 */
class Rodust_Settings {
    
    private static $instance = null;
    
    /**
     * Settings option name
     */
    const OPTION_NAME = 'rodust_ecommerce_settings';

    /**
     * Get instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_settings_page']);
    }

    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        // Menu raiz principal
        add_menu_page(
            __('Rodust Ecommerce', 'rodust-ecommerce'),
            __('Rodust Ecommerce', 'rodust-ecommerce'),
            'manage_options',
            'rodust-ecommerce',
            [$this, 'render_settings_page'],
            'dashicons-cart',
            59
        );
        
        // Submenu - Configurações (renomeia o primeiro item)
        add_submenu_page(
            'rodust-ecommerce',
            __('Configurações', 'rodust-ecommerce'),
            __('Configurações', 'rodust-ecommerce'),
            'manage_options',
            'rodust-ecommerce'
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'rodust_ecommerce_settings_group',
            self::OPTION_NAME,
            [$this, 'sanitize_settings']
        );

        // Seção: Conexão API
        add_settings_section(
            'rodust_api_section',
            __('Configurações da API Laravel', 'rodust-ecommerce'),
            [$this, 'render_api_section_description'],
            'rodust-ecommerce'
        );

        add_settings_field(
            'api_url',
            __('URL da API', 'rodust-ecommerce'),
            [$this, 'render_api_url_field'],
            'rodust-ecommerce',
            'rodust_api_section'
        );

        add_settings_field(
            'api_timeout',
            __('Timeout (segundos)', 'rodust-ecommerce'),
            [$this, 'render_timeout_field'],
            'rodust-ecommerce',
            'rodust_api_section'
        );

        // Seção: Sincronização
        add_settings_section(
            'rodust_sync_section',
            __('Sincronização', 'rodust-ecommerce'),
            [$this, 'render_sync_section_description'],
            'rodust-ecommerce'
        );

        add_settings_field(
            'sync_enabled',
            __('Sincronização Automática', 'rodust-ecommerce'),
            [$this, 'render_sync_enabled_field'],
            'rodust-ecommerce',
            'rodust_sync_section'
        );

        add_settings_field(
            'sync_interval',
            __('Intervalo de Sincronização', 'rodust-ecommerce'),
            [$this, 'render_sync_interval_field'],
            'rodust-ecommerce',
            'rodust_sync_section'
        );

        // Seção: Display
        add_settings_section(
            'rodust_display_section',
            __('Exibição', 'rodust-ecommerce'),
            null,
            'rodust-ecommerce'
        );

        add_settings_field(
            'products_per_page',
            __('Produtos por Página', 'rodust-ecommerce'),
            [$this, 'render_products_per_page_field'],
            'rodust-ecommerce',
            'rodust_display_section'
        );

        // Seção: Integrações Externas
        add_settings_section(
            'rodust_integrations_section',
            __('Integrações Externas', 'rodust-ecommerce'),
            [$this, 'render_integrations_section_description'],
            'rodust-ecommerce'
        );

        add_settings_field(
            'integrations_info',
            __('Painel de Configurações', 'rodust-ecommerce'),
            [$this, 'render_integrations_info_field'],
            'rodust-ecommerce',
            'rodust_integrations_section'
        );
    }

    /**
     * Render API section description
     */
    public function render_api_section_description() {
        echo '<p>' . __('Configure a conexão com o backend Laravel. <strong>Nunca insira tokens de terceiros aqui!</strong>', 'rodust-ecommerce') . '</p>';
        echo '<p style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;">';
        echo '<strong>⚠️ Segurança:</strong> Credenciais do Bling devem ficar apenas no arquivo <code>.env</code> do Laravel.';
        echo '</p>';
    }

    /**
     * Render sync section description
     */
    public function render_sync_section_description() {
        echo '<p>' . __('Configure como os produtos são sincronizados com o Laravel.', 'rodust-ecommerce') . '</p>';
    }

    /**
     * Render integrations section description
     */
    public function render_integrations_section_description() {
        echo '<p>' . __('Configurações de pagamento e frete são gerenciadas no servidor Laravel por segurança.', 'rodust-ecommerce') . '</p>';
        echo '<p style="background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin-top: 10px;">';
        echo '<strong>🔒 Segurança:</strong> Credenciais de APIs externas (Mercado Pago, Melhor Envio, Bling) ficam no arquivo <code>.env</code> do Laravel.<br>';
        echo 'Isso garante que informações sensíveis não fiquem expostas no banco de dados do WordPress.';
        echo '</p>';
    }

    /**
     * Render integrations info field
     */
    public function render_integrations_info_field() {
        $api_url = $this->get_setting('api_url', rodust_plugin_get_api_url());
        $base_url = rtrim(str_replace('/api', '', $api_url), '/');
        
        echo '<div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 4px;">';
        
        echo '<h3 style="margin-top: 0;">🎛️ Painéis Administrativos Laravel</h3>';
        echo '<p>Acesse os painéis do Laravel para gerenciar integrações:</p>';
        
        echo '<table class="widefat" style="margin-top: 15px;">';
        echo '<thead><tr><th>Integração</th><th>O que gerencia</th><th>Ação</th></tr></thead>';
        echo '<tbody>';
        
        // Bling
        echo '<tr>';
        echo '<td><strong>🔗 Bling (ERP)</strong></td>';
        echo '<td>Sincronização de produtos, clientes e pedidos</td>';
        echo '<td><a href="' . esc_url($base_url . '/bling') . '" class="button button-primary" target="_blank">Abrir Painel Bling</a></td>';
        echo '</tr>';
        
        // Mercado Pago (futuro)
        echo '<tr>';
        echo '<td><strong>💳 Mercado Pago</strong></td>';
        echo '<td>Credenciais de pagamento (sandbox/produção)</td>';
        echo '<td><a href="' . esc_url($base_url . '/admin/integrations/mercadopago') . '" class="button" target="_blank" disabled>Em breve</a></td>';
        echo '</tr>';
        
        // Melhor Envio (futuro)
        echo '<tr>';
        echo '<td><strong>📦 Melhor Envio</strong></td>';
        echo '<td>Credenciais de frete (sandbox/produção)</td>';
        echo '<td><a href="' . esc_url($base_url . '/admin/integrations/melhorenvio') . '" class="button" target="_blank" disabled>Em breve</a></td>';
        echo '</tr>';
        
        // Admin geral (futuro)
        echo '<tr style="background: #f9f9f9;">';
        echo '<td><strong>⚙️ Painel Admin Completo</strong></td>';
        echo '<td>Todas as configurações do sistema</td>';
        echo '<td><a href="' . esc_url($base_url . '/admin') . '" class="button button-secondary" target="_blank" disabled>Em breve</a></td>';
        echo '</tr>';
        
        echo '</tbody></table>';
        
        echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">';
        echo '<strong>📝 Nota:</strong> Os painéis em "Em breve" serão implementados nas próximas versões.<br>';
        echo 'Por enquanto, configure diretamente no arquivo <code>.env</code> do Laravel.';
        echo '</div>';
        
        echo '</div>';
    }

    /**
     * Render API URL field
     */
    public function render_api_url_field() {
        $settings = $this->get_settings();
        $api_url = $settings['api_url'] ?? rodust_plugin_get_api_url();
        ?>
        <input type="url" 
               name="<?php echo self::OPTION_NAME; ?>[api_url]" 
               value="<?php echo esc_attr($api_url); ?>" 
               class="regular-text"
               placeholder="<?php echo esc_attr(rodust_plugin_get_api_url()); ?>">
        <p class="description">
            <?php _e('URL base da API Laravel (ex: http://localhost:8000/api ou https://api.rodust.com.br/api)', 'rodust-ecommerce'); ?>
        </p>
        <button type="button" class="button button-secondary" id="rodust-test-connection">
            <?php _e('Testar Conexão', 'rodust-ecommerce'); ?>
        </button>
        <span id="rodust-connection-status"></span>
        <?php
    }

    /**
     * Render timeout field
     */
    public function render_timeout_field() {
        $settings = $this->get_settings();
        $timeout = $settings['api_timeout'] ?? 30;
        ?>
        <input type="number" 
               name="<?php echo self::OPTION_NAME; ?>[api_timeout]" 
               value="<?php echo esc_attr($timeout); ?>" 
               min="5" 
               max="60" 
               class="small-text">
        <span><?php _e('segundos', 'rodust-ecommerce'); ?></span>
        <p class="description">
            <?php _e('Tempo máximo de espera para requisições HTTP.', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render sync enabled field
     */
    public function render_sync_enabled_field() {
        $settings = $this->get_settings();
        $enabled = $settings['sync_enabled'] ?? true;
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo self::OPTION_NAME; ?>[sync_enabled]" 
                   value="1" 
                   <?php checked($enabled, true); ?>>
            <?php _e('Sincronizar produtos automaticamente com Laravel', 'rodust-ecommerce'); ?>
        </label>
        <?php
    }

    /**
     * Render sync interval field
     */
    public function render_sync_interval_field() {
        $settings = $this->get_settings();
        $interval = $settings['sync_interval'] ?? 3600;
        ?>
        <select name="<?php echo self::OPTION_NAME; ?>[sync_interval]">
            <option value="300" <?php selected($interval, 300); ?>>5 minutos</option>
            <option value="900" <?php selected($interval, 900); ?>>15 minutos</option>
            <option value="1800" <?php selected($interval, 1800); ?>>30 minutos</option>
            <option value="3600" <?php selected($interval, 3600); ?>>1 hora</option>
            <option value="7200" <?php selected($interval, 7200); ?>>2 horas</option>
            <option value="21600" <?php selected($interval, 21600); ?>>6 horas</option>
            <option value="86400" <?php selected($interval, 86400); ?>>1 dia</option>
        </select>
        <p class="description">
            <?php _e('Frequência de verificação de atualizações de produtos.', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render products per page field
     */
    public function render_products_per_page_field() {
        $settings = $this->get_settings();
        $per_page = $settings['products_per_page'] ?? 12;
        ?>
        <input type="number" 
               name="<?php echo self::OPTION_NAME; ?>[products_per_page]" 
               value="<?php echo esc_attr($per_page); ?>" 
               min="1" 
               max="100" 
               class="small-text">
        <p class="description">
            <?php _e('Número de produtos exibidos por página no shortcode [rodust_products].', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render shipping section description
     */
    public function render_shipping_section_description() {
        echo '<p>' . __('Configure o Melhor Envio para calcular frete automaticamente.', 'rodust-ecommerce') . '</p>';
        echo '<p>' . __('Criar conta: <a href="https://melhorenvio.com.br" target="_blank">https://melhorenvio.com.br</a>', 'rodust-ecommerce') . '</p>';
    }

    /**
     * Render origin postal code field
     */
    public function render_origin_postal_code_field() {
        $settings = $this->get_settings();
        $postal_code = $settings['origin_postal_code'] ?? '';
        ?>
        <input type="text" 
               name="<?php echo self::OPTION_NAME; ?>[origin_postal_code]" 
               value="<?php echo esc_attr($postal_code); ?>" 
               maxlength="9"
               class="regular-text"
               placeholder="00000-000">
        <p class="description">
            <?php _e('CEP de onde os produtos serão enviados.', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render Melhor Envio token field
     */
    public function render_melhorenvio_token_field() {
        $settings = $this->get_settings();
        $token = $settings['melhorenvio_token'] ?? '';
        ?>
        <input type="password" 
               name="<?php echo self::OPTION_NAME; ?>[melhorenvio_token]" 
               value="<?php echo esc_attr($token); ?>" 
               class="regular-text">
        <p class="description">
            <?php _e('Token de acesso da API Melhor Envio. Obtenha em: Configurações → Token & Chaves', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render Melhor Envio sandbox field
     */
    public function render_melhorenvio_sandbox_field() {
        $settings = $this->get_settings();
        $sandbox = $settings['melhorenvio_sandbox'] ?? false;
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo self::OPTION_NAME; ?>[melhorenvio_sandbox]" 
                   value="1" 
                   <?php checked($sandbox, true); ?>>
            <?php _e('Usar ambiente de testes (sandbox)', 'rodust-ecommerce'); ?>
        </label>
        <?php
    }

    /**
     * Render payment section description
     */
    public function render_payment_section_description() {
        echo '<p>' . __('Configure o Mercado Pago para processar pagamentos (PIX, Cartão, Boleto).', 'rodust-ecommerce') . '</p>';
        echo '<p>' . __('Obter credenciais: <a href="https://www.mercadopago.com.br/developers/panel/app" target="_blank">Painel de Desenvolvedores</a>', 'rodust-ecommerce') . '</p>';
    }

    /**
     * Render Mercado Pago access token field
     */
    public function render_mercadopago_access_token_field() {
        $settings = $this->get_settings();
        $token = $settings['mercadopago_access_token'] ?? '';
        ?>
        <input type="password" 
               name="<?php echo self::OPTION_NAME; ?>[mercadopago_access_token]" 
               value="<?php echo esc_attr($token); ?>" 
               class="regular-text">
        <p class="description">
            <?php _e('Access Token do Mercado Pago (começa com APP_USR-...)', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render Mercado Pago public key field
     */
    public function render_mercadopago_public_key_field() {
        $settings = $this->get_settings();
        $public_key = $settings['mercadopago_public_key'] ?? '';
        ?>
        <input type="text" 
               name="<?php echo self::OPTION_NAME; ?>[mercadopago_public_key]" 
               value="<?php echo esc_attr($public_key); ?>" 
               class="regular-text">
        <p class="description">
            <?php _e('Public Key do Mercado Pago (começa com APP_USR-...)', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render Mercado Pago sandbox field
     */
    public function render_mercadopago_sandbox_field() {
        $settings = $this->get_settings();
        $sandbox = $settings['mercadopago_sandbox'] ?? false;
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo self::OPTION_NAME; ?>[mercadopago_sandbox]" 
                   value="1" 
                   <?php checked($sandbox, true); ?>>
            <?php _e('Usar ambiente de testes (sandbox)', 'rodust-ecommerce'); ?>
        </label>
        <p class="description">
            <?php _e('Ative para usar as credenciais de teste. Desative em produção.', 'rodust-ecommerce'); ?>
        </p>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php settings_errors('rodust_ecommerce_settings'); ?>

            <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="margin-top: 0;">📚 Documentação</h3>
                <p><strong>README completo:</strong> <code>wp-content/plugins/rodust-ecommerce/README.md</code></p>
                <p><strong>Comando de validação Bling:</strong></p>
                <pre style="background: #f8f9fa; padding: 10px; overflow-x: auto;">cd M:\Websites\rodust.com.br\ecommerce
docker compose exec laravel.test php artisan bling:validate --token=SEU_TOKEN</pre>
                <p><strong>URLs:</strong></p>
                <ul>
                    <li>WordPress (XAMPP): <code>http://localhost</code></li>
                    <li>Laravel (Docker): <code>http://localhost:8000</code></li>
                </ul>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields('rodust_ecommerce_settings_group');
                do_settings_sections('rodust-ecommerce');
                submit_button(__('Salvar Configurações', 'rodust-ecommerce'));
                ?>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#rodust-test-connection').on('click', function() {
                var btn = $(this);
                var status = $('#rodust-connection-status');
                var apiUrl = $('input[name="<?php echo self::OPTION_NAME; ?>[api_url]"]').val();

                btn.prop('disabled', true).text('Testando...');
                status.html('<span style="color: #999;">⏳ Conectando...</span>');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'rodust_test_api_connection',
                        nonce: '<?php echo wp_create_nonce("rodust_test_connection"); ?>',
                        api_url: apiUrl
                    },
                    success: function(response) {
                        if (response.success) {
                            status.html('<span style="color: #28a745;">✓ Conexão bem-sucedida!</span>');
                        } else {
                            var errorMsg = (response.data && response.data.message) ? response.data.message : 'Erro desconhecido';
                            status.html('<span style="color: #dc3545;">✗ Erro: ' + errorMsg + '</span>');
                        }
                    },
                    error: function() {
                        status.html('<span style="color: #dc3545;">✗ Erro de conexão</span>');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('<?php _e("Testar Conexão", "rodust-ecommerce"); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Sanitize settings before saving
     */
    public function sanitize_settings($input) {
        $sanitized = [];

        if (isset($input['api_url'])) {
            $sanitized['api_url'] = esc_url_raw(rtrim($input['api_url'], '/'));
        }

        if (isset($input['api_timeout'])) {
            $sanitized['api_timeout'] = absint($input['api_timeout']);
            $sanitized['api_timeout'] = max(5, min(60, $sanitized['api_timeout']));
        }

        $sanitized['sync_enabled'] = isset($input['sync_enabled']);

        if (isset($input['sync_interval'])) {
            $sanitized['sync_interval'] = absint($input['sync_interval']);
        }

        if (isset($input['products_per_page'])) {
            $sanitized['products_per_page'] = absint($input['products_per_page']);
            $sanitized['products_per_page'] = max(1, min(100, $sanitized['products_per_page']));
        }

        return $sanitized;
    }

    /**
     * Get all settings
     */
    public function get_settings() {
        $defaults = [
            'api_url' => 'http://localhost:8000/api',
            'api_timeout' => 30,
            'sync_enabled' => true,
            'sync_interval' => 3600,
            'products_per_page' => 12,
        ];

        $settings = get_option(self::OPTION_NAME, []);
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Get specific setting
     */
    public static function get($key, $default = null) {
        $instance = self::instance();
        $settings = $instance->get_settings();
        return $settings[$key] ?? $default;
    }
}

