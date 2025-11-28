<?php
/**
 * Main plugin class
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

/**
 * Singleton class for plugin initialization
 */
final class Rodust_Ecommerce {
    
    /**
     * Plugin instance
     *
     * @var Rodust_Ecommerce
     */
    private static $instance = null;

    /**
     * Get plugin instance (Singleton pattern)
     *
     * @return Rodust_Ecommerce
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - Initialize plugin
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        // Core
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-api-client.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-settings.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-rest-products.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-sync-settings.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-customer-sync.php';
        
        // Features
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-product-post-type.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-cart-manager.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-shipping-calculator.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-payment-gateway.php';
        
        // Frontend
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-shortcodes.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-ajax-handlers.php';
        require_once RODUST_ECOMMERCE_PATH . 'includes/class-search-config.php';
        
        // Admin
        if (is_admin()) {
            require_once RODUST_ECOMMERCE_PATH . 'admin/class-admin-menu.php';
            require_once RODUST_ECOMMERCE_PATH . 'admin/class-admin-settings.php';
        }
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'init'], 0);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        
        // Activation/Deactivation
        register_activation_hook(RODUST_ECOMMERCE_BASENAME, [$this, 'activate']);
        register_deactivation_hook(RODUST_ECOMMERCE_BASENAME, [$this, 'deactivate']);
    }

    /**
     * Plugin initialization
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('rodust-ecommerce', false, dirname(RODUST_ECOMMERCE_BASENAME) . '/languages');
        
        // Initialize components
        Rodust_Product_Post_Type::instance();
        Rodust_Cart_Manager::instance(); // Inicializar carrinho para startar sessão
        Rodust_Shortcodes::instance();
        Rodust_Ajax_Handlers::instance();
        Rodust_Settings::instance();
        Rodust_REST_Products::instance(); // Initialize REST API
        
        if (is_admin()) {
            Rodust_Admin_Menu::instance();
        }
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'rodust-ecommerce',
            RODUST_ECOMMERCE_URL . 'assets/css/style.css',
            [],
            RODUST_ECOMMERCE_VERSION
        );

        wp_enqueue_script(
            'rodust-ecommerce',
            RODUST_ECOMMERCE_URL . 'assets/js/rodust-ecommerce.js',
            ['jquery'],
            RODUST_ECOMMERCE_VERSION,
            true
        );

        wp_localize_script('rodust-ecommerce', 'rodustEcommerce', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rodust_ecommerce_nonce'),
            'apiUrl' => get_option('rodust_api_url', 'http://localhost/api'),
            'i18n' => [
                'addedToCart' => __('Produto adicionado ao carrinho', 'rodust-ecommerce'),
                'error' => __('Ocorreu um erro. Tente novamente.', 'rodust-ecommerce'),
                'loading' => __('Carregando...', 'rodust-ecommerce'),
            ],
        ]);
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'rodust') === false) {
            return;
        }

        wp_enqueue_style(
            'rodust-ecommerce-admin',
            RODUST_ECOMMERCE_URL . 'assets/css/admin.css',
            [],
            RODUST_ECOMMERCE_VERSION
        );

        wp_enqueue_script(
            'rodust-ecommerce-admin',
            RODUST_ECOMMERCE_URL . 'assets/js/admin.js',
            ['jquery'],
            RODUST_ECOMMERCE_VERSION,
            true
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create custom post types
        Rodust_Product_Post_Type::register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        add_option('rodust_api_url', 'http://localhost/api');
        add_option('rodust_sync_enabled', '1');
        add_option('rodust_sync_interval', '3600'); // 1 hora
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled crons
        wp_clear_scheduled_hook('rodust_sync_products');
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}
